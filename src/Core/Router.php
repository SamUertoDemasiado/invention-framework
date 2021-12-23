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

class Router
{
    use HTTPMethodRouterHelper;
    use HTTPMethodControllerHelper;

    protected array $routes = [];
    protected Request $request;
    protected Response $response;

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

    /**
     * @throws HTTPException|FileNotFoundException
     */
    public function resolve()
    {
        $path = $this->request->baseURI;
        $method = $this->request->method;

        $callback = $this->routes[$method][$path] ?? false;

        /**
         *  @bug If there is a different route with the request method,
         *  it returns 404 instead of 405. [SOLVED]
         */
        if ($method !== 'HEAD' && !$this->hasRoute($path, $method) && $this->hasRoute($path)) {
            throw new HTTPException(405);
        }

        if ($method === 'HEAD') {
            return '';
        }

        if ($callback === false) {
            throw new HTTPException(404);
        }

        if (is_string($callback)) {
            $callback = new View($callback);
        }

        if (is_array($callback)) {
            /** @var string[]|Controller[] */
            $callback[0] = new $callback[0]();
            $callback[1] = $callback[1] ?? 'index';
            $globals = [];

            $kernel = new Config();
            $globalMiddlewares = $kernel->globalMiddlewares;

            foreach ($globalMiddlewares as $globalMiddleware) {
                $globals[] = new $globalMiddleware();
            }

            $middlewares = array_merge($globals, $callback[0]->getMiddleware());

            $userMiddlewareMethods = $callback[0]->getMiddlewareMethods();

            foreach ($middlewares as $middleware) {
                if ((!in_array($middleware, $globals) && ((!empty($userMiddlewareMethods) && in_array($callback[1], $userMiddlewareMethods)) || empty($userMiddlewareMethods))) || in_array($middleware, $globals)) {
                    $middlewareResponse = $middleware->handle(App::$app->request);

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

                    if (method_exists($request, 'handleInvalid')) {
                        $request->handleInvalid();
                    }
                }
            }
        }

        return call_user_func_array($callback, [$request ?? App::request()]);
    }
}
