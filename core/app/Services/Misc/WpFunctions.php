<?php namespace App\Services\Misc;


class WpFunctions {

    public function register_default_functions() {

    }


    public function force_admin_page_refresh() {
        if (!\headers_sent()) {
             $current_url = isset($_SERVER['REQUEST_URI']) ? admin_url(wp_unslash( $_SERVER['REQUEST_URI'])) : '';
             if ( $current_url ) {
                  wp_redirect($current_url);
                  exit;
             }     
        }    
    }

   
}