<?php

namespace App\Modules\Backend\Notification\Controllers;

use \CodeIgniter\Files\File;

class Notification extends \App\Controllers\BackendController
{
    protected $auth;
    protected $modulePath;
    protected $uploadFile;
    protected $notificationModel;
    function __construct()
    {
        $this->notificationModel = new \App\Modules\Backend\Notification\Models\NotificationModel();
        $this->auth = new \App\Libraries\Auth();
        $this->modulePath = ROOTPATH . 'public/uploads/notification/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        if (!$this->auth->loggedIn()) {
            return redirect()->to('/user/login');
        }
    }
    public function index()
    {
        // if (!$this->rbac->check_operation_access()) {
        //     set_message('toastr_msg', lang('App.permission.not.have'));
        //     set_message('toastr_type', 'error');
        //     return redirect()->to('/home');
        // }
        $this->data['title'] = 'Notificationication';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['notifications'] = $this->notificationModel->where('to', get_user_id())->findAll();
        echo view(APPPATH.'Modules/Backend/Notification/Views/list', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$this->rbac->check_operation_access()) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        $notificationDelete = $this->notificationModel->delete($id);
        if ($notificationDelete) {
            add_log('Hapus notificationication', 'notificationication', 'delete', 't_notificationication', $id);
            set_message('toastr_msg', 'notificationication berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/notificationication');
        } else {
            set_message('toastr_msg', 'notificationication gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', $this->auth->errors());
            return redirect()->to('/notificationication/delete/' . $id);
        }
    }
}
