<?php

namespace User\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Group\Models\GroupModel;
use Myth\Auth\Entities\User as MythUser;
use \Hermawan\DataTables\DataTable;
use User\Models\UserModel;

class User extends ResourceController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $userModel;
	public $groupModel;
	public $validation;
	public $session;
	public $config;
	public $uploadPath;
	public $modulePath;
	public $password;
	public $request;

	function __construct()
	{
		$this->session = session();
		$this->request = \Config\Services::request();
		$this->validation = service('validation');
		$this->config = config('Auth');
		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->password = new \Myth\Auth\Password();

		$this->userModel = new \User\Models\UserModel();
		$this->groupModel = new \Group\Models\GroupModel();

		$this->modulePath = ROOTPATH . 'public/uploads/user/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
        helper('adminigniter');  

        helper(['form', 'url', 'auth', 'app']);
	}

	public function datatable()
	{
		$db = db_connect();
		$builder = $db->table('users as a')
			->select('a.id, a.id as action, a.username, a.email, concat(a.first_name, " " , a.last_name) as full_name, a.first_name, a.last_name, a.active, a.updated_at, a.id as group_id');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('first_name', function($row){
				$html = '<div class="widget-content p-0">
								<div class="widget-content-wrapper">
									<div class="widget-content-left mr-3">
										<i class="far fa-user-alt fa-2x text-secondary"></i>
									</div>
									<div class="widget-content-left">
										<div class="widget-heading">' . $row->full_name . '</div>
										<div class="widget-subheading">
											<b>' . $row->username . '</b><br>
											<i class="fa fa-envelope opacity-4"></i> ' . $row->email . '
										</div>
									</div>
								</div>
							</div>';

				return $html;
			})
			->edit('group_id', function($row){
				$groups = $this->userModel->getGroups($row->id);
				$html = '';
				foreach($groups as $group){
					$html .= '<span class="badge badge-pill badge-secondary">'.$group.'</span> ';
				}
				return $html;
			})
			->edit('active', function($row){
				$status = $row->active == 1 ? 'Aktif' : 'Non Aktif';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill">' . $status . '</span>';
				return $html;
			})
			->edit('updated_at', function($row){
				$html = '';
				$html .= '<span class="badge badge-pill badge-info">'.$row->updated_at.'</span>';
				return $html;
			})
			->edit('action', function($row){
				$detail = '<a href="'.base_url('user/detail/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Detail" class="btn btn-primary show-data"><i class="pe-7s-user font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('user/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success publish-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('user/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('user/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				$html = $detail .' '. $active .' '. $inactive .' '. $delete;

				return $html;
			})
			->toJson(true);

		return $dataTable;
	}

	public function view($id = null)
	{
		$data = $this->userModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}
	public function create()
	{
		// Validate here first, since some things,
		// like the password, can only be validated properly here.
		$rules = [
			'username'  	=> [
				'label' => 'Username',
				'rules' => 'required|alpha_dash|min_length[3]|is_unique[users.username]',
			],
			'email'			=> [
				'label' => 'Email',
				'rules' => 'required|valid_email|is_unique[users.email]',
			],
			'password'	 	=> [
				'label' => 'Password',
				'rules' => 'required',
			],
			'pass_confirm' 	=> [
				'label' => 'Konfirmasi Password',
				'rules' => 'required|matches[password]',
			]
		];

		if (!$this->validate($rules)) {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}

		// Save the user
		$users = model(\Myth\Auth\Models\UserModel::class);
		$group = $this->request->getPost('group') ?? $this->config->defaultUserGroup;
		$username = $this->request->getPost('username');
		$allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
		$save_data = $this->request->getPost($allowedPostFields);
		$user = new \Myth\Auth\Entities\User($save_data);
		$user->activate();
		$users = $users->withGroup($group);

		if (!$users->save($user)) {
			set_message('message', $this->session->getFlashdata('message'));
			return $this->fail('<div class="alert alert-danger fade show" role="alert">Tambah User gagal disimpan</div>', 400);
		} else {
			$update_data = $this->request->getPost($this->config->validFields);

			$db = db_connect('default');
			$builder = $db->table('users')->where('username', $username);
			$builder->update($update_data);

			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Tambah User berhasil disimpan'
				]
			];
			return $this->respond($response);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('username', 'Username', 'required');
		$this->validation->setRule('email', 'Email', 'required');
		if ($this->request->getPost('password')) {
			$this->validation->setRule('password', 'Password', 'required|min_length[' . $this->config->minimumPasswordLength . ']');
			$this->validation->setRule('pass_confirm', 'Konfirmasi Password', 'required|matches[password]');
		}

		if (is_member('admin')) {
			$this->validation->setRule('groups', 'Group', 'required');
		}

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$update_data = array(
				'first_name' => $this->request->getPost('first_name'),
				'last_name' => $this->request->getPost('last_name'),
				'phone' => $this->request->getPost('phone'),
				'unit' => $this->request->getPost('unit'),
				'company' => $this->request->getPost('company'),
				'address' => $this->request->getPost('address'),
				'coordinate' => $this->request->getPost('coordinate'),
			);

			if (!empty($this->request->getPost('branch_id'))) {
				$branch_id = $this->request->getPost('branch_id');
				$update_data['branch_id'] = $branch_id;
			}

			if ($this->request->getPost('password')) {
				$update_data['password_hash'] = $this->password->hash($this->request->getPost('password'));
				$update_data['reset_hash'] = null;
				$update_data['reset_at'] = null;
				$update_data['reset_expires'] = null;
			}

			$userUpdate = $this->userModel->update($id, $update_data);
			if ($userUpdate) {
				if (is_member('admin')) {
					$groups = $this->authorize->groups();
					foreach ($groups as $group) {
						$this->authorize->removeUserFromGroup($id, $group->id);
					}

					$group_ids = $this->request->getPost('groups');
					foreach ($group_ids as $group_id) {
						$this->authorize->addUserToGroup($id, $group_id);
					}
				}

				$this->session->setFlashdata('toastr_msg', 'Profil User berhasil disimpan');
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => 'Profil User berhasil disimpan'
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">Profil User gagal disimpan</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
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
                }
            }
            $update_data[$upload_field] = implode(',', $listed_file);
        }

        // print_r($update_data);
        // dd('');

        $userModel = new \user\Models\userModel();
        $updateuser = $userModel->update($upload_id,$update_data);
        if ($updateuser) {
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

	public function delete($id = null)
	{
		try {
            $data = $this->userModel->find($id);
			if ($data) {
				$delete = $this->userModel->delete($id);
				$response = array(
					'error'    => false,
					'message' => 'Data deleted successfully'
				);
			} else {
				$response = array(
					'error'    => true,
					'message' => 'Could not find data for specified ID' . $id,
				);
			}
        } catch (Exception $e) {
			$response = array(
				'error'    => true,
				'message' => $e->getMessage()
			);
        }
		return $this->simpleResponse($response);
	}
}
