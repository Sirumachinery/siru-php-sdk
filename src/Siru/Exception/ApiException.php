<?php
namespace Siru\Exception;

/**
 * This exception is thrown when API responds with an error message.
 * In case of Payment API, there can be multiple error messages returned at once. You can retrieve list
 * of these using getErrorStack().
 */
class ApiException extends AbstractApiException
{
    
    private $errorStack = [];

    public static function create(?int $httpStatus, string $body) : ApiException
    {
        $json = json_decode($body, true);

        $message = 'API request failed.';
        if (isset($json['error'])) {
            if (is_string($json['error']) === true) {
                $message = $json['error'];
            } elseif (is_array($json['error']) === true && isset($json['error']['message']) === true ) {
                $message = $json['error']['message'];
            }
        }

        if(isset($json['errors'])) {
            $errorStack = $json['errors'];
        } else {
            $errorStack = [];
        }

        $exception = new self($message, $httpStatus ?: 0, null, $body);
        $exception->errorStack = $errorStack;
        return $exception;
    }

    /**
     * Returns all error messages received from API.
     * 
     * @return array
     */
    public function getErrorStack() : array
    {
        return $this->errorStack;
    }

}
