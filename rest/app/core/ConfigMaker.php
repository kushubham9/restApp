<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 24/05/17
 * Time: 1:25 AM
 */

namespace restApi\core;


class ConfigMaker
{
    /**
     * @var Stores the configuration input provided by the user.
     */
    private $config;

    /**
     * @var \MySQLi
     */
    private $db;

    /**
     * ConfigMaker constructor.
     * @param $dbConfig
     */
    public function __construct($dbConfig)
    {
        $this->config = $dbConfig;
    }

    /**
     * Checks if the credentials of DB provided by the user is fine and working.
     */
    private function _checkDbConnection(){

        $this->db = new \MySQLi(
            $this->config['host'],
            $this->config['user'],
            $this->config['pass'],
            $this->config['database']);

        $this->db->autocommit(TRUE);
        if(mysqli_connect_errno())
        {
            throw new \mysqli_sql_exception("Connection could not be established");
        }
    }

    /**
     * Creates 2 day zero tables required in the application. Product & User.
     * A sample user 'admin'/'admin' is also created for further testing.
     * @return bool
     */
    private function _createTables(){

        $query[] = "CREATE TABLE products
                    (
                        id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                        name VARCHAR(50),
                        description TEXT,
                        cost DOUBLE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                    )";

        $query[] = "CREATE TABLE user
                    (
                        id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                        username VARCHAR(50) NOT NULL,
                        password VARCHAR(50) NOT NULL,
                        access_key VARCHAR(100)
                    )";

        try {
            foreach ($query as $item) {
                $stmt = $this->db->prepare($item);
                $stmt->execute();
                $stmt->close();
            }

            return ($this->_insertRecord());
        } catch (\Exception $e){
            return false;
        } finally {
            $this->db->close();
        }
    }

    private function _insertRecord(){
        $query = 'insert into user(username,password,access_key) values (\'admin\',\'admin\',\'access_key\')';
        if($stmt = $this->db->prepare($query)){
            $stmt -> execute();
            $stmt -> close();
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Writes the configuration file which is loaded during the bootstrap process.
     * app/lib/config.php
     */
    private function _writeConfigFile(){
        $f = fopen(dirname(__FILE__) . '/../lib/config.php', 'a+') or die("can't open config file");
        foreach ($this->config as $key => $val){
            fwrite($f, '$config[\'db\'][\''. $key . '\'] = \'' . $val . '\';'."\n");
        }
        fclose($f);
    }

    /**
     * The main method to begin the execution.
     * @return bool
     */
    public function exec(){
        try {
            $this->_checkDbConnection();
            $this->_createTables();
            $this->_writeConfigFile();
            return true;
        } catch (\Exception $e){
            return false;
        }
    }
}