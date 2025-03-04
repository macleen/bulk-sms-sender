<?php declare ( strict_types=1 );

namespace ThisPlugin\DataHandlers\ProviderConfig;

#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class ProviderConfig {

    protected array $provider_config = [];
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __construct( string $provider = 'Test' ) { 

        $this->provider_config = [
            'provider'      => $provider,
            'input_fields'  =>[],
            'global_info'   => [
                'help_text' => '',
            ],
            'add_ons'       => [
                'account_info_available' => false,
                'ping_balance_available' => false,
                'live_log_available'     => false,
                'supports_sender_id'     => true,
            ]
        ];

    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function get_provider_fields(  ): array {

        return $this->provider_config;

    } 
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_input_field( string $entry, string $value = '', string $help = '', ?array $options = null ): self {

        $this->provider_config['input_fields'][ $entry ] = [ 
              'value' => $value,
              'help'  => $help,
        ];
        if ( $options )
             $this->provider_config['input_fields'][ $entry ]['options'] = $options; 

        return $this;
    }             
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_global_info( string $help = '' ): self {

        $this->provider_config['global_info'] = [ 
              'help_text' => $help
        ];
        return $this;
    }             
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_add_on_flag( string $flag, $value = true ): self {

        $this->provider_config['add_ons'][$flag] = $value;
        return $this;

    }             
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function set_input_fields( array $input_fields = [ ]): self {

        $this->provider_config['input_fields'] = $input_fields;
        return $this;

    }             
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __set( string $property, mixed $value = null ): void {

        if ( isset( $this->provider_config[ $property ])) {
             $this->{ $property } = $value;
        }

    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __get( $property = null ): mixed {

        return $this->provider_config[ $property ] ?: null;

    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
}