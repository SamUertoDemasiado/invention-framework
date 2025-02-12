<?php


namespace OSN\Framework\Core;


use OSN\Envoy\Exception;
use OSN\Envoy\ParseENV;
use OSN\Framework\Exceptions\HTTPException;
use OSN\Framework\Http\Request;
use OSN\Framework\Http\Response;
use OSN\Framework\View\View;

/**
 * Class App
 * @package App\Core
 */
class App
{
    use Initializable;

    public Config $config;

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
        $this->env = (new ParseENV())->parseFile($rootpath . "/.env");
        self::$app = $this;
        $this->config = new Config($rootpath . '/' . $this->env['CONF_DIR']);
        $this->config->root_dir = $rootpath;
        self::$app = $this;
        $this->loadInitializers();
        $this->preinit();

        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session();
        $this->db = new Database($this->env);
        self::$app = $this;
        $this->init();
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
            $this->afterinit();
            $output = $this->router->resolve();
            ($this->response)();
            echo $output;
        }
        catch (HTTPException $e) {
            $this->response->setCode($e->getCode());
            $this->response->setStatusText($e->getMessage());
            $this->response->setHeadersParsed($e->getHeaders());
            ($this->response)();

            if (view_exists("errors." . $e->getCode()))
                echo new View("errors." . $e->getCode(), ["uri" => $this->request->baseURI, "method" => $this->request->method], 'layouts.error');
        }
        catch (\Throwable $e) {
            echo new View('errors.exception', [
                "exception" => $e
            ], null);
        }
    }
}
