<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 8:38 PM
 */

namespace restApi\core;


class Model
{
    /**
     * Used in the index method when results are fetched in page wise manner.
     * @var int
     */
    public $limitPerPage = 10;

    /**
     * @var - The table name to which the model maps to.
     */
    public $table;
    /**
     * @var \MySQLi
     */
    private $db;

    /**
     * Establishes a DB Connection and returns a link.
     * @return \MySQLi
     * @throws \Exception
     */
    public function _getDbConnection(){
        global $config;

        $requiredProperties = ['host','database','user','pass'];
        foreach ($requiredProperties as $property){
            if (!in_array($property,array_keys($config['db'])))
                throw new \Exception("Database settings are not provided.");
        }

        $this->db = new \MySQLi(
            $config['db']['host'],
            $config['db']['user'],
            $config['db']['pass'],
            $config['db']['database']);

        if(mysqli_connect_errno())
        {
            throw new \mysqli_sql_exception("Database connection could not be established");
        }

        $this->db->autocommit(TRUE);
        return $this->db;
    }

    /**
     * Closes a DB connection
     */
    private function _closeDbConnection(){
        if ($this->db){
            $this->db->close();
        }
    }

    /**
     * option [] contains the different parameters which are used during the select query.
     * 'AND' keyword is only configured atm with the WHERE parameter
     * @param array $options
     * @return string - The final query string
     */
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

    /**
     * Fetches only one record from the database.
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function fetchOne($options = []){
        try{
            $this->_getDbConnection();
            $options['LIMIT'] = 1; // Fetch only 1 record.
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

    /**
     * Returns the count of the total records in the table.
     * If query is provided, the number of rows after execution of query is returned.
     * @param bool $query
     * @return int
     * @throws \Exception
     */
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
        } finally {
            $this->_closeDbConnection();
        }
    }

    /**
     * Fetches all the rows from the table.
     * Returns an empty array if no records are found.
     * @param array $options
     * @return array|mixed
     * @throws \Exception
     */
    public function fetchAll($options = []){
        try{
            $this->_getDbConnection(); // Get the connection

            /**
             * See if page value has been provided.
             */
            if (isset($_GET['page']) && is_numeric($_GET['page'])){
                $page = $_GET['page'];
                $offset = ($page - 1) * $this->limitPerPage;
                $options['LIMIT'] = $offset .', '.$this->limitPerPage;
            }

            else
                $options['LIMIT'] = $this->limitPerPage;

            $query = $this->_buildSelectQuery(array_merge($options));

            $result = $this->db->query($query);
            if ($result && $result->num_rows > 0)
                return $result->fetch_all(MYSQLI_ASSOC);

            return [];
        } catch (\Exception $e){
            throw $e;
        } finally{
            $this->_closeDbConnection();
        }
    }

    /**
     *
     * @param $table - The primary tabnle
     * @param $whereCondition - Condition mapped as ['Column' => value]
     * @param $updateValues - Condition mapped as ['Column' => value]
     * @return string - Returns the final query string.
     */
    private function _buildUpdateQuery($table, $whereCondition, $updateValues){
        $whereClause = [];
        $clause = [];

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

    /**
     * @param $table - Table to update
     * @param $whereCondition - Condition mapped as ['Column' => value]
     * @param $updateValues - Condition mapped as ['Column' => value]
     * @return bool
     * @throws \Exception
     */
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

    /**
     * @param $table - The table name.
     * @param $values - The values to be inserted mapped as [column=>value]
     * @return string
     */
    private function _buildInsertQuery($table, $values){
        $query = " INSERT INTO ". $table . '( '. implode(', ', array_keys($values)) .' )';
        $valueClause = implode('\',\'', array_values($values));
        $query .= ' VALUES (\''.$valueClause.'\')';

        return $query;
    }

    /**
     * This function is used to insert a record into a table.
     * @param $table - Table name
     * @param $values - The values to be inserted [$column => $value]
     * @return bool
     * @throws \Exception
     */
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

    /**
     * Build a delete query.
     * @param $table
     * @param $condition
     * @return string
     */
    private function _deleteQuery($table, $condition){
        $whereClause = [];
        $query = " DELETE from ".$table;
        foreach ($condition as $key=>$value){
            $whereClause[] = $key. ' = '. '\''. $value. '\'';
        }
        if (sizeof($whereClause) != 0)
            $query .= ' WHERE '. implode(' AND ',$whereClause);
        else
            $query .= ' WHERE 1=1 ';
        return $query;
    }

    /**
     * Used to delete a single record from the table.
     * @param $table
     * @param $condition
     * @return bool
     * @throws \Exception
     */
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
