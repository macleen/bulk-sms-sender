<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class InvalidValueException extends \Exception {

    protected $code = 400;
    protected $message = 'An iput invalid value was encountered';

}