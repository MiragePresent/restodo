<?php

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

if (!function_exists('env')) {
    function env($name, $default = null) {
        static $variables;

        if ($variables === null) {
            $variables = (new \Dotenv\Environment\DotenvFactory([
                new \Dotenv\Environment\Adapter\EnvConstAdapter(),
                new \Dotenv\Environment\Adapter\ServerConstAdapter()])
            )->createImmutable();
        }

        return $variables->offsetExists($name) ? $variables->get($name) : $default;
    }
}
