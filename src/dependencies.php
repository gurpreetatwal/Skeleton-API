<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;
use Slim\Container;
use SkeletonAPI\lib\ErrorHandler;

$container = $app->getContainer();

// Monolog
$container['logger'] = function (Container $c) {
    $settings = $c['settings']['logger'];
    $logger = new Logger($c['app-name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Logger::DEBUG));
    return $logger;
};

// Error handler
$this['errorHandler'] = function ($c) {
    return new ErrorHandler($c->get('settings')['displayErrorDetails']);
};

$container['capsule'] = function (Container $c) {
    $settings = array_merge(
        $c['settings']['database'],
        parse_ini_file(__DIR__ . '/../environment.ini', true)['database']
    );
    $capsule = new Capsule();
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};
$container['capsule'];
