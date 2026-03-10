<?php

namespace Historidirektur\Controllers;

use \CodeIgniter\Files\File;

class Historidirektur extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $historidirekturModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);
        $this->historidirekturModel = new \Historidirektur\Models\HistoridirekturModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/histori-direktur/';

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
        if (!is_allowed('cms/histori-direktur/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->historidirekturModel
            ->select('t_profil_histori_direktur.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_profil_histori_direktur.created_by', 'left')
            ->join('users updated', 'updated.id = t_profil_histori_direktur.updated_by', 'left');

        $historidirekturs = $query->findAll();

        $this->data['title'] = 'Direktur Masa ke Masa';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['historidirekturs'] = $historidirekturs;
        echo view('Historidirektur\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/histori-direktur/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Direktur Masa ke Masa';

        $this->validation->setRule('name', 'Judul Direktur Masa ke Masa', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'awal_menjabat' => $this->request->getPost('awal_menjabat'),
                'akhir_menjabat' => $this->request->getPost('akhir_menjabat'),
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
            $newHistoridirekturId = $this->historidirekturModel->insert($save_data);

            if ($newHistoridirekturId) {
                add_log('Tambah Direktur Masa ke Masa', 'historidirektur', 'create', 't_deposit_profil_histori_direktur', $newHistoridirekturId);
                set_message('toastr_msg', lang('Historidirektur.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/histori-direktur');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Historidirektur.info.failed_saved'));
                echo view('Historidirektur\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('cms/histori-direktur/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Historidirektur\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('cms/histori-direktur/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Direktur Masa ke Masa';
        $historidirektur = $this->historidirekturModel->find($id);
        $this->data['historidirektur'] = $historidirektur;

        $this->validation->setRule('name', 'Judul Direktur Masa ke Masa', 'required');
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
                $historidirekturUpdate = $this->historidirekturModel->update($id, $update_data);

                if ($historidirekturUpdate) {
                    add_log('Ubah Direktur Masa ke Masa', 'historidirektur', 'edit', 't_deposit_profil_histori_direktur', $id);
                    set_message('toastr_msg', 'Historidirektur berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/histori-direktur');
                } else {
                    set_message('toastr_msg', 'Historidirektur gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Historidirektur gagal diubah');
                    return redirect()->to('/cms/histori-direktur/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/histori-direktur/edit/' . $id);
        echo view('Historidirektur\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/histori-direktur/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/cms/histori-direktur');
        }
        $historidirektur = $this->historidirekturModel->find($id);
        $historidirekturDelete = $this->historidirekturModel->delete($id);
        if ($historidirekturDelete) {
            unlink_file($this->modulePath, $historidirektur->file);
            unlink_file($this->modulePath, 'thumb_' . $historidirektur->file);

            add_log('Hapus Direktur Masa ke Masa', 'historidirektur', 'delete', 't_deposit_profil_histori_direktur', $id);
            set_message('toastr_msg', lang('Historidirektur.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/histori-direktur');
        } else {
            set_message('toastr_msg', lang('Historidirektur.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Historidirektur.info.failed_deleted'));
            return redirect()->to('/cms/histori-direktur/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $historidirekturUpdate = $this->historidirekturModel->update($id, array($field => $value));

        if ($historidirekturUpdate) {
            set_message('toastr_msg', ' Historidirektur berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Historidirektur gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/histori-direktur');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $historidirektur = $this->historidirekturModel->find($i);
            $newFileName = $historidirektur->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
