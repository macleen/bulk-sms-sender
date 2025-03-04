<?php namespace App\ServiceProviders;

use eftec\bladeone\BladeOne;
use App\ServiceProviders\ServiceProvider;


class BladeServiceProvider extends ServiceProvider {
    public function boot(): void {
      
        $views = config('blade.views');
        $cache = config('blade.cache');
        $mode  = config('app.debug') ? BladeOne::MODE_DEBUG : BladeOne::MODE_AUTO;
        
        $this->plugin_kernel->instance(BladeOne::class, new BladeOne( $views, $cache, $mode ));

    }


}