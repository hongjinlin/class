<?php

ini_set("display_errors", 1);
 
error_reporting(E_ALL & ~E_NOTICE);

define('RUNLEVELS', 127);

ob_start();

$host = $_SERVER['HTTP_HOST'];

$hostArr = explode('.', $host);

if (count($hostArr) > 2) {
    unset($hostArr[0]);
}

$domain = implode('.', $hostArr);

session_set_cookie_params(0, '/', ".{$domain}", false, false);

session_start();

/* 指向public的上一级 */
define("APP_PATH", realpath(dirname(__FILE__) . '/../application/'));
require APP_PATH . '/../vendor/autoload.php';
$application = new Yaf_Application(APP_PATH . "/../config/application.ini");

$application->bootstrap()->run();
