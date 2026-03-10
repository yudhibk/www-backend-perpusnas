<?php

namespace App\Controllers;

class Flip extends BaseController
{
	public function index()
	{
		$file = $this->request->getVar('file');
		$path = $this->request->getVar('path');
		$file = base_url('uploads/' . $path . '/' . $file);

		$this->data['file'] = $file;
		echo view('Layout\Views\backend\flip', $this->data);
	}
}
