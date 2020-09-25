<?php 
session_start();
require_once("vendor".DIRECTORY_SEPARATOR."autoload.php");

use \Slim\Slim;
use \Brn\Page;
use \Brn\PageAdmin;
use \Brn\Model\User;
use \Brn\Model\Categories;


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
//forgot
$app->get('/admin/forgot', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});
// forgot POST
$app->post('/admin/forgot', function() {

	$user = User::getForget($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});
//Email enviado
$app->get('/admin/forgot/sent', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});
//Valida senha
$app->get('/admin/forgot/reset', function() {

	$user = User::validForgotDecrypt($_GET["code"]);
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name" => $user["desperson"],
		"code" => $_GET["code"],
	));
});

//Nova senha
$app->post('/admin/forgot/reset', function() {

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
 		"cost"=>12
 	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");
});
//categorias
$app->get('/admin/categories', function() {

	$categories = Categories::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", array(
		"categories"=>$categories,
	));
});
//cria categorias
$app->get('/admin/categories/create', function() {

	User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("categories-create");

});
//envia categorias create
$app->post('/admin/categories/create', function() {

	User::verifyLogin();

	$categories = new Categories();

	$categories->setData($_POST);

	$categories->save();

	header("Location: /admin/categories");
	exit;

});
//deleta categoria
$app->get('/admin/categories/:idcategory/delete', function($idcategory) {

	User::verifyLogin();

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$categories->delete();

	header("Location: /admin/categories");
	exit;

});
// update
$app->get('/admin/categories/:idcategory', function($idcategory) {

	User::verifyLogin();
	
	$categories = new Categories();

	$categories->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		'category'=> $categories->getValues()
	]);

});
// update post
$app->post('/admin/categories/:idcategory', function($idcategory) {

	User::verifyLogin();

	$categories = new Categories();

	$categories->get((int)$idcategory);

	$categories->setData($_POST);

	$categories->save();

	header("Location: /admin/categories");
	exit;
	

});


$app->run();

 ?>