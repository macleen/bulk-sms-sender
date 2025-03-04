<?php namespace ThisPlugin\Http\Controllers;

use \WP_REST_Response;
use App\Http\Response\WP_Response;
use ThisPlugin\Http\Controllers\BaseController;


class AnalyticsController extends BaseController {
   
   
    public function get_available_files(): WP_REST_Response {
 
        return match( !!1 ) {
            $this->lc => $this->hashed_storage
                       ? WP_Response::success(
                            $this->hashed_storage->listAllFiles()
                         )
                       : WP_Response::error(
                         'Hashed Storage component is not loaded', 500 
                         ),
            default   => $this->inappropriate_license( ),
        };        
          
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_file_by_path( ): WP_REST_Response {

        $get_content = function( ): WP_REST_Response {
            $res = $this->hashed_storage->getFileContentByPath( $this->request->path );
            return $res['ok']
                ? WP_Response::success($res['data'])
                : WP_Response::error( $res['message'], $res['status']);
           }; 
        return match( !!1 ) {
            $this->lc => $this->hashed_storage
                      ? $get_content( )
                      : WP_Response::error(
                    'Some package components are not fully loaded', 500 
                        ),
            default  => $this->inappropriate_license( ),
        };        

    }

    
    public function delete_file_by_path( ): WP_REST_Response {

        $delete_file = function( ) {
                        $res = $this->hashed_storage->deleteFileByPath( $this->request->path );
                        return $res['ok']
                            ? WP_Response::success()
                            : WP_Response::error( $res['message'], $res['status']);
                       }; 

        return match( !!1 ) {
            $this->lc => $this->hashed_storage
                                  ? $delete_file( )
                       : WP_Response::error(
                         'Hashed Storage component is not loaded', 500 
                         ),
            default   => $this->inappropriate_license( ),
        };                

    }

}