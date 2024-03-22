<?php
interface DB
{
    public function query($sql, $params = array());

    public function action($action, $table, $where = array());

    public function get($table, $where);

    public function delete($table, $where);

    public function insert($table, $fields = array());

    public function update($table, $id, $fields);

    public function results();

    public function first();

    public function error();

    public function count();
}
