<?php

namespace Pegawai\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;
use Pegawai\Models\PegawaiModel;

class Pegawai extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $pegawaiModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->pegawaiModel = new PegawaiModel();
		helper(['text','app','reference']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_pegawai as a')
			->select('a.id, a.id as action, a.name, a.file, a.class, a.division, a.position,a.nip, a.active');

		if(!empty($slug)){
			$builder = $builder->where('a.division', unslugify($slug));
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('name', function($row){
				$html = $row->name;
				return $html;
			})
			->edit('file', function($row){
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file)) ? base_url('uploads/pegawai/' . $row->file) : $default;

				$html = '<a href="'.$image.'" class="image-link"><img width="100" class="rounded" src="'.$default.'" id="lazy'.$row->id.'" class="lazy" data-src="'.$image.'" onerror="this.onerror=null;this.src='.$default.';" alt=""></a>';
				return $html;
			})
			->edit('division', function($row){
				$html = $row->division;
				// if(!empty($row->division)){
				// 	$html .= '<span class="badge badge-info badge-pill" >'.($row->division).'</span>';
				// }
				return $html;
			})
			->edit('active', function($row){
				$active = $row->active == 1 ? 'Active' : 'Inactive';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-'.$class.'  badge-pill" >'.$active.'</span>';
				return $html;
			})
			// ->edit('description', function($row){
			// 	return character_limiter($row->description,100);
			// })
			->edit('action', function($row){
				$edit = '<a href="'.base_url('cms/pegawai/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/pegawai/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/pegawai/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/pegawai/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit.' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function index($division = null)
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'asc';
		// $params['category'] = $params['category'] ?? '';
		// $params['feature'] = $params['feature'] ?? '';

		$query = $this->pegawaiModel
			->where('active', 1);
			//->where('feature', $params['feature']);

		// if(!empty($channel)){
		// 	$query->where('channel',$channel);
		// }

		if(!empty($params['division'])){
			$query->where('division',$params['division']);
		}

		// if(!empty($params['feature'])){
		// 	$query->where('feature',$params['feature']);
		// }

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
	
	// public function detail($slug)
	// {
	// 	try {
    //         $data = $this->pegawaiModel->where('slug', $slug)->first();
	// 		if ($data) {
	// 			$response = array(
	// 				'error'    => false,
	// 				'message' => 'Show data successfully',
	// 				'data' => $data,
	// 			);
	// 			return $this->simpleResponse($response);
	// 		} else {
	// 			return $this->failNotFound('No Data Found with slug ' . $slug);
	// 		}
    //     } catch (Exception $e) {
	// 		return $this->failServerError($e->getMessage());
    //     }
	// }

	public function switch($id = null)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $update_data_id = $this->pegawaiModel->update($id, array($field => ($value == 'true')?1:0));

        if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Pegawai berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Pegawai gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
    }
}
