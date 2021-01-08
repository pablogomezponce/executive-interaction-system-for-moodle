<?php

//LANDING
$app->get('/home', \TFG\Controller\IndexController::class);
$app->get('/', function () use ($app) {
    header('location: /home');
});

//LOGIN
$app->get('/profile', \TFG\Controller\ProfileController::class);
//INDICATORS
$app->get('/defineIndicators/{courseid}', \TFG\Controller\IndicatorController::class . ':defineParams');



//API

    //user
$app->post('/api/login', \API\user\userRequirements::class.':login');
$app->get('/api/checkUser', \API\user\userRequirements::class . ':checkUserSession');
$app->post('/api/logout', \API\user\userRequirements::class . ':logout');

    //content
$app->get('/api/courses/getCourses', \API\courses\courseExtraction::class.':getCourses')
    ->add(\API\validations\loggedMiddleware::class);
$app->get('/api/courses/details/{courseid}', \API\courses\courseExtraction::class.':getCourseDetails')
    ->add(\API\validations\loggedMiddleware::class);

    //Indicators
$app->post('/api/indicators/operateIndicators/{courseid}', \API\indicators\IndicatorHandler::class . ':extractIndicators');
$app->get('/api/indicators/getIndicators/{courseid}', \API\indicators\IndicatorHandler::class.':getCourseIndicators')
    ->add(\API\validations\loggedMiddleware::class);
