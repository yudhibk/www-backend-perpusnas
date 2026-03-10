<?php

namespace DepositPengajuanBahanpustaka\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\BahanpustakaModel;
use CodeIgniter\Files\File;

class Bahanpustaka extends \App\Controllers\BaseResourceController
{
	protected $bahanpustakaModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html', 'adminigniter']);
		$this->bahanpustakaModel = new \DepositPengajuanBahanpustaka\Models\BahanpustakaModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/bahan-pustaka/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
	}

	public function index()
	{
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['keyword'] = $params['keyword'] ?? '';
		$query = $this->bahanpustakaModel;
		if(!empty($params['keyword'])){
			$query->like('title',$params['keyword']);
		}


		$total = $query->countAllResults(false);

		$data = $query
			->orderBy('id', 'desc')
			->findAll($params['limit'], $params['offset']);

		$response = array(
			'error'    => false,
			'param' => $params,
			'data' => $data,
			'message' => 'Data retrieved successfully'
		);

		return $this->paginatedResponse($response, $total, $params['limit'], $params['offset']);
	}

	public function detail($slug = null)
    {
		try {
            $data = $this->bahanpustakaModel->where('slug', $slug)->first();
			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with slug ' . $slug);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }
	public function create()
	{
		if (!is_allowed('deposit/pengajuan/bahan-pustaka/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newBahanpustakaId = $this->bahanpustakaModel->insert($save_data);
			if ($newBahanpustakaId) {
				$this->session->setFlashdata('toastr_msg', lang('Bahanpustaka.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Bahanpustaka.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Bahanpustaka.info.failed_saved')
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function edit($id = null)
	{
		if (!is_allowed('deposit/pengajuan/bahan-pustaka/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$bahanpustakaUpdate = $this->bahanpustakaModel->update($id, $update_data);
			if ($bahanpustakaUpdate) {
				add_log('Ubah Bahanpustaka', 'bahanpustaka', 'edit', 't_deposit_pengajuan_bahan_pustaka', $id);
				$this->session->setFlashdata('toastr_msg', lang('Bahanpustaka.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Bahanpustaka.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Bahanpustaka.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('deposit/pengajuan/bahan-pustaka/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->bahanpustakaModel->find($id);
		if ($data) {
			$this->bahanpustakaModel->delete($id);
			add_log('Hapus Bahanpustaka', 'bahanpustaka', 'delete', 't_deposit_pengajuan_bahan_pustaka', $id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Bahanpustaka.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Bahanpustaka.info.not_found') . ' ID:' . $id);
		}
	}

	public function upload_file()
	{
		$upload_id = $this->request->getPost('upload_id');
		$upload_field = $this->request->getPost('upload_field');
		$upload_title = $this->request->getPost('upload_title');

		$update_data = [];
		$files = (array) $this->request->getPost('file_pendukung');
		if (count($files)) {
			$listed_file = array();
			foreach ($files as $uuid => $name) {
				if (file_exists($this->uploadPath . $name)) {
					$file = new File($this->uploadPath . $name);
					$newFileName = $file->getRandomName();
					$file->move($this->modulePath, $newFileName);
					$listed_file[] = $newFileName;

					if ($upload_field == 'file_image') {
						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
				}
			}
			$update_data[$upload_field] = implode(',', $listed_file);
		}
		$bahanpustaka = $this->bahanpustakaModel->find($upload_id);
		$bahanpustakaUpdate = $this->bahanpustakaModel->update($upload_id, $update_data);
		if ($bahanpustakaUpdate) {
			unlink_file($this->modulePath, $bahanpustaka->file_image);
			unlink_file($this->modulePath, 'thumb_' . $bahanpustaka->file_image);

			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}
}
