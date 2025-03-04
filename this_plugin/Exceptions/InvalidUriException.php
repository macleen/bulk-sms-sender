<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class InvalidUriException extends \Exception {

    protected $code = 400;
    protected $message = 'Invalid or malformed [indexing] uri/url';

}