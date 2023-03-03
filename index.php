<?php

define('BASE_PATH',__DIR__);
 
require __DIR__  .'/helper/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if((isset($uri[3]) && $uri[3] != 'aset') || !isset($uri[4])){
    header('HTTP/1.1 404 Not Found');
    exit;
}

require PROJECT_ROOT_PATH."/controller/ApiController.php";

$objController = new APIController();
$methodName = $uri[4]; # name of the method that will called in controller
$objController->{$methodName}();