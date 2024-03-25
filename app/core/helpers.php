<?php

function escape($string)
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function autoload($class_name)
{
    if (is_file('app/core/' . $class_name . '.php')) {
        require_once 'app/core/' . $class_name . '.php';
    } else if (is_file('app/controllers/' . $class_name . '.php')) {
        require_once 'app/controllers/' . $class_name . '.php';
    } else if (is_file('app/models/' . $class_name . '.php')) {
        require_once 'app/models/' . $class_name . '.php';
    }
}

function cleaner($string)
{
    return ucfirst(preg_replace('/_/', ' ', $string));
}

function toClassName($string)
{
    $className = str_replace('_', ' ', $string);
    $className = ucwords($className) . "Controller";
    return str_replace(' ', '', $className);
}

function toFunctionName($string)
{
    $functionName = toClassName($string);
    return lcfirst($functionName);
}

function getOS()
{
    $os = PHP_OS;
    if (strtoupper(substr($os, 0, 3)) === 'WIN') {
        return "Windows";
    } elseif (strtoupper(substr($os, 0, 5)) === 'LINUX') {
        return "Linux";
    } elseif (strtoupper(substr($os, 0, 6)) === 'DARWIN') {
        return "macOS";
    } else {
        return "Unknown";
    }
}
