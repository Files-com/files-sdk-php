<?php

namespace Files\Exception;

use Files\Logger;

class ApiException extends FilesException
{
    private $errorData = null;

    public function __construct($message, $code, $errorData)
    {
        Logger::debug(get_called_class() . ' > ' . $message . ' (code: ' . $code . ')');

        parent::__construct($message, $code);
        $this->errorData = $errorData;
    }

    public function getDetail()
    {
        return $this->errorData->detail;
    }

    public function getError()
    {
        return $this->errorData->error;
    }

    public function getErrors()
    {
        return $this->errorData->errors;
    }

    public function getHttpCode()
    {
        return $this->errorData->{'http-code'};
    }

    public function instance()
    {
        return $this->errorData->instance;
    }

    public function modelErrors()
    {
        return $this->errorData->{'model_errors'};
    }

    public function getTitle()
    {
        return $this->errorData->title;
    }

    public function getType()
    {
        return $this->errorData->type;
    }

    public function getData()
    {
        return $this->errorData->data;
    }
}
