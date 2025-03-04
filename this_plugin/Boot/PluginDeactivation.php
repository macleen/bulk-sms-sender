<?php namespace ThisPlugin\Boot;

use App\Support\Tools;
use ThisPlugin\Boot\PluginActivation;
use App\Services\DeActivation\PluginDeactivation as BaseDeactivator;
use App\Services\ShortCodes\ShortcodeManager;
use Boot\PluginKernel;

class PluginDeactivation {
    
    public function attach( BaseDeactivator $BaseDeactivator ) {
        // Register additional activation actions
        $BaseDeactivator->add_deactivation_callback([$this, 'on_deactivation']);
    }

    public function on_deactivation() {
        $this->remove_redirector_page( );
        $this->remove_short_codes( );
    }

    public function remove_redirector_page( ) {

        $page_name = get_option( 'shortner_page_name' ) ?: PluginActivation::MAC_SHORTNER_PAGE;
        $slug      = Tools::generate_wp_slug( $page_name, config('app.prefix'));
        $page      = get_page_by_path($page_name ); // the short slug for the page

        if ( $page ) {
             wp_delete_post($page->ID, true); // true forces deletion (bypass trash)
             delete_option($slug); // Clean up the option
        }
    }
    public function remove_short_codes(){

        $short_codes        = ['shortner_redirector'];
        $short_code_manager = PluginKernel::get_instance()
                                          ->get( ShortcodeManager::class );
        
        \array_walk( $short_codes, fn( $short_code ) => $short_code_manager->unregisterShortcode( $short_code ));
    }

}