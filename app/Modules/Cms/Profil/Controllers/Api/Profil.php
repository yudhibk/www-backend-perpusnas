<?php

namespace Profil\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Exception;
use \Hermawan\DataTables\DataTable;
use Profil\Models\ProfilModel;

class Profil extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $profilModel;
	
	
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->profilModel = new ProfilModel();
		helper(['text','app','reference']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_page as a')
			->select('a.id, a.id as action, a.title, a.slug, a.content, a.file_image, a.viewers, a.description,a.sort,  a.active')
			->select('a.category_sub, a.channel')
			->select('a.file_image as cover')
			->select('a.created_at, a.updated_at')
			->where('a.category', 'Profil');

		if(!empty($slug)){
			$builder = $builder->where('a.category_sub', unslugify($slug));
		}
		if(!is_member('admin')){
			$group = get_group();
			$builder = $builder->where('a.channel', $group->name);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function($row){
				$html  =  '<b>'.$row->title.'</b><br>';
				$html .=  $row->description.'<br>';
				$html .= '<a href="'.permalink('profil/'.$row->slug).'" target="_blank">'.permalink('profil/'.$row->slug).'</a><br>';
				return $html;
			})
			->edit('category_sub', function($row){
				$html = '<span class="badge badge-primary badge-pill" >'.character_limiter($row->category_sub,15).'</span> ';
				if(!empty($row->channel)){
					$html .= '<span class="badge badge-info badge-pill" >'.($row->channel).'</span>';
				}
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
				$edit = '<a href="'.base_url('cms/profil/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/profil/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/profil/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/profil/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit .' '. $active .' '. $inactive .' '. $delete;
			})
			->toJson(true);
		return $dataTable;
	}

	public function index($channel = null)
	{
		// parameter
		$params = $this->request->getGet();
		$params['limit'] = (int) ($params['limit'] ?? getenv('view.paginationLimit') ?? 10);
		$params['offset'] = (int) ($params['offset'] ?? (int) getenv('view.paginationOffset') ?? 0);
		$params['order'] = $params['order'] ?? 'sort';
		$params['direction'] = $params['direction'] ?? 'asc';
		$params['category'] = $params['category'] ?? '';

		$query = $this->profilModel
			->where('active', 1);

		if(!empty($channel)){
			$query->where('channel',$channel);
		}

		if(!empty($params['category'])){
			$query->where('category_sub',$params['category']);
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
            $data = $this->profilModel->where('active',1)->where('slug', $slug)->first();
			if ($data) {
				$clean_content = str_replace('../../..',base_url(), $data->content);
				$data->content = $clean_content;
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
