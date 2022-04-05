<?php
declare(strict_types=1);
use Moon\Application;
use Moon\Facades\APP;

include "../../../vendor/autoload.php";
include "../vendor/autoload.php";

$app = new Application(dirname(__DIR__));
APP::setAccessor($app);
APP::runHttp();