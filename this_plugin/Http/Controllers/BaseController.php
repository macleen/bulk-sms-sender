<?php namespace ThisPlugin\Http\Controllers;

use \WP_REST_Response;
use Boot\PluginKernel;
use App\Http\Request\Request;
use App\Support\HashedStorage;
use App\Http\Response\WP_Response;
use ThisPlugin\Support\ProviderTools;
use ThisPlugin\Interfaces\ProviderRepositoryInterface;
use App\Http\Controllers\BaseController as Level1Controller;

class BaseController extends Level1Controller {

    protected bool $lc;
    protected PluginKernel $plugin_kernel;
    protected Request $request;    
    protected ?HashedStorage $hashed_storage;


    public function __construct(  ) {

        $this->plugin_kernel  = PluginKernel::get_instance();
        $this->request        = $this->plugin_kernel->get( Request::class );
        $this->lc             = config('app.commercial_usage') === __COMMERCIAL_TYPE_PRO__;
        $this->hashed_storage = $this->plugin_kernel->has( HashedStorage::class )
                              ? $this->plugin_kernel->get( HashedStorage::class ) : null;  

        parent::__construct( );     
    }


    public function bind_provider( string $provider_id = 'Test') : self {
        $p = $this->fqn( $provider_id );
        $this->plugin_kernel
             ->bind( ProviderRepositoryInterface::class, 
                        fn( ) => $p 
                                ? new $p( $this->request )
                                : WP_Response::error("Provider not found or invalid provider id", 404)
               );
        return $this;
    }

    
    public function __call( string $call, array $args = [ ] ) : WP_REST_Response {

        $this->install_error_handler( );
        try {
            $provider = \array_shift( $args );
            $p = $this->bind_provider( $provider )
                    ->plugin_kernel->get( ProviderRepositoryInterface::class );

            $response = $p instanceof WP_REST_Response ? $p
                : ( !method_exists($p, $call)
                        ? WP_Response::error("Method $call not supported", 422)
                        : $this->plugin_kernel->call([ $p, $call ], $args )
                    );
            $this->restore_error_handler( );
            return $response;

        } catch( \Exception $e ) {
            $this->restore_error_handler( );
            return WP_Response::error( $e->getMessage( ), $e->getCode( ));
        }
    }

    private function fqn( ?string $provider = null ) : ?string {
        return ProviderTools::provider_exists( $provider )
             ? ProviderTools::service_provider_location( $provider ) : null;

    }

    protected function inappropriate_license( ) {
        return WP_Response::error(
            'Your License does not support this feature', 402 
        );
    }

}