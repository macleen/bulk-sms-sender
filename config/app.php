<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name'              => env('APP_NAME', 'MacLeen-App'),
    'version'           => env('APP_VERSION', 'v1.0.0'),
    'short_version'     => env('APP_SHORT_VERSION', ''),
    'secret_key'        => env('APP_SECRET_KEY'),
    'prefix'            => env('BRAND_ID', __BRAND_ID__).'-'.env('APP_PREFIX', __PLUGIN_ID__),
    'commercial_usage'  => env('APP_COMMERCIAL_USAGE', __COMMERCIAL_TYPE_BASIC__),
    'plugin_unique_id'  => env('BRAND_ID', __BRAND_ID__).'-'.
                           env('APP_PREFIX', __PLUGIN_ID__),
    'plugin' => [
          'author_email'      => env('APP_AUTHOR_SUPPORT_EMAIL'),
          'url'               => plugin_dir_url(__FILE__).'../',
          'root_folder'       => \realpath(base_path( )),
          'fqn'               => env('PLUGIN_FQN', ''),
          'requires_htaccess' => env('APP_RQUIRES_HTACCESS', false),
          'unique_id'         => env('BRAND_ID', __BRAND_ID__).'-'.
                                 env('APP_PREFIX', __PLUGIN_ID__),
          'default_transient_data.expiration' => 3600,

    ],

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),


    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

];