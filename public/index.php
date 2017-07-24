<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, User')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$container = $app->getContainer();

// Service factory for the ORM
$container['db'] = function ($container) {

    $request = $container['request'];

    if ($request->hasHeader('HTTP_USER')) {
        $headers = $request->getHeader('HTTP_USER');
    }

    $capsule = new \Illuminate\Database\Capsule\Manager;

    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => getenv('DATABASE_SERVER'),
        'database' => $headers[0],
        'username' => getenv('DATABASE_USERNAME'),
        'password' => getenv('DATABASE_PASSWORD'),
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => ''
        ]);

    return $capsule;
};

$app->get('/', function (Request $request, Response $response) {

    $response->getBody()->write("Api Start");

    return $response;
});

$app->get('/clientes', function (Request $request, Response $response) {

    $clientes = App\Clientes::all();

    $response = $response->withJson($clientes, 200);
    return $response;

});

// Register the database connection with Eloquent
$capsule = $app->getContainer()->get('db');
$capsule->bootEloquent();

$app->run();
