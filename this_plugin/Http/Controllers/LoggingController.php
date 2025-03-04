<?php namespace ThisPlugin\Http\Controllers;

use \WP_REST_Response;
use ThisPlugin\Support\SendLog;
use App\Http\Response\WP_Response;
use ThisPlugin\Http\Controllers\BaseController;


class LoggingController extends BaseController {
   
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_available_log_files(): WP_REST_Response {
        return WP_Response::success(
            SendLog::get_available_log_files()
        );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_log_by_date( $date ): WP_REST_Response {
        return match( !!1 ) {
            $this->lc => $this->hashed_storage
                            ? WP_Response::success(
                                SendLog::get_log_by_date( $date )
                                )
                            : WP_Response::error(
                                'Hashed Storage component is not loaded', 500 
                              ),
            default  => $this->inappropriate_license( ),
        };
    }


    public function delete_log_by_date( $date ): WP_REST_Response {

        $delete_file = function( ) use ( $date ) {
                        $res = SendLog::delete_log_by_date( $date );
                        return $res['ok']
                            ? WP_Response::success()
                            : WP_Response::error( $res['message'], $res['status']);
                        }; 


        return match( !!1 ) {
            $this->lc => $this->hashed_storage
                       ? $delete_file( )
                       : WP_Response::error(
                         'Some package components are not fully loaded', 500 
                         ),
            default  => $this->inappropriate_license( ),
        };  
    }
}