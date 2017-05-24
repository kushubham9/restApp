<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 8:26 PM
 */

namespace restApi\lib\controller;
use restApi\core\Controller as baseController;
use restApi\lib\model\UserModel;

class UserController extends baseController
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        /**
         * The accessRule is used to restrict/allow API access to users or guests.
         */
        $this->accessRule = [];
    }

    /**
     * This function maps the method to the Request type it accepts.
     * If the request type mentioned against the method is not provided, the request is not accepted.
     * @return array - The Request type accepted by the defined method.
     */
    public function verbs()
    {
        return [
            'login' => ['GET']
        ];
    }

    /**
     * This method is used to authenticate the user and give them access to the restricted APIs.
     * The method generates a new access-key on every request made.
     * @return array - The access-key which is used access other APIs.
     * @throws \Exception
     */
    public function loginMethod(){
        if (!isset($_GET['username']) || !isset($_GET['password'])){
            throw new \Exception("User id & password missing.");
        }

        $model = new UserModel();
        if ($model->isValidCredential($_GET['username'], $_GET['password'])){
            return ['access-key'=>$model->getAccessKey($_GET['username'])];
        }

        else{
            throw new \Exception("Invalid Credentials specified");
        }
    }
}