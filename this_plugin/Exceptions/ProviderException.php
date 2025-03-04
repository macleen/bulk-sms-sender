<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class ProviderException extends \Exception {

    protected $code = 400;
    protected $message = 'A provider exception has been thrown';    

}