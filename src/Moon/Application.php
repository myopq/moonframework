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
        $this->initLang();

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

    private function initLang(): void {
        $langs = parseLangs();
        $lang = C::get("default_lang");
        if (!empty($langs) && is_dir(resource_path('langs/' . array_shift($langs)))) {
            $lang = $langs[0];
        }
        $this->setlocale($lang);
    }

    private function initRoute(): Router {
        $routeFile = APP_PATH . '/routes/index.php';
        if (is_file($routeFile)) {
            require $routeFile;
        }
        return R::instance();
    }

    public function setLocale($lang): void {
        C::set('lang', $lang);
    }

    public function getLocale(): string {
        return C::get('lang');
    }

    public function isLocal($lang): bool {
        return $lang === C::get('lang');
    }

    public function runHttp() {
        $router = $this->initRoute();
        $router->dispatch();
    }
}