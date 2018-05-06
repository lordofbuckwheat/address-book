<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', \Controller::class . ':index');
$app->map(['GET', 'POST'], '/record/create', \Controller::class . ':create');
$app->map(['GET', 'POST'], '/record/read', \Controller::class . ':read');
$app->map(['GET', 'POST'], '/record/{recordID:[0-9]+}/update', \Controller::class . ':update');
$app->map(['GET', 'POST'], '/record/{recordID:[0-9]+}/delete', \Controller::class . ':delete');
$app->map(['GET', 'POST'], '/dummy-records', \Controller::class . ':dummy');