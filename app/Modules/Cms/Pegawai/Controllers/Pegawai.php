<?php

namespace Pegawai\Controllers;

use \CodeIgniter\Files\File;
use Pegawai\Models\PegawaiModel;

class Pegawai extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $pegawaiModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{

		$this->pegawaiModel = new PegawaiModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/pegawai/';
		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		$this->auth = \Myth\Auth\Config\Services::authentication();
		$this->authorize = \Myth\Auth\Config\Services::authorization();


		if (! $this->auth->check()) {
			$this->session->set('redirect_url', current_url());
			return redirect()->route('login');
		}

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
	}

	public function index()
	{
		if (!is_allowed('pegawai/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$this->data['title'] = 'Pegawai';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('Pegawai\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('banner/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$banner = $this->bannerModel->find($id);
		$this->data['title'] = 'Banner - Detail';
		$this->data['banner'] = $banner;
		echo view('Banner\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('pegawai/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah Pegawai';
		$slug = $this->request->getVar('slug');

		$this->validation->setRule('name', 'Nama Pegawai', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$name = $this->request->getPost('name');
			$save_data = [
				'name' => $this->request->getPost('name'),
				//'slug' => slugify($title),
				'class' => $this->request->getPost('class'),
				'nip' => $this->request->getPost('nip'),
				'position' => $this->request->getPost('position'),
				'division' => $this->request->getPost('division'),
				'created_by' => user_id(),
				'created_at' => now(),
			];

			// if(is_member('admin')){
			// 	$channel = $this->request->getPost('channel');
			// 	if(!empty($channel)){
			// 		$save_data['channel'] = $channel;
			// 	}
			// } else {
			// 	$group = get_group();
			// 	$save_data['channel'] = $group->name;
			// }
			// Logic Upload
			$files = (array) $this->request->getPost('file_image');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->uploadPath . $name)) {
						$file = new File($this->uploadPath . $name);
						$newFileName = $file->getRandomName();
						$file->move($this->modulePath, $newFileName);
						$listed_file[] = $newFileName;
					}
				}
				$save_data['file'] = implode(',', $listed_file);
			}

			// if(is_admin()){
			// 	$index_arr = $this->request->getPost('index');
			// 	if(!empty($index_arr)){
			// 		$meta = array();
			// 		foreach ($index_arr as $index => $value){
			// 			$meta[] = [
			// 				'key' => $this->request->getPost('key')[$value],
			// 				'value' => $this->request->getPost('value')[$value],
			// 			];
			// 		}
			// 		if(!empty($meta)){
			// 			$save_data['meta'] = json_encode($meta);
			// 		}
			// 	}
			// }

			$newPageId = $this->pegawaiModel->insert($save_data);
			if ($newPageId) {
				add_log('Tambah Pegawai', 'pegawai', 'create', 't_pegawai', $newPageId);
				set_message('toastr_msg', 'Pegawai berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/pegawai');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
				echo view('Pegawai\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('pegawai/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Pegawai\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('pegawai/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Pegawai';
		$pegawai = $this->pegawaiModel->find($id);
		$this->data['pegawai'] = $pegawai;

		$this->validation->setRule('name', 'Nama Pegawai', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$name = $this->request->getPost('name');
				$update_data = [
					'name' => $this->request->getPost('name'),
					'class' => $this->request->getPost('class'),
					'nip' => $this->request->getPost('nip'),
					'position' => $this->request->getPost('position'),
					'division' => $this->request->getPost('division'),
					'updated_at' => now(),
				];

				// if(is_member('admin')){
				// 	$channel = $this->request->getPost('channel');
				// 	if(!empty($channel)){
				// 		$update_data['channel'] = $channel;
				// 	} else {
				// 		$update_data['channel'] = '';
				// 	}
				// } else {
				// 	$group = get_group();
				// 	$update_data['channel'] = $group->name;
				// }

				// Logic Upload
				$files = (array) $this->request->getPost('file_image');
				if (count($files)) {
					$listed_file = array();
					foreach ($files as $uuid => $name) {
						if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_file[] = $newFileName;
						}
					}
					$update_data['file'] = implode(',', $listed_file);
				}

				// if(is_admin()){
				// 	$index_arr = $this->request->getPost('index');
				// 	if(!empty($index_arr)){
				// 		$meta = array();
				// 		foreach ($index_arr as $index => $value){
				// 			$meta[] = [
				// 				'key' => $this->request->getPost('key')[$value],
				// 				'value' => $this->request->getPost('value')[$value],
				// 			];
				// 		}
				// 		if(!empty($meta)){
				// 			$update_data['meta'] = json_encode($meta);
				// 		}
				// 	}
				// }
				$pageUpdate = $this->pegawaiModel->update($id, $update_data);

				if ($pageUpdate) {
					add_log('Ubah Pegawai', 'pegawai', 'edit', 't_pegawai', $id);
					set_message('toastr_msg', 'Pegawai berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/pegawai');
				} else {
					set_message('toastr_msg', 'Data gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Data gagal diubah');
					return redirect()->to('/cms/pegawai');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/pegawai/edit/' . $id);
		echo view('Pegawai\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('pegawai/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$pegawai = $this->pegawaiModel->find($id);
		$pegawaiDelete = $this->pegawaiModel->delete($id);
		if ($pegawaiDelete) {
			unlink_file($this->modulePath, $pegawai->file);

			add_log('Hapus Pegawai', 'pegawai', 'delete', 't_pegawai', $id);
			set_message('toastr_msg', ' Pegawai berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/pegawai');
		} else {
			set_message('toastr_msg', ' Pegawai gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/pegawai');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');
		$pegawai = $this->pegawaiModel->find($id);

		$pegawaiUpdate = $this->pegawaiModel->update($id, array($field => $value));

		if ($pegawaiUpdate) {
			set_message('toastr_msg', ' Data Pegawai berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Data Pegawai gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/pegawai');
	}

	// public function export()
	// {
	// 	if (!is_allowed('banner/access')) {
	//         set_message('toastr_msg', lang('App.permission.not.have'));
	//         set_message('toastr_type', 'error');
	//         return redirect()->to('/dashboard');
	//     }

	// 	$query = $this->bannerModel
	// 		->select('t_banner.*')
	// 		->select('created.username as created_name')
	// 		->select('updated.username as updated_name')
	// 		->join('users created','created.id = t_banner.created_by','left')
	// 		->join('users updated','updated.id = t_banner.updated_by','left');

	// 	$results = $query->findAll();
	// 	$spreadsheet = new Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();

	// 	$sheet->mergeCells('A1:H1');
	// 	$sheet->setCellValue("A1", "Banner");
	// 	$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

	// 	$sheet->setCellValue("A2", "No");
	// 	$sheet->setCellValue("B2", "Judul Artikel");
	// 	$sheet->setCellValue("C2", "Pengarang/Penulis");
	// 	$sheet->setCellValue("D2", "Aktif");
	// 	$sheet->setCellValue("E2", "Created By");
	// 	$sheet->setCellValue("F2", "Updated By");
	// 	$sheet->setCellValue("G2", "Foto Cover");
	// 	$sheet->setCellValue("H2", "Konten Digital");

	// 	$sheet->getColumnDimension('A')->setWidth(10);
	// 	$sheet->getColumnDimension('B')->setWidth(50);
	// 	$sheet->getColumnDimension('C')->setWidth(20);
	// 	$sheet->getColumnDimension('D')->setWidth(10);
	// 	$sheet->getColumnDimension('E')->setWidth(20);
	// 	$sheet->getColumnDimension('F')->setWidth(20);
	// 	$sheet->getColumnDimension('G')->setWidth(15);
	// 	$sheet->getColumnDimension('H')->setWidth(15);

	// 	$sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12);

	// 	$col = 3;
	// 	$no = 1;
	// 	$i = 1;
	// 	foreach ($results as $row) {
	// 		$sheet->setCellValue("A".$col, $no);
	// 		$sheet->setCellValue("B".$col, $row->title);
	// 		$sheet->setCellValue("C".$col, $row->author);
	// 		$sheet->setCellValue("D".$col, $row->active);
	// 		$sheet->setCellValue("E".$col, $row->created_at.' | '.strtoupper($row->created_name));
	// 		$sheet->setCellValue("F".$col, $row->updated_at.' | '.strtoupper($row->updated_name));
	// 		$sheet->setCellValue("G".$col, base_url('uploads/banner/' . $row->file_image));
	// 		$sheet->setCellValue("H".$col, base_url('uploads/banner/' . $row->file_pdf));

	// 		$col++;
	// 		$no++;
	// 		$i++;
	// 	}

	// 	$writer = new Xlsx($spreadsheet);
	// 	$subject = 'Banner';
	// 	$filename = ucwords($subject) . '-' . date('Y-m-d');

	// 	header('Content-Type: application/vnd.ms-excel');
	// 	header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
	// 	header('Cache-Control: max-age=0');

	// 	$writer->save('php://output');
	// }

	// public function thumb()
	// {
	// 	$from = $this->request->getVar('from');
	// 	$to = $this->request->getVar('to');

	// 	for ($i=$from; $i <= $to ; $i++) { 
	// 		$banner= $this->bannerModel->find($i);
	// 		$newFileName = $banner->file_image;
	// 		if(!file_exists($this->modulePath.'/thumb_'.$newFileName)){
	// 			create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
	// 			echo "success generate thumbnail for ID: ".$i." <br>";
	// 		} else {
	// 			echo "already exist, failed generate thumbnail for ID: ".$i." <br>";
	// 		}
	// 	}
	// }
}
