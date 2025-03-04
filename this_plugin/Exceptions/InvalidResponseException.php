<?php namespace ThisPlugin\Exceptions;


class InvalidResponseException extends \Exception {

    protected $code = 400;
    protected $message = 'Invalid response';
    
}