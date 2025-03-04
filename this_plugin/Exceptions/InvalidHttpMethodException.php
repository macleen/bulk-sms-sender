<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class InvalidHttpMethodException extends \Exception {

    protected $code = 405;
    protected $message = 'Invalid or unsupported Http method';

}