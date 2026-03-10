<?php

namespace Tentang\Controllers;

use \CodeIgniter\Files\File;
use Tentang\Models\TentangModel;

class Tentang extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $tentangModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);


        $this->tentangModel = new TentangModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/tentang/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath, 0777, true);
        }

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();


        if (! $this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }

        helper('adminigniter');
        helper('thumbnail');
        helper('reference');
    }
    public function index()
    {
        if (!is_allowed('cms/tentang/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->tentangModel
            ->select('t_tentang.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_tentang.created_by', 'left')
            ->join('users updated', 'updated.id = t_tentang.updated_by', 'left');

        $tentangs = $query->findAll();

        $this->data['title'] = 'Tentang';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['tentangs'] = $tentangs;
        echo view('Tentang\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/tentang/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Tentang';

        $this->validation->setRule('name', 'Judul Tentang', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'slug' => $slug,
                'created_by' => user_id(),
            ];
            if (is_member('admin')) {
                $channel = $this->request->getPost('channel');
                if (!empty($channel)) {
                    $save_data['channel'] = $channel;
                }
            } else {
                $group = get_group();
                $save_data['channel'] = $group->name;
            }

            // Logic Upload
            $files = $this->request->getPost('file');

            if (count($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $ext = ($file->getExtension());
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;

                        if ($ext != 'pdf' and $ext != 'xlsx' and $ext != 'xls' and $ext != 'docx' and $ext != 'doc') create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                    }
                }
                $save_data['file'] = implode(',', $listed_file);
            }
            $newTentangId = $this->tentangModel->insert($save_data);

            if ($newTentangId) {
                add_log('Tambah Tentang', 'tentang', 'create', 't_tentang', $newTentangId);
                set_message('toastr_msg', lang('Tentang.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/tentang');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Tentang.info.failed_saved'));
                echo view('Tentang\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('cms/tentang/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Tentang\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('cms/tentang/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Tentang';
        $tentang = $this->tentangModel->find($id);
        $this->data['tentang'] = $tentang;

        $this->validation->setRule('name', 'Judul Tentang', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $this->request->getPost('slug') ?? $slug,
                    'description' => $this->request->getPost('description'),
                    'file' => $this->request->getPost('file'),
                    'updated_by' => user_id(),
                ];
                if (is_member('admin')) {
                    $channel = $this->request->getPost('channel');
                    if (!empty($channel)) {
                        $update_data['channel'] = $channel;
                    }
                }

                // Logic Upload
                $files = (array) $this->request->getPost('file');
                if ($files) {
                    if (count($files)) {
                        $listed_file = array();
                        foreach ($files as $uuid => $name) {
                            if (file_exists($this->modulePath . $name)) {
                                $listed_file[] = $name;
                            } else {
                                if (file_exists($this->uploadPath . $name)) {
                                    $file = new File($this->uploadPath . $name);
                                    $newFileName = $file->getRandomName();
                                    $file->move($this->modulePath, $newFileName);
                                    $listed_file[] = $newFileName;
                                }
                            }
                        }
                        $update_data['file'] = implode(',', $listed_file);
                    }
                }
                $tentangUpdate = $this->tentangModel->update($id, $update_data);

                if ($tentangUpdate) {
                    add_log('Ubah Tentang', 'tentang', 'edit', 't_tentang', $id);
                    set_message('toastr_msg', 'Tentang berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/tentang');
                } else {
                    set_message('toastr_msg', 'Tentang gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Tentang gagal diubah');
                    return redirect()->to('/cms/tentang/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/tentang/edit/' . $id);
        echo view('Tentang\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/tentang/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/cms/tentang');
        }
        $tentang = $this->tentangModel->find($id);
        $tentangDelete = $this->tentangModel->delete($id);
        if ($tentangDelete) {
            unlink_file($this->modulePath, $tentang->file);
            unlink_file($this->modulePath, 'thumb_' . $tentang->file);

            add_log('Hapus Tentang', 'tentang', 'delete', 't_tentang', $id);
            set_message('toastr_msg', lang('Tentang.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/tentang');
        } else {
            set_message('toastr_msg', lang('Tentang.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Tentang.info.failed_deleted'));
            return redirect()->to('/cms/tentang/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $tentangUpdate = $this->tentangModel->update($id, array($field => $value));

        if ($tentangUpdate) {
            set_message('toastr_msg', ' Tentang berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Tentang gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/tentang');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $tentang = $this->tentangModel->find($i);
            $newFileName = $tentang->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
