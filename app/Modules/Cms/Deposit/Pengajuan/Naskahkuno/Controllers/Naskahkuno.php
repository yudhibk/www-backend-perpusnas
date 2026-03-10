<?php

namespace DepositPengajuanNaskahkuno\Controllers;

use \CodeIgniter\Files\File;

class Naskahkuno extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $naskahkunoModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        $this->naskahkunoModel = new \DepositPengajuanNaskahkuno\Models\NaskahkunoModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/deposit/pengajuan/naskah-kuno/';
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
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);
        helper('adminigniter');
        helper('thumbnail');
        helper('reference');
    }
    public function index()
    {
        if (!is_allowed('deposit/pengajuan/naskah-kuno/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->naskahkunoModel
            ->select('t_deposit_pengajuan_naskah_kuno.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_deposit_pengajuan_naskah_kuno.created_by', 'left')
            ->join('users updated', 'updated.id = t_deposit_pengajuan_naskah_kuno.updated_by', 'left');

        $naskahkunos = $query->findAll();

        $this->data['title'] = 'Pengajuan Naskah Kuno';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['naskahkunos'] = $naskahkunos;
        echo view('DepositPengajuanNaskahkuno\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('deposit/pengajuan/naskahkuno/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Pengajuan Naskah Kuno';

        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required'],
        
        ]);
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('title'), '-', TRUE);

            $save_data = $this->request->getPost();
           
            $save_data['slug'] = $slug;
            $save_data['created_by'] = user_id();
            $save_data['updated_by'] = user_id();

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
            $newNaskahkunoId = $this->naskahkunoModel->insert($save_data);

            if ($newNaskahkunoId) {
                add_log('Tambah Pengajuan Naskah Kuno', 'naskahkuno', 'create', 't_deposit_pengajuan_naskah_kuno', $newNaskahkunoId);
                set_message('toastr_msg', lang('Naskahkuno.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/pengajuan/naskah-kuno');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Naskahkuno.info.failed_saved'));
                echo view('DepositPengajuanNaskahkuno\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('deposit/pengajuan/naskah-kuno/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositPengajuanNaskahkuno\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('deposit/pengajuan/naskahkuno/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Pengajuan Naskah Kuno';
        $naskahkuno = $this->naskahkunoModel->find($id);
        $this->data['naskahkuno'] = $naskahkuno;
        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required']
        ]);
    
        if ($this->request->getMethod() === 'post') {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('title'), '-', true);
    
                $update_data = $this->request->getPost();
                $update_data['slug'] = $slug;
                $update_data['updated_by'] = user_id();
    
                if (is_member('admin')) {
                    $channel = $this->request->getPost('channel');
                    if (!empty($channel)) {
                        $update_data['channel'] = $channel;
                    }
                } else {
                    $group = get_group();
                    $update_data['channel'] = $group->name;
                }
    
                // Handle file upload
                $file = $this->request->getFile('file');
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Delete old file if exists
                    if (!empty($naskahkuno->file)) {
                        $oldFilePath = $this->modulePath . $naskahkuno->file;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                            // Delete thumbnail if exists
                            $thumbPath = $this->modulePath . 'thumb_' . $naskahkuno->file;
                            if (file_exists($thumbPath)) {
                                unlink($thumbPath);
                            }
                        }
                    }
    
                    // Generate new filename
                    $newFileName = $file->getRandomName();
                    
                    // Move file to destination
                    $file->move($this->modulePath, $newFileName);
                    
                    // Create thumbnail if it's an image
                    $ext = $file->getClientExtension();
                    if (!in_array($ext, ['pdf', 'xlsx', 'xls', 'docx', 'doc'])) {
                        create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                    }
                    
                    $update_data['file'] = $newFileName;
                }
                $naskahkunoUpdate = $this->naskahkunoModel->update($id, $update_data);

                if ($naskahkunoUpdate) {
                    add_log('Ubah Pengajuan Naskah Kuno', 'naskahkuno', 'edit', 't_deposit_pengajuan_naskah_kuno', $id);
                    set_message('toastr_msg', 'Naskahkuno berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/deposit/pengajuan/naskah-kuno');
                } else {
                    set_message('toastr_msg', 'Naskahkuno gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Naskahkuno gagal diubah');
                    return redirect()->to('/deposit/pengajuan/naskah-kuno/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('deposit/pengajuan/naskah-kuno/edit/' . $id);
        echo view('DepositPengajuanNaskahkuno\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('deposit/pengajuan/naskahkuno/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/deposit/pengajuan/naskah-kuno');
        }
        $naskahkuno = $this->naskahkunoModel->find($id);
        $naskahkunoDelete = $this->naskahkunoModel->delete($id);
        if ($naskahkunoDelete) {
            unlink_file($this->modulePath, $naskahkuno->file);
            unlink_file($this->modulePath, 'thumb_' . $naskahkuno->file);

            add_log('Hapus Pengajuan Naskah Kuno', 'naskahkuno', 'delete', 't_deposit_pengajuan_naskah_kuno', $id);
            set_message('toastr_msg', lang('Naskahkuno.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/deposit/pengajuan/naskah-kuno');
        } else {
            set_message('toastr_msg', lang('Naskahkuno.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Naskahkuno.info.failed_deleted'));
            return redirect()->to('/deposit/pengajuan/naskah-kuno/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $naskahkunoUpdate = $this->naskahkunoModel->update($id, array($field => $value));

        if ($naskahkunoUpdate) {
            set_message('toastr_msg', ' Naskahkuno berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Naskahkuno gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/pengajuan/naskah-kuno');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $naskahkuno = $this->naskahkunoModel->find($i);
            $newFileName = $naskahkuno->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
