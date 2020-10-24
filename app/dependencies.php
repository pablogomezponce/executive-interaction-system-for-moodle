<?php

use Slim\Flash\Messages;


$container = $app->getContainer();

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../src/View/templates', [
        'cache' => false
    ]);

    $router = $c->get('router');

    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));

    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};

$container['flash'] = function () {
    return new Messages();
};

$container['sql'] = function () {

    $dbSettings = array(
        'address' =>"192.168.1.138",
        'dbname' => "tfg",
        'userNameDB' => "root",
        'passwordDB' => "oikioiki1998J@",
    );

    return $dbSettings;
};
