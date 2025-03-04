<?php namespace Boot;

use Illuminate\Container\Container;
use Boot\Bootstrappers\Bootstrapper;

class PluginKernel {
    
    private static PluginKernel $__instance; 
    public array $bootstapper_loaders = [
        Bootstrappers\LoadEnvironmentVariables::class,
        Bootstrappers\LoadConfiguration::class,
        Bootstrappers\LoadActivationHooks::class,
        Bootstrappers\LoadHttpMiddleware::class,
        Bootstrappers\LoadServiceProviders::class,
    ];

    
    public static function get_instance(  ): PluginKernel { 
        self::$__instance ??= new static( new Container( ));
        return self::$__instance;
    }


    protected function __construct( public Container $container ) { 
        $this->validate_wp_environment( );
    }


    final public static function setup() {
        Bootstrapper::load( self::get_instance( ));
    }


    public function __call( $method, $arguments ): mixed {
        if ( \method_exists( $this->container, $method ))
              return $this->container->{ $method }( ...$arguments );
        throw new \Exception( esc_html("$method is not a service container method"));     
    }


    final public function validate_wp_environment( ) {
        global $wpdb;
        if ( !defined("ABSPATH") || empty($wpdb) )
              die(esc_html(__WP_ENV_IS_MISSING_MSG__));  
    }

}