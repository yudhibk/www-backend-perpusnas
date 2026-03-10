<?php

namespace Access\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Group\Models\GroupModel;
use Permission\Models\PermissionModel;

class Access extends ResourceController
{
	use ResponseTrait;
	protected $auth;
    protected $authorize;
	protected $groupModel;
	protected $permissionModel;
	
	
	function __construct()
	{
		$this->session = session();
		
		$this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
		$this->groupModel = new GroupModel();
		$this->permissionModel = new PermissionModel();
		$this->validation = \Config\Services::validation();
		helper(['app','auth']);
	}

	public function add_to_group($group_id)
	{
		$response = false;
		$permissions = $this->permissionModel->findAll();
		foreach ($permissions as $permission){
			$this->authorize->removePermissionFromGroup($permission->name, $group_id);
		}
		$access = '';
		$new_permissions = $this->request->getPost('permissions');
		foreach($new_permissions as $permission){
			$permission = clean_fullscreen($permission);
			$exist_permission = $this->authorize->permission($permission);
			if(empty($exist_permission)){
				$this->authorize->createPermission($permission, '');
			}

			$access .= $permission. ',';
			$this->authorize->addPermissionToGroup($permission, $group_id);
		}
		add_log('Tambah Access<br>Group ID: '.$group_id.'<br>Access: '.$access,'access', 'create', 'auth_groups_permission', '');
		$this->session->setFlashdata('toastr_msg', 'Access berhasil diupdate');
		$this->session->setFlashdata('toastr_type', 'success');
		$response = [
			'status'   => 201,
			'error'    => null,
			'messages' => [
				'success' => 'Access berhasil diupdate'
			]
		];
		return $this->respondCreated($response);

	}
}
