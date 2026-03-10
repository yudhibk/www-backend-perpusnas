<?php

namespace KoleksiUmum\Models;

class KoleksiUmumModel extends \App\Models\BaseModel
{
    protected $table      			= 't_koleksi_umum';
    protected $primaryKey 			= 'id';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'created_at';
    protected $updatedField  		= 'updated_at';
    protected $deletedField  		= 'deleted_at';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;
}
