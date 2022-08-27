<?php
declare(strict_types=1);
namespace Moon\Facades;

use Exception;
use Moon\Application;
use Moon\Config as MoonConfig;
use Moon\Router;

abstract class Facade {
    private static array $accessors = [];

    protected abstract static function getAccessor();
    protected abstract static function getAccessorName();

    static function setAccessor(mixed $obj): void {
        $accessorName = static::getAccessorName();
        if (!isset(self::$accessors[$accessorName])) {
            self::$accessors[$accessorName] = $obj;
        }
    }

    public static function __callStatic($method, $args)
    {
        $accessorName = static::getAccessorName();
        if (!isset(self::$accessors[$accessorName])) {
            self::$accessors[$accessorName] = static::getAccessor();
        }
        $instance = self::$accessors[$accessorName];

        if (!$instance) {
            throw new Exception('a facade accessor has not been set.');
        }

        return $instance->$method(...$args);
    }
}

class DB {
    private static $dbs = array();
    private static $cur_db = 'default';
    private static $required_drivers = array();

    public static function init(Array $config, $db_config_key = 'default') {
        if (array_key_exists($db_config_key, self::$dbs)) {
            return true;
        }

        if (empty($config)) {
            throw new \Exception("初始化数据库失败，无效的配置[{$db_config_key}]", 1);
        }

        $db_type = substr($config['dbdsn'], 0, strpos($config['dbdsn'], ':'));

        $pdo_class_name = "db_" . $db_type;
        if (!array_key_exists($db_type, self::$required_drivers)) {
            self::$required_drivers[$db_type] = true;
        }

        $db = new $pdo_class_name($config);
        $db->connect();

        self::$dbs[$db_config_key] = $db;
    }

    public static function get($db) {
        if (!array_key_exists($db, self::$dbs)) {
            throw new \Exception("在访问数据库之前需要先初始化[{$db}]", 1);
        }
        
        return self::$dbs[$db];
    }

    public static function switch_db($db) {
        if (!array_key_exists($db, self::$dbs)) {
            throw new \Exception("在切换默认数据库之前需要先初始化[{$db}]", 1);
        }
        self::$cur_db = $db;
    }

    public static function fetch_all($sql, $args = null) {
        return self::$dbs[self::$cur_db]->fetch_all($sql, $args);
    }

    public static function fetch_first($sql, $args = null) {
        return self::$dbs[self::$cur_db]->fetch_first($sql, $args);
    }

    public static function result_first($sql, $args = null) {
        return self::$dbs[self::$cur_db]->result_first($sql, $args);
    }

    public static function insert($table, $data, $batch = false, $replace = false) {
        return self::$dbs[self::$cur_db]->insert($table, $data, $batch, $replace);
    }

    public static function update($table, $data, $condition, $condition_args) {
        return self::$dbs[self::$cur_db]->update($table, $data, $condition, $condition_args);
    }

    public static function delete($table, $condition = '', $condition_args = ''){
        return self::$dbs[self::$cur_db]->delete($table, $condition, $condition_args);
    }

    public static function quote($str) {
        return self::$dbs[self::$cur_db]->quote($str);
    }

    public static function last_sql() {
        return self::$dbs[self::$cur_db]->last_sql();
    }

    public static function last_id() {
        return self::$dbs[self::$cur_db]->last_id();
    }


    public static function begin_transaction() {
        return self::$dbs[self::$cur_db]->begin_transaction();
    }

    public static function commit() {
        return self::$dbs[self::$cur_db]->commit();
    }

    public static function rollback() {
        return self::$dbs[self::$cur_db]->rollback();
    }

    public static function query($sql,$arg=null) {
        return self::$dbs[self::$cur_db]->execute($sql,$arg);
    }
}

class MEM {
    private static $instances = array();
    private static $cur_server = 'default';

