<?php
declare(strict_types=1);

use Moon\Config;
use Moon\Facades\APP;
use Moon\Facades\C;
use Moon\MoonException;
use Moon\ViewTemplate;

function config(string $key, mixed $value): mixed
{
    static $config = new Config();
    $config->get($key);
}

function dd(mixed $obj): void {
    var_dump($obj);
}

function storage_path(string $path = ""): string {
    return APP_PATH . '/storage' . ($path ? '/'.$path : '');
}

function resource_path(string $path = ""): string {
    return APP_PATH . '/resource' . ($path ? '/'.$path : '');
}

function public_path(string $path = ""): string {
    return APP_PATH . '/public' . ($path ? '/'.$path : '');
}

function template_cache_path(string $path = ""): string {
    return APP_PATH . '/storage/view' . ($path ? '/'.$path : '');
}

function lang(string $langKeyStr, array $langParams = []): string {
	static $langsCache = [];
	if (empty($langKeyStr)) {
		throw new Exception("invalid lang key.", 1);
	}
	$sections = explode('.', $langKeyStr);
	$file = $key = '';
	
	$sectionCount = count($sections);
	if ($sectionCount === 1) {
		$file = "common";
		$key = $sections[0];
	} elseif ($sectionCount ===2) {
		list($file, $key) = $sections;
	} else {
		$file = array_shift($sections);
		$key = implode('.', $sections);
	}
	
	if (!array_key_exists($file, $langsCache)) {
		$langFilePath = resource_path('lang/' . APP::getLocale() . '/' . $file . '.php');
		if (!file_exists($langFilePath)) {
			throw new Exception("invalid lang file [{$langFilePath}]", 1);
		}
		$langsCache[$file] = require($langFilePath);
	}

	$langValue = getValueByPath($langsCache[$file], $key, null, '.');
	if (null === $langValue) {
		return $langKeyStr;
	}

	if (!is_string($langValue)) {
		return $langValue;
	}

	if (empty($langParams)) {
		return $langValue;
	} else {
		return str_replace(
			array_keys($langParams),
			array_values($langParams),
			$langValue
		);
	}
}

/**
 * 从对象或数组里取值，key值可以是以/分隔的层级，如a/b/c
 */
function getValueByPath($from, $path, $default = null, $splitChar = '/') {
	if (is_object($from)) {
		$_obj = $from;
		foreach (explode($splitChar, $path) as $key) {
			if (property_exists($_obj, $key)) {
				$_obj = $_obj->$key;
			} else {
				$_obj = $default;
				break;
			}
		}

		return $_obj;
	} elseif (is_array($from)) {
		$keys = explode($splitChar, $path);

		foreach ($keys as $key) {
			if (!isset($from[$key])) {
				return $default;
			}
			$from = $from[$key];
		}

		return $from;
	}
}

function array_merge_recursive_cover(...$args)
{
	$rs = array_shift($args);
	foreach($args as $arr)
	{
		if(!is_array($arr))
		{
			return false;
		}
		foreach($arr as $key=>$val)
		{
			$rs[$key] = isset($rs[$key]) ? $rs[$key] : array();
			$rs[$key] = is_array($val) ? array_merge_recursive_cover($rs[$key], $val) : $val;
		}
	}
	return $rs;
}

function checktplrefresh($tpl_path, $sub_tpl_path, $cache_time, $cache_file) {
	if (!$cache_time && file_exists($cache_file)) {
		$cache_time = filemtime($cache_file);
	}
	if(@filemtime($sub_tpl_path) > $cache_time || (defined('DEBUG_MODE') && DEBUG_MODE === true)) {
		$template = new ViewTemplate();
		$template->parse_template($tpl_path, $cache_file);

		return true;
	}
	return false;
}

function template($file) {
	$themes = C::get('view/themes');
	$tpl_path = APP_PATH . '/resource/view/' . $themes . '/' . $file . '.htm';
	if (!file_exists($tpl_path)) {
		throw new MoonException("模板不存在！[{$tpl_path}]");
	}

	$cacheDir = template_cache_path(C::get('view/themes'));
	$cache_file = $cacheDir . '/' . (APP::getLocale()) . '_' . str_replace('/', '_', $file).'.tpl.php';

	checktplrefresh($tpl_path, $tpl_path, 0, $cache_file);

	return $cache_file;
}

/**
 * 解析浏览器请求的语言
 */
function parseLangs(): array {
	$langs = array();

	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

		if (count($lang_parse[1])) {
			$langs = array_combine($lang_parse[1], $lang_parse[4]);
			
			foreach ($langs as $lang => $val) {
				if ($val === '') $langs[$lang] = 1;
			}

			arsort($langs, SORT_NUMERIC);
		}
	}

	return $langs;
}