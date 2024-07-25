<?php
declare(strict_types=1);

namespace Moon;

use Moon\Exception\HttpException;
use Moon\Facades\C;

enum RouteMatchMode {
    case EQUAL;
    case REGEX;
}

enum RouteNodeType {
    case GROUP;
    case ROUTE;
}

class Router {
    const GET = "GET";
    const POST = "POST";

    private array $routes = [];
    private array $routesRegex = ['GET' => [], 'POST' => []];
    private array $routeGroups = ['GET' => [], 'POST' => []];

    public function instance() {
        return $this;
    }
    
    public function get(string $uri, string $action): Router {
        $pathInfo = $this->getPath($uri);
        if ($pathInfo['type'] == RouteMatchMode::EQUAL) {
            $this->routes[self::GET][$pathInfo['path']] = $action;
        } elseif ($pathInfo['type'] == RouteMatchMode::REGEX) {
            $this->routesRegex[self::GET][] = [
                'regex' => $pathInfo['path'],
                'params' => $pathInfo['params'],
                'action' => $action,
            ];
        }

        return $this;
    }

    public function post(string $uri, string $action): Router {
        $path = $this->getPath($uri);
        $this->routes[self::POST][$path] = $action;

        return $this;
    }

    public function any(string $uri, string $action): Router {
        $path = $this->getPath($uri);
        $this->routes[self::GET][$path] = $action;
        $this->routes[self::POST][$path] = $action;

        return $this;
    }

    public function group(array $attrs, callable $callback): Router
    {
        array_push($this->routeGroups, $attrs);
        $callback();
        array_pop($this->routeGroups);

        return $this;
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function dispatch() {
        $machedResult = $this->matchRequest();
        $actionInfo = $this->parseAction($machedResult->action);
        $class = $actionInfo['class'];
        $method = $actionInfo['method'];

        if (!method_exists($class, $method)) {
            throw new HttpException();
        }

        $httpRequest = new HttpRequest($class, $method);
        $httpRequest->setParams($machedResult->params);
        $controller = new $class($httpRequest);
        $controller->$method($httpRequest);
    }

    private function getPath(string $uri) {
        $uri = ltrim($uri, '/');
        $path = '/';
        // if (!empty($this->routeGroups)) {
        //     $path .= implode('/', array_column($this->routeGroups, 'prefix')) . '/';
        // }
        $path .= $uri;
        if (strpos($path, '{') !== false) {
            $ret['type'] = RouteMatchMode::REGEX;
            $ret['path'] = preg_replace('/\{(.*?)\}/', '([a-zA-Z0-9\-_]+?)', $path);
            $ret['path'] = str_replace('/', '\\/', $ret['path']);
            $isMatched = preg_match_all('/\{(.*?)\}/', $path, $matches);
            if (!$isMatched) {
                throw new \Exception("Route Rule Error: {$uri}", 1);
            }
            $ret['params'] = $matches[1];
        } else {
            $ret['type'] = RouteMatchMode::EQUAL;
            $ret['path'] = $path;
        }

        return $ret;
    }

    private function matchRequest() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $tmp = parse_url($requestUri);
        $requestPath = $tmp['path'] ?? '';
        
        // 如果有完全匹配的，就直接返回
        if (!empty($this->routes[$requestMethod][$requestPath])) {
            return new MatchedRequestResult($this->routes[$requestMethod][$requestPath]);
        }

        // 如果有正则路由，则进行匹配
        foreach ($this->routesRegex[$requestMethod] as $route) {
            if (preg_match('/^' . $route['regex'] . '$/', $requestPath, $matches)) {
                $params = [];
                foreach ($route['params'] as $key => $value) {
                    $params[$value] = $matches[$key + 1];
                }
                return new MatchedRequestResult($route['action'], $params);
            }
        }
        
        throw new HttpException(code:404);
    }

    private function parseAction($action) {
        $result = [
            'class' => 'App\Http\IndexController', 
            'method' => 'index'
        ];

        $tmp = explode("@", $action);
        if (empty($tmp)) {
            return $result;
        }
        if (isset($tmp[0])) {
            $result['class'] = $tmp[0];
        }
        if (isset($tmp[1])) {
            $result['method'] = $tmp[1];
        }

        return $result;
    }
}

class MatchedRequestResult {
    public string $action;
    public array $params;
    public function __construct(string $action, array $params = []) {
        $this->action = $action;
        $this->params = $params;
    }
}
