<?php


namespace OSN\Framework\Http;

use OSN\Framework\Exceptions\PropertyNotFoundException;
use OSN\Framework\Http\RequestValidator;

class Request extends AbstractRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
