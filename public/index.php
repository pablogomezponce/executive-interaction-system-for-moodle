<?php
session_start();

require_once __DIR__ . '/../app/databaseSettings.php';
require_once __DIR__ . '/../vendor/autoload.php';

$settings = require_once __DIR__ . '/../app/settings.php';

$app = new \Slim\App($settings);

require_once __DIR__ . '/../app/routes.php';

require_once __DIR__ . '/../app/dependencies.php';


$app->run();
