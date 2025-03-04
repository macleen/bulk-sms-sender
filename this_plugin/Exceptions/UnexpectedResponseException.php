<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class UnexpectedResponseException extends \Exception {

    protected $code = 400;
    protected $message = 'Received response was not expected';    

}