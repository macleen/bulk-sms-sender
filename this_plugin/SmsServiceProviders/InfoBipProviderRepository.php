<?php namespace ThisPlugin\SmsServiceProviders;


#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------
use \WP_REST_Response;
use App\Support\Tools;
use App\Http\Response\WP_Response;

#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class InfoBipProviderRepository extends ProviderRepository {


    protected string $provider = 'InfoBip';    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_local_fields( ): self {

        $this->balance_url      = '/account/1/balance';
        $this->account_info_url = '/account/1/total-balance';
        $this->sending_url      = '/sms/3/messages';


        $this->payload->set_required_fields([                              
                'apikey'    => '',
                'base_url'  => '',
                'message'   => '',
                'recipient' => '',
                'sender_id' => '',
        ])->fill_value_from_request( );        

        return $this;
    } 
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_provider_fields( ): void {

        $this->set_local_fields( )
             ->provider_config
             ->set_input_field( 'apikey', help:'Login then goto https://www.infobip.com/docs/api to grab your api key' )
             ->set_input_field( 'base_url', help:'Login then goto https://www.infobip.com/docs/api to grab your base url - format: https://xxxxxx.api.infobip.com' )
             ->set_add_on_flag( 'ping_balance_available' )
             ->set_add_on_flag( 'account_info_available' )
             ->set_add_on_flag( 'supports_sender_id' )
             ->set_global_info( 'Use your API_Key and BaseURL in the apikey and password fields. '.
                                'This provider supports unicode messages and alpha-numerical sender');
    } 
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function get_request_body( ): string {
        return json_encode([
            'messages' => [
                'sender' => $this->payload->sender_id,
                'destinations' => [
                    'to'=> $this->payload->recipient,
                ],
                'content' => [
                    'text' => $this->payload->message
                ],
            ]]);    
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function destructure_balance_content( array $response ): string {

        $data     = $response[__BODY__];
        $currency = $data->currency ?? '';
        $b        = ( string ) (( float ) $data->balance );
        $balance  = number_format($b , 2, '.', '').' ';
        $balance  = "$balance$currency";

        return $balance;
    }         
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function destructure_account_info( $response ) {

        $data = $response[__BODY__];
        return [
            'balance'           => $data->balance.' '.$data->currency->code ?? __UNSET__,
            'free emails'       => $data->freeMessages->email         ?? __UNSET__,
            'free hlr'          => $data->freeMessages->hlr           ?? __UNSET__,
            'free viber'        => $data->freeMessages->viber         ?? __UNSET__,
            'free viber-mo'     => $data->freeMessages->viber_mo      ?? __UNSET__,
            'free voice'        => $data->freeMessages->voice         ?? __UNSET__,
            'free calls'        => $data->freeMessages->voice_calls   ?? __UNSET__,
            'free voice inbound'=> $data->freeMessages->voice_inbound ?? __UNSET__,
        ];
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function parse_response( bool $success, int $status_code, mixed $response_body ): array {

        if ( !$success ) {
              if ( isset( $response_body->requestError )) {
                   $error_id  = $response_body->requestError->serviceException->messageId ?? 'Error';
                   $body      = $response_body->requestError->serviceException->text ?? 'Unknown Error';
                   $message   = "$error_id:$body"; 
               } elseif ( isset( $response_body->errorCode )) {    
                   $message    = $response_body->description;
               }    
               return [
                        __SUCCESS__     => false,
                        __STATUS_CODE__ => $status_code ?? 400,
                        __MESSAGE__     => $message,
                        __BODY__        => null,
                ];
        }
        return [
            __MESSAGE__     => '',
            __SUCCESS__     => $success,
            __STATUS_CODE__ => $status_code,
            __BODY__        => $response_body,
        ];

    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function contact_server( $enpoint, mixed $data = null ): array {
        $headers          = [
                            'Authorization' => 'App '.$this->payload->apikey,
                            'Content-Type'  => 'application/json',
                            'Accept'        => 'application/json'
                            ];

        return $this->http->send_package( $this->payload->base_url.$enpoint, $data, $headers,  [$this, 'parse_response']);
    }   
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_balance( ): string {
        $response = $this->contact_server( $this->balance_url );
        return $response[ __SUCCESS__ ]
             ? $this->destructure_balance_content( $response ) : '';
    }  
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_account_info( ): WP_REST_Response {

        $response = $this->contact_server( $this->account_info_url );
        return $response[ __SUCCESS__ ]
             ? WP_Response::success( $this->destructure_account_info( $response ))
             : WP_Response::error( $response[__MESSAGE__], $response[__STATUS_CODE__]);
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function send( ): WP_REST_Response {
        $response  = $this->contact_server( $this->sending_url, $this->get_request_body( ));
        $res       = $this->prepare_output_package( $response );
        return $res[__SUCCESS__]
             ? $this->format_response( $res[__STATUS_CODE__], $res )
             : $this->format_response( $res[__STATUS_CODE__], $res[__DESCRIPTION__]);

    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function prepare_output_package( array $response ) : array {

        if ( !$response[__SUCCESS__] ) {
             return [
                __SUCCESS__     => false,
                __DESCRIPTION__ => $response[__MESSAGE__],
                __STATUS_CODE__ => $response[__STATUS_CODE__],
             ];
        } else {
            $body                 = $response[__BODY__];  
            $success_codes        = [2,3,5,7,26];
            $smsResponse          = $body->messages[0];
            $status               = $smsResponse->status;
            $msgID                = $smsResponse->messageId;
            $description          = $msgID.': '.$status->name.': '.$status->description;
            $status_id            = $status->id;

            return [
                    __SUCCESS__     => true,
                    __RESULT__      => in_array( $status_id, $success_codes ), 
                    __DESCRIPTION__ => $description,
                    __LEAD__        => $this->payload->recipient,
                    __MESSAGE__     => $this->payload->message,
                    __STATUS_CODE__ => 200,
            ];
        }
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    function parse_response_body( int $statusCode, mixed $response_body ) : array{
        return $response_body;
    }      
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
}