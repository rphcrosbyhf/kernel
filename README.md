# Microservice

Simple kernel for running tiny microservices entirely in PHP.

## Installation

`composer require phlux/kernel`

## Requirements

- http://php.net/manual/en/book.pcntl.php

## Example

```
#!/usr/bin/env php
<?php declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use League\Container\Container;
use Phlux\Kernel\Provider\RouteProvider;
use Phlux\Kernel\Provider\ServerProvider;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Socket\Server as SocketServer;

// Create a new application
$app = new Container();

// Register service providers
$app->addServiceProvider(new ServerProvider('0.0.0.0', 1337));
$app->addServiceProvider(new RouteProvider(__DIR__ . '/routes.php'));

// Start the microservice
$app->get(Server::class)->listen($app->get(SocketServer::class));
$app->get(LoopInterface::class)->run();
```

```
<?php declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$router->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface {
    $response->getBody()->write('{ "message": "Hello World!" }');

    return $response;
});
```