<?php
class MySQL implements DB
{
    private static $_instance;
    private $_pdo;
    private $_query;
    private $_error;
    private $_results;
    private $_count;

    private $_table;
    private $_select;
    private $_limit;
    private $_skip;
    private $_where = array();
    private $_joins = "";

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
                $this->_results     = $this->_query->fetchAll(PDO::FETCH_ASSOC);
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

    // public function get($table, $where)
    // {
    //     return $this->action('SELECT *', $table, $where);
    // }

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

    public function insertBatch($table, $batch = array())
    {
        $sql = "";
        $stmt = null;
        $keys = array();
        foreach ($batch as $fields) {
            if ($sql == "") {
                $keys = array_keys($fields);
                $values = array_fill(0, count($keys), '?');
                $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ")";

                $stmt = $this->_pdo->prepare($sql);
            }
            $data = [];
            foreach ($keys as $name) {
                $data[] = $fields[$name];
            }
            $stmt->execute($data);
        }
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

    public function from($table)
    {
        $this->_table = $table;
    }

    public function rightJoin($joinTable, $name, $operator, $value)
    {
        $this->_joins .= "RIGHT JOIN $joinTable ON $name $operator $value ";
        return $this;
    }

    public function leftJoin($joinTable, $name, $operator, $value)
    {
        $this->_joins .= "LEFT JOIN $joinTable ON $name $operator $value ";
        return $this;
    }

    public function where($param1 = array(), $param2 = null, $param3 = null)
    {
        if (is_array($param1)) {
            $where = [];
            foreach ($param1 as $name => $value) {
                $where[] = array("name" => $name, "operator" => "=", "value" => $value);
            }
            $this->_where[] = array("type" => "AND", "conditions" => $where);
        } else if ($param3 == null) {
            $this->_where[] = array("type" => "AND", "conditions" => array(array("name" => $param1, "operator" => "=", "value" => $param2)));
        } else {
            $this->_where[] = array("type" => "AND", "conditions" => array(array("name" => $param1, "operator" => $param2, "value" => $param3)));
        }
        return $this;
    }

    public function whereBetween($name, $from, $to)
    {
        $value = "'$from' AND '$to'";
        if (is_numeric($from)) {
            $value = "$from AND $to";
        }
        $this->_where[] = array("type" => "AND", "conditions" => array(["name" => $name, "operator" => "BETWEEN", "value" => $value]));
        return $this;
    }

    public function select($select)
    {
        $this->_select = $select;
    }

    public function addSelect($select)
    {
        $this->_select .= ", " . $select;
    }

    public function limit($limit)
    {
        $this->_limit = $limit;
    }

    public function skip($skip)
    {
        $this->_skip = $skip;
    }

    public function get($table = null)
    {
        $select = empty($this->_select) ? "*" : $this->_select;
        $table = empty($table) ? $this->_table : $table;
        $where = empty($this->_where) ? "" : "WHERE " . $this->_renderWhere($this->_where, $table);

        $join = empty($this->_joins) ? "" : $this->_joins;

        $sql = "SELECT $select FROM $table $join $where ";

        if ($this->_limit) {
            $skip = empty($this->_skip) ? 0 : $this->_skip;
            $sql .= "LIMIT $skip, " . $this->_limit;
        }

        $this->_query = $this->_pdo->prepare($sql);
        if ($this->_query) {
            if ($this->_query->execute()) {
                $this->_results     = $this->_query->fetchAll(PDO::FETCH_ASSOC);
                $this->_count       = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        $this->_reset();
        return $this->results();
    }

    public function empty($table = null)
    {
        $sql = "DELETE FROM $table";
        if (!$this->query($sql)->error()) {
            return true;
        }
        return false;
    }

    public function transaction($callback)
    {
        try {
            $this->_pdo->beginTransaction();
            call_user_func($callback);
            $this->_pdo->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            $this->_pdo->rollBack();
        }
    }

    private function _reset()
    {
        $this->_where = array();
        $this->_table = "";
        $this->_select = "";
        $this->_limit = "";
        $this->_skip = "";
        $this->_joins = "";
    }

    private function _renderWhere($where, $table)
    {
        $sql = "";
        foreach ($this->_where as $where) {
            $conditions = $where["conditions"];
            $type = $where["type"];
            if ($type == "AND" && $sql != "") {
                $sql .= " AND ";
            } else if ($type == "OR" && $sql != "") {
                $sql .= " OR ";
            }
            $sql .= "(";
            $items = [];
            foreach ($conditions as $condition) {
                $name = $condition["name"];
                $op = $condition["operator"];
                $value = $condition["value"];
                if ($op == "BETWEEN") {
                    $items[] = "`$table`.`$name` $op $value";
                } else {
                    $items[] = "`$table`.`$name` $op '$value'";
                }
            }
            $sql .= join(" AND ", $items);
            $sql .= ")";
        }
        return $sql;
    }

    public function first($table)
    {
        $this->limit(1);
        $results = $this->get($table);
        return $results[0];
    }

    public function results()
    {
        return $this->_results;
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
