<?php

namespace Files\Exception;

use Files\Logger;

/**
 * An error object returned by the Files.com API. Generated subclasses, grouped by the first part of the error's type,
 * identify specific errors; ApiException itself covers a type the SDK does not recognize. Each getter returns null
 * when the error object does not include that field.
 */
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
        return $this->field('detail');
    }

    public function getError()
    {
        return $this->field('error');
    }

    public function getErrors()
    {
        return $this->field('errors');
    }

    public function getHttpCode()
    {
        return $this->field('http-code');
    }

    public function instance()
    {
        return $this->field('instance');
    }

    public function modelErrors()
    {
        return $this->field('model_errors');
    }

    public function getTitle()
    {
        return $this->field('title');
    }

    public function getType()
    {
        return $this->field('type');
    }

    public function getData()
    {
        return $this->field('data');
    }

    private function field($name)
    {
        return is_object($this->errorData) && isset($this->errorData->{$name}) ? $this->errorData->{$name} : null;
    }
}
