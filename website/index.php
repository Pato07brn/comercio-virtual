<?php 

require_once("vendor".DIRECTORY_SEPARATOR."autoload.php");

$app = new \Slim\Slim();

$app->get('/', function () {
    echo "Hello, the time is ". date("H:i:s");
});

$app->get('/date/:name', function ($name) {
    echo "Hello $name, the date is " .date("d/m/y") ;
});

$app->run();

 ?>