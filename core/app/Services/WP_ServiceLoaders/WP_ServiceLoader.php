<?php namespace App\Services\WP_ServiceLoaders;

use App\Support\Tools;
use Boot\PluginKernel;


class WP_ServiceLoader {

    protected array $local_services_loader = [
        MiscFunctionsServiceLoader::class,
        ProcessServiceLoader::class,
        LocalizerServiceLoader::class,
        EnqueuerServiceLoader::class,
        MenuServiceLoader::class,
        SettingsManagerServiceLoader::class,
        InitializationrServiceLoader::class,
        ShortCodesServiceLoader::class,
    ];

    public function __construct( protected PluginKernel $plugin_kernel ) { }

    
    final public static function load_plugin_services( PluginKernel $plugin_kernel ) {

        $self = new static( $plugin_kernel );
        $c    = collect($self->local_services_loader )
                    ->map( fn ( $service_loader ) => new $service_loader( $plugin_kernel ))
                    ->each(fn ( WP_ServiceLoader $service_loader ) => $service_loader->register());

        $c->each(fn ( WP_ServiceLoader $service_loader ) => $service_loader->boot());            

    }


    protected function register( ): void {}
    protected function boot( ): void {}


}