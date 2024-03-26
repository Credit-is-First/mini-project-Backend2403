<?php
class Router
{
    public static function process()
    {
        $url = $_SERVER['REQUEST_URI'];

        // Remove the leading slash
        $url = ltrim($url, '/');

        // Split the URL into an array of parameters
        $parameters = explode('/', $url);

        // Access the parameters
        $className = isset($parameters[0]) && !empty($parameters[0]) ? $parameters[0] : 'home';
        $className = toClassName($className);

        if (class_exists($className)) {
            $functionName = isset($parameters[1]) ? $parameters[1] : '';

            $arguments = array();
            if ($functionName && method_exists($className, $functionName)) {
                $arguments = array_slice($parameters, 2);
            } else {
                $arguments = array_slice($parameters, 1);
                $functionName = self::checkResource(reset($arguments));
            }

            if (method_exists($className, $functionName)) {
                $instance = new $className();
                call_user_func_array([$instance, $functionName], $arguments);
                return;
            }
        }

        echo "404 error";
    }

    public static function checkResource($index = false)
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return $index ? "get" : "find";
            case 'POST':
                return "create";
            case 'PUT':
                return "update";
            case 'DELETE':
                return "delete";

            default:
                return "";
        }
    }
}
