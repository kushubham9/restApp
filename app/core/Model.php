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
    public $limitPerPage = 10;

    public $table;
    /**
     * @var \MySQLi
     */
    private $db;

    public function _getDbConnection(){
        global $config;
//        {
            $this->db = new \MySQLi(
                $config['db']['host'],
                $config['db']['user'],
                $config['db']['pass'],
                $config['db']['database']);

            if(mysqli_connect_errno())
            {
                throw new \mysqli_sql_exception("Connection could not be established");
            }
//        }
        return $this->db;
    }

    private function _closeDbConnection(){
//        if ($this->db){
            $this->db->close();
//        }
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

        if (isset($options['LIMIT']))
            $query .= ' LIMIT '.$options['LIMIT'];

        return $query;
    }

    public function fetchOne($options = []){
        try{
            $this->_getDbConnection();
            $query = $this->_buildSelectQuery($options);
            $result = $this->db->query($query);

            if ($result && $result->num_rows > 0)
                return $result->fetch_assoc();

            return [];
        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }

    public function getRecordsCount($query = false){
        try{
            $this->_getDbConnection();
            if (!$query){
                $query = "SELECT count(*) from ".$this->table. " WHERE 1=1";
            }
            $result = $this->db->query($query);
            return $result->num_rows;
        } catch (\Exception $e){
            throw $e;
        }
    }


    public function fetchAll($options = []){
        try{
            $this->_getDbConnection();
            if (isset($_GET['page'])){
                $page = $_GET['page'];
                $offset = ($page - 1) * $this->limitPerPage;
                $options['LIMIT'] = $offset .', '.$this->limitPerPage;
            }
            else
                $options['LIMIT'] = $this->limitPerPage;

            $query = $this->_buildSelectQuery(array_merge($options));
            $result = $this->db->query($query);

            if ($result && $result->num_rows > 0)
                return $result->fetch_all();

            return [];
        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }

    private function _buildUpdateQuery($table, $whereCondition, $updateValues){
        $query = "UPDATE ". $table ." SET ";
        foreach ($updateValues as $key => $value){
            $clause[] = $key. ' = '. '\''. $value. '\'';
        }
        $query .= implode(', ',$clause);

        foreach ($whereCondition as $key=>$value){
            $whereClause[] = $key. ' = '. '\''. $value. '\'';
        }
        $query .= ' WHERE '. implode(' AND ',$whereClause);

        return $query;
    }

    public function updateRecord($table, $whereCondition, $updateValues){
        try{
            $this->_getDbConnection();
            $query = $this->_buildUpdateQuery($table,$whereCondition,$updateValues);
            $stmt = $this->db->prepare($query);

            return $stmt->execute();

        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }

    private function _buildInsertQuery($table, $values){
        $query = " INSERT INTO ". $table . '( '. implode(', ', array_keys($values)) .' )';
        $valueClause = implode('\',\'', array_values($values));
        $query .= ' VALUES (\''.$valueClause.'\')';

        return $query;
    }

    public function insertRecord($table, $values){
        try{
            $this->_getDbConnection();
            $query = $this->_buildInsertQuery($table,$values);
            $stmt = $this->db->prepare($query);

            return $stmt->execute();

        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }

    private function _deleteQuery($table, $condition){
        $query = " DELETE from ".$table;
        foreach ($condition as $key=>$value){
            $whereClause[] = $key. ' = '. '\''. $value. '\'';
        }
        $query .= ' WHERE '. implode(' AND ',$whereClause);
        return $query;
    }

    public function deleteRecord($table, $condition){
        try{
            $this->_getDbConnection();
            $query = $this->_deleteQuery($table, $condition);
            $stmt = $this->db->prepare($query);

            return $stmt->execute();

        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }
}