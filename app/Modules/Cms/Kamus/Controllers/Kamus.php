<?php

namespace Kamus\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Kamus\Models\KamusModel;

class Kamus extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $kamusModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {

        $this->kamusModel = new KamusModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/kamus/';
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
        if (!is_allowed('kamus/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' Kamus';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('Kamus\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('kamus/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $kamus = $this->kamusModel->find($id);
        $this->data['title'] = 'Kamus - Detail';
        $this->data['kamus'] = $kamus;
        echo view('Kamus\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('kamus/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Kamus';
        $slug = $this->request->getVar('slug');

        $this->validation->setRule('title', 'Judul Kamus', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $title = $this->request->getPost('title');
            $save_data = [
                'title' => $title,
                'slug' => slugify($title),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'content' => $this->request->getPost('content'),
                'publish_date' => $this->request->getPost('publish_date'),
                'created_by' => user_id(),
            ];

            $newPageId = $this->kamusModel->insert($save_data);
            if ($newPageId) {
                add_log('Tambah Kamus', 'kamus', 'create', 't_kamus', $newPageId);
                set_message('toastr_msg', 'Kamus berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/kamus');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('Kamus\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('kamus/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Kamus\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('kamus/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Kamus';
        $kamus = $this->kamusModel->find($id);
        $this->data['kamus'] = $kamus;

        $this->validation->setRule('title', 'Judul Kamus', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title = $this->request->getPost('title');
                $update_data = [
                    'title' => $title,
                    'slug' => slugify($title),
                    'category' => $this->request->getPost('category'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'publish_date' => $this->request->getPost('publish_date'),
                    'updated_by' => user_id(),
                ];
                $pageUpdate = $this->kamusModel->update($id, $update_data);

                if ($pageUpdate) {
                    add_log('Ubah Kamus', 'kamus', 'edit', 't_kamus', $id);
                    set_message('toastr_msg', 'Kamus berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/kamus');
                } else {
                    set_message('toastr_msg', 'Kamus gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Kamus gagal diubah');
                    return redirect()->to('/cms/kamus');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/kamus/edit/' . $id);
        echo view('Kamus\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('kamus/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        $kamus = $this->kamusModel->find($id);
        $kamusDelete = $this->kamusModel->delete($id);
        if ($kamusDelete) {
            add_log('Hapus  Kamus', 'kamus', 'delete', 't_kamus', $id);
            set_message('toastr_msg', ' Kamus berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('cms/kamus');
        } else {
            set_message('toastr_msg', ' Kamus gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', $this->auth->errors());
            return redirect()->to('cms/kamus');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $kamusUpdate = $this->kamusModel->update($id, array($field => $value));

        if ($kamusUpdate) {
            set_message('toastr_msg', ' Kamus berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Kamus gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/kamus');
    }

    public function export()
    {
        if (!is_allowed('kamus/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->kamusModel
            ->select('t_kamus.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_kamus.created_by', 'left')
            ->join('users updated', 'updated.id = t_kamus.updated_by', 'left');

        $results = $query->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "Kamus");
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
            $sheet->setCellValue("G" . $col, base_url('uploads/kamus/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/kamus/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Kamus';
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
            $kamus = $this->kamusModel->find($i);
            $newFileName = $kamus->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
