<?php
namespace plokko\MsGraph\Exceptions;

class InvalidAuthState extends LoginException{
    public function __construct($message = 'Invalid auth state', $description = 'The provided auth state did not match the expected value', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $description, $code, $previous);
    }
}
