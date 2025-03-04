<?php namespace App\Routing;

use App\Support\Tools;

class RouteGroup {
    private $definitions_callback;

    public function __construct( 
                                  private string $group_prefix    = '', 
                                  private array $middleware       = [], 
                                  ?callable $definitions_callback = null 
                                ) {
        $this->definitions_callback = $definitions_callback;
    }

    public function setup(): self {
        // Store the current route group prefix
        $previous_group_prefix = Route::$group_prefix;
        $previous_middleware = Route::$middleware;

        Route::$group_prefix .= $this->group_prefix;
        Route::$middleware = array_merge($previous_middleware, $this->middleware);
    
        // Define the routes (this calls the callback with route definitions)
        call_user_func($this->definitions_callback);

        // Register all routes with the correct group_prefix
        Route::register_all_routes(Route::$middleware);
    
        // Restore the previous state after route registration
        Route::$group_prefix = $previous_group_prefix;
        Route::$middleware = $previous_middleware;
    
        return $this;
    }
    
}