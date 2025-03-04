<?php namespace ThisPlugin\Boot\PluginLoaded;

use Boot\PluginKernel;
use ThisPlugin\Boot\Process;
use ThisPlugin\Interfaces\PluginLoadedActionInterface;


class UserFormInjector implements PluginLoadedActionInterface {

    protected Process $process;


    public function __construct() { 
        $this->process = PluginKernel::get_instance()->get( Process::class );
    }

    public function exported_callbacks( ): void{
        // Register additional activation actions
        $this->inject_in_admin_dashboard( );
        $this->inject_by_user_registration( );
        $this->inject_at_show_user( );
        $this->inject_at_edit_user( );
    }


    public function inject_in_admin_dashboard() {
        add_action('user_new_form', [self::class, 'render_admin_mobile_field'], 5);
    }
    
    public static function render_admin_mobile_field($form_type) {
        if ($form_type === 'add-new-user') {
            PluginKernel::get_instance()->get(Process::class)->render_mobile_field_in_forms();
        }
    }

    public function inject_by_user_registration( ){
        // Add to front-end registration forms
        add_action('register_form', function() {
            $this->process->render_mobile_field_in_forms( is_registration: true);
        }, 5);
        add_action('user_register', [$this, 'save_mobile_number']);
    }

    public function inject_at_show_user( ){
        // Add mobile number field to "Edit User" form (Admin Dashboard)
        add_action('show_user_profile', [ $this->process, 'render_mobile_field_in_forms']);
    }    
    public function inject_at_edit_user( ){
        add_action('edit_user_profile', [ $this->process, 'render_mobile_field_in_forms']);
        add_action('personal_options_update', [$this, 'save_mobile_number']);
        add_action('edit_user_profile_update', [$this, 'save_mobile_number']);
    }
    

    public function save_mobile_number($user_id) {
        // Only save the mobile number if the field is set
        if (isset($_POST['mobile_number']) && !empty($_POST['mobile_number'])) {
            $mobile_number = sanitize_text_field($_POST['mobile_number']);

            // Basic validation (E.164 format)
            if (preg_match('/^\+?[1-9]\d{1,14}$/', $mobile_number)) {
                update_user_meta($user_id, 'mobile_number', $mobile_number);
            } else {
                wp_die('Error: Invalid mobile number format.');
            }
         }
    }

}