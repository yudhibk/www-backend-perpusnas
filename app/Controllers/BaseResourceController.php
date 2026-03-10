<?php

namespace App\Controllers;

use App\Models\Log\LogActivity;
use App\Models\User\AppUserRole;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;

class BaseResourceController extends ResourceController
{
    use ResponseTrait;

    protected $email;
	protected $key;
	protected $cache;
	protected $redis_enabled;
	protected $session;
	protected $validation;
	protected $db;

    public function __construct()
    {
		$this->session = \Config\Services::session();
		$this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
		$this->email = new \App\Libraries\Mailer();
		$this->cache = new \App\Libraries\Cache();
		$this->redis_enabled = getenv('redis.enabled') == 'true';

		if($this->redis_enabled){
			$this->key = getenv('redis.key');
		}
		
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
    }

	public function paginatedResponseCache($key)
    {
		$redis = json_decode($this->cache->get($key));
		if(!empty($redis)){
			$response = [
				'total_record' => $redis->totalRecord,
				'per_page' => (int)$redis->limit,
				'total_page' => $redis->limit == 0 ? 0 : ceil($redis->totalRecord / $redis->limit),
				'current_page' => $redis->limit == 0 ? 0 : floor($redis->offset / $redis->limit) + 1,
				'result' => $redis->result
			];
			return $this->respond($response);
		} else {
			return $this->respond('Redis data not found');
		}
    }

	public function simpleResponseCache($key)
    {
		$redis = json_decode($this->cache->get($key));
		if(!empty($redis)){
			return $this->respond($redis->result);
		} else {
			return $this->respond('Redis data not found');
		}
    }

    public function paginatedResponse($result, $totalRecord, $limit, $offset)
    {
		// set redis
		if($this->redis_enabled){
			$this->cache->set($this->key, json_encode(array(
				'result' => $result,
				'totalRecord' => $totalRecord, 
				'limit' => $limit,
				'offset' => $offset
			)));
		}

        $response = [
            'total_record' => (int)$totalRecord,
            'per_page' => (int)$limit,
            'total_page' => (int)($limit == 0 ? 0 : ceil($totalRecord / $limit)),
            'current_page' => (int)$limit == 0 ? 0 : floor($offset / $limit) + 1,
            'result' => $result
        ];
        return $this->respond($response);
    }

    public function simpleResponse($result)
    {
		// set redis
		if($this->redis_enabled){
			$this->cache->set($this->key, json_encode(array(
				'result' => $result,
			)));
		}

        return $this->respond($result);
    }
}
