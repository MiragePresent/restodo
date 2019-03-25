<?php

// Root app directory
define("ROOT_PATH", dirname(__DIR__));

require ROOT_PATH . "/vendor/autoload.php";

// Load environment configurations
Dotenv\Dotenv::create(ROOT_PATH)->load();

$app = new \App\Application(new \App\Http\Router());
