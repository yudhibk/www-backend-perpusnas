<?php

namespace Sop\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\SopModel;
use CodeIgniter\Files\File;

class Sop extends ResourceController
{
	protected $sopModel;
	protected $validation;
	protected $session;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html', 'adminigniter']);
		$this->sopModel = new \Sop\Models\SopModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/rules-guide/';
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
		if (!is_allowed('deposit/pereaturan/sop/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->sopModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		if (!is_allowed('deposit/pereaturan/sop/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->sopModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		if (!is_allowed('deposit/pereaturan/sop/create')) {
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

			$newSopId = $this->sopModel->insert($save_data);
			if ($newSopId) {
				$this->session->setFlashdata('toastr_msg', lang('Sop.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Sop.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Sop.info.failed_saved')
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
		if (!is_allowed('deposit/pereaturan/sop/update')) {
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

			$rules_guideUpdate = $this->sopModel->update($id, $update_data);
			if ($rules_guideUpdate) {
				add_log('Ubah Peraturan SOP', 'sop', 'edit', 'sop', $id);
				$this->session->setFlashdata('toastr_msg', lang('Sop.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Sop.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Sop.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('deposit/pereaturan/sop/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
		}

		$data = $this->sopModel->find($id);
		if ($data) {
			$this->sopModel->delete($id);
			add_log('Hapus Sop', 'rules_guide', 'delete', 'rules_guide', $id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Sop.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Sop.info.not_found') . ' ID:' . $id);
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
		$rules_guide = $this->sopModel->find($upload_id);
		$rules_guideUpdate = $this->sopModel->update($upload_id, $update_data);
		if ($rules_guideUpdate) {
			unlink_file($this->modulePath, $rules_guide->file_image);
			unlink_file($this->modulePath, 'thumb_' . $rules_guide->file_image);

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
