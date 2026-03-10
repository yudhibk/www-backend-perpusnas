<?php

namespace DepositPublikasiSop\Models;


class SopModel extends \App\Models\BaseModel
{
    protected $DBGroup                = 'default';
    protected $table                  = 't_deposit_publikasi_sop';
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
