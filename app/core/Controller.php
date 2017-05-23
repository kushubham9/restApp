<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 1:01 PM
 */

namespace restApi\core;


class Controller
{
    /**
     * @var array contains 'method'=>'@' for registered users
     * 'method' => '/' for guests
     */
    public $accessRule = [];

    public static $defaultMethod = 'index';

    protected function verbs(){
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * @param $method
     * @return bool
     * @throws \HttpInvalidParamException
     */
    public function validateRequest($method){
        $acceptedRequests = $this->verbs();
        if (isset($acceptedRequests[$method]) && in_array($_SERVER['REQUEST_METHOD'], $acceptedRequests[$method])){
            return true;
        }
        else{
            throw new \HttpInvalidParamException("Request not valid.");
        }
    }


    public function isGuestAllowed($methodId){
        if (isset($this->accessRule[$methodId]) && $this->accessRule[$methodId] == '@'){
            return false; // Validate if the user is logged in and is authentic
        }
        return true;
    }

}