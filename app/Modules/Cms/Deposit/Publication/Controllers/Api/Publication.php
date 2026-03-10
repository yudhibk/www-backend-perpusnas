<?php

namespace DepositPublication\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

use CodeIgniter\Files\File;
use DepositPublication\Models\PublicationModel;

class Publication extends \App\Controllers\BaseResourceController
{
	protected $publicationModel;


	protected $modulePath;
	protected $uploadPath;
	protected $validation;
	protected $session;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html', 'adminigniter']);
		$this->publicationModel = new PublicationModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/publication/';
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
		$params['category'] = $params['category'] ?? '';
		$query = $this->publicationModel;
		if(!empty($params['keyword'])){
			$query->like('title',$params['keyword']);
		}
		if(!empty($params['category'])){
			$query->where('category',$params['category']);
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
		if (!is_allowed('cms/publication/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->publicationModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		if (!is_allowed('cms/publication/create')) {
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

			$newPublicationId = $this->publicationModel->insert($save_data);
			if ($newPublicationId) {
				$this->session->setFlashdata('toastr_msg', lang('Publication.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Publication.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Publication.info.failed_saved')
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
		if (!is_allowed('cms/publication/update')) {
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

			$publicationUpdate = $this->publicationModel->update($id, $update_data);
			if ($publicationUpdate) {
				add_log('Ubah Histori Direktur', 'publication', 'edit', 't_publication', $id);
				$this->session->setFlashdata('toastr_msg', lang('Publication.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Publication.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Publication.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('cms/publication/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->publicationModel->find($id);
		if ($data) {
			$this->publicationModel->delete($id);
			add_log('Hapus Publication', 'publication', 'delete', 't_publication', $id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Publication.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Publication.info.not_found') . ' ID:' . $id);
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
		$publication = $this->publicationModel->find($upload_id);
		$publicationUpdate = $this->publicationModel->update($upload_id, $update_data);
		if ($publicationUpdate) {
			unlink_file($this->modulePath, $publication->file_image);
			unlink_file($this->modulePath, 'thumb_' . $publication->file_image);

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
