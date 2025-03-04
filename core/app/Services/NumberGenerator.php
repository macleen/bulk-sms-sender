<?php namespace App\Services;

use App\Support\Tools;
use Illuminate\Support\Str;

#-------------------------------------------------------------------------------------------
#                          N U M B E R     G E N E R A T O R
#                           by  MacLeen   v2.0.0-A01-2024
#  -----------------------------------------------------------------------------------------
#               Copyright (C) MacLeen / Start of work 11.09.2024 / 05:50
#-------------------------------------------------------------------------------------------

class NumberGenerator {

    #-------------------------------------
    const INTERVAL_TRANSLATION_TABLE = [
            [ 'interval' => [ '00', '09' ], 'symbol' => 'P' ],
            [ 'interval' => [ '10', '19' ], 'symbol' => 'Q' ],
            [ 'interval' => [ '20', '29' ], 'symbol' => 'R' ],
            [ 'interval' => [ '30', '39' ], 'symbol' => 'S' ],
            [ 'interval' => [ '40', '49' ], 'symbol' => 'T' ],
            [ 'interval' => [ '50', '59' ], 'symbol' => 'V' ],
            [ 'interval' => [ '60', '69' ], 'symbol' => 'W' ],
            [ 'interval' => [ '70', '79' ], 'symbol' => 'X' ],
            [ 'interval' => [ '80', '89' ], 'symbol' => 'Y' ],
            [ 'interval' => [ '90', '99' ], 'symbol' => 'Z' ],
    ];

    private string $trx_prefix;
    private string $order_prefix;    
    private string $ticket_number_prefix;
    private int $secret_code_length;
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function __construct( ) { 

        $config                     = config('number_generator');
        $this->trx_prefix           = $config['transaction']['prefix'] ?? '';
        $this->order_prefix         = $config['order']['prefix'] ?? '';
        $this->ticket_number_prefix = config('services.voip.ticket_number_prefix', 'SN');
        $this->secret_code_length   = $config['secret-code']['min-length'] ?? 6;

    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function get_range( int $length = 7 ) : array {
        return [
                 pow(10, $length - 1 ),
                 (int) str_pad( '', $length, '9', STR_PAD_RIGHT ),
        ];  
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    private function get_interval_key( int $v ) : int {

        foreach( self::INTERVAL_TRANSLATION_TABLE as $i => $range ) {
            if (( $v >= (int) $range['interval'][0] ) && ( $v <= (int) $range['interval'][1] ))
                  return $i;  
        }
        throw new \Exception('Error Generating transaction number: Value is out of range');
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    private function get_trx_group_id(  ) : string {

        $dt     = (int) \date( 'y' );   // last 2 digits of current year
        $doy    = (int) \date( 'z' );   // day of the year
        return self::INTERVAL_TRANSLATION_TABLE[ $this->get_interval_key( $dt )]['symbol'] . strtoupper(dechex( $doy));

    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    private function get_lower_group_id(  ): string {
        return \date('His');
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function get_tracking_nbr(  ): string {

        $last_rec_id   = 14236;       // last db transaction record id  ==> for testing only
        [ $msec, $_ ]  = explode(' ', microtime( ));
        $precision     = \round( $msec * 1000 );
        return ( int ) ( $last_rec_id + $this->get_lower_group_id( ) + $precision );
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function generate_user_account_number( ): string {
        sleep(1);   // to ensure unicity of account numbers
        return strtoupper(
                        config('number_generator.user_account.prefix').                        
                        dechex((int)Str::before( microtime(!0), '.'))
               );
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function generate_transaction_number( ): array {

        \usleep(100); // to insure unique trx id's
        return [
                 'group'    => $this->trx_prefix .
                               $this->get_trx_group_id(),                                             
                 'tracking' => $this->get_tracking_nbr()
        ]; 
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function generate_order_id( ): string {

        \usleep(100); // to insure unique trx id's
        return $this->order_prefix . $this->get_trx_group_id(). $this->get_tracking_nbr(); 
    }
        #--------------------------------------------------
    #
    #--------------------------------------------------
    public function generate_ticket_number( ): string {

        \usleep(100); // to insure unique trx id's
        return $this->ticket_number_prefix . $this->get_trx_group_id(). $this->get_tracking_nbr(); 
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    public function generate_transaction_tracking_number( ): string {

        \usleep(100); // to insure unique trx id's
        return $this->trx_prefix . $this->get_trx_group_id(). $this->get_tracking_nbr(); 
    }   
    #--------------------------------------------------
    #
    #--------------------------------------------------
    /**
     * @return int
     */
    public static function get_random_code( ?int $range_length = null, bool $for_html_use=false ) : int {

        $range = $range_length ?? 10000;   
            if ( $range <= 999 ) {    // to get at least a code length of 4
             throw new \Exception('Secret code generator: Unacceptable code length');
        }
        $log    = log( $range, 2 );        
        $bytes  = (int) ( $log / 8 ) + 1;       // Length in bytes.        
        $bits   = (int) $log + 1;               // Length in bits.        
        $filter = (int) ( 1 << $bits ) - 1;     // shift left bits.

        do {
            $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes )));            
            $rnd = $rnd & $filter;              // Discard irrelevant bits.
        } while ( $rnd >= $range );

        return $for_html_use ? (int) (rand(1, $range).$rnd) : $rnd;
    }
    #--------------------------------------------------
    #
    #--------------------------------------------------
    /**
     * @return int
     */
    public function get_secret_code( ) : int {
        [ $min, $max ] = $this->get_range( $this->secret_code_length );
        return  $min   + self::get_random_code( $max - $min );
    }

    public function get_phone_verification_code(  int $code_length = 4) : int {
        $code_length   = strlen($code_length) < 4 ? 4 : $code_length;
        [ $min, $max ] = $this->get_range( $code_length );

        return \rand( $min, $max );
    }
 
    
}