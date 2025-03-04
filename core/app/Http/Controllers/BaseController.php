<?php namespace App\Http\Controllers;

use App\Services\ErrorHanlder;

class BaseController {
    
    public function __construct(  ) { }

    protected function install_error_handler( ) {
        if ( config('app.env') == __ENV_IS_PRODUCTION__ )
             ErrorHanlder::install( );
    }
    protected function restore_error_handler( ) {
        if ( config('app.env') == __ENV_IS_PRODUCTION__ )
             ErrorHanlder::restore( );
    }


}