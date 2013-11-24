<?php

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

set_time_limit(0);

require_once '../app/bootstrap.php.cache';
require_once '../app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput(['dispatch.php', 'fervo:deferred-event:dispatch', $_SERVER['DEFERRED_DATA']]);

$kernel = new AppKernel('dev', true);
$application = new Application($kernel);
$application->run($input);
