<?php
declare(strict_types=1);
namespace App\Http;

use Moon\Facades\C;
use Moon\HttpController;
use Moon\HttpRequest;

class IndexController extends HttpController {
    public function index(HttpRequest $httpRequest) {
        $breadcrumbs = [
            ['href'=>'/', 'text' => 'Home'],
            ['href'=>'#', 'text' => 'Home'],
        ];
        $this->assign("breadcrumbs", $breadcrumbs);
        $this->display();
    }

    public function test(HttpRequest $httpRequest) {
        $items = ['xxxxx', 'a', 'b', 'cccc', 'ddddd', 'eeee'];
        asort($items);
        
        $this->assign("items", $items);
        
        $this->display();
    }
}