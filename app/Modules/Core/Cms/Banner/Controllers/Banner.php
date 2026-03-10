<?php

namespace Banner\Controllers;

use \CodeIgniter\Files\File;

class Banner extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $bannerModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        helper(['url', 'text', 'form', 'auth', 'app', 'html']);


        $this->bannerModel = new \Banner\Models\BannerModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/banner/';
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
        if (!is_allowed('banner/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->bannerModel
            ->select('t_banner.*')
            ->select('c_references.name as category')
            ->join('c_references', 'c_references.id = t_banner.category_id', 'left')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_banner.created_by', 'left')
            ->join('users updated', 'updated.id = t_banner.updated_by', 'left');
        $banners = $query->findAll();

        $this->data['title'] = 'Banner';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['banners'] = $banners;
        echo view('Banner\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('banner/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Banner';

        $this->validation->setRule('name', 'Judul Banner', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'slug' => $slug,
                'category_id' => $this->request->getPost('category_id'),
                'sort' => $this->request->getPost('sort'),
                'description' => $this->request->getPost('description'),
                'url' => $this->request->getPost('url'),
                'url_title' => $this->request->getPost('url_title'),
                'url_target' => $this->request->getPost('url_target'),
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
            $newBannerId = $this->bannerModel->insert($save_data);

            if ($newBannerId) {
                add_log('Tambah Banner', 'banner', 'create', 't_banner', $newBannerId);
                set_message('toastr_msg', lang('Banner.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/banner');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Banner.info.failed_saved'));
                echo view('Banner\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('banner/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Banner\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('banner/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Banner';
        $banner = $this->bannerModel->find($id);
        $this->data['banner'] = $banner;

        $this->validation->setRule('name', 'Judul Banner', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $this->request->getPost('slug') ?? $slug,
                    'category_id' => $this->request->getPost('category_id'),
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'url' => $this->request->getPost('url'),
                    'url_title' => $this->request->getPost('url_title'),
                    'url_target' => $this->request->getPost('url_target'),
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
                $bannerUpdate = $this->bannerModel->update($id, $update_data);

                if ($bannerUpdate) {
                    add_log('Ubah Banner', 'banner', 'edit', 't_banner', $id);
                    set_message('toastr_msg', 'Banner berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/banner');
                } else {
                    set_message('toastr_msg', 'Banner gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Banner gagal diubah');
                    return redirect()->to('/banner/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('banner/edit/' . $id);
        echo view('Banner\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('banner/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/banner');
        }
        $banner = $this->bannerModel->find($id);
        $bannerDelete = $this->bannerModel->delete($id);
        if ($bannerDelete) {
            unlink_file($this->modulePath, $banner->file_image);
            unlink_file($this->modulePath, 'thumb_' . $banner->file_image);

            add_log('Hapus Banner', 'banner', 'delete', 't_banner', $id);
            set_message('toastr_msg', lang('Banner.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/banner');
        } else {
            set_message('toastr_msg', lang('Banner.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Banner.info.failed_deleted'));
            return redirect()->to('/banner/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $bannerUpdate = $this->bannerModel->update($id, array($field => $value));

        if ($bannerUpdate) {
            set_message('toastr_msg', ' Banner berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Banner gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/banner');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $banner = $this->bannerModel->find($i);
            $newFileName = $banner->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
