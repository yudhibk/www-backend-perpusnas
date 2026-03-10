<?php

namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
use Predis\Client;

class Cache extends BaseController
{
	use ResponseTrait;

	private $cache;
	public function __construct()
    {
		$this->cache = new \App\Libraries\Cache();
    }

    public function index()
    {
		$data = [
			'code' => 'Redis',
            'Command: SET' => ('cache/set/foo/bar'),
			'Command: GET' => ('cache/get/foo'),
			'Command: TTL' => ('cache/ttl/foo'),
			'Command: EXIST' => ('cache/exists/foo'),
			'Command: EXPIRE' => ('cache/expire/foo/60'),
			'Command: DEL' => ('cache/del/foo'),
			'Command: KEYS' => ('cache/keys'),
			'Command: FLUSHALL' => ('cache/flushall'),
			'messages' => 'Cache Client in Browser',
        ];

		return $this->respond($data, 200);
    }

	public function set($key, $value)
    {
		$this->cache->set($key,$value);
		$ttl = $this->cache->ttl($key);

		$data = [
			'key' => $key,
			'value' => $value,
			'ttl' => $ttl,
			'message' => "[cache] : set key succesfully",
        ];

		return $this->respond($data, 200);
    }

	public function get($key)
    {
		$value = $this->cache->get($key);
		$ttl = $this->cache->ttl($key);

		$data = [
			'key' => $key,
			'value' => $value,
			'ttl' => $ttl,
			'message' => "[cache] : get key succesfully",
        ];

		return $this->respond($data, 200);
    }

	public function ttl($key)
    {
		$ttl = $this->cache->ttl($key);

		$data = [
			'key' => $key,
			'ttl' => $ttl,
			'message' => "[cache] : ttl key in '{$ttl}' seconds",
        ];

		return $this->respond($data, 200);
    }

	public function exists($key)
    {
		$exists = $this->cache->exists($key);
		if($exists){
			$msg = "[cache] : key is exist";
		} else {
			$msg = "[cache] : key is not exist";
		}

		$data = [
			'key' => $key,
			'message' => $msg
        ];

		return $this->respond($data, 200);
    }

	public function expire($key, $ttl = 60)
    {
		$value = $this->cache->expire($key, $ttl);

		$data = [
			'key' => $key,
			'expire' => $ttl,
			'message' => "[cache] : set expired key in '{$ttl}' seconds",
        ];

		return $this->respond($data, 200);
    }

	public function del($key)
    {
		$value = $this->cache->del($key);

		$data = [
			'key' => $key,
			'message' => "[cache] : del key succesfully",
        ];

		return $this->respond($data, 200);
    }

	public function keys()
    {
		$keys = $this->cache->keys();
		sort($keys);

		$urls = array();
		foreach($keys as $row){
			array_push($urls, base64_decode($row));
		}

		$data = [
			'keys' => $keys,
			'urls' => $urls,
			'message' => "[cache] : get all keys succesfully",
        ];

		return $this->respond($data, 200);
    }

	public function flushall()
    {
		$this->cache->flushAll();

		$data = [
			'message' => "[cache] : all keys remove succesfully",
        ];

		return $this->respond($data, 200);
    }

}


