<?php

namespace Region\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Region\Models\RegionModel;

class Region extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $regionModel;

	function __construct()
	{
		$this->regionModel = new RegionModel();
	}

	public function get_provinces(){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 1)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_cities($code = '11'){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 2)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_districts($code = '11.01'){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 3)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_sub_districts($code = '11.01.01'){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 4)
			->like('code', $code)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_kab_kota(){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 2)
			->findAll();
		return $this->simpleResponse($response);
	}

	public function get_kelurahan($kab_kota_code = '73.73'){
		$response = $this->regionModel
			->select('code,name')
			->where('level', 4)
			->like('code', $kab_kota_code)
			->findAll();
		return $this->simpleResponse($response);
	}

}
