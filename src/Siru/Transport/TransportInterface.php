<?php

namespace Siru\Transport;

use Siru\Exception\ApiException;
use Siru\Exception\TransferException;

/**
 * Transport interface allows sending HTTP request to given end point and it returns the API response.
 */
interface TransportInterface
{

    /**
     * Set the base URL for all HTTP requests.
     *
     * @param string $baseUrl
     * @return mixed
     */
    public function setBaseUrl(string $baseUrl);

    /**
     * Sends request to Siru payment API using available transport mechanism.
     *
     * @param array $fields
     * @param string $endPoint
     * @param string $method
     * @return array Index 0 is HTTP status code as integer and index 1 is the response body as string
     * @throws ApiException If API responds with HTTP error
     * @throws TransferException If connection to API fails
     */
    public function request(array $fields, string $endPoint, string $method = 'GET') : array;

}
