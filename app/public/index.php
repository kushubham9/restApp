<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 12:42 PM
 */

require_once dirname(__FILE__) . '/../bootstrap.php';

if (file_exists(__DIR__.'/installer.php')){
    header("Location: installer.php");
    die();
}

else{
    $routeContoller = new restApi\core\RouteController();
    $routeContoller->exec();
}
