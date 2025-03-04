<?php namespace ThisPlugin\Boot;

use App\Support\Tools;
use Boot\PluginKernel;
use App\Services\Activation\PluginActivation as BaseActivator;
use App\Services\ShortCodes\ShortcodeManager;

class PluginActivation {

    public const MAC_SHORTNER_PAGE = 'msp';
    protected string $mac_shortner_page;
    public function __construct() { 
        $this->mac_shortner_page = Tools::generate_wp_slug( self::MAC_SHORTNER_PAGE );
    }

    public function attach(BaseActivator $BaseActivator) {
        // Register additional activation actions
        $BaseActivator->add_activation_callback([$this, 'on_activation']);
    }

    public function on_activation() {
        // Do something specific for this plugin on activation
        $this->create_redirector_page();
    }

    public function create_redirector_page() {

        $page_name = get_option('shortner_page_name') ?: self::MAC_SHORTNER_PAGE;
        $slug = Tools::generate_wp_slug($page_name, config('app.prefix'));
        $short_code = PluginKernel::get_instance()->get(ShortcodeManager::class)->generateShortcodeName( 'shortner_redirector' );

        // Check if the page already exists
        $page = get_page_by_path($page_name); // Get the page by slug

        if (!$page) { // If the page doesn't exist, create it
            // Register the page with the custom template
            $page_id = wp_insert_post([
                'post_title'   => $page_name,
                'post_name'    => $page_name,
                'post_content' => "[{$short_code}]", // The shortcode content (not the rendered output)
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
                'page_template' => 'msp.php', // Assign the custom template
            ]);

            update_option($slug, $page_name);
            wp_cache_flush();
        }

        if (is_wp_error($page_id)) {
            error_log('Page creation error: ' . $page_id->get_error_message());
        }

    }

}