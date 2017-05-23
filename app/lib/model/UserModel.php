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

    public function isValidCredential($username, $password)
    {
        $userRecord = $this->fetchOne(['where' => ['username' => $username]]);

        if (sizeof($userRecord) > 0){
            if ($userRecord['password'] == $password)
                return true;
        }
        return false;
    }

    public function getAccessKey($username){
        $userRecord = $this->fetchOne(['where' => ['username' => $username]]);
        return sizeof($userRecord) == 1 ? $userRecord['access_key'] : false;
    }
}