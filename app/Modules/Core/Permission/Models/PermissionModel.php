<?php

namespace Permission\Models;

use Myth\Auth\Authorization\PermissionModel as MythModel;

class PermissionModel extends MythModel
{
	protected $DBGroup              = 'default';
    protected $returnType = 'object';
    protected $allowedFields = [
        'name', 'description'
    ];
}
