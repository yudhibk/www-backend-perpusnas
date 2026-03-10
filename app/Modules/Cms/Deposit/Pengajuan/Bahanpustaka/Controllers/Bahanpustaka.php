<?php

namespace DepositPengajuanBahanpustaka\Controllers;

use \CodeIgniter\Files\File;

class Bahanpustaka extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $bahanpustakaModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        $this->bahanpustakaModel = new \DepositPengajuanBahanpustaka\Models\BahanpustakaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/deposit/pengajuan/bahan-pustaka/';
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
        if (!is_allowed('deposit/pengajuan/bahan-pustaka/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->bahanpustakaModel
            ->select('t_deposit_pengajuan_bahan_pustaka.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_deposit_pengajuan_bahan_pustaka.created_by', 'left')
            ->join('users updated', 'updated.id = t_deposit_pengajuan_bahan_pustaka.updated_by', 'left');

        $bahanpustakas = $query->findAll();

        $this->data['title'] = 'Pengajuan Bahan Pustaka';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['bahanpustakas'] = $bahanpustakas;
        echo view('DepositPengajuanBahanpustaka\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('deposit/pengajuan/bahanpustaka/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Pengajuan Bahan Pustaka';

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
            $newBahanpustakaId = $this->bahanpustakaModel->insert($save_data);

            if ($newBahanpustakaId) {
                add_log('Tambah Pengajuan Bahan Pustaka', 'bahanpustaka', 'create', 't_deposit_pengajuan_bahan_pustaka', $newBahanpustakaId);
                set_message('toastr_msg', lang('Bahanpustaka.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/pengajuan/bahan-pustaka');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Bahanpustaka.info.failed_saved'));
                echo view('DepositPengajuanBahanpustaka\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('deposit/pengajuan/bahan-pustaka/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositPengajuanBahanpustaka\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('deposit/pengajuan/bahanpustaka/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Pengajuan Bahan Pustaka';
        $bahanpustaka = $this->bahanpustakaModel->find($id);
        $this->data['bahanpustaka'] = $bahanpustaka;
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
                    if (!empty($bahanpustaka->file)) {
                        $oldFilePath = $this->modulePath . $bahanpustaka->file;
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                            // Delete thumbnail if exists
                            $thumbPath = $this->modulePath . 'thumb_' . $bahanpustaka->file;
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
                $bahanpustakaUpdate = $this->bahanpustakaModel->update($id, $update_data);

                if ($bahanpustakaUpdate) {
                    add_log('Ubah Pengajuan Bahan Pustaka', 'bahanpustaka', 'edit', 't_deposit_pengajuan_bahan_pustaka', $id);
                    set_message('toastr_msg', 'Bahanpustaka berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/deposit/pengajuan/bahan-pustaka');
                } else {
                    set_message('toastr_msg', 'Bahanpustaka gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Bahanpustaka gagal diubah');
                    return redirect()->to('/deposit/pengajuan/bahan-pustaka/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('deposit/pengajuan/bahan-pustaka/edit/' . $id);
        echo view('DepositPengajuanBahanpustaka\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('deposit/pengajuan/bahanpustaka/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/deposit/pengajuan/bahan-pustaka');
        }
        $bahanpustaka = $this->bahanpustakaModel->find($id);
        $bahanpustakaDelete = $this->bahanpustakaModel->delete($id);
        if ($bahanpustakaDelete) {
            unlink_file($this->modulePath, $bahanpustaka->file);
            unlink_file($this->modulePath, 'thumb_' . $bahanpustaka->file);

            add_log('Hapus Pengajuan Bahan Pustaka', 'bahanpustaka', 'delete', 't_deposit_pengajuan_bahan_pustaka', $id);
            set_message('toastr_msg', lang('Bahanpustaka.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/deposit/pengajuan/bahan-pustaka');
        } else {
            set_message('toastr_msg', lang('Bahanpustaka.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Bahanpustaka.info.failed_deleted'));
            return redirect()->to('/deposit/pengajuan/bahan-pustaka/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $bahanpustakaUpdate = $this->bahanpustakaModel->update($id, array($field => $value));

        if ($bahanpustakaUpdate) {
            set_message('toastr_msg', ' Bahanpustaka berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Bahanpustaka gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/pengajuan/bahan-pustaka');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $bahanpustaka = $this->bahanpustakaModel->find($i);
            $newFileName = $bahanpustaka->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
