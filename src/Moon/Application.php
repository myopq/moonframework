<?php
declare(strict_types=1);
namespace Moon;

use Moon\Facades\C;
use Moon\Config;
use Moon\Facades\R;

class Application {
    private array $glogal = [];
    private string $appPath = "";

    public function __construct(string $appPath) {
        $this->appPath = $appPath;
        $this->init();
    }

    public function init(): Application {
        $this->initConfig();

        return $this;
    }

    private function initConfig(): void {
        define('MOON_PATH', dirname(dirname(dirname(__FILE__))));
        define('APP_PATH', $this->appPath);
        $configAccessor = new Config();
        $configAccessor->load(MOON_PATH . '/src/Config/config.php');
        $configAccessor->load(APP_PATH . '/config/config.php');
        C::setAccessor($configAccessor);
        define('APP_ENV', C::get('app/env'));
        define('DEBUG_MODE', APP_ENV == 'develop');
    }

    private function initRoute(): Router {
        $routeFile = APP_PATH . '/routes/index.php';
        if (is_file($routeFile)) {
            require $routeFile;
        }
        return R::instance();
    }

    public function runHttp() {
        $router = $this->initRoute();
        $router->dispatch();
    }
}