<?php
namespace plokko\MsGraph\Exceptions;

class LoginException extends \Exception{
    public $description=null;
    public function __construct($message = "", $description=null,$code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->description = $description;
    }
}
