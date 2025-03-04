<?php namespace Boot\Bootstrappers;

use Illuminate\Support\Str;
use Boot\Bootstrappers\Bootstrapper;

class LoadConfiguration extends Bootstrapper {


    protected function boot(): void {

        $config       = [];
        $folder       = \scandir(config_path());
        $config_files = \array_slice($folder, 2, count($folder));

        if (!empty($config_files)) {
             foreach ($config_files as $file) {
                throw_when(
                    Str::after($file, '.') !== 'php',
                    'Config files must be .php files'
                );
                data_set($config, Str::before($file, '.php'), require config_path($file));
             }
        }

        $this->plugin_kernel->bind('config', fn() => $config );

    }

}