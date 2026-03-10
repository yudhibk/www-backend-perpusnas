<?php

namespace Banner\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;

class Banner extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $bannerModel;
	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->bannerModel = new \Banner\Models\BannerModel();
		helper(['text','app','reference']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_banner as a')
			->select('a.id, a.id as action, a.title, a.slug, a.content, a.file_image, a.file_cover, a.viewers, a.description,a.sort, a.active, a.feature')
			->select('a.category, a.channel');

		if(!empty($slug)){
			$builder = $builder->where('a.category', unslugify($slug));
		}
		if(!is_member('admin')){
			$group = get_group();
			$builder = $builder->where('a.channel', $group->name);
		}

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function($row){
				$html = '<b>'.$row->title.'</b><br>';
				$html .=  $row->description;
				return $html;
			})
			->edit('file_image', function($row){
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file_image)) ? base_url('uploads/banner/' . $row->file_image) : $default;

				$html = '<a href="'.$image.'" class="image-link"><img width="100" class="rounded" src="'.$default.'" id="lazy'.$row->id.'" class="lazy" data-src="'.$image.'" onerror="this.onerror=null;this.src='.$default.';" alt=""></a>';
				return $html;
			})
			->edit('category', function($row){
				$html = '<span class="badge badge-primary badge-pill" >'.character_limiter($row->category,15).'</span> ';
				if(!empty($row->channel)){
					$html .= '<span class="badge badge-info badge-pill" >'.($row->channel).'</span>';
				}
				return $html;
			})
			->edit('feature', function($row){
				$checked = $row->feature == 1 ? 'checked' : '';
				$html = '<input type="checkbox" class="apply-status" data-href="'.base_url('api/banner/switch/'.$row->id).'" data-checked="'.$checked.'" data-field="feature" '.$checked.' data-toggle="toggle" data-onstyle="success" data-on="Ya" data-off="Tdk" data-size="mini">';
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
				$edit = '<a href="'.base_url('cms/banner/edit/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="'.base_url('cms/banner/apply_status/'.$row->id.'?field=active&value=1').'"  data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="'.base_url('cms/banner/apply_status/'.$row->id.'?field=active&value=0').'" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="'.base_url('cms/banner/delete/'.$row->id).'" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
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
		$params['feature'] = $params['feature'] ?? '';

		$query = $this->bannerModel
			->where('active', 1)
			->where('feature', $params['feature']);

		if(!empty($channel)){
			$query->where('channel',$channel);
		}

		if(!empty($params['category'])){
			$query->where('category',$params['category']);
		}

		if(!empty($params['feature'])){
			$query->where('feature',$params['feature']);
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
            $data = $this->bannerModel->where('slug', $slug)->first();
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

	public function switch($id = null)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $update_data_id = $this->bannerModel->update($id, array($field => ($value == 'true')?1:0));

        if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => 'Field Banner berhasil disimpan',
			];
		} else {
			$response = [
				'error' => true,
				'message' => 'Field Banner gagal disimpan. Silakan coba lagi',
			];
		}
		return $this->simpleResponse($response);
    }
}
