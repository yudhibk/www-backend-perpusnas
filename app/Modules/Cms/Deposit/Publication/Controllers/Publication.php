<?php

namespace DepositPublication\Controllers;
use DepositPublication\Models\PublicationModel;

use \CodeIgniter\Files\File;

class Publication extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $publicationModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);
        $this->publicationModel = new PublicationModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/deposit/publication/';

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
        if (!is_allowed('deposit/publication/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->publicationModel
            ->select('t_publication.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_publication.created_by', 'left')
            ->join('users updated', 'updated.id = t_publication.updated_by', 'left');

        if ($this->request->getGet('slug')) {
            $query->where('t_publication.category', $this->request->getGet('slug'));
        }

        $publications = $query->findAll();

        // dd($publications);

        $this->data['title'] = 'Publikasi';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['publications'] = $publications;
        echo view('DepositPublication\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('deposit/publication/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Publikasi';

        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required'],
            'category' => ['label' => 'Category', 'rules' => 'required'],
            'publisher' => ['label' => 'Publisher', 'rules' => 'required'],
            'author' => ['label' => 'Author', 'rules' => 'required'],
            'city' => ['label' => 'City', 'rules' => 'required'],
            'edition' => ['label' => 'Edition', 'rules' => 'required'],
            'publication_year' => ['label' => 'Publish Year', 'rules' => 'required'],
        ]);
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('title'), '-', TRUE);

            $save_data = $this->request->getPost();
            $save_data['slug'] = $slug;
            $save_data['created_by'] = user_id();
            $save_data['updated_by'] = user_id();
            unset($save_data['file']);

            // Logic Upload
            $files = $this->request->getPost('file');

            if (!empty($files)) {
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
                $save_data['document'] = implode(',', $listed_file);
            }
            $newPublicationId = $this->publicationModel->insert($save_data);

            if ($newPublicationId) {
                add_log('Tambah Publikasi', 'publication', 'create', 't_publication', $newPublicationId);
                set_message('toastr_msg', lang('Publication.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/publication');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Publication.info.failed_saved'));
                echo view('DepositPublication\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('deposit/publication/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositPublication\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('deposit/publication/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Publikasi';
        $publication = $this->publicationModel->find($id);
        $this->data['publication'] = $publication;

        $this->validation->setRules([
            'title' => ['label' => 'Title', 'rules' => 'required'],
            'category' => ['label' => 'Category', 'rules' => 'required'],
            'publisher' => ['label' => 'Publisher', 'rules' => 'required'],
            'author' => ['label' => 'Author', 'rules' => 'required'],
            'city' => ['label' => 'City', 'rules' => 'required'],
            'edition' => ['label' => 'Edition', 'rules' => 'required'],
            'publication_year' => ['label' => 'Publish Year', 'rules' => 'required'],
        ]);
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('title'), '-', TRUE);

                $update_data = $this->request->getPost();
                $update_data['slug'] = $slug;
                $update_data['created_by'] = user_id();
                $update_data['updated_by'] = user_id();
                unset($update_data['file']);

                // Logic Upload
                $files = (array) $this->request->getPost('file');
                if (!empty($files)) {
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
                        $update_data['document'] = implode(',', $listed_file);
                    }
                }
                $publicationUpdate = $this->publicationModel->update($id, $update_data);

                if ($publicationUpdate) {
                    add_log('Ubah Publikasi', 'publication', 'edit', 't_publication', $id);
                    set_message('toastr_msg', 'Publication berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/deposit/publication');
                } else {
                    set_message('toastr_msg', 'Publication gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Publication gagal diubah');
                    return redirect()->to('/deposit/publication/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('deposit/publication/edit/' . $id);
        echo view('DepositPublication\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('deposit/publication/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/deposit/publication');
        }
        $publication = $this->publicationModel->find($id);
        $publicationDelete = $this->publicationModel->delete($id);
        if ($publicationDelete) {
            unlink_file($this->modulePath, $publication->document);
            unlink_file($this->modulePath, 'thumb_' . $publication->document);

            add_log('Hapus Publikasi', 'publication', 'delete', 't_publication', $id);
            set_message('toastr_msg', lang('Publication.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/deposit/publication');
        } else {
            set_message('toastr_msg', lang('Publication.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Publication.info.failed_deleted'));
            return redirect()->to('/deposit/publication/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $publicationUpdate = $this->publicationModel->update($id, array($field => $value));

        if ($publicationUpdate) {
            set_message('toastr_msg', ' Publication berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Publication gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/publication');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $publication = $this->publicationModel->find($i);
            $newFileName = $publication->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
