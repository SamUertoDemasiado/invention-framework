<?php


namespace OSN\Framework\Http;

trait HTTPMethodControllerHelper
{
    protected array $apiHandlers = [
        "get" => ["index", "view"],
        "post" => "store",
        "put" => "update",
        "patch" => "update",
        "delete" => "delete",
    ];

    protected array $webHandlers = [
        "get" => "index",
        "post" => "create",
    ];

    public function assignAPIController(string $route, string $controller, ?array $handlers = null)
    {
        if (class_exists($controller)) {
            if ($handlers !== null) {
                $this->apiHandlers = $handlers;
            }

            $this->get($route, [$controller, $this->apiHandlers['get'][0]]);
            $this->get($route . "/view", [$controller, $this->apiHandlers['get'][1]]);
            $this->post($route, [$controller, $this->apiHandlers['post']]);
            $this->put($route, [$controller, $this->apiHandlers['put']]);
            $this->patch($route, [$controller, $this->apiHandlers['patch']]);
            $this->delete($route, [$controller, $this->apiHandlers['delete']]);
        }
    }

    public function assignWebController(string $route, string $controller, ?array $handlers = null)
    {
        if (class_exists($controller)) {
            if ($handlers !== null) {
                $this->webHandlers = $handlers;
            }

            $this->get($route, [$controller, $this->webHandlers['get']]);
            $this->post($route, [$controller, $this->webHandlers['post']]);
        }
    }
}
