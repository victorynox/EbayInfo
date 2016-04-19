<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
        // Map middleware -> factories here
        'factories' => [
            zaboy\rest\Pipe\RestPipe::class => DataStore\Pipes\Factory\RestPipeFactory::class,
            App\Api\NotificationAction::class => App\Api\NotificationFactory::class,
        ],
    ],

    'routes' => [
        // Example:
        // [
        //     'name' => 'home',
        //     'path' => '/',
        //     'middleware' => App\Action\HomePageAction::class,
        //     'allowed_methods' => ['GET'],
        // ],
        [
            'name' => 'restAPI',
            'path' => '/rest[/{resourceName}[/{id}]]',
            'middleware' =>  zaboy\rest\Pipe\RestPipe::class,
            'allowed_method' => ['GET', 'POST', 'DELETE'],
        ],
        [
            'name' => 'api.notification',
            'path' => '/api/notification',
            'middleware' =>  App\Api\NotificationAction::class,
            'allowed_method' => ['GET', 'POST', 'DELETE'],
        ],
    ],
];
