<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class NotFoundException extends \Exception {

    protected $code = 404;
    protected $message = 'Requested resource was not found';
    
}