<?php

namespace Qna\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Qna\Models\QnaModel;

class Qna extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $qnaModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {


        $this->qnaModel = new QnaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/qna/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();


        if (!$this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }

        helper('reference');
    }

    public function index()
    {
        if (!is_allowed('qna/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Qna';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Qna\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('qna/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $qna = $this->qnaModel->find($id);
        $this->data['title'] = 'Qna - Detail';
        $this->data['qna'] = $qna;
        echo view('Qna\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('qna/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Qna';

        $this->validation->setRule('name', 'Kuesioner', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $slug = url_title($this->request->getPost('name'), '-', true);
            $save_data = [
                'slug' => $slug,
                'name' => $this->request->getPost('name'),
                'category' => $this->request->getPost('category'),
                'description' => $this->request->getPost('description'),
                'created_by' => user()->id,
                'active' => '1',
            ];

            // Option
            $index_arr = $this->request->getPost('index');
            if (!empty($index_arr)) {
                $content = [];
                foreach ($index_arr as $index => $value) {
                    $content[] = [
                        'option' => $this->request->getPost('option')[$value],
                        'score' => $this->request->getPost('score')[$value],
                    ];
                }
                if (!empty($content)) {
                    $save_data['content'] = json_encode($content);
                }
            }

            $newQnaId = $this->qnaModel->insert($save_data);

            if ($newQnaId) {
                add_log('Tambah Qna', 'qna', 'create', 't_qna', $newQnaId);
                set_message('toastr_msg', 'Qna berhasil ditambah');
                set_message('toastr_type', 'success');

                return redirect()->to('/qna');
            } else {
                set_message(
                    'message',
                    $this->validation->getErrors()
                        ? $this->validation->listErrors()
                        : 'Qna gagal ditambah'
                );
                echo view('Qna\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('qna/create');
            set_message(
                'message',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : $this->session->getFlashdata('message')
            );
            echo view('Qna\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('qna/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Qna';
        $qna = $this->qnaModel->find($id);
        $this->data['qna'] = $qna;

        $this->validation->setRule('name', 'Kuesioner', 'required');
        // $this->validation->setRule('file_pdf', 'Gambar', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', true);
                $update_data = [
                    'slug' => $slug,
                    'name' => $this->request->getPost('name'),
                    'category_id' => $this->request->getPost('category_id'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user()->id,
                    'active' => '1',
                    'sort' => $this->request->getPost('sort'),
                ];

                // Option
                $index_arr = $this->request->getPost('index');
                if (!empty($index_arr)) {
                    $content = [];
                    foreach ($index_arr as $index => $value) {
                        $content[] = [
                            'option' => $this->request->getPost('option')[$value],
                            'score' => $this->request->getPost('score')[$value],
                        ];
                    }
                    if (!empty($content)) {
                        $update_data['content'] = json_encode($content);
                    }
                }

                $qnaUpdate = $this->qnaModel->update($id, $update_data);

                if ($qnaUpdate) {
                    add_log('Ubah Qna', 'qna', 'edit', 't_qna', $id);
                    set_message('toastr_msg', 'Qna berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/qna');
                } else {
                    set_message('toastr_msg', 'Qna gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', $this->auth->errors());
                    return redirect()->to('/qna/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('qna/edit/' . $id);
        echo view('Qna\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('qna/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }
        $qna = $this->qnaModel->find($id);
        $qnaDelete = $this->qnaModel->delete($id);
        if ($qnaDelete) {
            add_log('Hapus Qna', 'qna', 'delete', 't_qna', $id);
            set_message('toastr_msg', 'Qna berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/qna');
        } else {
            set_message('toastr_msg', 'Qna gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', $this->auth->errors());
            return redirect()->to('/qna/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $qnaUpdate = $this->qnaModel->update($id, [$field => $value]);

        if ($qnaUpdate) {
            set_message('toastr_msg', 'Qna berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Qna gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/qna');
    }

    public function export()
    {
        if (!is_allowed('qna/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->qnaModel
            ->select('t_qna.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_qna.created_by', 'left')
            ->join('users updated', 'updated.id = t_qna.updated_by', 'left');

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Qna');
        $sheet
            ->getStyle('A1:H1')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Judul Artikel');
        $sheet->setCellValue('C2', 'Pengarang/Penulis');
        $sheet->setCellValue('D2', 'Aktif');
        $sheet->setCellValue('E2', 'Created By');
        $sheet->setCellValue('F2', 'Updated By');
        $sheet->setCellValue('G2', 'Foto Cover');
        $sheet->setCellValue('H2', 'Konten Digital');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet
            ->getStyle('A2:H2')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $col = 3;
        $no = 1;
        $i = 1;
        foreach ($results as $row) {
            $sheet->setCellValue('A' . $col, $no);
            $sheet->setCellValue('B' . $col, $row->title);
            $sheet->setCellValue('C' . $col, $row->author);
            $sheet->setCellValue('D' . $col, $row->active);
            $sheet->setCellValue(
                'E' . $col,
                $row->created_at . ' | ' . strtoupper($row->created_name)
            );
            $sheet->setCellValue(
                'F' . $col,
                $row->updated_at . ' | ' . strtoupper($row->updated_name)
            );
            $sheet->setCellValue(
                'G' . $col,
                base_url('uploads/qna/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/qna/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Qna';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header(
            'Content-Disposition: attachment;filename="' . $filename . '.xlsx"'
        );
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
