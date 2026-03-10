<?php

namespace Survey\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;
use Survey\Models\SurveyModel;

class Survey extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $surveyModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->surveyModel = new SurveyModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/survey/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['app', 'url', 'text', 'reference', 'thumbnail']);
	}

	public function datatable()
	{
		$db = db_connect();
		$builder = $db->table('t_survey as a')
			->select('a.id, a.id as action, a.total_score, a.created_at')
			->select('a.surveyor_name, a.surveyor_gender, a.surveyor_dob, a.surveyor_job, a.surveyor_education, a.surveyor_phone, a. surveyor_remark');

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('surveyor_name', function($row){
				$html  = '<b>'.$row->surveyor_name.'</b> <br>';
				$html .= '<span data-toggle="tooltip" data-placement="top" title="Nomor HP" class="badge badge-pill badge-success">'.$row->surveyor_phone.'</span> ';
				$html .= '<span data-toggle="tooltip" data-placement="top" title="Tanggal Lahir" class="badge badge-pill badge-warning">'.$row->surveyor_dob.'</span> ';
				$html .= '<span data-toggle="tooltip" data-placement="top" title="Jenis Kelamin" class="badge badge-pill badge-secondary">'.$row->surveyor_gender.'</span> ';
				$html .= '<span data-toggle="tooltip" data-placement="top" title="Pendidikan" class="badge badge-pill badge-info">'.$row->surveyor_education.'</span> ';
				$html .= '<span data-toggle="tooltip" data-placement="top" title="Pekerjaan" class="badge badge-pill badge-primary">'.$row->surveyor_job.'</span> ';

				return $html;
			})
			->edit('total_score', function($row){
				$html  = '<b>'.$row->total_score.'</b> <br>';
				return $html;
			})
			->edit('created_at', function($row){
				$html = '<span data-toggle="tooltip" data-placement="top" title="Indikator" class="badge badge-pill badge-info">'.$row->created_at.'</span> ';
				return $html;
			})

			// ->edit('content', function($row){
			// 	$items = json_decode($row->content);
			// 	$table  = '<table class="table table-striped" style="width:100%">';
			// 	$table .= '<tr><th>Pilihan</th><th>Score</th></tr>';
			// 	foreach($items as $item){
			// 		$style = '';
			// 		if($row->score == $item->score){
			// 			$style = 'style="background-color: #ccc;"';
			// 		}
			// 		$table .= '<tr '.$style.'><td>'.$item->option.'</td><td>'.$item->score.'</td></tr>';
			// 	}
			// 	$table .= '</table>';
			// 	return $table;
			// })
			// ->edit('description', function($row){
			// 	$table = '
			// 		<table class="table table-striped" style="width:100%">
			// 			<tr><th colspan="2">Info Responden</th></tr>
			// 			<tr><td>Nama</td><td>'.$row->surveyor_name.'</td></tr>
			// 			<tr><td>No Telepon</td><td>'.$row->surveyor_phone.'</td></tr>
			// 			<tr><td>Email</td><td>'.$row->surveyor_email.'</td></tr>
			// 			<tr><td>Jenis Kelamin</td><td>'.$row->surveyor_gender.'</td></tr>
			// 			<tr><td>Tanggal Lahir</td><td>'.$row->surveyor_dob.'</td></tr>
			// 			<tr><td>Tanggal Survey</td><td>'.$row->surveyor_dob.'</td></tr>
			// 		</table>';
			// 	return $table;
			// })
			// ->edit('indicator', function($row){
			// 	$html = '<span data-toggle="tooltip" data-placement="top" title="Indikator" class="badge badge-pill badge-info">'.$row->indicator.'</span> ';
			// 	return $html;
			// })
				->edit('action', function($row){
				$detail = '<a href="'.base_url('survey/detail/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Detail Survey" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('survey/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus Survey" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $detail ." ". $delete;
			})
			// ->filter(function ($builder, $request) {
			// 	if ($request->category){
			// 		$builder->where('b.slug', $request->category);
			// 	}
			// })
			->toJson(true);

		return $dataTable;
	}

	public function index()
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = $params['limit'] ?? getenv('view.paginationLimit');
		$params['offset'] = $params['offset'] ?? getenv('view.paginationOffset');
		$params['order'] = $params['order'] ?? 'date_published';
		$params['direction'] = $params['direction'] ?? 'desc';

		$total = $this->surveyModel->countAllResults();

		$data = $this->surveyModel
			->orderBy($params['order'], $params['direction'])
			->findAll($params['limit'], $params['offset']);

		$response = array(
			'error'    => null,
			'param' => $params,
			'data' => $data,
			'message' => 'Data retrieved successfully'
		);

		return $this->paginatedResponse($response, $total, $params['limit'], $params['offset']);
	}

	public function show($id = null)
    {
		try {

            $data = $this->surveyModel->find($id);

			if ($data) {
				$response = array(
					'error'    => null,
					'data' => $data,
					'message' => 'Show data successfully'
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('Could not find data for specified ID' . $id);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function create()
    {		$this->validation->setRule('surveyor_name', 'Nama lengkap', 'required');
		$this->validation->setRule('score', 'Form Survey', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$this->db = db_connect('default');
			$t_survey = $this->db->table('t_survey');
			$t_survey_detail = $this->db->table('t_survey_detail');
			try {
				#region logic
				$this->db->transBegin();
				$save_data = [
					'surveyor_name' => $this->request->getPost('surveyor_name'),
					'surveyor_phone' => $this->request->getPost('surveyor_phone'),
					'surveyor_dob' => $this->request->getPost('surveyor_dob'),
					'surveyor_gender' => $this->request->getPost('surveyor_gender'),
					'surveyor_education' => $this->request->getPost('surveyor_education'),
					'surveyor_job' => $this->request->getPost('surveyor_job'),
					'surveyor_remark' => $this->request->getPost('surveyor_remark'),
					'active' => '1',
					'created_at' => date('Y-m-d H:i:s'),
				];
	
				$save = $t_survey->insert($save_data);
				if($save){
					$survey_id = $this->db->insertID();	
					if($survey_id){
						$score_arr = $this->request->getPost('score');
						$qna_id_arr = $this->request->getPost('qna_id');
						$save_survey = array();
						$save_survey_detail = array();
						$total_score = 0;
						foreach ($qna_id_arr as $index => $qna_id){
							$total_score = $total_score + $score_arr[$qna_id];
							$save_survey_detail[] = [
								'survey_id' => $survey_id,
								'qna_id' => $qna_id,
								'score' => $score_arr[$qna_id],
								'active' => '1',
							];
						}


						if(!empty($save_survey_detail)){
							$t_survey_detail->insertBatch($save_survey_detail);	
							$t_survey->set('total_score', $total_score);
							$t_survey->where('id', $survey_id);
							$t_survey->update();									}
					}
				} 

				if ($this->db->transStatus() === false) {
					$this->db->transRollback();
					$response = [
						'error'    => true,
						'message' => 'Form Survei gagal dikirim!'
					];
					return $this->respond($response);
				} else {
					$this->db->transCommit();
					$response = [
						'error'    => false,
						'message' => 'Form Survei berhasil dikirim!'
					];
					return $this->respond($response);
				}

				#endregion
			} catch (\ReflectionException | \Exception $e) {
				// print_r($e);
				$this->db->transRollback();
				$response = [
					'error'    => true,
					'message' => 'Error, Silakan coba lagi atau hubungi Admin!'
				];
				return $this->respond($response);
			}
        } else {
			$response = [
				'error'    => true,
				'message' => 'Error, Data Responden dan Kritik & Saran tidak boleh kosong!'
			];
			return $this->respond($response);
        }
    }

	public function upload_file()
	{
        $upload_id = $this->request->getPost('upload_id');
        $upload_field = $this->request->getPost('upload_field');
        $upload_title = $this->request->getPost('upload_title');

        $update_data = [];
        $files = (array) $this->request->getPost('file_pendukung');
        if (count($files)) {
            $listed_file = array();
            foreach ($files as $uuid => $name) {
                if (file_exists($this->uploadPath . $name)) {
                    $file = new File($this->uploadPath . $name);
                    $newFileName = $file->getRandomName();
                    $file->move($this->modulePath, $newFileName);
                    $listed_file[] = $newFileName;

					if($upload_field == 'file_image'){
						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
                }
            }
            $update_data[$upload_field] = implode(',', $listed_file);
        }

        $survey= $this->surveyModel->find($upload_id);
        $surveyUpdate = $this->surveyModel->update($upload_id,$update_data);
        if ($surveyUpdate) {
			if($upload_field == 'file_image'){
				unlink_file($this->modulePath, $survey->file_image);
				unlink_file($this->modulePath, 'thumb_'.$survey->file_image);
			} else {
				unlink_file($this->modulePath, $survey->file_pdf);
			}	

            $this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
            $this->session->setFlashdata('toastr_type', 'success');
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'Upload file berhasil'
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status'   => 400,
                'error'    => null,
                'messages' => [
                    'error' => 'Upload file gagal'
                ]
            ];
            return $this->fail($response);
        }
	}
}
