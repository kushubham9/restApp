<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 8:26 PM
 */

namespace restApi\lib\controller;
use restApi\core\Controller as baseController;
use restApi\lib\model\UserModel;

class UserController extends baseController
{
    public function __construct()
    {
        $this->accessRule = [];
    }

    public function verbs()
    {
        return [
            'login' => ['GET']
        ];
    }

    public function loginMethod(){
        if (!isset($_GET['username']) || !isset($_GET['password'])){
            throw new \Exception("User id & password missing.");
        }

        $model = new UserModel();
        if ($model->isValidCredential($_GET['username'], $_GET['password'])){
            $model->getAccessKey($_GET['username']);
        }

        else{
            throw new \Exception("Invalid Credentials specified");
        }
    }
}