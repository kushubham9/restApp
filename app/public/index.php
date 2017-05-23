<?php
/**
 * Created by PhpStorm.
 * User: Shu
 * Date: 22/05/17
 * Time: 12:42 PM
 */

require_once dirname(__FILE__) . '/../bootstrap.php';
$routeContoller = new restApi\core\RouteController();

$routeContoller->exec();