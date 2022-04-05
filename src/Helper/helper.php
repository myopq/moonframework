<?php
declare(strict_types=1);

use Moon\Config;
use Moon\Exception\HttpException;
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

function lang(string $str): string {
    return $str;
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
	$cache_file = $cacheDir . '/'.str_replace('/', '_', $file).'.tpl.php';

	checktplrefresh($tpl_path, $tpl_path, 0, $cache_file);

	return $cache_file;
}