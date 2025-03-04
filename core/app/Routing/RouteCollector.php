<?php namespace App\Routing;

use App\Routing\Route;
use Boot\PluginKernel;

class RouteCollector {

    public static function setup( PluginKernel $plugin_kernel ) {
        // Registers and boots the route groups
        $middleware     = $plugin_kernel->get('middleware');
        $possible_route = ['api' => '/api', 'admin' => '/admin', 'web' => '' ];

        foreach( $possible_route as $group => $prefix ) {
            $routes_file = trim(routes_path("{$group}.php"));
            if ( \file_exists( $routes_file ) && \strlen(\trim( \file_get_contents( $routes_file )))) {
                  Route::group( 
                    $prefix, \array_merge(
                            $middleware[ $group],
                                    $middleware['global']
                    ), fn( ) => require $routes_file
                  );
            }
        }
    }

}