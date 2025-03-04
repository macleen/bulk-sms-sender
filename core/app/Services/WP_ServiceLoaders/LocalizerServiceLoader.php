<?php namespace App\Services\WP_ServiceLoaders;

use ThisPlugin\Boot\Process;
use App\Services\Enqueue\Localizer;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class LocalizerServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $process = $this->plugin_kernel->get( Process::class );
        $this->plugin_kernel->instance( Localizer::class, new Localizer( $process ));
    }

}