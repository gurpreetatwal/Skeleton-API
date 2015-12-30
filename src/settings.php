<?php
use Slim\Middleware\JwtAuthentication;

return [
    'app-name' => 'Skeleton-API',
    'settings' => [
        'displayErrorDetails' => true,

        // Monolog settings
        'logger' => [
            'path' => __DIR__ . '/../logs/app.log',
        ],

        //CORS Settings
        'CORS' => [
            'origin' => ['localhost'],
            'exposeHeaders' => [],
            'maxAge' => 864000,
            'allowCredentials' => false,
            'allowMethods' => ['POST', 'GET', 'DELETE'],
            'allowHeaders' => ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization']
        ],

        'JWT' => [
            'secure' => false,
            'callback' => function ($options) use ($app) {
                $app->jwt = $options['decoded'];
                return true;
            },
            'rules' => [
                new JwtAuthentication\RequestPathRule([
                    'path' => '/',
                    'passthrough' => ['/login', '/recover', '/reset-password']
                ]),
                new JwtAuthentication\RequestMethodRule([
                    'passthrough' => ['OPTIONS']
                ])
            ],

        ],

        //Default settings for database, overridden by environment.ini
        //Strongly suggest putting the username and password in the ini file
        'database' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'username' => '',
            'password' => '',
            'database' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ]
    ],
];
