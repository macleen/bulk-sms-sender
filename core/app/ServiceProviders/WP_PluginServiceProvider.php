<?php namespace App\ServiceProviders;


use App\Support\Tools;
use App\ServiceProviders\ServiceProvider;
use App\Services\WP_ServiceLoaders\WP_ServiceLoader;


class WP_PluginServiceProvider extends ServiceProvider {
    

    protected function boot( ): void {
        WP_ServiceLoader::load_plugin_services( $this->plugin_kernel );
    }
}