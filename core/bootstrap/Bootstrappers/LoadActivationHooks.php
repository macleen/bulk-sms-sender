<?php namespace Boot\Bootstrappers;

use Boot\Bootstrappers\Bootstrapper;
use App\Services\Activation\PluginActivation as BaseActivator;
use App\Services\Deactivation\PluginDeactivation as BaseDeactivator;
use ThisPlugin\Boot\PluginActivation;
use ThisPlugin\Boot\PluginDeactivation;

class LoadActivationHooks extends Bootstrapper {
    protected function boot( ): void {
        
        $base_activator   = $this->plugin_kernel->make( BaseActivator::class );
        $base_deactivator = $this->plugin_kernel->make( BaseDeactivator::class );
        
        $this->plugin_kernel->make( PluginActivation::class )->attach(  $base_activator );
        $this->plugin_kernel->make( PluginDeactivation::class )->attach( $base_deactivator );

        $base_activator->init( );
        $base_deactivator->init( );
        wp_cache_flush();

    }
}