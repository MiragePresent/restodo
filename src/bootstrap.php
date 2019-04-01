<?php

// Root app directory
define("ROOT_PATH", dirname(__DIR__));

// Require configurations
require ROOT_PATH . "/config.php";
require ROOT_PATH . "/vendor/autoload.php";

$app = new \App\Application(new \App\Http\Router());
