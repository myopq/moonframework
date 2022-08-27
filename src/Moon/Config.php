<?php
declare(strict_types=1);

namespace Moon;
use Exception;

class Config {
    private array $config = [];

    public function load(string $file = ''): void {
        if (!is_file($file)) {
            throw new Exception("invalid config file[{$file}]", 1);
        }

        $config = require $file;

        $this->config = array_merge_recursive_cover($this->config, $config);
    }

    /**
     * return config value.
     * @param $key example key: user, user/age
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (empty($key)) return $this->config;

        $keys = $this->parseKey($key);

        $return = $this->config;
        foreach ($keys as $_key) {
            if (!isset($return[$_key])) {
                return $default;
            }
            $return = $return[$_key];
        }

        return $return;
    }

    public function set(string $key, mixed $value): void
    {
        $keys = $this->parseKey($key);
        $configNew = [array_pop($keys) => $value];
        while ($key = array_pop($keys)) {
            $configNew[$key] = $configNew;
        }

        $this->config = array_replace_recursive($this->config, $configNew);
    }

    private function parseKey(string $key): array
    {
        return strpos($key, '/') > 1 ? explode('/', $key) : [$key];
    }
}