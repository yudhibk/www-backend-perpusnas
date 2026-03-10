<?php

namespace DepositPengajuanBahanpustaka\Models;


class BahanpustakaModel extends \App\Models\BaseModel
{
    protected $DBGroup                = 'default';
    protected $table                  = 't_deposit_pengajuan_bahan_pustaka';
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
