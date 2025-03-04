<?php namespace Boot\Bootstrappers;

use Boot\Bootstrappers\Bootstrapper;
use Boot\Services\EnvironmentVariablesManager;

class LoadEnvironmentVariables extends Bootstrapper {

    protected function register( ): void {   
        $this->plugin_kernel->bind( EnvironmentVariablesManager::class, fn( ) => new EnvironmentVariablesManager( ));
    }
    
    protected function boot( ): void {   
        $this->plugin_kernel->get( EnvironmentVariablesManager::class )->load( );
    }

}