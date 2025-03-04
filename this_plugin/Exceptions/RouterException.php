<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class RouterException extends \Exception {

    protected $code = 400;
    protected $message = 'Routing exception, check htaccess for correct redirections';
    
}