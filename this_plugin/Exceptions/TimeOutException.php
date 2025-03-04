<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class TimeOutException extends \Exception {

    protected $code = 400;
    protected $message = 'The operation has timed out';

}