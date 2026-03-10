<?php

namespace Peraturan\Controllers;

use \CodeIgniter\Files\File;
use Peraturan\Models\PeraturanModel;

class Peraturan extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $peraturanModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);


        $this->peraturanModel = new PeraturanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/peraturan/';

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
        if (!is_allowed('cms/peraturan/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->peraturanModel
            ->select('t_peraturan.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_peraturan.created_by', 'left')
            ->join('users updated', 'updated.id = t_peraturan.updated_by', 'left');
        
        if ($this->request->getGet('slug')) {
            $query->where('t_peraturan.category', $this->request->getGet('slug'));
        }

        $peraturans = $query->findAll();

        $this->data['title'] = 'Peraturan';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['peraturans'] = $peraturans;
        echo view('Peraturan\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/peraturan/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Peraturan';

        $this->validation->setRule('name', 'Judul Peraturan', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'category' => $this->request->getPost('category'),
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

            if (!empty($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['file'] = implode(',', $listed_file);
            }
            $newPeraturanId = $this->peraturanModel->insert($save_data);

            if ($newPeraturanId) {
                add_log('Tambah Peraturan', 'cms/peraturan', 'create', 't_peraturan', $newPeraturanId);
                set_message('toastr_msg', lang('Peraturan.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/peraturan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Peraturan.info.failed_saved'));
                echo view('Peraturan\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('cms/peraturan/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Peraturan\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('cms/peraturan/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Peraturan';
        $peraturan = $this->peraturanModel->find($id);
        $this->data['peraturan'] = $peraturan;

        $this->validation->setRule('name', 'Judul Peraturan', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'category' => $this->request->getPost('category'),
                    'description' => $this->request->getPost('description'),
                    'slug' => $this->request->getPost('slug') ?? $slug,
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
                $peraturanUpdate = $this->peraturanModel->update($id, $update_data);

                if ($peraturanUpdate) {
                    add_log('Ubah Peraturan', 'cms/peraturan', 'edit', 't_peraturan', $id);
                    set_message('toastr_msg', 'Peraturan berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/peraturan');
                } else {
                    set_message('toastr_msg', 'Peraturan gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Peraturan gagal diubah');
                    return redirect()->to('/cms/peraturan/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/peraturan/edit/' . $id);
        echo view('Peraturan\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/peraturan/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/cms/peraturan');
        }
        $peraturan = $this->peraturanModel->find($id);
        $peraturanDelete = $this->peraturanModel->delete($id);
        if ($peraturanDelete) {
            unlink_file($this->modulePath, $peraturan->file);
            unlink_file($this->modulePath, 'thumb_' . $peraturan->file);

            add_log('Hapus Peraturan', 'cms/peraturan', 'delete', 't_peraturan', $id);
            set_message('toastr_msg', lang('Peraturan.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/peraturan');
        } else {
            set_message('toastr_msg', lang('Peraturan.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Peraturan.info.failed_deleted'));
            return redirect()->to('/cms/peraturan/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $peraturanUpdate = $this->peraturanModel->update($id, array($field => $value));

        if ($peraturanUpdate) {
            set_message('toastr_msg', ' Peraturan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Peraturan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/peraturan');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $peraturan = $this->peraturanModel->find($i);
            $newFileName = $peraturan->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
