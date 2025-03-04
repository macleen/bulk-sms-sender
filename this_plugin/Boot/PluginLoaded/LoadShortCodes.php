<?php namespace ThisPlugin\Boot\PluginLoaded;

use Boot\PluginKernel;
use ThisPlugin\Boot\Process;
use ThisPlugin\Boot\PluginActivation;
use App\Services\ShortCodes\ShortcodeManager;
use ThisPlugin\Interfaces\PluginLoadedActionInterface;


class LoadShortCodes implements PluginLoadedActionInterface {

    protected Process $process;


    public function __construct() { 
        $this->process = PluginKernel::get_instance()->get( Process::class );
    }

    public function exported_callbacks( ): void{
        // Register additional activation actions
        $this->activate_msp_short_code( );
    }


    public function activate_msp_short_code() {
        // Register the shortcode and template
        $page_name = get_option('shortner_page_name') ?: PluginActivation::MAC_SHORTNER_PAGE;
        PluginKernel::get_instance()->get(ShortcodeManager::class)->registerShortcode(
            'shortner_redirector', 
            PluginActivation::MAC_SHORTNER_PAGE,
            'MSP Template', 
            function($atts) use ($page_name) {
                $atts['redirect_code'] = isset($_GET['r']) ? sanitize_text_field($_GET['r']) : null;
                return PluginKernel::get_instance()->get(ShortcodeManager::class)->renderTemplate($atts, null, $page_name);
            }
        );
    }

}