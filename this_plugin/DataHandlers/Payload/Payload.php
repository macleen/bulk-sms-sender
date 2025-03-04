<?php declare ( strict_types=1 );

namespace ThisPlugin\DataHandlers\Payload;

use App\Http\Request\Request;

class Payload {

    public const HASHED_LOG_ENTRY_KEY_1 = 'data';
    public const HASHED_LOG_ENTRY_KEY_2 = 'meta_data';

    protected array $payload = [];

    public function __construct( protected Request $request ) {}

    public function set_required_fields( array $required_fields = [ ]) : self { 

        $this->payload = $required_fields;
        return $this;

    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function change_key_name( $old_key_name, $new_key_name  ): self {
        $this->request->change_key_name( $old_key_name, $new_key_name );
        return $this;
    }         
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function build_query( ): string {
        return http_build_query($this->payload);
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __set( string $property, mixed $value = null ): void {
        $this->payload[ $property ]= $value;
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __get( $property = null ): mixed {
        return $property === 'all'? $this->payload 
             : ( $this->payload[ $property ] ?? $this->request->{ $property });
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function all( ): array {
        return \array_merge( $this->request->all(), $this->payload );
    }     
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function fill_value_from_request( ) : self {
        foreach( $this->payload as $k => $v ) 
                 $this->{ $k } = $this->request->{ $k };
        
        return $this;
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function forget( ?array $keys ): void {
        if ( $keys )
            $this->payload = array_diff( $this->payload, $keys );
    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __toString( ): string {
        return json_encode( $this->payload );
    }  
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function sanatize_for_storage( ){
        return [
            self::HASHED_LOG_ENTRY_KEY_1 => 
                    \array_intersect_key( 
                        $this->request->all( ), 
                        array_flip([
                            'dial_code_length',
                            'sending_target_country_code',
                            'full_country_name',
                            'language',
                            'recipient',
                            'message',
                            'email',
                            'full_name',
                            'generic_name',
                            'address',
                            'other_info',
                        ])),
            self::HASHED_LOG_ENTRY_KEY_2 => [],
        ];
    }
}