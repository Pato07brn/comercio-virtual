<?php 
session_start();
require_once("vendor".DIRECTORY_SEPARATOR."autoload.php");

use \Slim\Slim;
use \Brn\Page;
use \Brn\PageAdmin;
use \Brn\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});
//Admin
$app->get('/admin', function() {
	
	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("index");

});
//Login get
$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});
//Login post
$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");

	exit;
	
});
//Logout
$app->get('/admin/logout/', function() {
    User::logout();
	header("Location: /admin/login");
	exit;
});
//CRUD users
$app->get('/admin/users', function() {

	User::verifyLogin();

	$users = User::selectALL();
    
	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$users
	));
});
//Create get
$app->get('/admin/users/create', function() {
	
	User::verifyLogin();
    
	$page = new PageAdmin();

	$page->setTpl("users-create");
});
//Create post
$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$user = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
 	exit;

});
//Delete
$app->get('/admin/users/:iduser/delete', function($iduser) {
	
	User::verifyLogin();
 
   $user = new User();
 
   $user->get((int)$iduser);

   $user->delete();

   header("Location: /admin/users");
   exit;   

});
//Update get
$app->get('/admin/users/:iduser', function($iduser){
 
   User::verifyLogin();
 
   $user = new User();
 
   $user->get((int)$iduser);
 
   $page = new PageAdmin();
 
   $page ->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
});
//Update post
$app->post('/admin/users/:iduser', function($iduser) {

	User::verifyLogin();
 
   $user = new User();
 
   $user->get((int)$iduser);

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

   $user->setData($_POST);

   $user->update($_POST);

   header("Location: /admin/users");
   exit;

});

$app->run();

 ?>