<?php

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


$container['sql'] = function () {

    $dbSettings = array(
        'address' =>"192.168.1.101:3306",
        'dbname' => "eistudy",
        'userNameDB' => "eistudyuser",
        'passwordDB' => "oikioiki1998J@",
    );

    return $dbSettings;
};



