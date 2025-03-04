<?php namespace Boot\Bootstrappers;

use Boot\PluginKernel;


abstract class Bootstrapper {

    public function __construct( protected PluginKernel $plugin_kernel ) { }

    
    final public static function load( PluginKernel $plugin_kernel ) {

        collect($plugin_kernel->bootstapper_loaders )
            ->map( fn ( $bootstrapper ) => new $bootstrapper( $plugin_kernel ))
            ->each(fn ( Bootstrapper $bootstrapper ) => $bootstrapper->boot());

    }


    abstract protected function boot( ): void;


}