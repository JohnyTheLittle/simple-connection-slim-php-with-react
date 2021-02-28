<?php
$_ENV['SLIM_MODE']='production';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->addBodyParsingMiddleware();
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
    ->withHeader('Access-Control-Allow-Origin', '*')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
//here we imagining something like a database
$db = array();
$db = ['data1','data2','data3'];
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->get('/database', function (Request $request, Response $response, $args) {
  global $db;
  $payload = json_encode($db);

  $response->getBody()->write($payload);
  return $response -> withHeader('Content-Type', 'application/json');
});

$app->post('/database', function (Request $request, Response $response, $args) {
    global $db;
    $data = $request->getParsedBody();
    array_push($db, $data["msg"]);
    $response->getBody()->write(json_encode($db));
    return $response;
});
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});
$app->run();
?>