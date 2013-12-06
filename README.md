JiffyControl
============

Simple web interface for controlling a server on the [JiffyBox](https://www.jiffybox.de/) cloud service.

The following operations are currently supported: Start, Shutdown, Freeze, Thaw

Installation
------------

1. Put the repository contents into your web root
1. Download [composer](http://getcomposer.org/) and install dependencies
1. Create and adjust the configuration file `config.yml`.

Command line:

    git clone git@github.com:bforchhammer/jiffycontrol.git
    cd jiffycontrol
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
    cp config.default.yml config.yml
    nano config.yml

Configuration
-------------

The following properties should be configured in `config.yml`:

- `jiffybox.token`: The API token for your JiffyBox Account. If you do not have one yet, you can create it via the [JiffyBox Control-Panel](https://admin.jiffybox.de/) (Account > API-Zugriff).
- `jiffybox.server`: The id of the server you wish to control. These can be found in the [JiffyBox Control-Panel](https://admin.jiffybox.de/) in the list of servers. The id is displayed after the name of each server and starts with an uppercase J (e.g. `J1234`). Only enter the numeric part in your config file! Note that this script currently only supports controlling one server.
- `users`: Add at least one username/password combination to secure the script via HTTP Authentication. If left empty, anyone will have access.


