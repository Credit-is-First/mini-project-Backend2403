<?php
class Database
{
    private static $_instance;
    private $_engine;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct($engine = false)
    {
        if (!$engine) Config::get("database.default");
        if ($engine == "mysql") {
            $this->_engine = MySQL::getInstance();
        } else {
            $this->_engine = MySQL::getInstance();
        }
        // TODO: add more database engines
    }

    public function __call($methodName, $arguments)
    {
        if (method_exists($this->_engine, $methodName)) {
            return call_user_func_array([$this->_engine, $methodName], $arguments);
        } else {
            throw new Exception("Method $methodName does not exist.");
        }
    }
}
