<?php namespace App\Services\WP_ServiceLoaders;

use App\Services\Misc\WpFunctions;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class MiscFunctionsServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $this->plugin_kernel->instance( WpFunctions::class, new WpFunctions( ));
    }

    protected function boot( ): void {
        $this->plugin_kernel
             ->get( WpFunctions::class )
             ->register_default_functions( );
    }

}