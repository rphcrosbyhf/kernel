<?php declare(strict_types = 1);

use League\Route\RouteCollection;
use Phlux\Kernel\Http\Middleware\JsonDecodingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Phlux\Kernel\Http\Middleware\RequestSchemaMiddleware;

/** @var RouteCOllection $router */

$router->post('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface {
    $response->getBody()->write('{ "message": "Hello World!" }');

    return $response;
})->middlewares([
    new RequestSchemaMiddleware(__DIR__ . '/schema.json'),
    new JsonDecodingMiddleware(),
]);