<?php

namespace RulesGuide\Controllers;

use \CodeIgniter\Files\File;

class RulesGuide extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $rulesGuideModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);
        $this->rulesGuideModel = new \RulesGuide\Models\RulesGuideModel();
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
        if (!is_allowed('cms/rules-guide/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->rulesGuideModel
            ->select('rules_guide.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->select('categories.name as category_name')
            ->join('t_categories categories', 'categories.id = rules_guide.category', 'left')
            ->join('users created', 'created.id = rules_guide.created_by', 'left')
            ->join('users updated', 'updated.id = rules_guide.updated_by', 'left');

        $rules_guides = $query->findAll();

        // dd($rules_guides);

        $this->data['title'] = 'Rules Guide';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['rules_guides'] = $rules_guides;
        echo view('RulesGuide\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/rules-guide/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Rules Guide';

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
            

            $newRulesGuideId = $this->rulesGuideModel->insert($save_data);

            if ($newRulesGuideId) {
                add_log('Tambah Rules Guide', 'rules_guide', 'create', 'rules_guide', $newRulesGuideId);
                set_message('toastr_msg', lang('RulesGuide.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/rules-guide');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('RulesGuide.info.failed_saved'));
                echo view('RulesGuide\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('cms/rules-guide/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('RulesGuide\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('cms/rules-guide/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Rules Guide';
        $rules_guide = $this->rulesGuideModel->find($id);
        $this->data['rules_guide'] = $rules_guide;
        $db      = \Config\Database::connect();
        $builder = $db->table('t_categories');
        $query = $builder->get();
        $this->data['t_categories'] = $query->getResultArray();

        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required'],
            'category' => ['label' => 'Category', 'rules' => 'required'],
        ]);
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('title'), '-', TRUE);

                $updated_data = $this->request->getPost();
                $updated_data['slug'] = $slug;
                $updated_data['created_by'] = user_id();
                $updated_data['updated_by'] = user_id();

                $rules_guideUpdate = $this->rulesGuideModel->update($id, $updated_data);

                if ($rules_guideUpdate) {
                    add_log('Ubah Rules Guide', 'rules_guide', 'edit', 'rules_guide', $id);
                    set_message('toastr_msg', 'RulesGuide berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/rules-guide');
                } else {
                    set_message('toastr_msg', 'RulesGuide gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'RulesGuide gagal diubah');
                    return redirect()->to('/cms/rules-guide/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/rules-guide/edit/' . $id);
        echo view('RulesGuide\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/rules-guide/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/cms/rules-guide');
        }
        $rules_guide = $this->rulesGuideModel->find($id);
        $rules_guideDelete = $this->rulesGuideModel->delete($id);
        if ($rules_guideDelete) {
            unlink_file($this->modulePath, $rules_guide->file);
            unlink_file($this->modulePath, 'thumb_' . $rules_guide->file);

            add_log('Hapus Rules Guide', 'rules_guide', 'delete', 'rules_guide', $id);
            set_message('toastr_msg', lang('RulesGuide.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/rules-guide');
        } else {
            set_message('toastr_msg', lang('RulesGuide.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('RulesGuide.info.failed_deleted'));
            return redirect()->to('/cms/rules-guide/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $rules_guideUpdate = $this->rulesGuideModel->update($id, array($field => $value));

        if ($rules_guideUpdate) {
            set_message('toastr_msg', ' RulesGuide berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' RulesGuide gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/rules-guide');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $rules_guide = $this->rulesGuideModel->find($i);
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
