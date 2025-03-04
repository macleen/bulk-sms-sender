<?php namespace App\ServiceProviders;

use App\Http\Client\Http;
use App\Http\Client\ClientRequest;



class HttpClientServiceProvider extends ServiceProvider {

    public function boot(): void {
        $this->plugin_kernel->bind(ClientRequest::class, fn() => new ClientRequest );
        $this->plugin_kernel->bind(Http::class, fn() => new Http );
    }
}