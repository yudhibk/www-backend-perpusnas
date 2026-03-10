<?php

namespace Qna\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Hermawan\DataTables\DataTable;
use Qna\Models\QnaModel;

class Qna extends \App\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $client;
    protected $qnaModel;
    
    
    protected $modulePath;
    protected $uploadPath;

    function __construct()
    {
        $this->client = \Config\Services::curlrequest();

        $this->qnaModel = new QnaModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->modulePath = ROOTPATH . 'public/uploads/qna/';
        $this->uploadPath = WRITEPATH . 'uploads/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        helper(['app', 'url', 'text', 'reference', 'thumbnail']);
    }

    public function datatable()
    {
        $db = db_connect();
        $builder = $db
            ->table('t_qna as a')
            ->select(
                'a.id, a.id as action, a.name, a.description, a.content, a.sort, a.active, a.category, a.category as indicator'
            );

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('name', function ($row) {
                $html = '<b>' . $row->name . '</b> <br>';
                $html .=
                    '<span data-toggle="tooltip" data-placement="top" title="Indikator" class="badge badge-pill badge-info">' .
                    $row->indicator .
                    '</span> ';
                return $html;
            })
            ->edit('content', function ($row) {
                $items = json_decode($row->content);
                $table =
                    '<table class="table table-striped" style="width:100%">';
                $table .= '<tr><th>Pilihan</th><th>Score</th></tr>';
                foreach ($items as $item) {
                    $table .=
                        '<tr><td>' .
                        $item->option .
                        '</td><td>' .
                        $item->score .
                        '</td></tr>';
                }
                $table .= '</table>';
                return $table;
            })
            ->edit('active', function ($row) {
                $html =
                    '<span class="badge badge-pill badge-danger">Inactive</span>';
                if ($row->active == 1) {
                    $html =
                        '<span class="badge badge-pill badge-success">Active</span>';
                }
                return $html;
            })
            ->edit('action', function ($row) {
                $edit =
                    '<a href="' .
                    base_url(
                        'qna/edit/' .
                            $row->id .
                            '?category=' .
                            get_var('category') .
                            '&path=qna'
                    ) .
                    '" data-toggle="tooltip" data-placement="top" title="Ubah " class="btn btn-warning show-data"><i class="pe-7s-note font-weight-bold"> </i></a>';
                $delete =
                    '<a href="javascript:void(0);" data-href="' .
                    base_url(
                        'qna/delete/' .
                            $row->id .
                            '?category=' .
                            get_var('category') .
                            '&path=qna'
                    ) .
                    '" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger remove-data"><i class="pe-7s-trash font-weight-bold"> </i></a>';
                $active =
                    '<a href="' .
                    base_url(
                        'qna/apply_status/' . $row->id . '?field=active&value=1'
                    ) .
                    '"  data-id="' .
                    $row->id .
                    '" data-toggle="tooltip" data-placement="top" title="Active" class="btn btn-success publish-data"><i class="pe-7s-check font-weight-bold"> </i> </a>';
                $inactive =
                    '<a href="' .
                    base_url(
                        'qna/apply_status/' . $row->id . '?field=active&value=0'
                    ) .
                    '" data-id="' .
                    $row->id .
                    '" data-toggle="tooltip" data-placement="top" title="Inactive" class="btn btn-danger draft-data"><i class="pe-7s-close font-weight-bold"> </i> </a>';
                return $edit . ' ' . $active . ' ' . $inactive . ' ' . $delete;
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
        $params['offset'] =
            $params['offset'] ?? getenv('view.paginationOffset');
        $params['order'] = $params['order'] ?? 'date_published';
        $params['direction'] = $params['direction'] ?? 'desc';

        $total = $this->qnaModel->countAllResults();

        $data = $this->qnaModel
            ->orderBy($params['order'], $params['direction'])
            ->findAll($params['limit'], $params['offset']);

        $response = [
            'error' => null,
            'param' => $params,
            'data' => $data,
            'message' => 'Data retrieved successfully',
        ];

        return $this->paginatedResponse(
            $response,
            $total,
            $params['limit'],
            $params['offset']
        );
    }

    public function show($id = null)
    {
        try {
            $data = $this->qnaModel->find($id);

            if ($data) {
                $response = [
                    'error' => null,
                    'data' => $data,
                    'message' => 'Show data successfully',
                ];
                return $this->simpleResponse($response);
            } else {
                return $this->failNotFound(
                    'Could not find data for specified ID' . $id
                );
            }
        } catch (Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function create()
    {
        $this->validation->setRule('title', 'Title', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);
            $save_data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $this->request->getPost('content') ?? '',
                'id' => get_unique_id(),
                'category' => 'Qna',
                'newsdate' => date('Y-m-d'),
                'images' => '220301083234ODMEqIWekT.jpg',
                'language' => 'id',
            ];

            $pk = $this->qnaModel->insert($save_data);
            if ($pk) {
                $data = $this->qnaModel->find($pk);
                $response = [
                    'error' => null,
                    'data' => $data,
                    'message' => 'Data added successfully',
                ];
                return $this->simpleResponse($response);
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Data failed to add',
                ];
                return $this->fail($response);
            }
        } else {
            $message = $this->validation->listErrors();
            return $this->fail($message, 400);
        }
    }

    public function update($pk = null)
    {
        $this->validation->setRule('title', 'Title', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
            $title = $this->request->getPost('title');
            $slug = url_title($title, '-', true);
            $update_data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $this->request->getPost('content') ?? '',
            ];

            $qnaUpdate = $this->qnaModel->update($pk, $update_data);
            if ($qnaUpdate) {
                $data = $this->qnaModel->find($pk);
                $response = [
                    'error' => null,
                    'data' => $data,
                    'message' => 'Data updated successfully',
                ];
                return $this->simpleResponse($response);
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Data failed to updated',
                ];
                return $this->fail($response);
            }
        } else {
            $message = $this->validation->listErrors();
            return $this->fail($message, 400);
        }
    }

    public function delete($pk = null)
    {
        $data = $this->qnaModel->find($pk);
        if ($data) {
            $delete = $this->qnaModel->delete($pk);
            $response = [
                'error' => null,
                'message' => 'Data deleted successfully',
            ];
            return $this->simpleResponse($response);
        } else {
            return $this->failNotFound(
                'Could not find data for specified ID' . $pk
            );
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
            $listed_file = [];
            foreach ($files as $uuid => $name) {
                if (file_exists($this->uploadPath . $name)) {
                    $file = new File($this->uploadPath . $name);
                    $newFileName = $file->getRandomName();
                    $file->move($this->modulePath, $newFileName);
                    $listed_file[] = $newFileName;

                    if ($upload_field == 'file_image') {
                        create_thumbnail(
                            $this->modulePath,
                            $newFileName,
                            'thumb_',
                            250
                        );
                    }
                }
            }
            $update_data[$upload_field] = implode(',', $listed_file);
        }

        $qna = $this->qnaModel->find($upload_id);
        $qnaUpdate = $this->qnaModel->update($upload_id, $update_data);
        if ($qnaUpdate) {
            if ($upload_field == 'file_image') {
                unlink_file($this->modulePath, $qna->file_image);
                unlink_file($this->modulePath, 'thumb_' . $qna->file_image);
            } else {
                unlink_file($this->modulePath, $qna->file_pdf);
            }

            $this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
            $this->session->setFlashdata('toastr_type', 'success');
            $response = [
                'status' => 201,
                'error' => null,
                'messages' => [
                    'success' => 'Upload file berhasil',
                ],
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'error' => null,
                'messages' => [
                    'error' => 'Upload file gagal',
                ],
            ];
            return $this->fail($response);
        }
    }
}
