<?php
declare(strict_types=1);
namespace Moon;

use Moon\Facades\C;

class HttpRequest {
    public $requestClass = '';
    public $requestMethod = '';
    public $httpHost = '';
    public $httpPort = '80';
    public $httpScheme = 'http';
    public $params = [];
    
    function __construct($requestClass, $requestMethod) {
        $this->httpHost = $_SERVER['HTTP_HOST'];
        $this->httpPort = $_SERVER['SERVER_PORT'];
        $this->httpScheme = $_SERVER['REQUEST_SCHEME'];
        $this->requestClass = $requestClass;
        $this->requestMethod = $requestMethod;

        C::set('http/request_class', $requestClass);
        C::set('http/request_method', $requestMethod);
        C::set('http/host', $this->httpHost);
        C::set('http/port', $this->httpPort);
        C::set('http/scheme', $this->httpScheme);
    }

    public function getParam($key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    public function setParams($params) {
        $this->params = $params;
    }
}