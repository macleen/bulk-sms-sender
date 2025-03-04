<?php namespace ThisPlugin\Interfaces;

use \WP_REST_Response;
/**
 * This is an interface so that the model is coupled to a specific backend.
 *
 * required to bind an interface to an implementation with PHP-DI.
 */

interface ProviderRepositoryInterface {


    /**
     * @return string balance
     */
    public function get_balance( ): string;

    /**
     * @return array account_info
     */
    public function get_account_info( ): WP_REST_Response;

    /**
     * @return array provider_response
     */

     public function get_provider_fields( ):WP_REST_Response;
     

    public function get_packet_format( ):WP_REST_Response;

    public function send( ): WP_REST_Response;

}