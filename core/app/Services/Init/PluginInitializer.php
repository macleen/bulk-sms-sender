<?php namespace App\Services\Init;

// Define the PluginInitializer class
class PluginInitializer {
    private static array $callbacks = [];

    public function on_plugins_loaded_event(): void {
        // Hook PluginInitializer to 'plugins_loaded'
        add_action('plugins_loaded', [ $this, 'on_plugins_loaded']);
    }

    // Register a callback to be executed on 'plugins_loaded'
    public static function import(callable $callback): void {
        self::$callbacks[] = $callback;
    }

    // Initialize all registered callbacks when plugins are loaded
    public function on_plugins_loaded(): void {
        foreach (self::$callbacks as $callback) {
            call_user_func($callback);
        }
    }
}