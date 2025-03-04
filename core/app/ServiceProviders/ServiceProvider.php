<?php namespace App\ServiceProviders;

use App\Support\Tools;
use Boot\PluginKernel;
use App\ServiceProviders\BladeServiceProvider;


class ServiceProvider {
    
    protected array $service_providers = [
        RouteServiceProvider::class,
        BladeServiceProvider::class,
        PluginServiceProvider::class,
        WP_PluginServiceProvider::class,
        HashedStorageServiceProvider::class,
    ];
    
    public function __construct( protected PluginKernel $plugin_kernel ) { }

    
    final public static function load( PluginKernel $plugin_kernel ) {

        $self = new static( $plugin_kernel );
        $c    = collect($self->service_providers )
                    ->map( fn ( $service_provider ) => new $service_provider( $plugin_kernel ))
                    ->each(fn ( ServiceProvider $service_provider ) => $service_provider->register());

        $c->each(fn ( ServiceProvider $service_provider ) => $service_provider->boot());            

    }


    protected function register( ): void {}
    protected function boot( ): void {}


}