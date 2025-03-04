<?php namespace ThisPlugin\Http\Middleware\Api;

use \WP_Error;
use App\Http\Request\Request;
use App\Http\Middleware\Middleware;
use App\Interfaces\MiddlewareInterface;

class ExampleMiddleware extends Middleware  implements MiddlewareInterface {


    public function handle( Request $request, callable $next) {
        
        // if ($request->token !== 'secret') {
        //     return new WP_Error('unauthorized', 'Invalid token', ['status' => 403]);
        // }
        
        return $next( $request ); // Continue to next middleware or controller
    }
}