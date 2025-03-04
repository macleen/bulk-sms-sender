<?php namespace App\Services\WP_ServiceLoaders;

use App\Support\Tools;
use App\Services\Init\PluginInitializer;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class InitializationrServiceLoader extends WP_ServiceLoader {

    protected const INITIALIZATION_NS    = 'ThisPlugin\\Boot\\PluginLoaded\\';
    protected function register( ): void {

        $location = this_plugin_path( 'Boot/PluginLoaded');
        $classes  = Tools::get_classes_from_folder( $location, self::INITIALIZATION_NS );
        foreach( $classes as $class ){
            require_once $class['file_name'];
            $p = new $class['fqn'];
            PluginInitializer::import( [$p, 'exported_callbacks' ]);
        }

    }


    protected function boot( ): void {
        $this->plugin_kernel
             ->make( PluginInitializer::class )
             ->on_plugins_loaded_event( );
    }
}