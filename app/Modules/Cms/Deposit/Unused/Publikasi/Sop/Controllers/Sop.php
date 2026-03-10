<?php

namespace DepositPublikasiSop\Controllers;

use \CodeIgniter\Files\File;
use DepositPublikasi\Sop\Models\SopModel;

class Sop extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $sopModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);


        $this->sopModel = new SopModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/deposit/publikasi/sop/';

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
        if (!is_allowed('sop/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->sopModel
            ->select('t_deposit_publikasi_sop.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_deposit_publikasi_sop.created_by', 'left')
            ->join('users updated', 'updated.id = t_deposit_publikasi_sop.updated_by', 'left');

        $sops = $query->findAll();

        $this->data['title'] = 'Sop';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['sops'] = $sops;
        echo view('DepositPublikasiSop\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('deposit/publikasi/sop/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Sop';

        $db      = \Config\Database::connect();
        $builder = $db->table('t_categories');
        $query = $builder->get();
        $this->data['t_categories'] = $query->getResultArray();


        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required'],
            'category' => ['label' => 'Category', 'rules' => 'required']
        
        ]);
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('title'), '-', TRUE);

            $save_data = $this->request->getPost();
            $save_data['slug'] = $slug;
            $save_data['created_by'] = user_id();
            $save_data['updated_by'] = user_id();

            if (is_member('admin')) {
                $channel = $this->request->getPost('channel');
                if (!empty($channel)) {
                    $save_data['channel'] = $channel;
                }
            } else {
                $group = get_group();
                $save_data['channel'] = $group->name;
            }

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
            
            $newSopId = $this->sopModel->insert($save_data);

            if ($newSopId) {
                add_log('Tambah Sop', 'sop', 'create', 't_deposit_publikasi_sop', $newSopId);
                set_message('toastr_msg', lang('Sop.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/publikasi/sop');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Sop.info.failed_saved'));
                echo view('DepositPublikasiSop\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('sop/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositPublikasiSop\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('sop/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Sop';
        $sop = $this->sopModel->find($id);
        $this->data['sop'] = $sop;

        $this->validation->setRule('name', 'Judul Sop', 'required');
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
                if (!empty($sop->file) && file_exists(FCPATH . 'uploads/rulesguid/' . $sop->file)) {
                    unlink(FCPATH . 'uploads/rules-guid/' . $sop->file);
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
                
                // Save the new file logic here.
                
                $sopUpdate = $this->sopModel->update($id, $update_data);

                if ($sopUpdate) {
                    add_log('Ubah Sop', 'sop', 'edit', 't_deposit_publikasi_sop', $id);
                    set_message('toastr_msg', 'Sop berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/deposit/publikasi/sop');
                } else {
                    set_message('toastr_msg', 'Sop gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Sop gagal diubah');
                    return redirect()->to('/deposit/publikasi/sop/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('sop/edit/' . $id);
        echo view('DepositPublikasiSop\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('sop/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/deposit/publikasi/sop');
        }
        $sop = $this->sopModel->find($id);
        $sopDelete = $this->sopModel->delete($id);
        if ($sopDelete) {
            unlink_file($this->modulePath, $sop->file);
            unlink_file($this->modulePath, 'thumb_' . $sop->file);

            add_log('Hapus Sop', 'sop', 'delete', 't_deposit_publikasi_sop', $id);
            set_message('toastr_msg', lang('Sop.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/deposit/publikasi/sop');
        } else {
            set_message('toastr_msg', lang('Sop.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Sop.info.failed_deleted'));
            return redirect()->to('/deposit/publikasi/sop/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $sopUpdate = $this->sopModel->update($id, array($field => $value));

        if ($sopUpdate) {
            set_message('toastr_msg', ' Sop berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Sop gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/publikasi/sop');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $sop = $this->sopModel->find($i);
            $newFileName = $sop->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
