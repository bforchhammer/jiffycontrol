<?php
require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app = new Silex\Application();

// Load yaml config as a shared service.
$app['config'] = $app->share(function() {
    return Yaml::parse(__DIR__ . '/config.yml');
});

// Register the jiffybox api as shared service.
$app['jiffybox'] = $app->share(function ($app) {
    if (empty($app['config']['jiffybox']['token'])) {
        throw new Exception("Property jiffybox.token not configured in config.yml!");
    }
    if (empty($app['config']['jiffybox']['server'])) {
        throw new Exception("Property jiffybox.server not configured in config.yml!");
    }
    $api = new Api\JiffyBox($app['config']['jiffybox']['token']);
    $api->setId($app['config']['jiffybox']['server']);
    return $api;
});

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Set debug from configuration.
$app['debug'] = $app['config']['debug'];

$app->get('/', function() use ($app) {
    $box = $app['jiffybox']->get();
    $messages = $app['jiffybox']->getLastMessages();
    $error = $app['jiffybox']->getCurlError();
    if (!empty($error)) $messages[] = $error;
    return $app['twig']->render('index.twig', array(
      'server' => array(
        'ip' => $box->ips->public[0],
        'name' => $box->name,
        'status' => $box->status,
        'running' => $box->running,
      ),
      'errors' => $errors,
      'messages' => $messages,
    ));
})->bind('homepage');

$app->get('/freeze', function() use ($app) {
    $app['jiffybox']->freeze();
    return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('freeze');

$app->get('/thaw', function() use ($app) {
    $planId = $app['jiffybox']->get()->plan->id;
    $app['jiffybox']->thaw($planId);
    return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('thaw');

$app->get('/start', function() use ($app) {
    $app['jiffybox']->start();
    return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('start');

$app->get('/shutdown', function() use ($app) {
    $app['jiffybox']->stop();
    return $app->redirect($app['url_generator']->generate('homepage'));
})->bind('stop');

$app->run();
