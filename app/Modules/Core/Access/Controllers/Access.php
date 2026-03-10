<?php

namespace Access\Controllers;

use Group\Models\GroupModel;
use Menu\Models\MenuModel;
use Permission\Models\PermissionModel;

class Access extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $permissionModel;
    protected $groupModel;
    protected $menuModel;
    function __construct()
    {
        $this->permissionModel = new PermissionModel();
        $this->groupModel = new GroupModel();
        $this->menuModel = new MenuModel();
        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        if (! $this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }
    }

    public function index()
    {
        if (!is_admin()) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $keyword = $this->request->getVar('keyword');
        $group_id = $this->request->getVar('group_id') ?? 1;
        $group = $this->groupModel->find($group_id);
        $this->data['group'] = $group;

        $permissions = $this->groupModel->getPermissions($group_id);
        $access = array();
        foreach ($permissions as $permission) {
            $access[] = $permission['name'];
        }

        $query = $this->menuModel->where('parent', '0')->where('category_id', '1');
        if (!empty($keyword)) {
            $query->groupStart();
            $query->like('name', $keyword);
            $query->orLike('controller', $keyword);
            $query->groupEnd();
        }
        $groups = $this->groupModel->findAll();
        $menus = $query->findAll();
        $this->data['title'] = 'Access';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['groups'] = $groups;
        $this->data['menus'] = $menus;
        $this->data['access'] = $access;
        $this->data['group'] = $group;
        echo view('Access\Views\list', $this->data);
    }
}
