<?php
declare(strict_types=1);

namespace Moon;

class MemRedis {
	private $config = null;

	function __construct($config) {
		if (!empty($config)) {
			$this->config = $config;
		}
	}

	public function connect() {
		// 是否使用长连接
		$config = $this->config;
		$func = $config['pconnect'] === true ? 'pconnect' : 'connect';
		$this->handler = new \Redis();
		$this->handler->$func($config['host'], $config['port']);
        $this->handler->select($config['db']);
        // $this->handler->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
	}

	public function get($name) {
        return $this->handler->get($name);
	}

    public function set($name, $value, $expire = null) {
        if($expire === null) {
            return $this->handler->set($name, $value);
        } else {
            return $this->handler->setEx($name, $expire, $value);
        }
    }

    public function del($name, $ttl = 0) {
        return $this->handler->delete($name);
    }

    public function flush() {
        return $this->handler->flushDb();
    }

    public function gets(array $keys) : array {
        return $this->handler->mGet($keys);
    }

    public function sets(array $data) : bool {
        return $this->handler->mSet($data);
    }

    public function exists($key) {
        return $this->handler->exists($key);
    }

    public function rpush($key, $data) : bool {
        return $this->handler->rPush($key, $data);
    }

    public function lpop($key) {
        return $this->handler->lPop($key);
    }

    public function lget($key, $index = 0) {
        return $this->handler->lGet($key, $index);
    }

    public function sadd($key, $value) {
        return $this->handler->sAdd($key, $value);
    }

    public function spop($key) {
        $tmp = $this->handler->sPop($key);
        if ($tmp === false) {
            return false;
        }
        return $tmp;
    }

    public function sismember($key, $value) {
        return $this->handler->sismember($key, $value);
    }

    // set operations
    
    public function hget($key, $field) {
        $tmp = $this->handler->hGet($key, $field);
        if ($tmp === false) {
            return false;
        }

        return $tmp;
    }

    // $fields为数组
    public function hmget($key, $fields) {
        $tmp = $this->handler->hMGet($key, $fields);
        if ($tmp === false) {
            return false;
        }

        return $tmp;
    }

    public function hgetall($key) {
        $tmp = $this->handler->hGetAll($key);

        if ($tmp === false) {
            return false;
        }
        return $tmp;
    }

    // $data为一个k-v pair(s)
    public function hmset($key, $data) {
        return $this->handler->hMSet($key, $data);
    }

    public function hset($key, $field_name, $value) {
        return $this->handler->hSet($key, $field_name, $value);
    }

    public function hexists($key, $field_name) {
        return $this->handler->hExists($key, $field_name);
    }
}