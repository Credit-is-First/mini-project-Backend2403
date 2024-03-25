<?php

class Model implements JsonSerializable, Tabular
{
    private $_db;
    private $_data = array();

    protected $_table;
    protected $_pk_id = "id";

    public function __construct($data = array())
    {
        $this->_db = Database::getInstance();
        if (!empty($data)) {
            $this->_data = $data;
        }
    }

    public function save()
    {
        if ($this->id) {
            $this->update($this->_data);
        } else {
            $this->create($this->_data);
        }
    }

    public function update($fields = array())
    {
        $this->_db->update($this->_table, $this->id, $fields);
        return $this->find($this->id);
    }

    public function create($fields = array())
    {
        $this->_db->insert($this->_table, $fields);
        return $this->find($this->id);
    }

    public function find($id)
    {
        $data = $this->_db->get($this->_table, array($this->_pk_id, '=', $id));

        if ($data->count()) {
            $this->_data = $data->first();
            return $this;
        }
        return null;
    }

    public function all()
    {
        $table = $this->_table;
        $this->_db->query("SELECT * from $table");
        return $this->_db->results();
    }

    public function exists()
    {
        return (!empty($this->_data)) ? true : false;
    }

    public function getTableName()
    {
        return $this->_table;
    }

    public function first()
    {
        $this->_data = $this->_db->first($this->_table);
        return $this;
    }

    public function get()
    {
        return $this->_db->get($this->_table);
    }

    public function jsonSerialize()
    {
        return $this->_data;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        call_user_func_array([$this->_db, $name], $arguments);
        return $this;
    }

    public static function __callStatic($name, $arguments)
    {
        $className = get_called_class();
        $model = new $className();
        call_user_func_array([$model, $name], $arguments);
        return $model;
    }
}
