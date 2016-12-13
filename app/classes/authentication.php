<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/login', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Login</h1>");
    return $response;
});

$app->post('/forgetPassword', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Forget Password</h1>");
    return $response;
});

$app->post('/signUp', function (Request $request, Response $response) {
    $response->getBody()->write("<h1>Hello Sign Up</h1>");
    return $response;
});

