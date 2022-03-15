<?php

namespace StarkInfra\Error;
use \Exception;

class StarkError extends Exception {}

class ErrorElement extends StarkError
{
    public function __construct($code, $message)
    {
        parent::__construct($code . ": " . $message, 0, null);

        $this->errorCode = $code;
        $this->errorMessage = $message;
    }
}

class InputErrors extends StarkError
{
    public function __construct($content)
    {
        parent::__construct(json_encode($content), 0, null);

        $errors = [];
        foreach ($content as $error){ 
            $errors[] = new ErrorElement($error["code"], $error["message"]); 
        } 
        $this->errors = $errors;
    }
}

class InternalServerError extends StarkError
{
    public function __construct($message = "Houston, we have a problem.")
    {
        parent::__construct($message, 0, null);
    }
}

class UnknownError extends StarkError
{
    public function __construct($message)
    {
        parent::__construct("Unknown exception encountered: " . $message, 0, null);
    }
}

class InvalidSignatureError extends StarkError
{
    public function __construct($message)
    {
        parent::__construct($message, 0, null);
    }
}

?>
