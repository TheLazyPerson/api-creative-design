<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/cart', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>List All Products in Cart</h1>");
    return $response;
});

$app->put('/cart', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Cart Put</h1>");
    return $response;
});

$app->get('/cart/remove/{id}', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Cart Remove</h1>");
    return $response;
});

$app->get('/cart/reduce/{id}', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Cart Put</h1>");
    return $response;
});

