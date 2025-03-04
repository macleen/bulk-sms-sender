<?php namespace App\Services\WP_ServiceLoaders;

use ThisPlugin\Boot\Process;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class ProcessServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $this->plugin_kernel->instance( Process::class, new Process( $this->plugin_kernel ) );
    }

}