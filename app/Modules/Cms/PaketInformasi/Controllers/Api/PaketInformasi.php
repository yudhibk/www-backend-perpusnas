<?php

namespace PaketInformasi\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;
use PaketInformasi\Models\PaketInformasiModel;

class PaketInformasi extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $paketinformasiModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->paketinformasiModel = new PaketInformasiModel();
		helper(['text','app','reference']);
	}

	public function datatable()
	{
		$db = db_connect();
		$builder = $db->table('t_paketinformasi as a')
			->select('a.id, a.id as action, a.title, a.slug, a.content, a.source, a.viewers,a.sort,  a.active')
			->select('a.category');


		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function($row){
				$html  =  '<b>'.$row->title.'</b><br>';
					return $html;
			})
			->edit('Source link', function($row){
				$html  =  '<b>'.$row->source.'</b><br>';
					return $html;
			})
				->edit('active', function($row){
				$status = $row->active == 1 ? 'Publish' : 'Draft';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-'.$class.'  badge-pill" >'.$status.'</span>';
				return $html;
			})
			->edit('description', function($row){
				return character_limiter($row->description,100);
			})
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/paketinformasi/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/paketinformasi/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/paketinformasi/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/paketinformasi/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function index()
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'sort';
		$params['direction'] = $params['direction'] ?? 'asc';
		$params['category'] = $params['category'] ?? '';

		$query = $this->paketinformasiModel
			->where('active', 1);

		if(!empty($params['category'])){
			$query->where('category',$params['category']);
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
	
	public function detail($slug)
	{
		try {
            $data = $this->paketinformasiModel->where('slug', $slug)->first();
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
}
