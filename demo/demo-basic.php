<?php
// phpinfo();

require __DIR__ . '/../vendor/autoload.php';

//initialize a PlasterApplication, passing it a
//list of config files to use
$config = array(__DIR__ . '/demo.yaml');
$app    = new jobyone\Plaster\PlasterApplication($config);

//render with no arguments to use $_SERVER['PATH_INFO']
//as the url
$app->render();

//render() can also take a URL explicitly as
//its first argument, if you want to get it in
//some other way
