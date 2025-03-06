<?php namespace App\ServiceProviders;

use App\Support\HashedStorage;

class HashedStorageServiceProvider extends ServiceProvider {

    public function boot(): void {
        $this->plugin_kernel->bind(HashedStorage::class, fn() => new HashedStorage( ) );
    }
}