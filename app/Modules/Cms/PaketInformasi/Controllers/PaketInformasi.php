<?php

namespace PaketInformasi\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PaketInformasi\Models\PaketInformasiModel;

class PaketInformasi extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $paketinformasiModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {

        $this->paketinformasiModel = new PaketInformasiModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/paketinformasi/';
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
        if (!is_allowed('paketinformasi/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' PaketInformasi';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('PaketInformasi\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('paketinformasi/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $paketinformasi = $this->paketinformasiModel->find($id);
        $this->data['title'] = 'PaketInformasi - Detail';
        $this->data['paketinformasi'] = $paketinformasi;
        echo view('PaketInformasi\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('paketinformasi/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah PaketInformasi';
        $slug = $this->request->getVar('slug');

        $this->validation->setRule('title', 'Judul PaketInformasi', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $title = $this->request->getPost('title');
            $save_data = [
                'title' => $this->request->getPost('title'),
                'slug' => slugify($title),
                'source' => $this->request->getPost('source'),
                'sort' => $this->request->getPost('sort'),
                'content' => $this->request->getPost('content'),
                'created_by' => user_id(),
            ];
            // Logic Upload
            $files = (array) $this->request->getPost('file_cover');
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
                $save_data['file_cover'] = implode(',', $listed_file);
            }

            $files = (array) $this->request->getPost('file_image');
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
                $save_data['file_image'] = implode(',', $listed_file);
            }

            if (is_admin()) {
                $index_arr = $this->request->getPost('index');
                if (!empty($index_arr)) {
                    $meta = array();
                    foreach ($index_arr as $index => $value) {
                        $meta[] = [
                            'key' => $this->request->getPost('key')[$value],
                            'value' => $this->request->getPost('value')[$value],
                        ];
                    }
                    if (!empty($meta)) {
                        $save_data['meta'] = json_encode($meta);
                    }
                }
            }

            $newPageId = $this->paketinformasiModel->insert($save_data);
            if ($newPageId) {
                add_log('Tambah PaketInformasi', 'paketinformasi', 'create', 't_paketinformasi', $newPageId);
                set_message('toastr_msg', 'PaketInformasi berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/paketinformasi');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('PaketInformasi\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('paketinformasi/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('PaketInformasi\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('paketinformasi/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah PaketInformasi';
        $paketinformasi = $this->paketinformasiModel->find($id);
        $this->data['paketinformasi'] = $paketinformasi;

        $this->validation->setRule('title', 'Judul PaketInformasi', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title = $this->request->getPost('title');
                $update_data = [
                    'title' => $title,
                    'slug' => slugify($title),
                    'category' => $this->request->getPost('category'),
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

                // Logic Upload
                $files = (array) $this->request->getPost('file_cover');
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
                    $update_data['file_cover'] = implode(',', $listed_file);
                }

                $files = (array) $this->request->getPost('file_image');
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
                    $update_data['file_image'] = implode(',', $listed_file);
                }

                if (is_admin()) {
                    $index_arr = $this->request->getPost('index');
                    if (!empty($index_arr)) {
                        $meta = array();
                        foreach ($index_arr as $index => $value) {
                            $meta[] = [
                                'key' => $this->request->getPost('key')[$value],
                                'value' => $this->request->getPost('value')[$value],
                            ];
                        }
                        if (!empty($meta)) {
                            $update_data['meta'] = json_encode($meta);
                        }
                    }
                }
                $pageUpdate = $this->paketinformasiModel->update($id, $update_data);

                if ($pageUpdate) {
                    add_log('Ubah PaketInformasi', 'paketinformasi', 'edit', 't_paketinformasi', $id);
                    set_message('toastr_msg', 'PaketInformasi berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/paketinformasi');
                } else {
                    set_message('toastr_msg', 'PaketInformasi gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'PaketInformasi gagal diubah');
                    return redirect()->to('/cms/paketinformasi');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/paketinformasi/edit/' . $id);
        echo view('PaketInformasi\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('paketinformasi/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $paketinformasi = $this->paketinformasiModel->find($id);
        $paketinformasiDelete = $this->paketinformasiModel->delete($id);
        if ($paketinformasiDelete) {
            unlink_file($this->modulePath, $paketinformasi->file_image);
            unlink_file($this->modulePath, 'thumb_' . $paketinformasi->file_image);
            unlink_file($this->modulePath, $paketinformasi->file_pdf);

            add_log('Hapus  PaketInformasi', 'paketinformasi', 'delete', 't_paketinformasi', $id);
            set_message('toastr_msg', ' PaketInformasi berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/paketinformasi');
        } else {
            set_message('toastr_msg', ' PaketInformasi gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/paketinformasi');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');
        $paketinformasi = $this->paketinformasiModel->find($id);

        $paketinformasiUpdate = $this->paketinformasiModel->update($id, array($field => $value));

        if ($paketinformasiUpdate) {
            set_message('toastr_msg', ' PaketInformasi berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' PaketInformasi gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/paketinformasi');
    }

    public function export()
    {
        if (!is_allowed('paketinformasi/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->paketinformasiModel
            ->select('t_paketinformasi.*')
            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_paketinformasi.created_by', 'left')
            ->join('users updated', 'updated.id = t_paketinformasi.updated_by', 'left');

        $results = $query->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "PaketInformasi");
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue("A2", "No");
        $sheet->setCellValue("B2", "Judul Artikel");
        $sheet->setCellValue("C2", "Pengarang/Penulis");
        $sheet->setCellValue("D2", "Aktif");
        $sheet->setCellValue("E2", "Created By");
        $sheet->setCellValue("F2", "Updated By");
        $sheet->setCellValue("G2", "Foto Cover");
        $sheet->setCellValue("H2", "Konten Digital");

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12);

        $col = 3;
        $no = 1;
        $i = 1;
        foreach ($results as $row) {
            $sheet->setCellValue("A" . $col, $no);
            $sheet->setCellValue("B" . $col, $row->title);
            $sheet->setCellValue("C" . $col, $row->author);
            $sheet->setCellValue("D" . $col, $row->active);
            $sheet->setCellValue("E" . $col, $row->created_at . ' | ' . strtoupper($row->created_name));
            $sheet->setCellValue("F" . $col, $row->updated_at . ' | ' . strtoupper($row->updated_name));
            $sheet->setCellValue("G" . $col, base_url('uploads/paketinformasi/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/paketinformasi/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'PaketInformasi';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $paketinformasi = $this->paketinformasiModel->find($i);
            $newFileName = $paketinformasi->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
