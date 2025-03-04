<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class LinkLocatorNotFoundException extends \Exception {

    protected $code = 400;
    protected $message = 'An index was specified but the message is missing the __INDEX__ link locator/positioner';

}