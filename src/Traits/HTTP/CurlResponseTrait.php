<?php

namespace App\Traits\HTTP;

use Exception;

trait CurlResponseTrait
{

    /**
     * @throws Exception
     */
    protected function handleResponse($response, $ch): array
    {
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return ['code' => $httpCode, 'body' => $response];
    }
}