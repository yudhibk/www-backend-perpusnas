<?php

namespace Page\Controllers;

use \CodeIgniter\Files\File;

class Page extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $pageModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {

        $this->pageModel = new \Page\Models\PageModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/page/';
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
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
        if (!is_allowed('page/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->pageModel
            ->select('t_page.*')
            ->select('c_references.name as category')
            ->join('c_references', 'c_references.id = t_page.category_id', 'left')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_page.created_by', 'left')
            ->join('users updated', 'updated.id = t_page.updated_by', 'left');
        $pages = $query->findAll();

        $this->data['title'] = 'Page';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['pages'] = $pages;
        $this->data['redirect'] = base_url('page/index');
        echo view('Page\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('page/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Page';

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'slug' => $slug,
                'category_id' => $this->request->getPost('category_id'),
                'sort' => $this->request->getPost('sort'),
                'description' => $this->request->getPost('description'),
                'content' => $this->request->getPost('content'),
                'created_by' => user_id(),
            ];
            // Logic Upload
            $files = (array) $this->request->getPost('file_image');
            if (count($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;

                        create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                    }
                }
                $save_data['file_image'] = implode(',', $listed_file);
            }

            $files = (array) $this->request->getPost('file_pdf');
            if (count($files)) {
                $listed_file = array();
                foreach ($files as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['file_pdf'] = implode(',', $listed_file);
            }
            $newPageId = $this->pageModel->insert($save_data);

            if ($newPageId) {
                add_log('Tambah Page', 'page', 'create', 't_page', $newPageId);
                set_message('toastr_msg', lang('Page.info.successfully_saved'));
                set_message('toastr_type', 'success');

                $redirect = '/page';
                if (!empty(get_var('path'))) {
                    $redirect = '/' . get_var('path');
                    if (!empty(get_var('path'))) {
                        $redirect .= '?category=' . get_var('category');
                    }
                }

                return redirect()->to($redirect);
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('Page\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('page/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Page\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('page/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Page';
        $page = $this->pageModel->find($id);
        $this->data['page'] = $page;

        $this->validation->setRule('name', 'Nama', 'required');
        $this->validation->setRule('category_id', 'Jenis Tokoh', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $this->request->getPost('slug') ?? $slug,
                    'category_id' => $this->request->getPost('category_id'),
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

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
                                $newFileName = $file->getRandomName();
                                $file->move($this->modulePath, $newFileName);
                                $listed_file[] = $newFileName;
                            }
                        }
                    }
                    $update_data['file_image'] = implode(',', $listed_file);
                }

                $files = (array) $this->request->getPost('file_pdf');
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
                    $update_data['file_pdf'] = implode(',', $listed_file);
                }

                $pageUpdate = $this->pageModel->update($id, $update_data);

                if ($pageUpdate) {
                    add_log('Ubah Page', 'page', 'edit', 't_page', $id);
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/page');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/page/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('page/edit/' . $id);
        echo view('Page\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('page/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/page');
        }
        $page = $this->pageModel->find($id);
        $pageDelete = $this->pageModel->delete($id);
        if ($pageDelete) {
            unlink_file($this->modulePath, $page->file_image);
            unlink_file($this->modulePath, 'thumb_' . $page->file_image);
            unlink_file($this->modulePath, $page->file_pdf);

            add_log('Hapus Page', 'page', 'delete', 't_page', $id);
            set_message('toastr_msg', lang('Page.info.successfully_deleted'));
            set_message('toastr_type', 'success');

            $redirect = '/page';
            if (!empty(get_var('path'))) {
                $redirect = '/' . get_var('path');
                if (!empty(get_var('path'))) {
                    $redirect .= '?category=' . get_var('category');
                }
            }
            return redirect()->to($redirect);
        } else {
            set_message('toastr_msg', lang('Page.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Page.info.failed_deleted'));
            return redirect()->to('/page/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $pageUpdate = $this->pageModel->update($id, array($field => $value));

        if ($pageUpdate) {
            set_message('toastr_msg', ' Page berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Page gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/page');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $page = $this->pageModel->find($i);
            $newFileName = $page->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
