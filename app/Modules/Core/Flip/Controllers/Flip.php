<?php

namespace Flip\Controllers;

class Flip extends \App\Controllers\BaseController
{
	function __construct()
	{
		helper('app');
	}

	public function index()
	{
		$file = $this->request->getVar('file');
		$this->data['file'] = preg_replace('/([^:])(\/{2,})/', '$1/', base_url($file));
		echo view('Flip\Views\index', $this->data);
	}
}
