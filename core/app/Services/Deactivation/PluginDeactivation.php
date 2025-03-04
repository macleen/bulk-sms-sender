<?php namespace App\Services\Deactivation;

use App\Services\Settings\SettingsManager;

class PluginDeactivation {
    
    private array $user_deactivation_callbacks = [];

    public function __construct( ) { }

    public function init( ){
        $plugin_fqn = realpath(base_path(config('app.plugin.fqn')));        
        register_deactivation_hook($plugin_fqn, [ $this, 'on_deactivation']);
    }

    public function add_deactivation_callback(callable $callback) {
        $this->user_deactivation_callbacks[] = $callback;
    }

    public function on_deactivation() {
        SettingsManager::unregister_plugin_settings();
        SettingsManager::remove_transient_data();
                // Execute additional callbacks
        foreach ($this->user_deactivation_callbacks as $callback) {
            call_user_func($callback);
        }
    }
    

}