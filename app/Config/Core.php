<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Core extends BaseConfig
{
    public $site_name = 'Perpusnas RI';
    public $layout_popup = 'Layout\Views\backend\popup'; 
    public $layout_blank = 'Layout\Views\backend\blank'; 
    public $layout_backend = 'Layout\Views\backend\main'; 
    public $layout_frontend = 'Layout\Views\frontend\home\index';

}