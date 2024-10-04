<?php

namespace App\HTTP;

use App\Traits\HTTP\CurlConfigTrait;
use App\Traits\HTTP\CurlResponseTrait;
use App\Traits\HTTP\EnumReturnTypeTrait;
use Exception;

class Curl
{
    use CurlConfigTrait, CurlResponseTrait, EnumReturnTypeTrait;

    protected string $returnType = self::RETURN_JSON;

    public function setOption($option, $value): static
    {
        $this->curlOptions[$option] = $value;
        return $this;
    }

    public function setOptions(array $options): static
    {
        $this->curlOptions = $options + $this->curlOptions;
        return $this;
    }

    public function addHeader(string $name, string $value): static
    {
        $this->curlOptions[\CURLOPT_HTTPHEADER][] = "{$name}: {$value}";
        return $this;
    }

    public function setReturnType(string $type): static
    {
        $allowedTypes = [self::RETURN_JSON, self::RETURN_OBJECT, self::RETURN_ARRAY];
        if (!in_array($type, $allowedTypes, true)) {
            throw new \InvalidArgumentException("Invalid return type: {$type}");
        }
        $this->returnType = $type;
        return $this;
    }

    public function toJson(): static
    {
        return $this->setReturnType(self::RETURN_JSON);
    }

    public function toObject(): static
    {
        return $this->setReturnType(self::RETURN_OBJECT);
    }

    public function toArray(): static
    {
        return $this->setReturnType(self::RETURN_ARRAY);
    }

    /**
     * @throws Exception
     */
    public function get(string $url): mixed
    {
        $this->setOption(\CURLOPT_HTTPGET, true);
        return $this->execute($url);
    }

    /**
     * @throws Exception
     */
    public function post(string $url, array $data = []): mixed
    {
        $this->setOption(\CURLOPT_POST, true);
        $this->setOption(\CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->execute($url);
    }

    /**
     * @throws Exception
     */
    public function put(string $url, array $data = []): mixed
    {
        $this->setOption(\CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOption(\CURLOPT_POSTFIELDS, http_build_query($data));
        return $this->execute($url);
    }

    /**
     * @throws Exception
     */
    public function delete(string $url): mixed
    {
        $this->setOption(\CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->execute($url);
    }

    /**
     * Execute a cURL request.
     *
     * @param string $url
     * @return mixed
     * @throws Exception
     */
    public function execute(string $url): mixed
    {
        $this->setOption(\CURLOPT_URL, $url);
        $ch = $this->initializeCurl();
        $response = curl_exec($ch);
        $result = $this->handleResponse($response, $ch);
        curl_close($ch);

        return $this->formatResponse($result);
    }

    /**
     * @throws Exception
     */
    protected function formatResponse(array $result)
    {
        return match ($this->returnType) {
            self::RETURN_ARRAY => json_decode($result['body'], true),
            self::RETURN_OBJECT => json_decode($result['body']),
            self::RETURN_JSON => $result['body'],
            default => throw new Exception('Invalid return type'),
        };
    }
}