<?php namespace ThisPlugin\Http\Middleware\Admin;

use App\Http\Request\Request;
use App\Http\Response\WP_Response;
use App\Http\Middleware\Middleware;
use App\Interfaces\MiddlewareInterface;
use App\Services\Settings\SettingsManager;

class LimitRateMiddleware extends Middleware implements MiddlewareInterface {

    
    public function handle( Request $request, callable $next) {

        $userIdentifier = $request->ip();  // Could be IP, Token, User ID, etc.
        $cacheKey = "rate_limit_{$userIdentifier}";

        // Get the current request count from the cache or set it if not found
        $requestCount = SettingsManager::get_transient( $cacheKey, 1, config('api.time_window'));
        if ( $requestCount >= config('api.max_requests'))
             // Exceeded limit
             return WP_Response::error('Rate_limit_exceeded, Too many requests. Please try again later.',429);
             // Increment request count if under the limit
        SettingsManager::set_transient( $cacheKey, $requestCount + 1, config('api.time_window'));
        // Proceed to the next middleware or controller
        return $next($request);
    }
}