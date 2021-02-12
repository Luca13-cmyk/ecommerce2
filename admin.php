<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Hcode\PageAdmin;
use \Hcode\Model\User;



$app->get('/admin', function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl('index');

    // echo User::getPasswordHash('admin');

	return $response;
    
});


$app->get('/admin/login', function (Request $request, Response $response, $args) {

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl('login');

	return $response;
    
});



$app->post('/admin/login', function (Request $request, Response $response, $args) {

    User::login($_POST['login'], $_POST['password']);


    header("Location: /admin");

	exit;

    
});

$app->get('/admin/logout', function (Request $request, Response $response, $args) {

    
    User::logout();
    header("Location: /admin/login");
    exit;
    
});

$app->get('/admin/users', function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl('users', array(
        "users"=>$users
    ));

	return $response;
    
});


$app->get('/admin/users/create', function (Request $request, Response $response, $args) {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl('users-create');

	return $response;
    
});

$app->post('/admin/users/create', function (Request $request, Response $response, $args) {

    User::verifyLogin();

    
    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]) ? 1 : 0);

    $user->setData($_POST); //DAO Data Acess Object

    $user->save();
    
    header("Location: /admin/users");
    exit;
    
});

$app->get('/admin/users/{iduser}/delete', function (Request $request, Response $response, $args) {


    User::verifyLogin();

    $user = new User();
    $user->get((int)$args['iduser']);
    $user->delete();
    header("Location: /admin/users");
    exit;
    
});

$app->get('/admin/users/{iduser}', function (Request $request, Response $response, $args) {

    // echo $args['iduser']; // Pega o iduser. Slim 4.0
    // exit;

    User::verifyLogin();

    $user = new User();

    $user->get((int)$args['iduser']);

    $page = new PageAdmin();

    $page->setTpl('users-update', array(
        "user"=>$user->getValues()
    ));

	return $response;
    
});


$app->post('/admin/users/{iduser}', function (Request $request, Response $response, $args) {


    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]) ? 1 : 0);


    $user->get((int)$args['iduser']);
    $user->setData($_POST);
    $user->update();

    header("Location: /admin/users");
    exit;
    
});








?>