    public static function init($cache_config_key = 'default', $force_reconnect = false) {
        if (array_key_exists($cache_config_key, self::$instances) && !$force_reconnect) {
            return true;
        }

        // 如果是强制重连接，则先关闭
        if ($force_reconnect && array_key_exists($cache_config_key, self::$instances)) {
            self::$instances[self::$cur_server]->close();
            unset(self::$instances[self::$cur_server]);
        }

        $cache_config = C::S("cache/{$cache_config_key}");
        if (empty($cache_config)) {
            throw new \Exception("初始化缓存失败，无效的配置文件[{$cache_config}]", 1);
        }

        $cache_class_name = 'cache_' . strtolower($cache_config['type']);
        if (!class_exists($cache_class_name)) {
            require(MOON_PATH . '/moon/library/cache/cache_'.strtolower($cache_config['type']).'.php');
        }
        if (!class_exists($cache_class_name)) {
            throw new \Exception("不支持的缓存接口[{$cache_config['type']}]", 1);
        }

        $cache = new $cache_class_name($cache_config);
        $cache->connect();

        self::$instances[$cache_config_key] = $cache;
    }

    // 切换当前缓存服务器
    public static function switch_mem($cache_config_key) {
        if (!array_key_exists($cache_config_key, self::$instances)) {
            self::init($cache_config_key);
        }

        self::$cur_server = $cache_config_key;
    }

    public static function get_instance($config_key) {
        return self::$instances[$config_key];
    }

    public static function ping($msg = '') {
        return self::$instances[self::$cur_server]->ping($msg);
    }

    public static function rename($old_key, $new_key)
    {
        return self::$instances[self::$cur_server]->rename($old_key, $new_key);
    }
    /**
     *
     * @param $name
     * @param bool $origin true：不加工, false:msgpack_unpack
     * @return mixed
     */
    public static function get($name,$origin=false) {
        if ($origin){
            return self::$instances[self::$cur_server]->get_origin($name);
        }else{
            return self::$instances[self::$cur_server]->get($name);
        }
    }

    public static function del($name, $ttl = 0) {
        return self::$instances[self::$cur_server]->del($name, $ttl = 0);
    }

    public static function flush() {
        return self::$instances[self::$cur_server]->flush();
    }

    public static function gets(array $keys) : array {
        return self::$instances[self::$cur_server]->gets($keys);
    }

    public static function sets(array $data) : bool {
        return self::$instances[self::$cur_server]->sets($data);
    }

    public static function exists($key) {
        return self::$instances[self::$cur_server]->exists($key);
    }

    public static function rpush($key, $data) : bool {
        return self::$instances[self::$cur_server]->rpush($key, $data);
    }

    public static function lpop($key) {
        return self::$instances[self::$cur_server]->lpop($key);
    }

    // 阻塞式弹出
    public static function blpop($key, $timeout = 3600) {
        return self::$instances[self::$cur_server]->blpop($key, $timeout);
    }

    public static function llen( $key ) {
        return self::$instances[self::$cur_server]->lLen($key);
    }

    // 阻塞式弹出并加到另一个list里。
    public static function brpoplpush($key_src, $key_dst) {
        return self::$instances[self::$cur_server]->brpoplpush($key_src, $key_dst);
    }

    public static function lindex($key,$index) {
        return self::$instances[self::$cur_server]->lindex($key,$index);
    }

    public static function lget($key, $index = 0) {
        return self::$instances[self::$cur_server]->lget($key, $index);
    }

    public static function sadd($key, $value) : bool {
        return self::$instances[self::$cur_server]->sadd($key, $value);
    }

    public static function spop($key) {
        return self::$instances[self::$cur_server]->spop($key);
    }

    public static function sismember($key, $value) {
        return self::$instances[self::$cur_server]->sismember($key, $value);
    }

    public static function srandmember($key, $count) {
        return self::$instances[self::$cur_server]->srandmember($key, $count);
    }

    public static function scard($key) {
        return self::$instances[self::$cur_server]->scard($key);
    }

    public static function srem($key, $value) {
        return self::$instances[self::$cur_server]->srem($key, $value);
    }

    public static function hget($key, $field) {
        return self::$instances[self::$cur_server]->hget($key, $field);
    }

    public static function hdel($key, $field) {
        return self::$instances[self::$cur_server]->hdel($key, $field);
    }

    public static function hmget($key, $fields) {
        return self::$instances[self::$cur_server]->hmget($key, $fields);
    }

    public static function hgetall($key) {
        return self::$instances[self::$cur_server]->hgetall($key);
    }

    public static function hlen($key) {
        return self::$instances[self::$cur_server]->hlen($key);
    }

    // $data为一个k-v pair(s)
    public static function hmset($key, $data) {
        return self::$instances[self::$cur_server]->hmset($key, $data);
    }

