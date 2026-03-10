<?php

namespace Kamus\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;
use Kamus\Models\KamusModel;

class Kamus extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $kamusModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->kamusModel = new KamusModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/kamus/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('thumbnail');
		helper(['url', 'text', 'form', 'auth', 'app', 'html']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_kamus as a')
			->select('a.id, a.id as action, a.title, a.slug, a.file_cover, a.file_image, a.viewers, a.description, a.active')
			->select('a.category, a.category_sub')
			->select('a.created_at,  a.updated_at')
			->select('a.publish_date')
			->where('a.language', 'id');

		if(!empty($slug)){
			$builder = $builder->where('a.category', unslugify($slug));
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function($row){
				$html  =  '<b>'.$row->title.'</b><br>';
				$html .=  $row->description;
				return $html;
			})
			->edit('category', function($row){
				$html = '<span class="badge badge-primary badge-pill" >'.character_limiter($row->category,15).'</span> ';
				if(!empty($row->category_sub)){
					$html .= '<br><span class="badge badge-secondary badge-pill" >'.character_limiter($row->category_sub,15).'</span>';
				}
				return $html;
			})
			->edit('active', function($row){
				$status = $row->active == 1 ? 'Publish' : 'Draft';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-'.$class.'  badge-pill">'.$status.'</span>';
				return $html;
			})
			->edit('publish_date', function($row){
				$html = '<span class="badge badge-info badge-pill">'.$row->publish_date.'</span>';
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/kamus/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/kamus/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/kamus/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/kamus/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function category()
	{	
		$db = db_connect();
		$builder = $db->table('t_kamus')
			->select('category')
			->select('lower(REPLACE(category," ","-")) as category_slug')
			->distinct()
			->where('active', 1)
			->where('language', 'id');

		$total = $builder->countAllResults(false);
		$data = $builder->get()->getResult();
		$response = array(
			'error'    => false,
			'total' => $total,
			'data' => $data,
			'message' => 'Data retrieved successfully'
		);

		return $this->simpleResponse($response);
	}

	public function index($slug = null)
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = $params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = $params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'desc';
		$params['keyword'] = $params['keyword'] ?? '';
		$params['language'] = $params['language'] ?? 'id';

		$query = $this->kamusModel
			->select('*')
			->select('lower(REPLACE(category," ","-")) as category_slug')
			->where('active', 1);

		if(!empty($slug)){
			$query->where('lower(REPLACE(category," ","-"))',slugify(strtolower($slug)));
		}

		if(!empty($params['language'])){
			$query->where('language',$params['language']);
		}

		if(!empty($params['keyword'])){
			$query->like('title',$params['keyword']);
		}

		$total = $query->countAllResults(false);
		$data = $query
			->orderBy($params['order'], $params['direction'])
			->findAll($params['limit'], $params['offset']);

		$response = array(
			'error'    => false,
			'param' => $params,
			'data' => $data,
			'message' => 'Data retrieved successfully'
		);

		return $this->paginatedResponse($response, $total, $params['limit'], $params['offset']);
	}

	public function detail($slug = null)
    {
		try {
            $data = $this->kamusModel->where('slug', $slug)->first();
			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with slug ' . $slug);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function show($pk = null)
    {
		try {

            $data = $this->kamusModel->find($pk);

			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with id ' . $pk);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function create()
	{
		if (!is_allowed('kamus/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newKamusId = $this->kamusModel->insert($save_data);
			if ($newKamusId) {
				$this->session->setFlashdata('toastr_msg', lang('Kamus.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => false,
					'messages' => [
						'success' => lang('Kamus.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => false,
					'messages' => [
						'error' =>  lang('Kamus.info.failed_saved')
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function edit($id = null)
	{
		if (!is_allowed('kamus/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$kamusUpdate = $this->kamusModel->update($id, $update_data);
			if ($kamusUpdate) {
				add_log('Ubah Kamus', 'kamus', 'edit', 't_kamus', $id);
				$this->session->setFlashdata('toastr_msg', lang('Kamus.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => false,
					'messages' => [
						'success' => lang('Kamus.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">'.lang('Kamus.info.failed_updated').'</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		if (!is_allowed('kamus/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
			return $this->respond(array('status' => 201, 'error' => lang('App.permission.not.have')));
        }

		$data = $this->kamusModel->find($id);
		if ($data) {
			$this->kamusModel->delete($id);
			add_log('Hapus Kamus', 'kamus', 'delete', 't_kamus', $id);
			$response = [
				'status'   => 200,
				'error'    => false,
				'messages' => [
					'success' => lang('Kamus.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Kamus.info.not_found').' ID:' . $id);
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

        $kamus= $this->kamusModel->find($upload_id);
        $kamusUpdate = $this->kamusModel->update($upload_id,$update_data);
        if ($kamusUpdate) {
			if($upload_field == 'file_image'){
				unlink_file($this->modulePath, $kamus->file_image);
				unlink_file($this->modulePath, 'thumb_'.$kamus->file_image);
			} else {
				unlink_file($this->modulePath, $kamus->file_pdf);
			}	

            $this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
            $this->session->setFlashdata('toastr_type', 'success');
            $response = [
                'status'   => 201,
                'error'    => false,
                'messages' => [
                    'success' => 'Upload file berhasil'
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status'   => 400,
                'error'    => false,
                'messages' => [
                    'error' => 'Upload file gagal'
                ]
            ];
            return $this->fail($response);
        }
	}
}
