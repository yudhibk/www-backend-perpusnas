<?php

namespace Page\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PageModel;
use CodeIgniter\Files\File;

class Page extends ResourceController
{
	protected $pageModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
		$this->pageModel = new \Page\Models\PageModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/page/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
		helper('reference');
	}

	public function index()
	{
		if (!is_allowed('page/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$data = $this->pageModel->findAll();
		return $this->respond($data, 200);
	}

	public function detail($id = null)
	{
		if (!is_allowed('page/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$data = $this->pageModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		if (!is_allowed('page/create')) {
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

			$newPageId = $this->pageModel->insert($save_data);
			if ($newPageId) {
				$this->session->setFlashdata('toastr_msg', lang('Page.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Page.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Page.info.failed_saved')
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
		if (!is_allowed('page/update')) {
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

			$pageUpdate = $this->pageModel->update($id, $update_data);
			if ($pageUpdate) {
				add_log('Ubah Page', 'page', 'edit', 't_page', $id);
				$this->session->setFlashdata('toastr_msg', lang('Page.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Page.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">'.lang('Page.info.failed_updated').'</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('page/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$data = $this->pageModel->find($id);
		if ($data) {
			$this->pageModel->delete($id);
			add_log('Hapus Page', 'page', 'delete', 't_page', $id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Page.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Page.info.not_found').' ID:' . $id);
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

					if($upload_field == 'file_image'){
						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
                }
            }
            $update_data[$upload_field] = implode(',', $listed_file);
        }

        $page = $this->pageModel->find($upload_id);
        $pageUpdate = $this->pageModel->update($upload_id,$update_data);
        if ($pageUpdate) {
			unlink_file($this->modulePath, $page->file_image);
			unlink_file($this->modulePath, 'thumb_'.$page->file_image);
			unlink_file($this->modulePath, $page->file_pdf);

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