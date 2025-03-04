<?php namespace App\Http\Middleware;


use Boot\PluginKernel;
use App\Http\Request\Request;
use App\Interfaces\MiddlewareInterface;


abstract class Middleware implements MiddlewareInterface{


    public static function get_middleware( ?string $middleware_group = null ): array {
        $middleware = PluginKernel::get_instance()->get('middleware');

        return $middleware_group 
             ? $middleware[ $middleware_group ] ?? $middleware
             : $middleware;
    }        


   
    public static function apply_middleware(array $routeMiddleware, callable $next, Request $request, string $route = '/') {
        // If there is no middleware to apply, directly call the next function
        if (empty($routeMiddleware)) {
            return $next($request);
        }
    
        // Merge global, prefix-specific, and route-specific middleware
        $middlewareStack = array_merge(
            $routeMiddleware,
            self::get_middleware(self::get_group_prefix($route)), // e.g., 'api', 'admin', etc.
            self::get_middleware('global')
        );
    
        // Remove duplicates from the middleware stack
        $middlewareStack = array_unique($middlewareStack, SORT_REGULAR);
   
        // Build the middleware chain
        $middlewareChain = array_reduce(
            array_reverse($middlewareStack), // Reverse so middleware is applied in the correct order
            function ($nextMiddleware, $middleware) use ($request) {
                // Ensure middleware implements MiddlewareInterface
                if (!$middleware instanceof MiddlewareInterface) {
                    throw new \InvalidArgumentException("Middleware must implement " . MiddlewareInterface::class);
                }
    
                // Return middleware instance wrapped with the next function to pass the chain along
                return function () use ($middleware, $nextMiddleware, $request) {
                    return $middleware->handle($request, $nextMiddleware);
                };
            },
            fn() => $next($request) // Final callable, the actual route handler
        );
    
        // Start the chain execution
        return $middlewareChain();
    }
    
    
    private static function get_group_prefix( $route ) {
        $r   = \explode('/',$route );
        return \count($r) < 2 
             ? '' : $r[1];
    }

    abstract public function handle( Request $request, callable $next );
}