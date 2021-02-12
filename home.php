<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \Hcode\Page;



$app->get('/', function (Request $request, Response $response, $args) {


    $page = new Page();

    $page->setTpl('index');

	return $response;
    
});



?>