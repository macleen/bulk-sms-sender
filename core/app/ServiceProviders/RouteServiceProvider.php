<?php namespace App\ServiceProviders;

use App\Routing\RouteCollector;

class RouteServiceProvider extends ServiceProvider {

    public function boot(): void {
        RouteCollector::setup( $this->plugin_kernel );
    }
}