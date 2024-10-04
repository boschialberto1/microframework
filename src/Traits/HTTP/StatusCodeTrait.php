<?php

namespace App\Traits\HTTP;

trait StatusCodeTrait
{
    public const STATUS_OK = 200;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_INTERNAL_ERROR = 500;
}