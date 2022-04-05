<?php
declare(strict_types=1);
namespace Moon;

class Result
{
    public $code = 0;
    public $data = '';
    public $msg = '';
    
    function __construct($code, $data, $msg)
    {
        $this->code = $code;
        $this->data = $data;
        $this->msg = $msg;
    }

    function toJson() {
        return json_encode([
            'code'=>$this->code,
            'data'=>$this->data,
            'msg'=>$this->msg
        ]);
    }

    function isSuccess() { return $this->code === 0; }
    function isError() { return $this->code !== 0; }

    static function success($data = '', $code = 0, $msg = '') {
        return new static($code, $data, $msg);
    }

    static function error($msg = '', $code = 1, $data = '') {
        return new static($code, $data, $msg);
    }
}