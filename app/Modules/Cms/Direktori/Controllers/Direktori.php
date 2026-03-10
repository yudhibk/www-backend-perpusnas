<?php

namespace Direktori\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Direktori\Models\DirektoriModel;

class Direktori extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $direktoriModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {

        $this->direktoriModel = new DirektoriModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/direktori/';
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
        if (!is_allowed('direktori/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $categories = $this->direktoriModel->select('category')->distinct()->get()->getResult();
        $this->data['categories'] = $categories;

        $this->data['title'] = 'Direktori';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('Direktori\Views\list', $this->data);
    }

    public function index_pg()
    {
        if (!is_allowed('direktori/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' Direktori';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('Direktori\Views\list_pg', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('direktori/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $direktori = $this->direktoriModel->find($id);
        $this->data['title'] = 'Direktori - Detail';
        $this->data['direktori'] = $direktori;
        echo view('Direktori\Views\view', $this->data);
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('direktori/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah  Direktori';
        $direktori = $this->direktoriModel->find($id);
        $this->data['direktori'] = $direktori;

        $this->validation->setRule('title', 'Judul Artikel', 'required');
        $this->validation->setRule('category', 'kategori', 'required');
        $this->validation->setRule('area', 'Area', 'required');
        $this->validation->setRule('address', 'Alamat', 'required');
        // $this->validation->setRule('file_pdf', 'Gambar', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('title'), '-', TRUE);
                $update_data = [
                    'title' => $this->request->getPost('title') ?? $slug,
                    'slug' => $this->request->getPost('slug'),
                    'address' => $this->request->getPost('address'),
                    'category' => $this->request->getPost('category'),
                    'area' => $this->request->getPost('area'),
                    'description' => $this->request->getPost('description'),
                    'email' => $this->request->getPost('email'),
                    'updated_by' => get_user()->id,
                    'download_at' => $this->request->getPost('download_at'),
                ];

                $subjects = (array) $this->request->getPost('subject');
                $update_data['subject'] = implode(';', $subjects);

                $author_additionals = (array) $this->request->getPost('author_additional');
                $update_data['author_additional'] = implode(';', $author_additionals);

                $institutions = (array) $this->request->getPost('institution');
                $update_data['institution'] = implode(';', $institutions);

                // Logic Upload
                $direktoriUpdate = $this->direktoriModel->update($id, $update_data);

                if ($direktoriUpdate) {
                    add_log('Ubah  Direktori', 'direktori', 'edit', 't_direktori', $id);
                    set_message('toastr_msg', ' Direktori berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/direktori');
                } else {
                    set_message('toastr_msg', ' Direktori gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', $this->auth->errors());
                    return redirect()->to('/direktori/edit/' . $id);
                }
            }
        }




        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('direktori/edit/' . $id);
        echo view('Direktori\Views\update', $this->data);
    }

    public function create()
    {
        if (!is_allowed('direktori/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah  Direktori';

        $this->validation->setRule('title', 'Judul Artikel', 'required');
        $this->validation->setRule('category', 'kategori', 'required');
        $this->validation->setRule('area', 'Area', 'required');
        $this->validation->setRule('address', 'Alamat', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

            $slug = url_title($this->request->getPost('title'), '-', TRUE);
            $save_data = [
                'title' => $this->request->getPost('title') ?? $slug,
                'slug' => $this->request->getPost('slug'),
                'address' => $this->request->getPost('address'),
                'category' => $this->request->getPost('category'),
                'area' => $this->request->getPost('area'),
                'description' => $this->request->getPost('description'),
                'email' => $this->request->getPost('email'),
                'created_by' => get_user()->id,
                'active' => '1',
                // 'download_at' => $this->request->getPost('download_at'),
            ];

            $newDirektoriId = $this->direktoriModel->insert($save_data);

            if ($newDirektoriId) {
                add_log('Tambah  Direktori', 'direktori', 'create', 't_direktori', $newDirektoriId);
                set_message('toastr_msg', ' Direktori berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/direktori');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' Direktori gagal ditambah');
                echo view('Direktori\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('direktori/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Direktori\Views\add', $this->data);
        }
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('direktori/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        $direktori = $this->direktoriModel->find($id);
        $direktoriDelete = $this->direktoriModel->delete($id);
        if ($direktoriDelete) {
            unlink_file($this->modulePath, $direktori->file_image);
            unlink_file($this->modulePath, 'thumb_' . $direktori->file_image);
            unlink_file($this->modulePath, $direktori->file_pdf);

            add_log('Hapus  Direktori', 'direktori', 'delete', 't_direktori', $id);
            set_message('toastr_msg', ' Direktori berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/direktori');
        } else {
            set_message('toastr_msg', ' Direktori gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', $this->auth->errors());
            return redirect()->to('/direktori/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $direktoriUpdate = $this->direktoriModel->update($id, array($field => $value));

        if ($direktoriUpdate) {
            set_message('toastr_msg', ' Direktori berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Direktori gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/direktori');
    }

    public function export()
    {
        if (!is_allowed('direktori/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->direktoriModel
            ->select('t_direktori.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_direktori.created_by', 'left')
            ->join('users updated', 'updated.id = t_direktori.updated_by', 'left');

        $results = $query->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "Direktori");
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
            $sheet->setCellValue("G" . $col, base_url('uploads/direktori/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/direktori/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Direktori';
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
            $direktori = $this->direktoriModel->find($i);
            $newFileName = $direktori->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
