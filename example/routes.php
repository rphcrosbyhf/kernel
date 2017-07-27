<?php declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$router->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface {
    $response->getBody()->write('{ "message": "Hello World!" }');

    return $response;
});