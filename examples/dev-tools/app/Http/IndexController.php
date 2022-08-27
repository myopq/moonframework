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

    public function fr(HttpRequest $httpRequest) {
        C::set('lang', 'fr');
        $this->_index($httpRequest);
    }

    public function de(HttpRequest $httpRequest) {
        C::set('lang', 'de');
        $this->_index($httpRequest);
    }

    public function no(HttpRequest $httpRequest) {
        C::set('lang', 'no');
        $this->_index($httpRequest);
    }

    public function ro(HttpRequest $httpRequest) {
        C::set('lang', 'ro');
        $this->_index($httpRequest);
    }

    public function fi(HttpRequest $httpRequest) {
        C::set('lang', 'fi');
        $this->_index($httpRequest);
    }

    public function da(HttpRequest $httpRequest) {
        C::set('lang', 'da');
        $this->_index($httpRequest);
    }

    public function ko(HttpRequest $httpRequest) {
        C::set('lang', 'ko');
        $this->_index($httpRequest);
    }

    public function ja(HttpRequest $httpRequest) {
        C::set('lang', 'ja');
        $this->_index($httpRequest);
    }

    public function nl(HttpRequest $httpRequest) {
        C::set('lang', 'nl');
        $this->_index($httpRequest);
    }

    public function pt(HttpRequest $httpRequest) {
        C::set('lang', 'pt');
        $this->_index($httpRequest);
    }

    public function es(HttpRequest $httpRequest) {
        C::set('lang', 'es');
        $this->_index($httpRequest);
    }

    public function ru(HttpRequest $httpRequest) {
        C::set('lang', 'ru');
        $this->_index($httpRequest);
    }

    public function it(HttpRequest $httpRequest) {
        C::set('lang', 'it');
        $this->_index($httpRequest);
    }

    public function hu(HttpRequest $httpRequest) {
        C::set('lang', 'hu');
        $this->_index($httpRequest);
    }

    public function tr(HttpRequest $httpRequest) {
        C::set('lang', 'tr');
        $this->_index($httpRequest);
    }

    public function sl(HttpRequest $httpRequest) {
        C::set('lang', 'sl');
        $this->_index($httpRequest);
    }

    public function pl(HttpRequest $httpRequest) {
        C::set('lang', 'pl');
        $this->_index($httpRequest);
    }

    public function he(HttpRequest $httpRequest) {
        C::set('lang', 'he');
        $this->_index($httpRequest);
    }

    public function vi(HttpRequest $httpRequest) {
        C::set('lang', 'vi');
        $this->_index($httpRequest);
    }

    public function el(HttpRequest $httpRequest) {
        C::set('lang', 'el');
        $this->_index($httpRequest);
    }

    public function id(HttpRequest $httpRequest) {
        C::set('lang', 'id');
        $this->_index($httpRequest);
    }

    public function th(HttpRequest $httpRequest) {
        C::set('lang', 'th');
        $this->_index($httpRequest);
    }

    public function cs(HttpRequest $httpRequest) {
        C::set('lang', 'cs');
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

    public function sv(HttpRequest $httpRequest) {
        C::set('lang', 'sv');
        $this->_index($httpRequest);
    }

    public function ar(HttpRequest $httpRequest) {
        C::set('lang', 'ar');
        $this->_index($httpRequest);
    }

    public function sk(HttpRequest $httpRequest) {
        C::set('lang', 'sk');
        $this->_index($httpRequest);
    }

    public function bg(HttpRequest $httpRequest) {
        C::set('lang', 'bg');
        $this->_index($httpRequest);
    }

    public function bn(HttpRequest $httpRequest) {
        C::set('lang', 'bn');
        $this->_index($httpRequest);
    }

    public function hi(HttpRequest $httpRequest) {
        C::set('lang', 'hi');
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
}