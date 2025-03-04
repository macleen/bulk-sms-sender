<?php namespace App\Services\WP_ServiceLoaders;

use ThisPlugin\Boot\Process;
use App\Services\MenuBuilder\AdminMenuBuilder;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class MenuServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $process = $this->plugin_kernel->get( Process::class );
        $this->plugin_kernel->instance( AdminMenuBuilder::class, new AdminMenuBuilder( $process ));
    }

    protected function boot( ): void {

        $this->plugin_kernel
             ->get( AdminMenuBuilder::class )
             ->register_admin_menus( );
    }
}