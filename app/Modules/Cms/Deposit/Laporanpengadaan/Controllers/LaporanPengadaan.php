<?php

namespace DepositLaporanpengadaan\Controllers; 

use \CodeIgniter\Files\File;

class Laporanpengadaan extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $laporanpengadaanModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {
        $this->laporanpengadaanModel = new \DepositLaporanpengadaan\Models\LaporanpengadaanModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/deposit/laporan/pengadaan/';
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
        if (!is_allowed('deposit/laporan/pengadaan/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->laporanpengadaanModel
            ->select('t_deposit_laporan_pengadaan.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_deposit_laporan_pengadaan.created_by', 'left')
            ->join('users updated', 'updated.id = t_deposit_laporan_pengadaan.updated_by', 'left');

        $laporanpengadaans = $query->findAll();

        $this->data['title'] = 'Laporan Pengadaan';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['laporanpengadaans'] = $laporanpengadaans;
        echo view('DepositLaporanpengadaan\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('deposit/laporan/pengadaan/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Laporan Pengadaan';

        $this->validation->setRule('name', 'Judul Laporan Pengadaan', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
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
            $newLaporanpengadaanId = $this->laporanpengadaanModel->insert($save_data);

            if ($newLaporanpengadaanId) {
                add_log('Tambah Laporan Pengadaan', 'laporanpengadaan', 'create', 't_deposit_laporan_pengadaan', $newLaporanpengadaanId);
                set_message('toastr_msg', lang('Laporanpengadaan.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/deposit/laporan/pengadaan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Laporanpengadaan.info.failed_saved'));
                echo view('DepositLaporanpengadaan\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('deposit/laporan/pengadaan/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('DepositLaporanpengadaan\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('deposit/laporan/pengadaan/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Laporan Pengadaan';
        $laporanpengadaan = $this->laporanpengadaanModel->find($id);
        $this->data['laporanpengadaan'] = $laporanpengadaan;

        $this->validation->setRule('name', 'Judul Laporan Pengadaan', 'required');
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
                $laporanpengadaanUpdate = $this->laporanpengadaanModel->update($id, $update_data);

                if ($laporanpengadaanUpdate) {
                    add_log('Ubah Laporan Pengadaan', 'laporanpengadaan', 'edit', 't_deposit_laporan_pengadaan', $id);
                    set_message('toastr_msg', 'Laporanpengadaan berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/deposit/laporan/pengadaan');
                } else {
                    set_message('toastr_msg', 'Laporanpengadaan gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Laporanpengadaan gagal diubah');
                    return redirect()->to('/deposit/laporan/pengadaan/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('deposit/laporan/pengadaan/edit/' . $id);
        echo view('DepositLaporanpengadaan\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('deposit/laporan/pengadaan/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/deposit/laporan/pengadaan');
        }
        $laporanpengadaan = $this->laporanpengadaanModel->find($id);
        $laporanpengadaanDelete = $this->laporanpengadaanModel->delete($id);
        if ($laporanpengadaanDelete) {
            unlink_file($this->modulePath, $laporanpengadaan->file);
            unlink_file($this->modulePath, 'thumb_' . $laporanpengadaan->file);

            add_log('Hapus Laporan Pengadaan', 'laporanpengadaan', 'delete', 't_deposit_laporan_pengadaan', $id);
            set_message('toastr_msg', lang('Laporanpengadaan.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/deposit/laporan/pengadaan');
        } else {
            set_message('toastr_msg', lang('Laporanpengadaan.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Laporanpengadaan.info.failed_deleted'));
            return redirect()->to('/deposit/laporan/pengadaan/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $laporanpengadaanUpdate = $this->laporanpengadaanModel->update($id, array($field => $value));

        if ($laporanpengadaanUpdate) {
            set_message('toastr_msg', ' Laporanpengadaan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Laporanpengadaan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/deposit/laporan/pengadaan');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $laporanpengadaan = $this->laporanpengadaanModel->find($i);
            $newFileName = $laporanpengadaan->file;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
