<?php namespace App\Services\WP_ServiceLoaders;

use App\Services\Settings\SettingsManager;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class SettingsManagerServiceLoader extends WP_ServiceLoader {
        
    protected function register( ): void {
        $this->plugin_kernel->instance( SettingsManager::class, new SettingsManager );
    }

    protected function boot( ): void {
        $this->plugin_kernel->get( SettingsManager::class )->init( );
    }

}