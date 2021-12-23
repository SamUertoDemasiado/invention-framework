<?php


namespace OSN\Framework\Http;


use OSN\Framework\Core\App;

trait CSRFHelper
{
    public static function generate(): string
    {
        return sha1(rand());
    }

    public static function get()
    {
        return App::$app->session->get("__csrf_token");
    }

    public static function endCSRF()
    {
        App::$app->session->unset("__csrf_token");
    }
}
