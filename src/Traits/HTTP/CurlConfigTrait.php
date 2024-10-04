<?php

namespace App\Traits\HTTP;

trait CurlConfigTrait
{
    protected array $curlOptions = [];

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

    protected function initializeCurl(): \CurlHandle|false
    {
        $ch = curl_init();
        curl_setopt_array($ch, $this->curlOptions);
        return $ch;
    }
}