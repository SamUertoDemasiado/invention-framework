<?php


namespace OSN\Framework\Core;


use App\Http\Config;
use OSN\Envoy\Exception;
use OSN\Envoy\ParseENV;
use OSN\Framework\Exceptions\HTTPException;
use OSN\Framework\Http\Request;
use OSN\Framework\Http\Response;

/**
 * Class App
 * @package App\Core
 */
class App
{
    public array $config = [
        "root_dir" => ".",
        "layout" => "main"
    ];

    public static self $app;

    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;
    public Session $session;

    public array $env = [];

    /**
     * @throws Exception
     */
    public function __construct(string $rootpath = ".")
    {
        $this->config["root_dir"] = $rootpath;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->env = (new ParseENV())->parseFile($this->config["root_dir"] . "/.env");
        $this->db = new Database($this->env);
        self::$app = $this;
    }

    public static function session(): Session
    {
        return self::$app->session;
    }

    public static function db(): Database
    {
        return self::$app->db;
    }

    public static function request(): Request
    {
        return self::$app->request;
    }

    public static function response(): Response
    {
        return self::$app->response;
    }

    public static function config($key)
    {
        return self::$app->config[$key];
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        }
        catch (HTTPException $e) {
            $this->response->setStatus($e->getCode() . " " . $e->getMessage());
            echo new View("errors." . $e->getCode(), ["uri" => $this->request->baseURI, "method" => $this->request->method]);
        }
    }
}
