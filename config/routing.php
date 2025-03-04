<?php

return [
    'package_rest_api_name_space' => env('APP_REST_NAMESPACE'),
    'controllers' => [
        'namespace' => '\\ThisPlugin\\Http\\Controllers\\',
    ],
    'plugins' => [
        'folder' => this_plugin_path('SmsServiceProviders'),
        'namespace' => '\\ThisPlugin\\SmsServiceProviders\\',
    ],
];