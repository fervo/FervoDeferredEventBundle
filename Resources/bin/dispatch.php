<?php

set_time_limit(0);

$path=array(
    'short'=>__DIR__.'/../app/',
    'long'=>__DIR__.'/../../../../../../../app/',
);

(@include_once $path['short'] . 'bootstrap.php.cache') || @include_once $path['long'] . 'bootstrap.php.cache';
(@include_once $path['short'] . 'AppKernel.php') || @include_once $path['long'] . 'AppKernel.php';


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

/*
 * Look both in $_SERVER and $_POST after some data.
 */
$data=null;
if (isset($_SERVER['DEFERRED_DATA'])) {
    $data=$_SERVER['DEFERRED_DATA'];
} elseif (isset($_POST['DEFERRED_DATA'])) {
    $data=$_POST['DEFERRED_DATA'];
}

$input = new ArgvInput(['dispatch.php', 'fervo:deferred-event:dispatch', $data]);

$kernel = new AppKernel('dev', true);
$application = new Application($kernel);
$application->run($input);
