<?php namespace App\Interfaces;

use App\Http\Request\Request;

interface MiddlewareInterface {
    public function handle( Request $request, callable $next);
}