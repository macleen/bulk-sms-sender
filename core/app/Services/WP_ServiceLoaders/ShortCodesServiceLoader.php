<?php namespace App\Services\WP_ServiceLoaders;

use App\Services\WP_ServiceLoaders\WP_ServiceLoader;
use App\Services\ShortCodes\ShortcodeManager;
use App\Services\Templates\TemplateManager;

class ShortCodesServiceLoader extends WP_ServiceLoader {
    
    protected function register( ): void {
        $this->plugin_kernel->bind( ShortcodeManager::class, fn() =>
            new ShortcodeManager( $this->plugin_kernel->make( TemplateManager::class )));
    }
}