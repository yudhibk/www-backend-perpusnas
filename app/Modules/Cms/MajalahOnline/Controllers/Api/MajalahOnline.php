<?php

namespace MajalahOnline\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;
use MajalahOnline\Models\MajalahOnlineModel;

class MajalahOnline extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $majalahonlineModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->majalahonlineModel = new MajalahOnlineModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/majalahonline/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['app', 'url', 'text', 'reference', 'thumbnail']);
	}

	public function datatable_edition($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_majalah_online as a')
			->select('count(a.id) as total')
			->select('a.edition, a.category')
			->select('a.edition as file_cover, a.edition as action')
			->where('a.language', 'id')
			->groupBy('a.edition, a.category');

		if(!empty($slug)){
			$builder = $builder->where('a.category', unslugify($slug));
		}

		$dataTable = DataTable::of($builder)
		->addNumbering('no')
			->edit('edition', function($row){
				$html  =  '<b>'.$row->edition.'</b><br>';
				$html .= '<a href="'.permalink('majalah-online/'.slugify($row->edition)).'" target="_blank">'.permalink('majalah-online/'.slugify($row->edition)).'</a><br>';
				return $html;
			})
			->edit('total', function($row){
				$html  =  '<b>'.formatRupiah($row->total,'').'</b><br>';
				return $html;
			})
			->edit('file_cover', function($row){
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file_cover)) ? base_url('uploads/majalahonline/' . $row->file_cover) : $default;

				$html = '<a href="'.$image.'" class="image-link"><img width="100" class="rounded" src="'.$default.'" id="lazy'.slugify($row->edition).'" class="lazy" data-src="'.$image.'" onerror="this.onerror=null;this.src='.$default.';" alt=""></a>';
				return $html;
			})
			->edit('category', function($row){
				$html = '<span class="badge badge-primary badge-pill" >'.character_limiter($row->category,15).'</span> ';
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/majalahonline/index/'.slugify($row->edition)).'" data-toggle="tooltip" data-placement="top" title="Artikel" class="btn btn-primary show-data"><i class="fa fa-eye"> </i></a>';
				return $edit;
			})
			->toJson(true);

		return $dataTable;
	}

	public function datatable($edition = null)
	{
		$db = db_connect();
		$builder = $db->table('t_majalah_online as a')
			->select('a.id, a.id as action, a.title, a.slug, a.file_cover, a.file_image, a.viewers, a.description, a.active')
			->select('a.category, a.category_sub')
			->select('a.created_at,  a.updated_at')
			->select('a.publish_date')
			->where('a.language', 'id');

		if(!empty($edition)){
			$builder = $builder->where('slugify(a.edition)', ($edition));
		}

		$dataTable = DataTable::of($builder)
		->addNumbering('no')
			->edit('title', function($row){
				$html  =  '<b>'.$row->title.'</b><br>';
				$html .= '<a href="'.permalink('majalah-online/'.$row->slug).'" target="_blank">'.permalink('majalah-online/'.$row->slug).'</a><br>';
				return $html;
			})
			->edit('file_cover', function($row){
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file_cover)) ? base_url('uploads/majalahonline/' . $row->file_cover) : $default;

				$html = '<a href="'.$image.'" class="image-link"><img width="100" class="rounded" src="'.$default.'" id="lazy'.$row->id.'" class="lazy" data-src="'.$image.'" onerror="this.onerror=null;this.src='.$default.';" alt=""></a>';
				return $html;
			})
			->edit('category', function($row){
				$html = '<span class="badge badge-primary badge-pill" >'.character_limiter($row->category,15).'</span> ';
				if(!empty($row->category_sub)){
					$html .= '<br><span class="badge badge-secondary badge-pill" >'.$row->category_sub.'</span>';
				}
				return $html;
			})
			->edit('active', function($row){
				$status = $row->active == 1 ? 'Publish' : 'Draft';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-'.$class.'  badge-pill" >'.$status.'</span>';
				return $html;
			})
			->edit('publish_date', function($row){
				$html = '<span class="badge badge-info badge-pill">'.$row->publish_date.'</span>';
				return $html;
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/majalahonline/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/majalahonline/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/majalahonline/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/majalahonline/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson(true);

		return $dataTable;
	}

	public function edition()
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'edition';
		$params['direction'] = $params['direction'] ?? 'asc';
		$params['language'] = $params['language'] ?? 'id';
		$params['keyword'] = $params['keyword'] ?? '';

		$query = $this->majalahonlineModel
			->select('count(id) as total')
			->select('edition, lower(REPLACE(edition," ","-")) as edition_slug, category,  lower(REPLACE(category," ","-")) as category_slug')
			->where('language', 'id')
			->where('active', 1)
			->groupBy('edition, category');

		if(!empty($params['category'])){
			$query->where('lower(REPLACE(category," ","-"))',slugify($params['category']));
		}

		if(!empty($params['keyword'])){
			$query->like('edition',$params['keyword']);
		}

		if(!empty($params['language'])){
			$query->where('language',$params['language']);
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

	public function index($slug = null)
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'desc';
		$params['category'] = $params['category'] ?? '';
		$params['language'] = $params['language'] ?? 'id';
		$params['keyword'] = $params['keyword'] ?? '';

		$query = $this->majalahonlineModel
			->select('*')
			->select('slugify(lower(edition)) edition_slug, slugify(lower(category)) category_slug')
			->where('active', 1);

		if(!empty($slug)){
			$query->where('slugify(lower(edition))',slugify(strtolower($slug)));
		}

		if(!empty($params['category'])){
			$query->where('slugify(lower(category))',slugify(strtolower($params['category'])));
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
            $data = $this->majalahonlineModel->where('slug', $slug)->first();
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

	public function show($id = null)
    {
		try {

            $data = $this->majalahonlineModel->find($id);

			if ($data) {
				$response = array(
					'error'    => false,
					'message' => 'Show data successfully',
					'data' => $data,
				);
				return $this->simpleResponse($response);
			} else {
				return $this->failNotFound('No Data Found with id ' . $id);
			}
        } catch (Exception $e) {
			return $this->failServerError($e->getMessage());
        }
    }

	public function create()
	{
		$this->validation->setRule('title', 'Title', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$slug = url_title($title, '-', TRUE);
			$save_data = array(
				'title' => $title,
				'slug' => $slug,
				'content' => $this->request->getPost('content')??'',
				'id' => get_unique_id(),
				'category' => 'MajalahOnline',
				'newsdate' => date('Y-m-d'),
				'images' => '220301083234ODMEqIWekT.jpg',
				'language' => 'id',
			);

			$id = $this->majalahonlineModel->insert($save_data);
			if ($id) {
				$data = $this->majalahonlineModel->find($id);
				$response = [
					'error'    => false,
					'data'	=> $data,
					'message' => 'Data added successfully'
				];
				return $this->simpleResponse($response);
			} else {
				$response = [
					'error'    => true,
					'message' => 'Data failed to add'
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function update($id = null)
	{
		$this->validation->setRule('title', 'Title', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$slug = url_title($title, '-', TRUE);
			$update_data = array(
				'title' => $title,
				'slug' => $slug,
				'content' => $this->request->getPost('content')??'',
			);

			$majalahonlineUpdate = $this->majalahonlineModel->update($id, $update_data);
			if ($majalahonlineUpdate) {
				$data = $this->majalahonlineModel->find($id);
				$response = [
					'error'    => false,
					'data'	=> $data,
					'message' => 'Data updated successfully'
				];
				return $this->simpleResponse($response);
			} else {
				$response = [
					'error'    => true,
					'message' => 'Data failed to updated'
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->majalahonlineModel->find($id);
		if ($data) {
			$delete = $this->majalahonlineModel->delete($id);
			$response = [
				'error'    => false,
				'message' => 'Data deleted successfully'
			];
			return $this->simpleResponse($response);
		} else {
			return $this->failNotFound('Could not find data for specified ID' . $id);
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

        $majalahonline= $this->majalahonlineModel->find($upload_id);
        $majalahonlineUpdate = $this->majalahonlineModel->update($upload_id,$update_data);
        if ($majalahonlineUpdate) {
			if($upload_field == 'file_image'){
				unlink_file($this->modulePath, $majalahonline->file_image);
				unlink_file($this->modulePath, 'thumb_'.$majalahonline->file_image);
			} else {
				unlink_file($this->modulePath, $majalahonline->file_pdf);
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
