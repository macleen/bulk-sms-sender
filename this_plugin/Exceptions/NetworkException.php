<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class NetworkException extends \Exception {

    protected $code = 400;
    protected $message = 'A network error/exception was encountered';
}