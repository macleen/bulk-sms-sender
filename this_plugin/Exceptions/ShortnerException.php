<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class ShortnerException extends \Exception {

    protected $code = 400;
    protected $message = 'Shortner exception returned, most probably the Shortner server is not responding or the input was invalid';

}