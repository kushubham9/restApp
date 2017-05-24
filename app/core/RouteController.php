<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 23/05/17
 * Time: 3:29 PM
 */

namespace restApi\core;


class RouteController
{
    /**
     * Each controller is identified by its controller ID. All lowercase.
     * @var string
     */
    private $controllerId;

    /**
     * @var Controller
     */
    private $controllerObj;
    /**
     * @var \ReflectionClass
     */
    private $controllerClass;
    private $methodId;
    private $libDir = 'restApi\lib\controller';

    /**
     * Init the route controller
     */
    private function _init()
    {
        $this->_parseRequestUrl();
        $this->_setControllerClass();
        $this->_setControllerMethod();
    }

    /**
     * Identify the controller and method from the URL.
     */
    private function _parseRequestUrl(){
        if (isset($_GET['c']) && trim($_GET['c'])!=''){
            $this->controllerId = strtolower($_GET['c']);
        }

        if (isset($_GET['m']) && trim($_GET['m'])!=''){
            $this->methodId = strtolower($_GET['m']);
        }
    }

    /**
     * Sets the controller class through reflection and also instantiates an object of it.
     * Default controller is called if not provided in the url argument.
     */
    private function _setControllerClass(){
        // Get the controller class
        if (!$this->controllerId){
            global $config;
            $this->controllerId = $config['defaultController'];
        }

        if ($this->controllerId){
            $class = $this->libDir.'\\'.ucfirst($this->controllerId).'Controller';

            //Create a Reflection Class
            $this->controllerClass = new \ReflectionClass($class);

            //Create Controller Object instance
            $this->controllerObj = new $class();
        }
    }

    /**
     * Fetches the default method, incase it is not provided in the request string.
     */
    private function _setControllerMethod(){
        if (!$this->methodId) {
            $this->methodId = $this->controllerClass->getStaticPropertyValue('defaultMethod');
        }
    }

    /**
     * Begins the execution of the router controller.
     */
    public function exec(){
        // Validate the input Request.
        try{
            // Initialize the route controller
            $this->_init();

            $this->controllerObj->validateRequest($this->methodId);
            if (!$this->controllerObj->checkAccess($this->methodId)){
                throw new \Exception("Not Allowed. This API is restricted.");
            }
            ResponseCollector::buildJSON($this->_invokeMethod());
        } catch (\Exception $e){
            ResponseCollector::buildFailure($e);
        }
    }

    /**
     * Invokes the method of the controller.
     * @return mixed
     * @throws \Exception
     */
    private function _invokeMethod(){
        if (!$this->controllerClass || !$this->controllerObj){
            throw new \Exception("Controller not initialized.");
        }

        if (!$this->methodId) {
            $this->methodId = $this->controllerClass->getStaticPropertyValue('defaultMethod');
        }

        // Calls the method instance through reflection.
        $reflectionMethod = $this->controllerClass->getMethod($this->methodId.'Method');

        // Get the response from the invoked function
        $response = $reflectionMethod->invoke($this->controllerObj);

        return $response;
    }
}
