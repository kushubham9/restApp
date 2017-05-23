<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 8:38 PM
 */

namespace restApi\core;


class Model
{
    public $table;
    /**
     * @var \MySQLi
     */
    public $db;

    public function __construct()
    {
        $this->_getDbConnection();
    }

    private function _getDbConnection(){
        global $config;
        $this->db = new \MySQLi(
            $config['db']['host'],
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['database']);

        if(mysqli_connect_errno())
        {
            die("Connection could not be established");
        }
    }


    private function _buildSelectQuery($options = []){
        $query = 'SELECT ';
        if (isset($options['select'])){
            $query .= implode(', ',$options['select']);
        }
        else
            $query .= " * ";

        if (isset($options['from'])){
            $query .= " FROM ".$options['from'].' ';
        }
        else
            $query .= "FROM ".$this->table." ";

        if (isset($options['where'])){
            $condition = [];
            foreach ($options['where'] as $key=>$value)
                $condition[] = $key .' = \''. mysqli_escape_string($this->db, $value) .'\'';

            $query .= ' WHERE '.implode(' AND ',$condition);
        }

        return $query;
    }

    public function fetchOne($options = []){
        $query = $this->_buildSelectQuery($options);
        $result = $this->db->query($query);

        if ($result->num_rows > 0)
            return $result->fetch_assoc();

        return [];
    }
}