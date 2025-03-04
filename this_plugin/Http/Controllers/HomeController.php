<?php namespace ThisPlugin\Http\Controllers;

use \WP_REST_Response;
use App\Support\Tools;
use App\Support\Zipper;
use ThisPlugin\Models\User;
use App\Http\Response\WP_Response;
use ThisPlugin\Support\ProviderTools;
use ThisPlugin\Http\Controllers\BaseController;


class HomeController extends BaseController {
   
    protected const UPLOAD_DIR = '/plugin_installs/';
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_available_providers(): WP_REST_Response {
        sleep(1);
        return WP_Response::success(
            ProviderTools::get_available_service_providers( )
        );
    }

    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function install_plugin( ): WP_REST_Response {

        if (!isset($_FILES['zip_file']))
            return WP_Response::error( 'Invalid or no file uploaded', 400);

        $uploaded_file = $_FILES['zip_file'];
        $file_ext      = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);   // Validate file type
        if ($file_ext !== 'zip')
            return WP_Response::error('Only ZIP files are allowed', 400);

        $upload_dir = wp_upload_dir();                              // Define upload path
        $plugin_dir = $upload_dir['basedir'] . self::UPLOAD_DIR;

        if (!file_exists($plugin_dir)) {
             wp_mkdir_p($plugin_dir);
        }
       
        $zip_file = $plugin_dir . basename($uploaded_file['name']);
        if (!move_uploaded_file($uploaded_file['tmp_name'], $zip_file))     // Move uploaded filehe uploaded file   
            return WP_Response::error( 'Failed to move uploaded file', 500);

        $extraction_path = "{$plugin_dir}extracted/";                               // Extract the ZIP file
        if (!file_exists($extraction_path)) 
             wp_mkdir_p($extraction_path);

        $zip = new Zipper( );

        $res = $zip->decompress( $zip_file, $extraction_path );
        if ( $res['ok']) 
             $res = Tools::move_files( $extraction_path, __PLUGIN_PATH__ );
        return $res['ok']
             ? WP_Response::success(message: 'Files successfully installed and published.')
             : WP_Response::error( $res['message'], 500);

    }


    public function installed_plugins_tree( ): WP_REST_Response {
        return WP_Response::success(
            ProviderTools::get_available_plugins_tree( )
        );
    }


    public function members_with_phone( $role ): WP_REST_Response {
        global $wpdb;

        $get_members = function( $role ) use ( $wpdb ) {
                $res = (new User( $wpdb ))->member_list_with_phones( $role );
                return $res['ok']
                    ? WP_Response::success( \implode( __LF__, array_values( $res['data'])))
                    : WP_Response::error( $res['message'], $res['status']);
        };

        return $this->lc && $this->hashed_storage
             ? $get_members( $role )
             : $this->inappropriate_license( );

    }

}