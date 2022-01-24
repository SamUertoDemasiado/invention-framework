<?php


namespace OSN\Framework\Core;

use App\Http\Config;
use OSN\Framework\Exceptions\FileNotFoundException;
use OSN\Framework\Exceptions\HTTPException;
use OSN\Framework\Facades\FunctionUtils;
use OSN\Framework\Http\HTTPMethodControllerHelper;
use OSN\Framework\Http\HTTPMethodRouterHelper;
use OSN\Framework\Http\Request;
use OSN\Framework\Http\Response;
use OSN\Framework\Facades\Response as ResponseFacade;
use OSN\Framework\Routing\Route;
use OSN\Framework\View\View;
use stdClass;

class Router
{
    use HTTPMethodRouterHelper;
    use HTTPMethodControllerHelper;

    /**
     * @var Route[] $routes
     */
    protected array $routes = [];

    public Request $request;
    public Response $response;

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * @throws HTTPException|FileNotFoundException
     */
    public function resolve()
    {
        $path = $this->request->baseURI;
        $method = $this->request->method;

        $route = $this->findRoute($path, $method);
        $anyRoute = $this->findRoute($path);

        if ($method !== 'HEAD' && $route == false && $anyRoute != false) {
            throw new HTTPException(405);
        }


        if ($method === 'HEAD' && $anyRoute != false) {
            return '';
        }

        if ($route === false) {
            throw new HTTPException(404);
        }

        $callback = $route->action();

        if (is_string($callback)) {
            $callback = new View($callback);
        }

        if (is_array($callback)) {
            /** @var string[]|Controller[] */
            $callback[0] = new $callback[0]();
            $callback[1] = $callback[1] ?? 'index';
            $globals = [];

            $globalMiddlewares = Config::$globalMiddlewares;

            foreach ($globalMiddlewares as $globalMiddleware) {
                $globals[] = new $globalMiddleware();
            }

            $middleware = array_merge($globals, $route->middleware(), $callback[0]->getMiddleware());

            $userMiddlewareMethods = $callback[0]->getMiddlewareMethods();

            foreach ($middleware as $middleware) {
                if ((!in_array($middleware, $globals) && ((!empty($userMiddlewareMethods) && in_array($callback[1], $userMiddlewareMethods)) || empty($userMiddlewareMethods))) || in_array($middleware, $globals)) {
                    $middlewareResponse = $middleware->execute(App::$app->request);

                    if($middlewareResponse === true || $middlewareResponse === null){
                        continue;
                    }

                    ResponseFacade::setCode($middlewareResponse instanceof Response ? $middlewareResponse->getCode() : 200);

                    return $middlewareResponse;
                }
            }

            $params = FunctionUtils::getParameterTypes(...$callback);

            if (isset($params[0])) {
                $request = new $params[0]();

                if ($request instanceof Request) {
                    if (!$request->authorize()) {
                        throw new HTTPException(403, "Forbidden");
                    }

                    if ($request->autoValidate && !$request->validate()) {
                        if (method_exists($request, 'handleInvalid')) {
                            $request->handleInvalid();
                        }
                        else {
                            if ($this->request->header('Referer')) {
                                $this->response->setCode(406);
                                $this->response->redirect($this->request->header('Referer'));
                                return '';
                            }
                            else {
                                throw new HTTPException(406);
                            }
                        }
                    }
                }
            }
        }

        $output = call_user_func_array($callback, [$request ?? App::request()]);

        if (is_jsonable($output) && !($output instanceof Response || $output instanceof View)) {
            $this->response->header("Content-Type", "application/json");
            return json_encode($output, env('APP_ENV') != "production" ? JSON_PRETTY_PRINT : 0);
        }

        return $output;
    }
}
