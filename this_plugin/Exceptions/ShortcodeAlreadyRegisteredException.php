<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class ShortcodeAlreadyRegisteredException extends \Exception {

    protected $code = 500;
    protected $message = 'Shortcode already exists.';

}