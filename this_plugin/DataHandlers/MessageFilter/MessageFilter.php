<?php declare ( strict_types=1 );

namespace ThisPlugin\DataHandlers\MessageFilter;

use Boot\PluginKernel;
use App\Support\HashedStorage;
use ThisPlugin\Boot\PluginActivation;
use ThisPlugin\DataHandlers\Payload\Payload;
use ThisPlugin\Exceptions\InvalidUriException;

class MessageFilter {

    protected ?HashedStorage $hashed_storage;


    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public function __construct( protected Payload &$payload ) { 
        $this->hashed_storage = PluginKernel::get_instance( )->has( HashedStorage::class )
                              ? PluginKernel::get_instance( )->get( HashedStorage::class ) : null;
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function get_url_injection_mode( ) : ?string { 
        return \in_array( $this->payload->{__MSG_URL_MODE__}, 
                [__MSG_URL_MODE_INDEX__,__MSG_URL_MODE_SHORTNER__]) 
             ?  $this->payload->{__MSG_URL_MODE__} : null; 

    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function validate_url( $url ): self {
        return \filter_var($url, FILTER_VALIDATE_URL)
            ? $this
            : throw new InvalidUriException( );
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function inject_url_in_message( string $url ): self {
        $this->validate_url( $this->payload->index )
             ->payload
             ->message = \str_replace(
                            '__INDEX__', 
                            $url,
                            $this->payload->message 
                        );
        return $this;
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function inject_index_in_message( ): Payload {


        $this->validate_url( $this->payload->index )
             ->inject_url_in_message( $this->payload->index );
        return $this->payload;     
    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    protected function inject_shortner_code_in_message( ): Payload {

        if ( $this->hashed_storage){
            $reference = $this->hashed_storage->write( $this->payload->sanatize_for_storage( ));        
            // $url       = home_url(get_option( 'shortner_page_name' )).'?'.$reference;
            $url       = home_url(PluginActivation::MAC_SHORTNER_PAGE).'?'.$reference;

            $this->validate_url( $url )  
                ->inject_url_in_message( $url );
        }        
        return $this->payload;

    }
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------
    public static function remodel_message( Payload &$payload ): Payload {

        $self = new static( $payload );
        return $self->hashed_storage 
             ? match( $self->get_url_injection_mode( )) {
                    __MSG_URL_MODE_INDEX__    => $self->inject_index_in_message( ),
                    __MSG_URL_MODE_SHORTNER__ => $self->inject_shortner_code_in_message( ),
                      default                 => $payload, 
               } : $payload;

    }    
    #------------------------------------------------------------------------------------------
    #
    #------------------------------------------------------------------------------------------

}