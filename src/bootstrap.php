<?php

require "../vendor/autoload.php";

define("ROOT_PATH", dirname(__DIR__));

// Load environment configurations
Dotenv\Dotenv::create(ROOT_PATH)->load();
