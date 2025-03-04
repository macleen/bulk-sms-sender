<?php namespace ThisPlugin\Exceptions;
#------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------

class UnauthorizedException extends \Exception {

    protected $code = 403;
    protected $message = 'Access denied, wrong or invvalid credentials';

}