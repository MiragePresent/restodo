<?php

spl_autoload_register(function($class) {
    $src = ROOT_PATH . "/src/";
    $path = str_replace("\\", "/", substr($class, 4));
    $include = $src . $path . ".php";

    if (!file_exists($include)) {
        throw new \Exception("Undefined class {$class}");
    }

    include $include;
}, true, true);
