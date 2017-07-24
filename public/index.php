<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response) {

    var_dump(getenv('mysql_server'));

    $response->getBody()->write("Que onda");

    return $response;
});

$app->run();
