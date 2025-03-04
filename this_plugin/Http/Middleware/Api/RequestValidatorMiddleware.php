<?php namespace ThisPlugin\Http\Middleware\Api;

use Boot\PluginKernel;
use App\Http\Request\Request;
use App\Support\HashedStorage;
use App\Http\Response\WP_Response;
use App\Http\Middleware\Middleware;
use App\Interfaces\MiddlewareInterface;

class RequestValidatorMiddleware extends Middleware implements MiddlewareInterface {

    public function handle( Request $request, callable $next) {
        if ( !$request->url_contains('/api/install')) {
              if ( config('app.commercial_usage') == __COMMERCIAL_TYPE_PRO__) {
                   if ( !PluginKernel::get_instance()->has( HashedStorage:: class )) {
                         env_update('APP_COMMERCIAL_USAGE', __COMMERCIAL_TYPE_BASIC__);
                         return WP_Response::error("\x45\x6e\x76\x69\x72\x6f\x6e\x6d\x65\x6e\x74\x20\x76\x61\x72\x69\x61\x62\x6c\x65\x20\x63\x6f\x6e\x66\x6c\x69\x63\x74",500);
                   }                   
              }
        }      
        return $next($request);
    }
}