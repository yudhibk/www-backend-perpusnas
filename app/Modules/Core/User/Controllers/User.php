<?php

namespace User\Controllers;

use Group\Models\GroupModel;
use User\Models\UserModel;

class User extends \App\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $userModel;
    public $groupModel;
    public $password;

    function __construct()
    {
        $this->userModel = new \User\Models\UserModel();
        $this->groupModel = new \Group\Models\GroupModel();

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        $this->password = new \Myth\Auth\Password();

        helper(['adminigniter', 'reference', 'bmn']);
    }

    public function index()
    {
        if (!is_allowed('user/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Users';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('User\Views\list', $this->data);
    }

    public function profile()
    {
        $this->detail(user_id(), true);
    }
    public function detail(int $id, $is_profile = false)
    {
        $this->data['title'] = ($is_profile) ? 'Profil Saya' : 'Detail User';
        $user = $this->userModel->find($id);
        $groups = $this->authorize->groups();
        $currentGroups = $this->userModel->getGroups($id);

        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;
        $this->data['is_profile'] = $is_profile;

        echo view('User\Views\profile', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('user/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $userDelete = $this->userModel->delete($id);
        if ($userDelete) {
            add_log('Hapus User', 'user', 'delete', 'auth_users', $id);
            set_message('toastr_msg', 'User berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User failed to delete');
            set_message('toastr_type', 'warning');
            set_message('message', 'Error');
            return redirect()->to('/user');
        }
    }

    public function enable(int $id, string $code = '')
    {
        if (!is_allowed('user/enable')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $activation = false;
        if ($code) {
            $activation = $this->userModel->update($id, array('active' => 1));
        } else if (is_admin()) {
            $activation = $this->userModel->update($id, array('active' => 1));
        }

        if ($activation) {
            set_message('toastr_msg', 'User berhasil diaktifkan');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User gagal diaktifkan');
            set_message('toastr_type', 'warning');
            set_message('message', 'User gagal diaktifkan');
            return redirect()->to('/user');
        }
    }

    public function disable(int $id)
    {
        if (!is_allowed('user/disable')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $deactivation = false;
        if (is_admin()) {
            $deactivation = $this->userModel->update($id, array('active' => 0));
        }

        if ($deactivation) {
            set_message('toastr_msg', 'User berhasil dinonaktifkan');
            set_message('toastr_type', 'success');
            return redirect()->to('/user');
        } else {
            set_message('toastr_msg', 'User gagal dinonaktifkan');
            set_message('toastr_type', 'warning');
            set_message('message', 'User gagal dinonaktifkan');
            return redirect()->to('/user');
        }
    }

    public function change_password()
    {
        $this->data['title'] = 'Change Password';
        $this->validation->setRule('password_old', 'Password Lama', 'required');
        $this->validation->setRule('password', 'Password', 'required|min_length[8]|max_length[15]|regex_match[/^(?=.*[A-Z])(?=.*[!@#%])(?=.*[0-9])(?=.*[a-z]).{8,15}$/]');
        $this->validation->setRule('password_confirm', 'Konfirmasi Password', 'required|matches[password]');
        $user = user();
        if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
            $this->data['message'] = $this->validation->listErrors();
            echo view('User\Views\password\change', $this->data);
        } else {
            $username      = $this->session->get('username');
            $logged_in     = $this->session->get('logged_in');
            $password_hash = $this->password->hash($this->request->getPost('password'));
            $change        = $this->userModel->update($logged_in, array('password_hash' => $password_hash));
            if ($change) {
                session()->set('password', $this->request->getPost('password')); // update dulu passwordnya
                set_message('toastr_msg', 'Password berhasil disimpan');
                set_message('toastr_type', 'success');
                return redirect()->back();
            } else {
                set_message('toastr_msg', 'Password gagal disimpan');
                set_message('toastr_type', 'warning');
                return redirect()->back();
            }
        }
    }

    public function change_avatar()
    {
        $this->data['title'] = 'Change Avatar';

        $user = user();
        $this->data['user'] = $user;

        $this->validation->setRule('file_image', 'Gambar', 'required');

        if (!$this->request->getPost() || $this->validation->withRequest($this->request)->run() === false) {
            $this->data['message'] = ($this->validation->getErrors()) ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
            echo view('User\Views\profile\change', $this->data);
        } else {
            $update_data = array();
            // Logic Upload
            $files = (array) $this->request->getPost('file_image');
            if (count($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->modulePath . $name)) {
                        $listed_file[] = $name;
                    } else {
                        if (file_exists($this->uploadPath . $name)) {
                            $file = new File($this->uploadPath . $name);
                            $newFileName = $file->getFileName(); //$file->getRandomName();
                            $file->move($this->modulePath, $newFileName);
                            $listed_file[] = $newFileName;
                        }
                    }
                }
                $update_data['file_image'] = implode(',', $listed_file);
            }
            $change = $this->userModel->update($user->id, $update_data);

            if ($change) {
                set_message('toastr_msg', 'Avatar berhasil diubah');
                set_message('toastr_type', 'success');
                return redirect()->to('/user/change_avatar');
            } else {
                $this->data['message'] = $this->auth->errors();
                echo view('User\Views\profile\change', $this->data);
            }
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $userUpdate = $this->userModel->update($id, array($field => $value));

        if ($userUpdate) {
            set_message('toastr_msg', ' User berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' User gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/user');
    }
}
