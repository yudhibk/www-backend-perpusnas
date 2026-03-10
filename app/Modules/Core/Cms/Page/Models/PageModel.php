<?php

namespace Page\Models;
use SteeveDroz\CiSlug\Slugify;

class PageModel extends \App\Models\BaseModel
{
	protected $DBGroup              = 'default';
    protected $table      			= 't_page';
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

	protected $beforeInsert = ['setSlug'];
	public function setSlug($data)
    {
        $slugify = new Slugify($this);
		$slugify->setField('slug');
        $data = $slugify->addSlug($data, 'title');
        return $data;
    }
}
