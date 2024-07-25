<?php
declare(strict_types=1);
namespace Moon;

use Moon\Exception\HttpException;
use Moon\Facades\C;

class HttpController {
    protected HttpRequest $httpRequest;
    protected Array $t_var = [];

    public function __construct(HttpRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    protected function assign($name,$val = false) {
		if(is_array($name)) {
			$this->t_var = array_merge($this->t_var,$name);
		}else{
			$this->t_var[$name] = $val;
		}
	}
    
    function display(string $tplFile = '') {
        // $themes = C::get('view/themes');
        extract($this->t_var, EXTR_OVERWRITE);
        if (!$tplFile) {
            $actionClass = explode('\\', $this->httpRequest->requestClass);
            $callClass = strtolower(str_replace('Controller', '', end($actionClass)));
            $tplFile = $callClass . '/' . strtolower($this->httpRequest->requestMethod);
        }

        // $tplPath = resource_path("view/{$themes}/{$tplFile}.htm");
        // if (!file_exists($tplPath)) {
        //     throw new HttpException("模板不存在！[$tplPath]", 500);
        // }

        // $cacheFile = template_cache_path($themes.'/'.str_replace('/', '_', $tplFile).'.tpl.php');
        include template($tplFile);
    }

    function json($data, $code = 0, $msg = 'success') {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ]);
        exit;
    }
}