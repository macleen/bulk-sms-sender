<?php namespace Boot\Bootstrappers;


use App\ServiceProviders\ServiceProvider;
use Boot\Bootstrappers\Bootstrapper;


class LoadServiceProviders extends Bootstrapper {
    

    protected function boot( ): void {
        ServiceProvider::load( $this->plugin_kernel );
    }
}