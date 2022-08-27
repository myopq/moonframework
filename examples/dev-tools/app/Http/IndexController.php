<?php
declare(strict_types=1);
namespace App\Http;

use Moon\Facades\C;
use Moon\HttpController;
use Moon\HttpRequest;

class IndexController extends HttpController {
    public function index(HttpRequest $httpRequest) {
        header("Location: /en");
    }

    public function en(HttpRequest $httpRequest) {
        C::set('lang', 'en');
        $this->_index($httpRequest);
    }

    public function zh_cn(HttpRequest $httpRequest) {
        C::set('lang', 'zh-cn');
        $this->_index($httpRequest);
    }

    public function zh_tw(HttpRequest $httpRequest) {
        C::set('lang', 'zh-tw');
        $this->_index($httpRequest);
    }

    private function _index(HttpRequest $httpRequest) {
        $breadcrumbs = [
            ['href'=>'/', 'text' => 'Home'],
            ['href'=>'#', 'text' => 'Home'],
        ];
        $this->assign("breadcrumbs", $breadcrumbs);
        $this->display('index/index');
    }

    public function test(HttpRequest $httpRequest) {
        $items = ['xxxxx', 'a', 'b', 'cccc', 'ddddd', 'eeee'];
        asort($items);
        
        $this->assign("items", $items);
        
        $this->display();
    }
}