<?php
declare(strict_types=1);

use Moon\Facades\R;

$router = R::instance();
$router->get('/', 'App\Http\IndexController@index');
$router->get('/en', 'App\Http\IndexController@en');
$router->get('/fr', 'App\Http\IndexController@fr');
$router->get('/de', 'App\Http\IndexController@de');
$router->get('/no', 'App\Http\IndexController@no');
$router->get('/ro', 'App\Http\IndexController@ro');
$router->get('/fi', 'App\Http\IndexController@fi');
$router->get('/da', 'App\Http\IndexController@da');
$router->get('/ko', 'App\Http\IndexController@ko');
$router->get('/ja', 'App\Http\IndexController@ja');
$router->get('/nl', 'App\Http\IndexController@nl');
$router->get('/pt', 'App\Http\IndexController@pt');
$router->get('/es', 'App\Http\IndexController@es');
$router->get('/ru', 'App\Http\IndexController@ru');
$router->get('/it', 'App\Http\IndexController@it');
$router->get('/hu', 'App\Http\IndexController@hu');
$router->get('/tr', 'App\Http\IndexController@tr');
$router->get('/sl', 'App\Http\IndexController@sl');
$router->get('/pl', 'App\Http\IndexController@pl');
$router->get('/he', 'App\Http\IndexController@he');
$router->get('/vi', 'App\Http\IndexController@vi');
$router->get('/el', 'App\Http\IndexController@el');
$router->get('/id', 'App\Http\IndexController@id');
$router->get('/th', 'App\Http\IndexController@th');
$router->get('/cs', 'App\Http\IndexController@cs');
$router->get('/zh-cn', 'App\Http\IndexController@zh_cn');
$router->get('/zh-tw', 'App\Http\IndexController@zh_tw');
$router->get('/sv', 'App\Http\IndexController@sv');
$router->get('/ar', 'App\Http\IndexController@ar');
$router->get('/sk', 'App\Http\IndexController@sk');
$router->get('/bg', 'App\Http\IndexController@bg');
$router->get('/bn', 'App\Http\IndexController@bn');
$router->get('/hi', 'App\Http\IndexController@hi');

$router->any('/t', 'App\Http\TranslateController@index');