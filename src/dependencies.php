<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;

$container = $app->getContainer();

// Monolog
$container['logger'] = function ($c) {
    $settings = $c['settings']['logger'];
    $logger = new Logger($c['app-name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Logger::DEBUG));
    return $logger;
};

$container['capsule'] = function ($c) {
    $settings = array_merge(
        $c['settings']['database'],
        parse_ini_file('../environment.ini', true)['database']
    );
    $capsule = new Capsule();
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};
$container['capsule'];
