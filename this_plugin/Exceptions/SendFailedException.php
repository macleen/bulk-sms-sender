<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class SendFailedException extends \Exception {

    protected $code = 400;
    protected $message = 'The sending operation has failed';

}