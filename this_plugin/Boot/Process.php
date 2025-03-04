<?php namespace ThisPlugin\Boot;

use App\Support\Tools;
use Boot\PluginKernel;
use eftec\bladeone\BladeOne;
use ThisPlugin\Models\Phone;
use App\Services\Misc\WpFunctions;
use App\Services\MenuBuilder\AdminMenuBuilder;


class Process {

    protected BladeOne $blade;
    protected WpFunctions $wp_functions;
    
    public string $nonce;

    public function __construct( PluginKernel $plugin_kernel ) {
        $this->blade = $plugin_kernel->get(BladeOne::class);
        $this->process_default_actions( );
    }

    protected function process_default_actions( ){

    }

    public function get_wp_nonce( string $action_id = 'wp_nonce' ) {
        return '<input type="hidden" name="_wpnonce_choko" id="_wpnonce_choko" value="'.wp_create_nonce("wp_nonce").'">';
    }



    public function show_settings_page() {

        $help_page_link = PluginKernel::get_instance()->get(AdminMenuBuilder::class)->get_menu_link( 'Documentation' );
        $groups         = config('plugin_settings');
        $plugin_url     = esc_url(config('app.plugin.url'));
        $disabled       = config('app.commercial_usage') != __COMMERCIAL_TYPE_PRO__ ? 'disabled' : '';

        ob_start();
        foreach( $groups as $groups_data )
            foreach( $groups_data as $group )
                settings_fields($group['group_name']);
        $settings_fields_html = ob_get_clean();


        ob_start();
        foreach( $groups as $groups_data )
            foreach( $groups_data as $group )
              if ( isset( $group['section']))
                   do_settings_sections($group['section']);
        $settings_sections_html = ob_get_clean();

        ob_start();
        submit_button();
        $submit_button_html = ob_get_clean();
        
        echo $this->blade->run('admin.pages.settings', [
            'settings_fields_html' => $settings_fields_html,
            'settings_sections_html' => $settings_sections_html,
            'submit_button_html'  => $submit_button_html,
            'plugin_url'          => $plugin_url,  
            'help_page_link'      => $help_page_link,
            'input_fg_color'      => $disabled ? '#9f9393' : '#f3ebeb',  
            'input_bg_color'      => $disabled ? '#a9ada880' : '#0f100f80',  
            'disabled'            => $disabled,
        ]);
    }

    
    public function show_sms_panels() {
        $plugin_url = esc_url(config('app.plugin.url'));
        $disabled   = config('app.commercial_usage') != __COMMERCIAL_TYPE_PRO__ ? 'disabled' : '';
        echo $this->blade->run('admin.pages.panels', compact('plugin_url', 'disabled'));
    }

    public function show_help_page() {
        $plugin_url = esc_url(config('app.plugin.url'));
        echo $this->blade->run('admin.pages.help-page', compact('plugin_url'));
    }


    /**
     * Replace localizer placeholders with actual values
     */
    protected function ajaxUrl( $path ) {
        return esc_url('/wp-json/'.config('routing.package_rest_api_name_space'));
    }



    protected function nonce( $nonce_id ) {
        return wp_create_nonce( $nonce_id );
    }



    public function register_download_action( ) {
        add_action('wp_ajax_download_messages', [$this, 'download_messages_callback']);
    }

    public function download_messages_callback() {
        // Ensure the request is coming from an authenticated user
        if (!isset($_POST['messages']) || !current_user_can('manage_options')) {
            wp_send_json_error('No messages or permission issue.');
        }
    
        // Get the messages content from the request
        $messages = sanitize_textarea_field($_POST['messages']);
    
        // Get the upload directory
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/messages.txt';
    
        // Write the content to the file
        file_put_contents($file_path, $messages);
    
        // Check if the file is actually created
        if (file_exists($file_path)) {
            // Clean the file URL to ensure proper format
            $file_url = $upload_dir['url'] . '/messages.txt';
    
            // Send back the clean URL of the saved file for downloading
            wp_send_json_success(['file_url' => $file_url]);
        } else {
            wp_send_json_error('Failed to create the file.');
        }
    }


    public function register_send_mail_action( ) {
        add_action('wp_ajax_send_messages_email', [$this, 'send_messages_email_callback']);
    }

    public function send_messages_email_callback() {
        // Check permissions
        $messages = isset($_POST['messages'])
                  ? sanitize_text_field(wp_unslash($_POST['messages'])) : null;

        $email    = isset($_POST['email'])
                  ? sanitize_text_field(wp_unslash($_POST['email'])) : null;

        if (!$messages || !$email || !current_user_can('manage_options')) {
            wp_send_json_error('Missing data or permission denied.');
        }
    
        // Sanitize inputs
    
        if (!is_email($email)) {
            wp_send_json_error('Invalid email address.');
        }
    
        // Email subject & body
        $subject = 'A Copy of the Chat Messages';
        $body    = "Here are the messages:\n\n$messages";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
    
        // Send the email
        $sent = wp_mail($email, $subject, $body, $headers);
    
        if ($sent) {
            wp_send_json_success('Email sent successfully.');
        } else {
            wp_send_json_error('Failed to send email.');
        }
    }

    public function copyright_data() {
        return config('app.name').' - '.config('app.version');
    }

    public function plugin_url(): string {
        return esc_url(config('app.plugin.url'));
    }

    public function license() {
        return config('app.commercial_usage') ?? __COMMERCIAL_TYPE_BASIC__;
    }


    public function render_mobile_field_in_forms( $user = null, $is_registration = false ) {

        // Get the mobile number from user meta if available
        $mobile_number = !$is_registration && $user
            ? get_user_meta($user->ID, 'mobile_number', true)
            : '';
        echo $this->blade->run('admin.sections.user-mobile-input-field', compact('mobile_number', 'is_registration'));
    }



    public function show_db_field_sync_page( ){
        global $wpdb;
        $phone_model    = new Phone( $wpdb );
        $plugin_url     = esc_url(config('app.plugin.url'));
        $disabled       = config('app.commercial_usage') != __COMMERCIAL_TYPE_PRO__ ? 'disabled' : '';
        $input_fg_color = $disabled ? '#9f9393' : '#f3ebeb';
        $input_bg_color = $disabled ? '#a9ada880' : '#2096ec80';

        $fields         = $phone_model->getPotentialPhoneFields( );
        echo $this->blade->run('admin.pages.sync-db-phone-fields', compact(
            'plugin_url', 
            'disabled', 
            'fields', 
            'input_fg_color',
            'input_bg_color',
        ));
        $phone_model->update_field_names_from_form( );
    }

    public function show_logging_page( ){
        $plugin_url     = esc_url(config('app.plugin.url'));
        $disabled       = config('app.commercial_usage') != __COMMERCIAL_TYPE_PRO__ ? 'disabled' : '';
        echo $this->blade->run('admin.pages.logging', compact(
            'plugin_url', 
            'disabled', 
        ));

    }


    public function show_analytics_log( ){
        $plugin_url     = esc_url(config('app.plugin.url'));
        $disabled       = config('app.commercial_usage') != __COMMERCIAL_TYPE_PRO__ ? 'disabled' : '';
        echo $this->blade->run('admin.pages.analytics', compact(
            'plugin_url', 
            'disabled', 
        ));

    }


    public function __call( $method, $arguments ): mixed {
        if ( \method_exists( $this, $method ))
              return $this->{ $method }( ...$arguments );
        throw new \Exception( "$method is not defined in the local process set of functions"); 
    }
}