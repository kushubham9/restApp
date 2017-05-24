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
     * @var - Stores the configuration input provided by the user.
     */
    private $config;

    /**
     * @var \MySQLi
     */
    private $db = false;

    /**
     * @var resource pointer to file
     */
    private $configFile = false;

    /**
     * ConfigMaker constructor.
     * @param $dbConfig
     */
    public function __construct($dbConfig) {
        $this->config = $dbConfig;
        $this->configFile = fopen(dirname(__FILE__) . '/../lib/config.php', 'a+') or die("Can't open config file. Please check file permission.");
    }

    /**
     * Checks if the credentials of DB provided by the user is fine and working.
     * @return bool
     * @throws \Exception
     */
    private function _checkDbConnection(){
        $requiredProperties = ['host','database','user','pass'];

        foreach ($requiredProperties as $property){
            if (!in_array($property, array_keys($this->config)))
                throw new \Exception("Incomplete configuration provided.");
        }

        $this->db = new \MySQLi(
            $this->config['host'],
            $this->config['user'],
            $this->config['pass'],
            $this->config['database']);

        // If configuration is invalid or unable to establish a link.
        if(mysqli_connect_errno())
        {
            throw new \mysqli_sql_exception("Connection could not be established/");
        } else{
            $this->db->autocommit(TRUE);
            return true;
        }

    }

    /**
     * Creates 2 day zero tables required in the application. Product & User.
     * A sample user 'admin'/'admin' is also created for further testing.
     * @return bool
     */
    private function _createTables(){
        if ($this->db === false){
            $this->_checkDbConnection();
        }


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
                if ($stmt = $this->db->prepare($item)){
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if ($this->_insertRecord())
                return true;

            else
                return false;

        } catch (\Exception $e){
            return false;
        } finally {
            $this->db->close();
        }
    }

    /**
     * Inserts a default user record in the user table
     * @return bool
     */
    private function _insertRecord(){
        if ($this->db === false){
            $this->_checkDbConnection();
        }

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
        if ($this->configFile===false){
            throw new \Exception("Config file not found.");
        }

        foreach ($this->config as $key => $val){
            fwrite($this->configFile, '$config[\'db\'][\''. $key . '\'] = \'' . $val . '\';'."\n");
        }
        fclose($this->configFile);
    }

    /**
     * The main method to begin the execution.
     * @return bool
     * @throws \Exception
     */
    public function exec(){
        try {
            if ($this->_checkDbConnection() && $this->_createTables())
            {
                $this->_writeConfigFile();
                return true;
            }
            else
                return false;
        } catch (\Exception $e){
            throw $e;
        }
    }
}