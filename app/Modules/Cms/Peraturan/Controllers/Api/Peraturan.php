<?php

namespace Peraturan\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Peraturan\Models\PeraturanModel;

class Peraturan extends \App\Controllers\BaseResourceController
{
	protected $peraturanModel;


	protected $modulePath;
	protected $uploadPath;
	protected $validation;
	protected $session;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html', 'adminigniter']);
		$this->peraturanModel = new PeraturanModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/peraturan/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
	}

	public function index($channel = null)
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['category'] = $params['category'] ?? '';
		$params['keyword'] = $params['keyword'] ?? '';

		$query = $this->peraturanModel;

		if(!empty($channel)){
			$query->where('channel',$channel);
		}

		if(!empty($params['category'])){
			$query->where('category',$params['category']);
		}


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

	public function detail($id = null)
	{
		if (!is_allowed('cms/peraturan/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->peraturanModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		if (!is_allowed('cms/peraturan/create')) {
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

			$newPeraturanId = $this->peraturanModel->insert($save_data);
			if ($newPeraturanId) {
				$this->session->setFlashdata('toastr_msg', lang('Peraturan.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Peraturan.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Peraturan.info.failed_saved')
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
		if (!is_allowed('cms/peraturan/update')) {
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

			$peraturanUpdate = $this->peraturanModel->update($id, $update_data);
			if ($peraturanUpdate) {
				add_log('Ubah Undang-Undang', 'cms/peraturan', 'edit', 't_peraturan', $id);
				$this->session->setFlashdata('toastr_msg', lang('Peraturan.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Peraturan.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Peraturan.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('cms/peraturan/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->peraturanModel->find($id);
		if ($data) {
			$this->peraturanModel->delete($id);
			add_log('Hapus Peraturan', 'cms/peraturan', 'delete', 't_peraturan', $id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Peraturan.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Peraturan.info.not_found') . ' ID:' . $id);
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
		$peraturan = $this->peraturanModel->find($upload_id);
		$peraturanUpdate = $this->peraturanModel->update($upload_id, $update_data);
		if ($peraturanUpdate) {
			unlink_file($this->modulePath, $peraturan->file_image);
			unlink_file($this->modulePath, 'thumb_' . $peraturan->file_image);

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
