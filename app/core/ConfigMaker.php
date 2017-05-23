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
    private $config;
    /**
     * @var \MySQLi
     */
    private $db;

    public function __construct($dbConfig)
    {
        $this->config = $dbConfig;
    }

    private function _checkDbConnection(){

        $this->db = new \MySQLi(
            $this->config['host'],
            $this->config['user'],
            $this->config['pass'],
            $this->config['database']);

        if(mysqli_connect_errno())
        {
            throw new \mysqli_sql_exception("Connection could not be established");
        }
    }

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

         $query[] = "CREATE UNIQUE INDEX user_username_uindex ON user (username)";
         $query[] = "INSERT INTO USER (username, password, access_key) values ('admin','admin','access_key')";

        try {
            foreach ($query as $item){
                $stmt = $this->db->prepare($item);
                $stmt->execute();
            }
        } catch (\Exception $e){
            return false;
        }
    }

    private function _writeConfigFile(){
        $f = fopen(dirname(__FILE__) . '/../lib/config.php', 'a+') or die("can't open config file");
        foreach ($this->config as $key => $val){
            fwrite($f, '$config[\'db\'][\''. $key . '\'] = \'' . $val . '\';'."\n");
        }
        fclose($f);
    }

//    private function _removeInstaller(){
//        return (rename(dirname(__FILE__).'../public/installer.php', dirname(__FILE__).'../public/installer_bk.php'));
//    }

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