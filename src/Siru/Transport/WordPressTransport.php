<?php

namespace Siru\Transport;

use Siru\Exception\ApiException;
use Siru\Exception\TransferException;

class WordPressTransport implements TransportInterface
{

    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function request(array $fields, string $endPoint, string $method = 'GET') : array
    {

        if ($method === 'GET') {
            $query = http_build_query($fields);
            $url = $this->baseUrl . $endPoint . '?' . $query;
            $response = wp_remote_get($url);
        } else {
            $response = wp_remote_post($this->baseUrl . $endPoint, [
                'body' => wp_json_encode($fields),
                'headers'     => [
                    'Content-Type' => 'application/json',
                ]
            ]);
        }

        if (is_wp_error($response) === true) {
            throw new TransferException($response->get_error_message(), $response->get_error_code());
        }

        /** @var int|string $httpCode Http status code or empty string */
        $httpCode = wp_remote_retrieve_response_code($response);
        /** @var string $body */
        $body = wp_remote_retrieve_body($response);

        if (empty($httpCode) === true) {
            throw new TransferException();
        }

        if ($httpCode < 200 || $httpCode > 299) {
            throw ApiException::create($httpCode, $body);
        }

        return [
            $httpCode,
            $body
        ];
    }

}
