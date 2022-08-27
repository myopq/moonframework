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
    private array $routeGroups = [];

    public function instance() {
        return $this;
    }
    
    public function get(string $uri, string $action): Router {
        $path = $this->getPath($uri);
        $this->routes[self::GET][$path] = $action;

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
        $action = $this->matchRequest();
        $actionInfo = $this->parseAction($action);
        $class = $actionInfo['class'];
        $method = $actionInfo['method'];

        if (!method_exists($class, $method)) {
            throw new HttpException();
        }

        $httpRequest = new HttpRequest($class, $method);
        $controller = new $class($httpRequest);
        $controller->$method($httpRequest);
    }

    private function getPath(string $uri) {
        $uri = ltrim($uri, '/');
        $path = '/';
        if (!empty($this->routeGroups)) {
            $path .= implode('/', array_column($this->routeGroups, 'prefix')) . '/';
        }
        $path .= $uri;

        return $path;
    }

    private function matchRequest() {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if (empty($this->routes[$requestMethod])) {
            throw new HttpException(code:404);
        }
        if (empty($this->routes[$requestMethod][$requestUri])) {
            throw new HttpException(code:404);
        }

        return $this->routes[$requestMethod][$requestUri];
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
