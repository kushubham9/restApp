<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 3:29 PM
 */

namespace restApi\core;


class RouteController
{
    private $requestUri;
    private $controllerId;

    private $controllerObj;
    /**
     * @var \ReflectionClass
     */
    private $controllerClass;
    private $methodId;
    private $method;
    private $arg;
    private $libDir = 'restApi\lib\controller';

    public function __construct()
    {
        $this->_parseRequestUrl();
        $this->_setControllerClass();
        $this->_setControllerMethod();
    }

    private function _setControllerClass(){
        // Get the controller class
        if ($this->controllerId){
            $class = $this->libDir.'\\'.ucfirst($this->controllerId).'Controller';
            try{
                $this->controllerClass = new \ReflectionClass($class);
            } catch (\Throwable $e){
                throw $e;
            }
            $this->controllerObj = new $class();
        }
    }

    private function _setControllerMethod(){
        if (!$this->methodId) {
            $this->methodId = $this->controllerClass->getStaticPropertyValue('defaultMethod');
        }
    }

    private function _parseRequestUrl(){
        $this->requestUri = $_SERVER['REQUEST_URI'];

        if (isset($_GET['c']) && trim($_GET['c'])!=''){
            $this->controllerId = strtolower($_GET['c']);
        }

        if (isset($_GET['m']) && trim($_GET['m'])!=''){
            $this->methodId = strtolower($_GET['m']);
        }
    }

    private function _invokeMethod(){
        $reflectionMethod = $this->controllerClass->getMethod($this->methodId.'Method');
//        $params = $reflectionMethod->getParameters();
//        var_dump($params);
        $reflectionMethod->invoke($this->controllerObj);
    }

    public function exec(){
        // Validate the input Request.
        $this->controllerObj->validateRequest($this->methodId);
        if (!$this->controllerObj->isGuestAllowed($this->methodId)){
            // Validate the user.
        }

        $this->_invokeMethod();
    }
}