<?php namespace App\Services\Settings;

use App\DataObjects\Setting;
use App\DataObjects\SettingsSection;
use App\DataObjects\SettingsField;

class SettingsManager {

    protected const TRANSIENT_KEYS_POSTFIX = '_transient_keys';

    public static function get_transient_key_id( ) : string {
        return config('app.plugin.unique_id').self::TRANSIENT_KEYS_POSTFIX;
    }
    
    public function init( ) {
        add_action('admin_init', [$this, 'register_plugin_settings']);
    }

    /**
     * Registers multiple settings in a structured way.
     *
     * @param string $option_group The settings group name.
     * @param Setting[] $settings An array of Setting objects.
     */
    public function register_settings( string $option_group, array $settings ): void {
        foreach ( $settings as $setting ) {
            if ( !get_option( $setting->name )) {
                  add_option( $setting->name, $setting->default, '', $setting->autoload );
            }
            register_setting( $option_group, $setting->name, [
                'sanitize_callback' => $setting->sanitize_callback, // Directly pass the function
            ] );
        }
    }

    /**
     * Registers a settings section.
     *
     * @param SettingsSection $section The section object.
     */
    public function register_section( SettingsSection $section ): void {
        add_settings_section( 
            $section->id,
            $section->title,
            fn(  ) => print esc_html("<p>{$section->description}</p>"),
            $section->page
         );
    }

    /**
     * Registers a settings field.
     *
     * @param SettingsField $field The field object.
     */
    public function register_field( SettingsField $field ): void {
        add_settings_field( 
            $field->id,
            $field->title,
            $field->callback,
            $field->page,
            $field->section,
            $field->args
         );
    }

    /**
     * Retrieves a setting value, returning the default if not found.
     *
     * @param Setting $setting The setting object.
     * @return mixed
     */
    public function get_setting( Setting $setting ): mixed {
        return get_option( $setting->name, $setting->default );
    }


    public function get_option_by_name(string $option_name, mixed $default = ''): mixed {
        return get_option($option_name, $default);
    }


    /**
     * Updates a setting value with automatic sanitization.
     *
     * @param Setting $setting The setting object.
     * @param mixed $value The new value.
     * @return bool
     */
    public function update_setting( Setting $setting, mixed $value ): bool {
        return update_option( $setting->name, $this->sanitize( $value, $setting->sanitize_callback ) );
    }


    public static function delete_setting( string $group_name, Setting $setting ): void {
        delete_option( $setting->name );
        unregister_setting( $group_name, $setting->name );
    }


    /**
     * Generic sanitization method.
     *
     * @param mixed $value The value to sanitize.
     * @param string $sanitize_callback The sanitization function name.
     * @return mixed
     */
    public function sanitize( mixed $value, string $sanitize_callback ): mixed {
        return function_exists( $sanitize_callback ) ? $sanitize_callback( $value ) : $value;
    }



    #-----------------------------------------------------------------------------------------------
    #
    #
    #               A U T O   ( U N ) / R E G I S T E R    P L U G I N    S E T T I N G S
    #
    #
    #-----------------------------------------------------------------------------------------------
    public function register_plugin_settings() {
        
        $plugin_settings = config('plugin_settings');

        if ( !empty( $plugin_settings ) && is_array( $plugin_settings ) ) {
              $groups = $plugin_settings['groups'] ?? [];
              foreach( $groups as $settings_group ) {

                       $group_name     = $settings_group['group_name'] ?? '';
                       $group_options = $settings_group['group_settings'] ?? [];
                       $settings       = [];
                       if ( !empty( $group_name ) && !empty( $group_options)) {
                             foreach( $group_options as $option_name => $default )
                                $settings[] = new Setting( $option_name, $default );
                             $this->register_settings( $group_name, $settings );
                       }     
              }
              wp_cache_flush();
        }
    }



    public static function unregister_plugin_settings() {
        
        $plugin_settings = config('plugin_settings');

        if ( !empty( $plugin_settings ) && is_array( $plugin_settings ) ) {
              $groups = $plugin_settings['groups'] ?? [];
              foreach( $groups as $settings_group ) {

                       $group_name    = $settings_group['group_name'] ?? '';
                       $group_options = $settings_group['group_settings'] ?? [];

                       if ( !empty( $group_name ) && !empty( $group_options)) {
                             foreach( $group_options as $option_name => $_ ) {
                                $settings = new Setting( $option_name );
                               self::delete_setting( $group_name, $settings );
                             }
                       }     
              }
              wp_cache_flush();
        }
    }



    public static function set_transient( string $key, mixed $value, ?int $expiration = null ) {

        $expiration ??= config('app.plugin.default_transient_data.expiration', 3600 );
        set_transient($key, $value, $expiration );                    // Save the transient    
        $stored_transients = get_option( self::get_transient_key_id( ), []); // Get the list of stored transient keys

        if (!\in_array($key, $stored_transients)) {                               // Add the new key if it doesn't exist
            $stored_transients[] = $key;
            update_option(self::get_transient_key_id( ), $stored_transients);
        }
    }


    public static function get_transient( $key, $default = '', ?int $expiration = null ): mixed {

        $v = get_transient($key);
        if ( $v === false) {
             $v = $default;
             self::set_transient( $key, $v, $expiration );
        }
        return $v;     
    }


    public static function remove_transient_data() {

        $stored_transients = get_option(self::get_transient_key_id( ), []);             // Get stored transient keys
        // Loop through and delete each transient
        if ( !empty( $stored_transients ))
              foreach ($stored_transients as $key) {
                 delete_transient($key);
              }
    
        // Remove the option storing transient keys
        delete_option(self::get_transient_key_id( ) );
        
    }
}