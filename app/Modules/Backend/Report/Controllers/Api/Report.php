<?php

namespace Report\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use Hermawan\DataTables\DataTable;

class Report extends \App\Controllers\BaseResourceController
{
    use ResponseTrait;
    protected $visitorModel;

    function __construct()
    {
        $this->visitorModel = new \Report\Models\VisitorModel();
        helper(['text', 'app', 'reference']);
    }

    public function index()
    {
        // parameter
        $params = $this->request->getGet();
        $params['limit'] =
            (int) ($params['limit'] ?? (getenv('view.paginationLimit') ?? 10));
        $params['offset'] =
            (int) ($params['offset'] ??
                ((int) getenv('view.paginationOffset') ?? 0));
        $params['order'] = $params['order'] ?? 'id';
        $params['direction'] = $params['direction'] ?? 'desc';

        $query = $this->visitorModel->where('active', 1);

        $total = $query->countAllResults(false);

        $data = $query
            ->orderBy($params['order'], $params['direction'])
            ->findAll($params['limit'], $params['offset']);

        $response = [
            'error' => false,
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

    public function visitor_datatable($date_from = null, $date_to = null)
    {
        $db = db_connect();
        $builder = $db
            ->table('c_visitors as a')
            ->select(
                'a.timestamp, a.ip_address, a.slug, a.ip_city, a.ip_country, a.hits'
            )
            ->select('a.created_at, a.updated_at');

        if (!empty($date_from)) {
            $builder->where('timestamp >=', $date_from);
        }

        if (!empty($date_to)) {
            $builder->where('timestamp <=', $date_to);
        }

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('slug', function ($row) {
                return permalink($row->slug);
            })
            ->toJson(true);
        return $dataTable;
    }
}
