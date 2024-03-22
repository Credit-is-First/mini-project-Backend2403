<?php
class Config
{
    public static function get($path = null)
    {
        global $config;

        if ($path) {
            $path = explode('.', $path);

            foreach ($path as $key) {
                if (isset($config[$key])) {
                    $config = $config[$key];
                }
            }

            return $config;
        }

        return false;
    }
}
