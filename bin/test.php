#!/usr/bin/env php
<?php
require (__DIR__.'/../vendor/autoload.php');

$options     = 's:';
$longOptions = ['config-path:'];

$opts = getopt($options, $longOptions);

function help() {
    exit('Usage：bin/crontab.php -s [start | stop] [options]'."\n");
}

if (!isset($opts['s']) || !$opts['s'])
    help();

switch ($opts['s']) {
    case 'start':
        \Menory\Timer\TimerManager::start();
        break;
    default:
        help();
        break;
}