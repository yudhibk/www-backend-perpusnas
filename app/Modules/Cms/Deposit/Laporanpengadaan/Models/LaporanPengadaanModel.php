<?php

namespace DepositLaporanpengadaan\Models;


class LaporanpengadaanModel extends \App\Models\BaseModel
{
    protected $DBGroup                = 'default';
    protected $table                  = 't_deposit_laporan_pengadaan';
    protected $primaryKey             = 'id';
    protected $returnType             = 'object';
    protected $useSoftDeletes         = false;
    protected $protectFields          = false;
    protected $useTimestamps          = true;
    protected $createdField           = 'created_at';
    protected $updatedField           = 'updated_at';
    protected $deletedField           = 'deleted_at';
    protected $validationRules        = [];
    protected $validationMessages     = [];
    protected $skipValidation         = true;
}
