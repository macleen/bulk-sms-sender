<?php namespace App\Services\WP_ServiceLoaders;

use App\Services\Enqueue\Localizer;
use App\Services\Enqueue\ScriptEnqueuer;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class EnqueuerServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $localizer = $this->plugin_kernel->get( Localizer::class );
        $this->plugin_kernel->instance( ScriptEnqueuer::class,  new ScriptEnqueuer( $localizer ));
    }

    protected function boot( ): void {

        $this->plugin_kernel
             ->get( ScriptEnqueuer::class )
             ->init( )
             ->enqueue( );
    }
}