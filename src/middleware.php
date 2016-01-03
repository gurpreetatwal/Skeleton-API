<?php
$container = isset($container) ? $container : $app->getContainer();
$app->add(new CorsSlim\CorsSlim($container['settings']['CORS']));
$app->add(new Slim\Middleware\JwtAuthentication(array_merge(
    $container['settings']['JWT'],
    [
        'logger' => $container->get('logger'),
        'secret' => parse_ini_file('/../environment.ini')['jwt-secret']
    ]
)));