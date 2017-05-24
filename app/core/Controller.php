<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 1:01 PM
 */

namespace restApi\core;


use restApi\lib\model\UserModel;

class Controller
{
    /**
     * @var array contains 'method'=>'@' for registered users
     * ['method' => '/'] for guests
     */
    public $accessRule = [];

    /**
     * @var string - The default method called when the method id is not provided.
     */
    public static $defaultMethod = 'index';

    /**
     * The different input request type accepted by the method must be mapped in the array.
     * If not Exception is thrown by the app.
     * @return array
     */
    protected function verbs(){
        return [
            'index' => ['GET'],
            'create' => ['POST'],
            'update' => ['PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * @param $method - The method id.
     * @return bool - If, request method is same as one mentioned in the verbs.
     * @throws \Exception
     */
    public function validateRequest($method){
        $acceptedRequests = $this->verbs();

        if (isset($acceptedRequests[$method]) && !in_array($_SERVER['REQUEST_METHOD'], $acceptedRequests[$method])){
            throw new \Exception("Request method not valid.");
        }
        else{
            return true;
        }
    }

    /**
     * Checks if the user has access to the API. If not, permission is not granted to the user.
     * @param $methodId - The method id
     * @return bool - True: When the API can be accessed (Either guest is allowed/ User is having a valid access_key)
     */
    public function checkAccess($methodId){
        global $config;

        if (isset($this->accessRule[$methodId]) && $this->accessRule[$methodId] == '@'){
            if (isset($_GET['access_key']))
            {
                // Get the user identification model from the configuration file.
                $userModel = new $config['userModel']();
                /**
                 * @var $userModel UserModel
                 */
                if ($userModel->checkAccessByKey($_GET['access_key']))
                    return true;
            }
            return false; // Validate if the user is logged in and is authentic
        }
        return true;
    }

}