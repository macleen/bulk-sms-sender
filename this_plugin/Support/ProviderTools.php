<?php namespace ThisPlugin\Support;

use App\Support\Tools;
use ThisPlugin\Support\DateTime;


#---------------------------------------------------------------
#
#---------------------------------------------------------------

class ProviderTools {

    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------

    public static function get_available_service_providers( ) {        
        return Tools::get_sorted_folder_files( this_plugin_path(config('sms.provider_folder')), config('sms.provider_postfix'));
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public static function service_provider_location( ?string $repository ) : string{
        return sprintf( config('sms.repository_location'), DateTime::in_zone_rotational_cycle( ) ? $repository : 'Test');
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public static function provider_exists( string $provider_name ): ?string  {

        if ( $provider_name ) {
             $providers = self::get_available_service_providers( );
             $key = array_search(strtolower($provider_name), array_map('strtolower',$providers));
             if ( $key !== false) $key = $providers[ $key ];

             return $key;
        }
        return null;     
        
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private static function get_installed_plugins( ): ?array {
        
        $path = config('routing.plugins.folder');        
        return \file_exists($path)
            ?   Tools::get_sorted_folder_files( $path, $post_fix = NULL, $sort = 'DESC' ) : null;
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private static function sort_plugins( ): ?array {
        
        $template = [
            'fqn'       =>'', 
            'location'  => '', 
            'supported' => true
        ];
        $sorted_plugins = [
            'Already-Installed'=> [],
            'Purchasable'      => [],
        ];

        $all_plugins       = config('all_plugins');
        $installed_plugins = self::get_installed_plugins( );
        foreach( $all_plugins as $plugin_name => $plugin_data ) {
            if ( $plugin_data['supported'] ) {
                 if ( in_array( $plugin_data['fqn'], $installed_plugins )) {
                      $sorted_plugins['Already-Installed'][$plugin_name] = $template;
                      unset( $all_plugins[$plugin_name]);
                 }     
            } else   unset( $all_plugins[$plugin_name]);
        }         
        $sorted_plugins['Purchasable'] = $all_plugins;
        return $sorted_plugins;
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------


    public static function get_available_plugins_tree(): array{
        $template = [
            'title' => '',
            'location' => '',
            'name' => '',
            'folder' => true, 
            'children' => [ ]
        ]; 

        $total_purchasable_plugins = 0;
        $res = $template;
        $res['title'] = 'All Plugins';

        $all_plugins = self::sort_plugins( );
        if (!empty( $all_plugins )) {
            foreach( $all_plugins as $plugin_group_name => $plugin_group_data ) {
                $child          = $template;
                $child['title'] = $plugin_group_name;
                if (!empty( $plugin_group_data )) {
                    foreach( $plugin_group_data as $plugin_name => $plugin_data ) {
                             $child_entry            = $template;
                             $child_entry['title']   = $plugin_name;
                             $child_entry['folder']  = false;
                             $child_entry['location']= $plugin_data['location'];
                             $child_entry['name']= $plugin_name;
                             $child['children'][]    = $child_entry;
                             $total_purchasable_plugins++;
                    }
                    $res['children'][] = $child;
                }                
            }
        }
 
        return [
                'total' => [
                        'purchasable' => $total_purchasable_plugins,
                        'installed'   => count($all_plugins['Already-Installed']),
                ],        
                'source' => [$res],
        ];        
    }
        
}