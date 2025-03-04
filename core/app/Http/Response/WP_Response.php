<?php namespace App\Http\Response;

use \WP_REST_Response;

class WP_Response {

    

/**
     * Create a WP_REST_Response object.
     *
     * @return WP_REST_Response
     */


    public function __construct( protected WP_REST_Response $wpResponse ) { }

    /**
     * Create a WP_REST_Response object.
     *
     * @param bool   $success  Whether the response is successful.
     * @param string $message  The response message.
     * @param array  $data     Optional response data.
     * @param int    $status   HTTP status code (default 200 for success, 400 for error).
     * @param array  $headers  Optional headers to include in the response.
     *
     * @return WP_REST_Response
     */
    public static function make(
                                   bool   $success, 
                                   string $message, 
                                   mixed  $data     = [], 
                                   int    $status   = 200, 
                                   array  $headers  = []
                                ): WP_REST_Response {

        return new WP_REST_Response([
            'success'       => $success,
            'message'       => $message,
            'data'          => $data,
            __STATUS_CODE__ => $status,
        ], $status, $headers);        

    }

    /**
     * Helper method to return a success response
     */
    public static function success( mixed $data = null, string $message = 'OK', array $headers = []): WP_REST_Response {
        return self::make(true,$message, $data, 200, $headers );
    }

    /**
     * Helper method to return an error response
     */
    public static function error(string $message, int $status = 400, mixed $data = null, array $headers = []): WP_REST_Response {
        return self::make( false, $message, $data, $status, $headers );
    }

    /**
     * 404 Not Found response
     */
    public static function notFound(string $message = 'Not Found', mixed $data = null, array $headers = []): WP_REST_Response {
        return self::error( $message, 404,  $data, $headers);
    }

    /**
     * 401 Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized', mixed $data = null, array $headers = []): WP_REST_Response {
        return self::error( $message, 401,  $data, $headers);
    }

    /**
     * 500 Internal Server Error response
     */
    public static function serverError(string $message = 'Internal Server Error', mixed $data = null, array $headers = []): WP_REST_Response {
        return self::error( $message, 500,  $data, $headers);
    }


    public static function exception( \Throwable $e ): WP_REST_Response {
        $status_code = $e->getCode( ); 
        return self::error( $e->getMessage(), $status_code ?: 400 );
    }


    public static function get_success_flag( WP_Rest_Response $response ): bool {
        return !$response->is_error();
    }
}