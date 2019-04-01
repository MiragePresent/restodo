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

if (!function_exists("url")) {
    function url(string $path): string {
        return sprintf("http://%s%s", BASE_URL, $path);
    }
}

if (!function_exists("decamelize")) {
    function decamelize(string $input): string {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}

if (!function_exists("camelize")) {
    function camelize(string $input): string {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
        $str[0] = strtolower($str[0]);

        return $str;
    }
}
