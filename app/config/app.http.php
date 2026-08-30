<?php
/**
 * HTTP application config
 *
 * Routes are explicit: one entry per page, one controller action per page.
 * The route table doubles as the sitemap.
 */

return [
    'routes' => [
        '[/]' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'index'
        ],
        '/build[/]' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'build'
        ],
        '/import[/]' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'import'
        ],
        '/extract[/]' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'extract'
        ],
        '/license[/]' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'license'
        ],
        '*' => [
            'controller' => 'App\Http\Controller\IndexController',
            'action'     => 'error'
        ]
    ],

    'http_options_headers' => [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Headers' => 'Accept, Authorization, Content-Type',
        'Access-Control-Allow-Methods' => 'HEAD, OPTIONS, GET, PUT, POST, PATCH, DELETE',
        'Content-Type'                 => 'application/json'
    ]
];
