<?php
class Database
{
    private $_engine;

    private function __construct($engine = false)
    {
        if (!$engine) Config::get("database.default");
        if ($engine == "mysql") {
            $this->_engine = MySQL::getInstance();
        }
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
