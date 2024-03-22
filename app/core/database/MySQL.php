<?php
class MySQL implements DB
{
    private static $_instance;
    private $_pdo;
    private $_query;
    private $_error;
    private $_results;
    private $_count;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $host = Config::get("database.mysql.host");
        $db_name = Config::get("database.mysql.db_name");
        $username = Config::get("database.mysql.username");
        $password = Config::get("database.mysql.password");
        try {
            $this->_pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function query($sql, $params = array())
    {
        $this->_error = false;

        $this->_query = $this->_pdo->prepare($sql);
        if ($this->_query) {
            $x = 1;

            if (count($params)) {
                foreach ($params as $param) {
                    $this->_query->bindvalue($x, $param);
                    $x++;
                }
            }

            if ($this->_query->execute()) {
                $this->_results     = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count       = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }

        return $this;
    }
    public function action($action, $table, $where = array())
    {
        if (count($where) === 3) {
            $operators  = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if (!$this->query($sql, array($value))->error()) {
                    return $this;
                }
            }
        }

        return false;
    }

    public function get($table, $where)
    {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where)
    {
        return $this->action('DELETE', $table, $where);
    }

    public function insert($table, $fields = array())
    {
        if (count($fields)) {
            $keys   = array_keys($fields);
            $values = '';
            $x      = 1;

            foreach ($fields as $field) {
                $values .= '?';

                if ($x < count($fields)) {
                    $values .= ', ';
                }

                $x++;
            }

            $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

            if (!$this->query($sql, $fields)->error()) {
                return true;
            }
        }

        return false;
    }

    public function update($table, $id, $fields)
    {
        $set    = '';
        $x      = 1;

        foreach (array_keys($fields) as $name) {
            $set .= "{$name} = ?";

            if ($x < count($fields)) {
                $set .= ', ';
            }

            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE uid = {$id}";

        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function results()
    {
        return $this->_results;
    }

    public function first()
    {
        return $this->results()[0];
    }

    public function error()
    {
        return $this->_error;
    }

    public function count()
    {
        return $this->_count;
    }
}
