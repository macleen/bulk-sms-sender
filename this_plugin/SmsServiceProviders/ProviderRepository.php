<?php declare ( strict_types=1 );

namespace ThisPlugin\SmsServiceProviders;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

use WP_REST_Response;
use App\Support\Tools;
use Boot\PluginKernel;
use App\Http\Client\Http;
use App\Http\Request\Request;
use ThisPlugin\Support\SendLog;
use ThisPlugin\Support\DateTime;
use App\Http\Response\WP_Response;
use ThisPlugin\DataHandlers\Payload\Payload;
use ThisPlugin\Interfaces\ProviderRepositoryInterface;
use ThisPlugin\DataHandlers\MessageFilter\MessageFilter;
use ThisPlugin\DataHandlers\ProviderConfig\ProviderConfig;

#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

abstract class ProviderRepository implements ProviderRepositoryInterface {

    protected const LOG_ENTRY_KEY_1 = 'request';
    protected const LOG_ENTRY_KEY_2 = 'response';
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public Http $http;    
    public Payload $payload;
    public MessageFilter $message_filter;
    public ProviderConfig $provider_config;    
    protected string $provider = '';
    protected ?string $gateway;    
    protected ?string $sending_url;
    protected ?string $balance_url;
    protected ?string $reporting_url;    
    protected ?string $account_info_url;
    protected array   $headers = [];    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __construct( Request $request) { 

        $this->http            = PluginKernel::get_instance()->get( Http::class );
        $this->payload         = new Payload( $request );
        $this->provider_config = new ProviderConfig( $this->provider );

        DateTime::set_time_zone( 'Europe/Brussels' );
        $this->set_provider_fields( );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_provider_fields( ) : WP_REST_Response {
        return WP_Response::success( $this->provider_config->get_provider_fields( ));
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_balance( ): string {
        return '';
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_account_info( ): WP_REST_Response {
        return WP_Response::error('This provider does not offer account information via api requests', 404);
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_packet_format( ): WP_REST_Response { 
        return WP_Response::success( );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function prepare_to_send( ): void {
        $this->payload = MessageFilter::remodel_message( $this->payload );
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function format_response( int $status_code = 200, mixed $response_body = null ): WP_REST_Response {

        if ( $status_code < 400 ) {
             if ( $response_body ) {
                  $bl                            = $this->provider_config->{'add_ons'}[__PING_BALANCE_AVAILABLE__];
                  $success                       = (bool) $response_body[__RESULT__];
                  $class                         = $success ? __RESPONSE_SUCCESS_TAG__ : __RESPONSE_ERROR_TAG__;

                  $response_body[__BALANCE__]    = $bl && !isset($res[__BALANCE__]) ? $this->get_balance( ) : '';
                  $response_body[__DESCRIPTION__]= \sprintf( $class, $response_body[__DESCRIPTION__]);
                  $response_body[__LEAD__]       = $this->payload->recipient ?? '';
                  $response_body[__MESSAGE__]    = $this->payload->message ?? '';
                  $response_body[__PROVIDER__]   = $this->provider;

                  if ( get_option( 'keep_logs'))
                       SendLog::write( [
                                                self::LOG_ENTRY_KEY_1 => $this->payload->all( ), 
                                                self::LOG_ENTRY_KEY_2 => $response_body 
                                            ], $success 
                                );
                  return WP_Response::success( $response_body );

             } else return WP_Response::error( 'Received an empty response block', 400 );
        } else {    
             $message =    empty( $response_body ) ? 'Invalid response packet'
                      : ( \is_string( $response_body ) ? $response_body : \json_encode( $response_body, JSON_PRETTY_PRINT ));
             return WP_Response::error( $message, $status_code );
        }

    }  
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function trigger_send(): WP_REST_Response {
        $this->prepare_to_send();
        return $this->send( );
    }  
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function parse_response_body(int $statusCode, mixed $response_body): array {
        return [];
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------

    abstract public function send( ) : WP_REST_Response;
    abstract public function set_provider_fields( ) : void;

  

}