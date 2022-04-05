<?php
declare(strict_types=1);

use Moon\CommandParser;

include "../../vendor/autoload.php";
include "./vendor/autoload.php";

if (empty($argv[1])) {
    throw new \Exception("invalid command", 1);
}

/**
 * 命令格式
 * -h10.11.11.11 或 -h 11.1.1.1 这是短格式
 * --user=abc这是长格式
 */
$cmdArgs = CommandParser::parseArgs();

if (empty($cmdArgs['args'][0])) {
    throw new \Exception("invalid command", 1);
}

$cmdClass = 'App\Command\\'.$cmdArgs['args'][0];
if (!preg_match('/^[\\\_a-z]+$/i', $cmdClass)) {
    throw new \Exception("invalid class name", 1);
}

if (!class_exists($cmdClass)) {
    throw new \Exception("command class don\'t exists.", 1);
}

$cmdObj = new $cmdClass();

$method = empty($args[1]) ? 'index' : $args[1];
if (!preg_match('/^[\\\_a-z]+$/i', $method)) {
    throw new \Exception("invalid method name", 1);
}

if (!method_exists($cmdObj, $method)) {
    throw new \Exception("method don\'t exists.", 1);
}

$cmdObj->$method();


