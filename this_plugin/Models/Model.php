<?php namespace ThisPlugin\Models;
use App\Support\WP_DbWrapper;
use wpdb;

class Model extends WP_DbWrapper  {
   

    protected array $result;

    public function __construct( wpdb $wpdb ) {
        parent::__construct( $wpdb );
        $this->result = ['ok' => true, 'message' => '', 'data' => [ ], 'status' => 200 ];
    }


}