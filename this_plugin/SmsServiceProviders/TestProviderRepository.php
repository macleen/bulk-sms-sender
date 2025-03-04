<?php namespace ThisPlugin\SmsServiceProviders;

use \WP_REST_Response;
use ThisPlugin\SmsServiceProviders\ProviderRepository;

#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class TestProviderRepository extends ProviderRepository {

    protected string $provider = 'Test';
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_provider_fields( ): void {

        $this->provider_config
             ->set_input_field( 'apikey', help: 'Enter a dummy key' )
             ->set_input_field( 'password', help: 'Enter a dummy password' )
             ->set_add_on_flag( 'ping_balance_available' )
             ->set_global_info( 'This plugin is constructed for test purposes only. It mimmicks successful and failed messages. '.
                                'The message ids are Unix time stamps of the sending moement in MD5 format');

        $this->payload->set_required_fields(['recipient'=>'', 'message' => ''])->fill_value_from_request( );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_balance( ): string {
        sleep(1);
        $balance = (strtotime("+1 hour") - time( )) / 10;
        return $balance - 0.77 - (rand(0, 20)) .' USD';
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function send( ): WP_REST_Response {

        sleep(1);
        $v       = rand(1, 100);
        $result  = $v <= 70;
        $error   = [__SMS_STATUS_CODE__=> $v * 10, __DESCRIPTION__=>'failed to send message'];
        $success = [__MSG_ID__=> md5(\microtime())];
        $res     = $result ? $success : $error;

        $res[__RESULT__]  = $result;
        
        return $this->format_response( 200, $this->parse_message_sending_body( $res ));

    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    private function parse_message_sending_body( mixed $send_response_body ) : array{

        $send_response_body[__DESCRIPTION__] = $send_response_body[__RESULT__] 
                                             ? 'Successfully Delivered - '.__MSG_ID__.': '.$send_response_body[__MSG_ID__] 
                                             : 'Failed: code: '.$send_response_body[__SMS_STATUS_CODE__].' - '.$send_response_body[__DESCRIPTION__];
        return $send_response_body;
    } 
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
}