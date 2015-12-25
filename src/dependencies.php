<?php
use  Monolog\Logger;
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
