<?php
class Config
{
    public static function get($path = null)
    {
        global $config;
        $item = $config;

        if ($path) {
            $path = explode('.', $path);

            foreach ($path as $key) {
                if (isset($item[$key])) {
                    $item = $item[$key];
                }
            }

            return $item;
        }

        return false;
    }
}
