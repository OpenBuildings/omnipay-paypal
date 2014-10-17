<?php

error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('Omnipay\\PaypalRest\\Test\\', __DIR__.'/src');
