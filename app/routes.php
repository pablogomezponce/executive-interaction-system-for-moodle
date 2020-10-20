<?php

//LANDING
$app->get('/home', \TFG\Controller\IndexController::class);
$app->get('/', function () use ($app) {
    header('location: /home');
});

//LOGIN
$app->get('/login', \TFG\Controller\UserController::class . ':login');
$app->get('/profile', \TFG\Controller\ProfileController::class);
//INDICATORS
$app->get('/defineIndicators/{courseid}', \TFG\Controller\IndicatorController::class . ':defineParams');



//API

    //user
$app->post('/api/relatedContent', \TFG\Controller\APIController::class . ':relatedContent');
$app->post('/api/login', \API\user\userRequirements::class.':login');
$app->get('/api/checkUser', \API\user\userRequirements::class . ':checkUserSession');
$app->post('/api/logout', \API\user\userRequirements::class . ':logout');
$app->get('/api/token', \API\user\userRequirements::class . ':getToken');

    //content
$app->get('/api/courses/getCourses', \API\courses\courseExtraction::class.':getCourses')
    ->add(\API\loggedMiddleware::class);
$app->get('/api/courses/getIndicators/{courseid}', \API\courses\courseExtraction::class.':getCourseIndicators')
    ->add(\API\loggedMiddleware::class);


    //Indicators
$app->post('/api/askIndicators/{courseid}', \API\indicators\IndicatorHandler::class . ':extractIndicators');
// $app->get('/api/relatedContent', \SallePW\Controller\APIController::class . ':relatedContent');
