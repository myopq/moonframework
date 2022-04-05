<?php
declare(strict_types=1);

use Moon\Facades\R;

$router = R::instance();
$router->get('/', 'App\Http\IndexController@index');
$router->get('/en/', 'App\Http\IndexController@index');
$router->get('/index/test', 'App\Http\IndexController@test');
