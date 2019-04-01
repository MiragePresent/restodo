<?php

// Root app directory
define("ROOT_PATH", dirname(__DIR__));

// Require configurations
require ROOT_PATH . "/config.php";
require ROOT_PATH . "/src/class_loader.php";
require ROOT_PATH . "/src/functions.php";

$app = new \App\Application(new \App\Http\Router());
