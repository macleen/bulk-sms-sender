<?php namespace App\Http\Client;

use \Exception;
use \WP_REST_Response;
use App\Support\Tools;
use App\Http\Response\WP_Response;
use App\Http\Client\ClientRequest as Request;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class Http {

    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __construct( ) {
        Request::verifyPeer( config('app.env') == __ENV_IS_PRODUCTION__ );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function post( $url, $body = [], $headers = [], $user_name = null, $password = null ) : ?WP_REST_Response {
        return Request::post( $url, $headers, $body, $user_name, $password );
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get( $url, $body = [], $headers = [] ): ?WP_REST_Response {
        return Request::get( $url, $headers, $body );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function send_package( string $enpoint, mixed $data = null, array $headers = [ ], ?callable $callback = null ): array {

        $res = [__SUCCESS__ => true, __STATUS_CODE__ => 200, __MESSAGE__ => '', __BODY__ => null ];
        try {
            $resp = empty( $data )
                  ? $this->get( $enpoint, headers: $headers )
                  : $this->post( $enpoint, $data, headers: $headers );

            $status_code   = (int) $resp->get_status( );
            $success       = WP_Response::get_success_flag( $resp );
            $response_body = $resp->get_data( )['data'];            

            Tools::writeTolog( $resp, ' RESPONSE in HTTP SEND PACKAGE');
            if ( $status_code ){
                 if ( \is_callable( $callback )) { // let the provider plugin determin the ouput
                      return $callback( $success, $status_code, $response_body );
                 }
                 $res[__SUCCESS__]     = $success;
                 $res[__STATUS_CODE__] = $status_code;
                 $res[__BODY__]        = $response_body;

            } else throw new Exception('Invalid or no response returned', 400);

        } catch ( \Throwable $e){
            $status               = $e->getCode();
            $res[__SUCCESS__]     = false;
            $res[__STATUS_CODE__] = $status === 0 ? 400 : $status;
            $res[__MESSAGE__]     = $e->getMessage();           
        }
        return $res;
    }   
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
}