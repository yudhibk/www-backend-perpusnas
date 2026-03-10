<?php

namespace DepositPeraturanSop\Controllers;
use DepositPeraturan\Sop\Models\SopModel;
use \CodeIgniter\Files\File;

// use DepositPublikasi\Sop\Models\SopModel as ModelsSopModel;

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
        $this->modulePath = ROOTPATH . 'public/uploads/rules-guide/';

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
        if (!is_allowed('deposit/peraturan/sop/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->sopModel
            ->select('rules_guide.*')
            ->where('category', 2)
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->select('categories.name as category_name')
            ->join('t_categories categories', 'categories.id = rules_guide.category', 'left')
            ->join('users created', 'created.id = rules_guide.created_by', 'left')
            ->join('users updated', 'updated.id = rules_guide.updated_by', 'left');

        $peraturan_sops = $query->findAll();

        // dd($rules_guides);

        $this->data['title'] = 'Peraturan SOP';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['peraturan_sops'] = $peraturan_sops;
        echo view('DepositPeraturanSop\Views\list', $this->data);
    }
    public function create()
    {
        if (!is_allowed('deposit/peraturan/sop/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Peraturan SOP';

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
                add_log('Tambah Peraturan SOP', 'sop', 'create', 'rules_guide', $newSopId);
                set_message('toastr_msg', lang('Sop.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/peraturan/sop');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Sop.info.failed_saved'));
                echo view('DepositPeraturanSop\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('deposit/peraturan/sop/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositPeraturanSop\Views\add', $this->data);
        }
    }
    public function edit($id = null)
{
    if (!is_allowed('deposit/peraturan/sop/edit')) {
        set_message('toastr_msg', lang('App.permission.not.have'));
        set_message('toastr_type', 'error');
        return redirect()->to('/dashboard');
    }

    $this->data['title'] = 'Edit Peraturan SOP';

    $db = \Config\Database::connect();
    $builder = $db->table('t_categories');
    $query = $builder->get();
    $this->data['t_categories'] = $query->getResultArray();

    // Ambil data SOP berdasarkan ID
    $sopData = $this->sopModel->find($id);
    if (!$sopData) {
        set_message('toastr_msg', lang('Sop.info.not_found'));
        set_message('toastr_type', 'error');
        return redirect()->to('/deposit/peraturan/sop');
    }

    $this->data['sop'] = $sopData;

    $this->validation->setRules([
        'title' => ['label' => 'Title', 'rules' => 'required'],
        'category' => ['label' => 'Category', 'rules' => 'required']
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
                if (!empty($sopData->file)) {
                    $oldFilePath = $this->modulePath . $sopData->file;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                        // Delete thumbnail if exists
                        $thumbPath = $this->modulePath . 'thumb_' . $sopData->file;
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

            // Update data ke database
            $isUpdated = $this->sopModel->update($id, $update_data);

            if ($isUpdated) {
                add_log('Edit Peraturan SOP', 'sop', 'edit', 'rules_guide', $id);
                set_message('toastr_msg', lang('Sop.info.successfully_updated'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/peraturan/sop');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Sop.info.failed_updated'));
                echo view('DepositPeraturanSop\Views\update', $this->data);
            }
        }
    }

    $this->data['redirect'] = base_url("deposit/peraturan/sop/edit/$id");
    set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
    echo view('DepositPeraturanSop\Views\update', $this->data);
}
    

    public function delete(int $id = 0)
    {
        if (!is_allowed('deposit/peraturan/sop/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/cms/rules-guide');
        }
        $rules_guide = $this->sopModel->find($id);
        $rules_guideDelete = $this->sopModel->delete($id);
        if ($rules_guideDelete) {
            unlink_file($this->modulePath, $rules_guide->file);
            unlink_file($this->modulePath, 'thumb_' . $rules_guide->file);

            add_log('Hapus Peraturan SOP', 'sop', 'delete', 'rules_guide', $id);
            set_message('toastr_msg', lang('Sop.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/rules-guide');
        } else {
            set_message('toastr_msg', lang('Sop.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Sop.info.failed_deleted'));
            return redirect()->to('deposit/peraturan/sop/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $rules_guideUpdate = $this->sopModel->update($id, array($field => $value));

        if ($rules_guideUpdate) {
            set_message('toastr_msg', ' Sop berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Sop gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/peraturan/sop');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $rules_guide = $this->sopModel->find($i);
            $newFileName = $rules_guide->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
