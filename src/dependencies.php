<?php
// DIC configuration

use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['notFoundHandler'] = $container['notAllowedHandler'] = $container['phpErrorHandler'] = $container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, Throwable $e) use ($c) {
        /** @var Response $result */
        $result = $c['response']->withJson([
            'message' => $e->getMessage(),
            'success' => false
        ]);
        $callback = $request->getParam('callback');
        if (!empty($callback)) {
            $body = $result->getBody();
            $body->rewind();
            $json = $body->getContents();
            $body->rewind();
            $body->write($callback.'('.$json.');');
            return $result->withHeader('Content-Type', 'application/javascript');
        }
        return $result;
    };
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
