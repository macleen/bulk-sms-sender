<?php 

return [

    /*
    |--------------------------------------------------------------------------
    | Installation Requirements
    |--------------------------------------------------------------------------
    |
    */

    'php'   => [
        'min_version'    =>  "8.0.13",
        'extensions'    => [
            'mbstring',
            'tokenizer',
            'json',
            'curl',
            'zip',
            'zlib',
            'fileinfo',
            'exif',
        ],
    ],
    'apache'    => [
        'mod_rewrite',
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is default folder permission for process installation.
    |
    */

    'permissions'   => [
        'storage/framework/'     => '775',
        'storage/logs/'          => '775',
        'bootstrap/cache/'       => '775',
    ],
];