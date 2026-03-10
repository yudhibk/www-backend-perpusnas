<?php

namespace Agenda\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use \Hermawan\DataTables\DataTable;

class Agenda extends \App\Controllers\BaseResourceController
{
	use ResponseTrait;
	protected $client;
	protected $agendaModel;


	protected $modulePath;
	protected $uploadPath;

	function __construct()
	{
		$this->client = \Config\Services::curlrequest();

		$this->agendaModel = new \Agenda\Models\AgendaModel();
		$this->validation = \Config\Services::validation();
		$this->session = session();
		$this->modulePath = ROOTPATH . 'public/uploads/agenda/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper(['app', 'url', 'text', 'reference', 'thumbnail']);
	}

	public function datatable($slug = null)
	{
		$db = db_connect();
		$builder = $db->table('t_agenda as a')
			->select('a.id, a.id as action, a.title, a.slug, a.viewers, a.description, a.active, a.file_cover')
			->select('a.category, a.category_sub')
			->select('a.created_at,  a.updated_at')
			->select('a.date_from,  a.date_to')
			->select('a.publish_date')
			->where('a.language', 'id');

		if (!empty($slug)) {
			$builder = $builder->where('a.category', unslugify($slug));
		}

		if (!is_member('admin')) {
			$group = get_group();
			$builder = $builder->where('a.channel', $group->name);
		}
		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('title', function ($row) {
				$html  =  '<b>' . $row->title . '</b><br>';
				$html .= '<a href="' . permalink('publikasi/agenda/' . $row->slug) . '" target="_blank">' . permalink('publikasi/agenda/' . $row->slug) . '</a><br>';
				return $html;
			})
			->edit('file_cover', function ($row) {
				$default = base_url('uploads/default/no_cover.jpg');
				$image = (!empty($row->file_cover)) ? base_url('uploads/agenda/' . $row->file_cover) : $default;

				$html = '<a href="' . $image . '" class="image-link"><img width="100" class="rounded" src="' . $default . '" id="lazy' . $row->id . '" class="lazy" data-src="' . $image . '" onerror="this.onerror=null;this.src=' . $default . ';" alt=""></a>';
				return $html;
			})
			->edit('category', function ($row) {
				$html = '<span class="badge badge-primary badge-pill" >' . character_limiter($row->category, 15) . '</span> ';
				if (!empty($row->category_sub)) {
					$html .= '<br><span class="badge badge-secondary badge-pill" >' . character_limiter($row->category_sub, 15) . '</span>';
				}
				return $html;
			})
			->edit('active', function ($row) {
				$status = $row->active == 1 ? 'Publish' : 'Draft';
				$class = $row->active == 1 ? 'success' : 'danger';
				$html = '<span class="badge badge-' . $class . '  badge-pill" >' . $status . '</span>';
				return $html;
			})
			->edit('publish_date', function ($row) {
				$html = '<span class="badge badge-info badge-pill">' . $row->publish_date . '</span>';
				return $html;
			})
			->edit('action', function ($row) {
				$edit = '<a href="' . base_url('cms/agenda/edit/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$active = '<a href="' . base_url('cms/agenda/apply_status/' . $row->id . '?field=active&value=1') . '"  data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success active-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
				$inactive = '<a href="' . base_url('cms/agenda/apply_status/' . $row->id . '?field=active&value=0') . '" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-warning draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('cms/agenda/delete/' . $row->id) . '" data-toggle="tooltip" data-placement="top" title="Hapus " class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
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
		$params['order'] = $params['order'] ?? 'id';
		$params['direction'] = $params['direction'] ?? 'desc';
		$params['category'] = $params['category'] ?? '';
		$params['language'] = $params['language'] ?? 'id';
		$params['keyword'] = $params['keyword'] ?? '';

		$query = $this->agendaModel
			->where('active', 1);

		if (!empty($channel)) {
			$query->where('channel', $channel);
		}

		if (!empty($params['category'])) {
			$query->where('category', $params['category']);
		}
		if (!empty($params['language'])) {
			$query->where('language', $params['language']);
		}

		if (!empty($params['keyword'])) {
			$query->like('title', $params['keyword']);
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
			$data = $this->agendaModel->where('slug', $slug)->first();
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
			$data = $this->agendaModel->find($id);
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
				'content' => $this->request->getPost('content') ?? '',
				'id' => get_unique_id(),
				'category' => 'Agenda',
				'newsdate' => date('Y-m-d'),
				'images' => '220301083234ODMEqIWekT.jpg',
				'language' => 'id',
			);

			$id = $this->agendaModel->insert($save_data);
			if ($id) {
				$data = $this->agendaModel->find($id);
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
				'content' => $this->request->getPost('content') ?? '',
			);

			$agendaUpdate = $this->agendaModel->update($id, $update_data);
			if ($agendaUpdate) {
				$data = $this->agendaModel->find($id);
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
		$data = $this->agendaModel->find($id);
		if ($data) {
			$delete = $this->agendaModel->delete($id);
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

					if ($upload_field == 'file_image') {
						create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
					}
				}
			}
			$update_data[$upload_field] = implode(',', $listed_file);
		}

		$agenda = $this->agendaModel->find($upload_id);
		$agendaUpdate = $this->agendaModel->update($upload_id, $update_data);
		if ($agendaUpdate) {
			if ($upload_field == 'file_image') {
				unlink_file($this->modulePath, $agenda->file_image);
				unlink_file($this->modulePath, 'thumb_' . $agenda->file_image);
			} else {
				unlink_file($this->modulePath, $agenda->file_pdf);
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
