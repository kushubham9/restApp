<?php

/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 12:43 PM
 */
namespace restApi\lib\controller;
use restApi\core\Controller as baseController;

class ProductController extends baseController
{

    public function __construct()
    {
        $this->accessRule = ['index' => '@'];
    }

    public function indexMethod(){
        "This is an index method call";
    }
}