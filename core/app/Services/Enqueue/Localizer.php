<?php namespace App\Services\Enqueue;

use App\Support\Tools;
use ThisPlugin\Boot\Process;


class Localizer {


    public function __construct( protected Process $process ) { }


    private function is_valid_block( $enqueue_data ): bool {
        return $enqueue_data && 
                is_array( $enqueue_data ) && 
                isset( $enqueue_data[__SCRIPT_LOCALIZE_LTR__] ) &&
                is_array( $enqueue_data[__SCRIPT_LOCALIZE_LTR__] ) &&
                !empty( $enqueue_data[__SCRIPT_LOCALIZE_LTR__]);
    }


    /**
     * Replace placeholders with actual dynamic values
     */
    public function localize_script( ?array $enqueue_data = null ): void {
        if ( $this->is_valid_block( $enqueue_data )) {
             foreach ( $enqueue_data[__SCRIPT_LOCALIZE_LTR__] as $var_name => $data ) {
                       $localized_data = $this->get_dynamic_values( $data );
                       wp_localize_script($enqueue_data[__SCRIPT_HANDLE_LTR__], $var_name, $localized_data );
             }
        }
    }



    /**
     * Replace placeholders with actual dynamic values
     */
    protected function get_dynamic_values( array|string $entries ): array {
        $entries = is_string( $entries ) ? [$entries] : $entries;
        foreach( $entries as $key => $entry ) {
            $args = \is_array( $entry ) ? $entry : \explode( ',', $entry );
            $entries[ $key ] = $this->process->{ $key }( ...$args );    
        }
        return $entries;
    }


}