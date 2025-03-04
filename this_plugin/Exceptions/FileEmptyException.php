<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class FileEmptyException extends \Exception {

    protected $code = 400;
    protected $message = 'The requested file exists but does not hold any content';
    
}