    public static function hset($key, $field_name, $value) {
        return self::$instances[self::$cur_server]->hset($key, $field_name, $value);
    }

    public static function hincrby($key, $field_name, $value) {
        return self::$instances[self::$cur_server]->hincrby($key, $field_name, $value);
    }

    public static function hexists($key, $field_name) {
        return self::$instances[self::$cur_server]->hexists($key, $field_name);
    }

    public static function incr($key,$num=1){
        return self::$instances[self::$cur_server]->incrBy($key, $num);
    }

    /**
     * 一般不要使用
     */
    public static  function set($name,$value,$time=null){
        return self::$instances[self::$cur_server]->set($name,$value,$time);
    }

    public static function xadd($str_key, $arr_message, $str_id = '*', $maxlen=null) {
        return self::$instances[self::$cur_server]->xadd($str_key, $str_id, $arr_message, $maxlen);
    }

    // 创建group，如果stream不存在，会默认创建
    public static function xgroupcreate($stream_name, $group_name, $init_id = '$', $create_no_exists=true) {
        return self::$instances[self::$cur_server]->xgroup('create', $stream_name, $group_name, $init_id, $create_no_exists);
    }

    public static function xreadgroup($group_name, $streams, $consumer_name='cs_default', $read_count = 1, $block_time = 0) {
        return self::$instances[self::$cur_server]->xreadgroup($group_name, $consumer_name, $streams, $read_count, $block_time);
    }

    public static function xack($stream_name, $group_name, $ids) {
        return self::$instances[self::$cur_server]->xack($stream_name, $group_name, $ids);
    }

    public static function xread($arr_streams, $i_count = null, $i_block = null) {
        return self::$instances[self::$cur_server]->xread($arr_streams, $i_count, $i_block);
    }

    public static function xtrim($str_stream, $i_max_len, $boo_approximate) {
        return self::$instances[self::$cur_server]->xtrim($str_stream, $i_max_len, $boo_approximate);
    }

    public static function xlen($str_stream) {
        return self::$instances[self::$cur_server]->xlen($str_stream);
    }

    public static function xrange($str_stream, $str_start, $str_end, $i_count = null) {
        return self::$instances[self::$cur_server]->xrange($str_stream, $str_start, $str_end, $i_count);
    }
    public static function getlasterror() {
        return self::$instances[self::$cur_server]->getlasterror();
    }

    /**
     * 获取一个锁
     * @param $key
     * @param int $timeOut 单位毫秒
     * @return mixed
     */
    public static function getLock($key, $timeOut = 1000) {
        return self::$instances[self::$cur_server]->getLock($key, $timeOut);
    }

    public static function unLock($key) {
        return self::$instances[self::$cur_server]->unLock($key);
    }
}

class LOG {
    public static function fault($msg) {
        self::console('FAULT', $msg);
    }
    public static function error($msg) {
        self::console('ERROR', $msg);
    }

    public static function info($msg) {
        self::console('INFO', $msg);
    }

    public static function debug($msg) {
        self::console('DEBUG', $msg);
    }

    public static function warning($msg) {
        self::console('WARNING', $msg);
    }

    private static function console($type, $msg) {
        echo $type . ' - ' . date('Y-m-d H:i:s') . ' - ' . $msg . "\n";
    }
}

/**
 * Router
 * @method static \Moon\Router instance()
 * @method static \Moon\Router get(string $path, string|callable $action)
 * @method static \Moon\Router group(array $attrs, callable $action)
 * @method static array getRouters()
 */
class R extends Facade {
    protected static function getAccessorName()
    {
        return 'router';
    }

    protected static function getAccessor()
    {
        return new Router();
    }
}
/**
 * Core
 * @method static void runHttp()
 * @method static void setLocale($lang)
 * @method static void getLocale()
 * @method static bool isLocale($lang)
 */
class APP extends Facade {
    private static $app;

    protected static function getAccessorName()
    {
        return 'app';
    }

    protected static function getAccessor()
    {
        
    }
}

/**
 * Config
 * @method static void load(string $file = '')
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 */
class C extends Facade {
    protected static function getAccessorName()
    {
        return 'config';
    }

    protected static function getAccessor()
    {
        return new MoonConfig();
    }
}