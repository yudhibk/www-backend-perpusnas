<?php

namespace Survey\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Survey\Models\SurveyDetailModel;
use Survey\Models\SurveyModel;

class Survey extends \App\Controllers\BaseController
{
    protected $surveyModel;
    protected $surveyDetailModel;
    protected $qnaModel;
    protected $auth;
    protected $authorize;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {


        $this->surveyModel = new SurveyModel();
        $this->surveyDetailModel = new SurveyDetailModel();
        $this->qnaModel = new \Qna\Models\QnaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/survey/';

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
        if (!is_allowed('survey/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Survei';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Survey\Views\list', $this->data);
    }

    public function detail($id)
    {
        if (!is_allowed('survey/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $qnas = $this->qnaModel
            ->where('active', 1)
            ->orderBy('sort')
            ->findAll();
        $ans = $this->surveyDetailModel
            ->select('qna_id, score')
            ->where('survey_id', $id)
            ->orderBy('qna_id')
            ->findAll();
        $answers = [];
        foreach ($ans as $row) {
            $answers[$row->qna_id] = $row->score;
        }
        $survey = $this->surveyModel->find($id);
        $this->data['qnas'] = $qnas;
        $this->data['answers'] = $answers;
        $this->data['survey'] = $survey;

        $this->data['title'] = 'Form Survei';
        $this->validation->setRule('surveyor_name', 'Nama lengkap', 'required');
        $this->validation->setRule('score', 'Form Survey', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $this->db = db_connect('default');
            try {
                #region logic
                $this->db->transBegin();
                $update_data = [
                    'surveyor_name' => $this->request->getPost('surveyor_name'),
                    'surveyor_gender' => $this->request->getPost(
                        'surveyor_gender'
                    ),
                    'surveyor_dob' => $this->request->getPost('surveyor_dob'),
                    'surveyor_education' => $this->request->getPost(
                        'surveyor_education'
                    ),
                    'surveyor_job' => $this->request->getPost('surveyor_job'),
                    'surveyor_phone' => $this->request->getPost(
                        'surveyor_phone'
                    ),
                    'updated_by' => user()->id,
                ];

                $update = $this->surveyModel->update($id, $update_data);
                if ($update) {
                    $score_arr = $this->request->getPost('score');
                    $qna_id_arr = $this->request->getPost('qna_id');

                    $update_survey = [];
                    $update_survey_detail = [];
                    $total_score = 0;
                    foreach ($qna_id_arr as $index => $qna_id) {
                        $total_score = $total_score + $score_arr[$qna_id];
                        $update_survey_detail[] = [
                            'survey_id' => $survey_id,
                            'qna_id' => $qna_id,
                            'score' => $score_arr[$qna_id],
                            'updated_by' => user()->id,
                        ];
                    }

                    // $this->surveyDetailModel->updateBatch($update_survey_detail);
                    $this->surveyModel->update($id, [
                        'total_score' => $total_score,
                    ]);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    set_message('toastr_msg', 'Form Survey gagal disimpan');
                    set_message('toastr_type', 'warning');
                    return redirect()->back();
                } else {
                    $this->db->transCommit();
                    set_message('toastr_msg', 'Form Survey berhasil disimpan');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/survey');
                }

                #endregion
            } catch (\ReflectionException | \Exception $e) {
                $this->db->transRollback();

                set_message('toastr_msg', 'Maaf terjadi kesalahan sistem');
                set_message('toastr_type', 'error');
                return redirect()->back();
            }
        } else {
            $this->data['redirect'] = base_url('survey/form');
            set_message(
                'message',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : $this->session->getFlashdata('message')
            );
            echo view('Survey\Views\view', $this->data);
        }
    }

    public function create()
    {
        if (!is_allowed('survey/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $qnas = $this->qnaModel
            ->where('active', 1)
            ->orderBy('sort')
            ->findAll();
        $this->data['title'] = 'Tambah Survei';
        $this->data['qnas'] = $qnas;

        $this->validation->setRule('surveyor_name', 'Nama lengkap', 'required');
        $this->validation->setRule('surveyor_phone', 'Nomor HP', 'required');
        $this->validation->setRule(
            'surveyor_remark',
            'Kritik dan Saran',
            'required'
        );
        $this->validation->setRule('score', 'Form Survey', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $this->db = db_connect('default');
            try {
                #region logic
                $this->db->transBegin();
                $save_data = [
                    'surveyor_name' => $this->request->getPost('surveyor_name'),
                    'surveyor_gender' => $this->request->getPost(
                        'surveyor_gender'
                    ),
                    'surveyor_dob' => $this->request->getPost('surveyor_dob'),
                    'surveyor_education' => $this->request->getPost(
                        'surveyor_education'
                    ),
                    'surveyor_job' => $this->request->getPost('surveyor_job'),
                    'surveyor_phone' => $this->request->getPost(
                        'surveyor_phone'
                    ),
                    'surveyor_remark' => $this->request->getPost(
                        'surveyor_remark'
                    ),
                    'created_by' => user()->id,
                    'active' => '1',
                ];

                $survey_id = $this->surveyModel->insert($save_data);
                if ($survey_id) {
                    $score_arr = $this->request->getPost('score');
                    $qna_id_arr = $this->request->getPost('qna_id');
                    $save_survey = [];
                    $save_survey_detail = [];
                    $total_score = 0;
                    foreach ($qna_id_arr as $index => $qna_id) {
                        $total_score = $total_score + $score_arr[$qna_id];
                        $save_survey_detail[] = [
                            'survey_id' => $survey_id,
                            'qna_id' => $qna_id,
                            'score' => $score_arr[$qna_id],
                            'created_by' => user()->id,
                            'active' => '1',
                        ];
                    }

                    if (!empty($save_survey_detail)) {
                        $this->surveyDetailModel->insertBatch(
                            $save_survey_detail
                        );
                        $this->surveyModel->update($survey_id, [
                            'total_score' => $total_score,
                        ]);
                    }
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    set_message('toastr_msg', 'Form Survey gagal diinput');
                    set_message('toastr_type', 'warning');
                    return redirect()->back();
                } else {
                    $this->db->transCommit();
                    set_message('toastr_msg', 'Form Survey berhasil diinput');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/survey');
                }

                #endregion
            } catch (\ReflectionException | \Exception $e) {
                $this->db->transRollback();

                set_message('toastr_msg', 'Maaf terjadi kesalahan sistem');
                set_message('toastr_type', 'error');
                return redirect()->back();
            }
        } else {
            $this->data['redirect'] = base_url('survey/create');
            set_message(
                'message',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : $this->session->getFlashdata('message')
            );
            echo view('Survey\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('survey/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Survei';
        $survey = $this->surveyModel->find($id);
        $this->data['survey'] = $survey;

        $this->validation->setRule('name', 'Pertanyaan', 'required');
        // $this->validation->setRule('file_pdf', 'Gambar', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', true);
                $update_data = [
                    'slug' => $slug,
                    'name' => $this->request->getPost('name'),
                    'category_id' => $this->request->getPost('category_id'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user()->id,
                    'active' => '1',
                ];

                $surveyUpdate = $this->surveyModel->update($id, $update_data);

                if ($surveyUpdate) {
                    add_log('Ubah Survey', 'survey', 'edit', 't_survey', $id);
                    set_message('toastr_msg', 'Survei berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/survey');
                } else {
                    set_message('toastr_msg', 'Survei gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', $this->auth->errors());
                    return redirect()->to('/survey/edit/' . $id);
                }
            }
        }

        if (file_exists($this->modulePath . '/' . $survey->file_pdf)) {
            $file = new \CodeIgniter\Files\File(
                $this->modulePath . '/' . $survey->file_pdf
            );
            $this->data['image_size'] = $file->getSize('kb');
        } else {
            $this->data['image_size'] = 0;
        }

        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('survey/edit/' . $id);
        echo view('Survey\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('survey/delete')) {
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
        $survey = $this->surveyModel->find($id);
        $surveyDelete = $this->surveyModel->delete($id);
        if ($surveyDelete) {
            add_log('Hapus Survey', 'survey', 'delete', 't_survey', $id);
            set_message('toastr_msg', 'Survei berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/survey');
        } else {
            set_message('toastr_msg', 'Survei gagal dihapus');
            set_message('toastr_type', 'warning');
            set_message('message', $this->auth->errors());
            return redirect()->to('/survey/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $surveyUpdate = $this->surveyModel->update($id, [$field => $value]);

        if ($surveyUpdate) {
            set_message('toastr_msg', 'Survei berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', 'Survei gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/survey');
    }

    public function export()
    {
        if (!is_allowed('survey/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->surveyModel
            ->select('t_survey.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_survey.created_by', 'left')
            ->join('users updated', 'updated.id = t_survey.updated_by', 'left');

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Survei');
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
                base_url('uploads/survey/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/survey/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Survei';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header(
            'Content-Disposition: attachment;filename="' . $filename . '.xlsx"'
        );
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
