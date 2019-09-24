<?php
require_once '../include/functions.php';
spl_autoload_register('myAutoloader');

/**
 * Make connection and executing quries
 */
class Database extends Config
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all element from database according to query
     * @param $query tkaes sql execution code
     * @return $params array of element from our query
     */
    public function getParams($query)
    {
        try {
            $params = [];
            $result = $this->conn->query($query, PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $params[] = $row;
            }
            return $params;
        } catch (PDOException $pdoex) {
            return;
        } finally {
            parent::db_close();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }


    public function update($table, $id, $fields)
    {
        $set = '';
        $x = 1;
        foreach ($fields as $name => $value) {
            $set .= "{$name} ='" . $value . "'";
            if ($x < count($fields)) {
                $set .= ',';
            }
            $x++;
        }
        $sql = "UPDATE {$table} SET {$set} WHERE [ENV_id] = {$id}";
        $sth = $this->conn->prepare($sql);
        if (!empty($sth)) {
            $sth->execute();
            return true;
        }
        return false;
    }

    public function insert($database, $data)
    {
        $row = array();
        $arr = array();
        foreach ($data as $column =>$value) {
            $row[] = $column;
            $arr[] = "'".$value."'";
        }

        $result = $this->conn->query("INSERT INTO ". $database ."(". implode(',', $row) .")
                     VALUES (". implode(',', $arr) .")");
    }
}
