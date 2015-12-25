<?php
return [
    'app-name' => 'Slim-REST-API',
    'settings' => [
        'displayErrorDetails' => true,

        // Monolog settings
        'logger' => [
            'path' => __DIR__ . '/../logs/app.log',
        ],

        //CORS Settings
        'CORS' => [
            "origin" => "localhost",
            "exposeHeaders" => [],
            "maxAge" => 864000,
            "allowCredentials" => false,
            "allowMethods" => ["POST", "GET", "DELETE"],
            "allowHeaders" => ["Origin", "X-Requested-With", "Content-Type", "Accept", "Authorization"]
        ]
    ],
];
