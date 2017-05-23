<?php

/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 8:30 PM
 */
namespace restApi\lib\model;
use restApi\core\Model;


class UserModel extends Model
{
    public $table = 'user';

    /**
     * This method is used to authenticate the users on the system.
     * @param $username
     * @param $password
     * @return bool True if valid
     */
    public function isValidCredential($username, $password)
    {
        $userRecord = $this->fetchOne(['where' => ['username' => $username]]);

        if (sizeof($userRecord) > 0){
            if ($userRecord['password'] == $password)
                return true;
        }
        return false;
    }

    /**
     * Generates & fetches the access key for the user from the database.
     * @param $username
     * @return bool|mixed The access key if exists else returns false.
     */
    public function getAccessKey($username){
        $this->updateRecord($this->table,['username'=>$username],['access_key'=>$this->_generateRandomString()]);
        $userRecord = $this->fetchOne(['where' => ['username' => $username]]);
        if(is_array($userRecord) && sizeof($userRecord)>0 && isset($userRecord['access_key']))
            return $userRecord['access_key'];

        return false;
    }

    /**
     * Random String generator. Used to generate a random string during login request.
     * @param int $length
     * @return string
     */
    private function _generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function checkAccessByKey($access_key){
        $user = $this->fetchOne(['where' => ['access_key'=>$access_key]]);
        if (is_array($user) && sizeof($user)!=0)
        {
            return true;
        }
        return false;
    }
}