<?php


namespace OSN\Framework\Http;


/**
 * Interface CustomRequestInterface
 * @package OSN\Framework\Http
 */
interface CustomRequestInterface
{
    /**
     * @return array
     */
    function rules(): array;

    /**
     * @return bool
     */
    function authorize(): bool;
}
