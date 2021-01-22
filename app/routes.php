<?php

//LANDING
$app->get('/home', \TFG\Controller\IndexController::class);
$app->get('/', function () use ($app) {
    header('location: /home');
});

//LOGIN
$app->get('/profile', \TFG\Controller\CourseController::class);
//INDICATORS
$app->get('/defineIndicators/{courseid}', \TFG\Controller\IndicatorController::class . ':defineParams');



//API

    //user
$app->post('/api/login', \API\user\UserHandler::class.':login');
$app->get('/api/checkUser', \API\user\UserHandler::class . ':checkUserSession');
$app->post('/api/logout', \API\user\UserHandler::class . ':logout');

    //content
$app->get('/api/courses/getCourses', \API\courses\CourseHandler::class.':getCourses')
    ->add(\API\validations\LoggedMiddleware::class);
$app->get('/api/courses/details/{courseid}', \API\courses\CourseHandler::class.':getCourseDetails')
    ->add(\API\validations\LoggedMiddleware::class);

    //Indicators
$app->post('/api/indicators/operateIndicators/{courseid}', \API\indicators\IndicatorHandler::class . ':extractIndicators');
$app->get('/api/indicators/getIndicators/{courseid}', \API\indicators\IndicatorHandler::class.':getCourseIndicators')
    ->add(\API\validations\LoggedMiddleware::class);
