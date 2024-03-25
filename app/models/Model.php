<?php

class Model implements JsonSerializable
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
        return $this->_find($this->id);
    }

    public function create($fields = array())
    {
        $this->_db->insert($this->_table, $fields);
        return $this->_find($this->id);
    }

    public function _find($id)
    {
        $data = $this->_db->get($this->_table, array($this->_pk_id, '=', $id));

        if ($data->count()) {
            $this->_data = $data->first();
            return $this;
        }
        return null;
    }

    public static function find($id)
    {
        $className = get_called_class();
        $model = new $className();
        return $model->_find($id);
    }

    public function exists()
    {
        return (!empty($this->_data)) ? true : false;
    }

    public function jsonSerialize() {
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
}
