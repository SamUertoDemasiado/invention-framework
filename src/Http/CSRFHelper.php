<?php


namespace OSN\Framework\Http;


use OSN\Framework\Core\App;

trait CSRFHelper
{
    public function generate(): string
    {
        return sha1(rand());
    }

    public function get()
    {
        return App::$app->session->get("__csrf_token");
    }

    public function set($token)
    {
        App::$app->session->set("__csrf_token", $token);
    }

    public function endCSRF()
    {
        App::$app->session->unset("__csrf_token");
    }

    public function new(): string
    {
        $token = $this->generate();
        $this->set($token);
        return $token;
    }
}
