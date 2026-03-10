<?php

namespace KoleksiUmum\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use KoleksiUmum\Models\KoleksiUmumModel;

class KoleksiUmum extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $koleksiumumModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{

		$this->koleksiumumModel = new KoleksiUmumModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/koleksiumum/';
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
		if (!is_allowed('koleksiumum/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Koleksi Umum';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('KoleksiUmum\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('koleksiumum/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$koleksiumum = $this->koleksiumumModel->find($id);
		$this->data['title'] = 'KoleksiUmum - Detail';
		$this->data['koleksiumum'] = $koleksiumum;
		echo view('KoleksiUmum\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('koleksiumum/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah  Koleksi Umum';

		$this->validation->setRule('title', 'Judul Koleksi Umum', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'category_sub' => $this->request->getPost('category_sub'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'publisher' => $this->request->getPost('publisher'),
				'numbers' => $this->request->getPost('numbers'),
				'author' => $this->request->getPost('author'),
				'url' => $this->request->getPost('url'),
				'publish_date' => $this->request->getPost('publish_date'),
				'created_by' => user_id(),
			];

			// Logic Upload
			$files = (array) $this->request->getPost('file_cover');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->modulePath . $name)) {
						$listed_file[] = $name;
					} else {
						if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_file[] = $newFileName;
						}
					}
				}
				$save_data['file_cover'] = implode(',', $listed_file);
			}

			$files = (array) $this->request->getPost('file_image');
			if (count($files)) {
				$listed_file = array();
				foreach ($files as $uuid => $name) {
					if (file_exists($this->modulePath . $name)) {
						$listed_file[] = $name;
					} else {
						if (file_exists($this->uploadPath . $name)) {
							$file = new File($this->uploadPath . $name);
							$newFileName = $file->getRandomName();
							$file->move($this->modulePath, $newFileName);
							$listed_file[] = $newFileName;
						}
					}
				}
				$save_data['file_image'] = implode(',', $listed_file);
			}

			$newKoleksiUmumId = $this->koleksiumumModel->insert($save_data);
			if ($newKoleksiUmumId) {
				add_log('Tambah  Koleksi Umum', 'koleksiumum', 'create', 't_koleksi_umum', $newKoleksiUmumId);
				set_message('toastr_msg', ' KoleksiUmum berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/koleksiumum');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' KoleksiUmum gagal ditambah');
				echo view('KoleksiUmum\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('koleksiumum/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('KoleksiUmum\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('koleksiumum/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Halaman';
		$koleksiumum = $this->koleksiumumModel->find($id);
		$this->data['koleksiumum'] = $koleksiumum;

		$this->validation->setRule('title', 'Judul Koleksi Umum', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$title = $this->request->getPost('title');
				$update_data = [
					'title' => $title,
					'slug' => slugify($title),
					'category' => $this->request->getPost('category'),
					'category_sub' => $this->request->getPost('category_sub'),
					'description' => $this->request->getPost('description'),
					'content' => $this->request->getPost('content'),
					'publisher' => $this->request->getPost('publisher'),
					'numbers' => $this->request->getPost('numbers'),
					'author' => $this->request->getPost('author'),
					'url' => $this->request->getPost('url'),
					'publish_date' => $this->request->getPost('publish_date'),
					'updated_by' => user_id(),
				];

				// Logic Upload
				$files = (array) $this->request->getPost('file_cover');
				if (count($files)) {
					$listed_file = array();
					foreach ($files as $uuid => $name) {
						if (file_exists($this->modulePath . $name)) {
							$listed_file[] = $name;
						} else {
							if (file_exists($this->uploadPath . $name)) {
								$file = new File($this->uploadPath . $name);
								$newFileName = $file->getRandomName();
								$file->move($this->modulePath, $newFileName);
								$listed_file[] = $newFileName;
							}
						}
					}
					$update_data['file_cover'] = implode(',', $listed_file);
				}

				// Logic Upload
				$files = (array) $this->request->getPost('file_image');
				if (count($files)) {
					$listed_file = array();
					foreach ($files as $uuid => $name) {
						if (file_exists($this->modulePath . $name)) {
							$listed_file[] = $name;
						} else {
							if (file_exists($this->uploadPath . $name)) {
								$file = new File($this->uploadPath . $name);
								$newFileName = $file->getRandomName();
								$file->move($this->modulePath, $newFileName);
								$listed_file[] = $newFileName;
							}
						}
					}

					$new_file_images = $listed_file;
					$del_file_images = (array) $this->request->getPost('file_image_del');
					$old_file_images = array_filter(explode(',', $koleksiumum->file_image));
					$merge_file_images = array();
					if (!empty($del_file_images)) {
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$merge_file_images = array_merge($dif_file_images, $new_file_images);
					} else {
						$merge_file_images = array_merge($old_file_images, $new_file_images);
					}

					$file_image = implode(',', $merge_file_images);
					$update_data['file_image'] = $file_image;
				} else {
					$merge_file_images = array();
					$del_file_images = (array) $this->request->getPost('file_image_del');
					$old_file_images = array_filter(explode(',', $koleksiumum->file_image));
					if (count($del_file_images)) {
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$file_image = implode(',', $dif_file_images);
						$update_data['file_image'] = $file_image;

						foreach ($del_file_images as $row) {
							unlink_file($this->modulePath, $row);
						}
					}
				}
				$update_data_id = $this->koleksiumumModel->update($id, $update_data);
				if ($update_data_id) {
					add_log('Ubah Halaman', 'koleksiumum', 'edit', 't_page', $id);
					set_message('toastr_msg', 'Page berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/koleksiumum');
				} else {
					set_message('toastr_msg', 'Page gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Page gagal diubah');
					return redirect()->to('/cms/koleksiumum');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/koleksiumum/edit/' . $id);
		echo view('KoleksiUmum\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('koleksiumum/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$koleksiumum = $this->koleksiumumModel->find($id);
		$koleksiumumDelete = $this->koleksiumumModel->delete($id);
		if ($koleksiumumDelete) {
			// unlink_file($this->modulePath, $koleksiumum->images);
			// unlink_file($this->modulePath, 'thumb_'.$koleksiumum->images);

			add_log('Hapus  Koleksi Umum', 'koleksiumum', 'delete', 't_koleksi_umum', $id);
			set_message('toastr_msg', ' KoleksiUmum berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/koleksiumum');
		} else {
			set_message('toastr_msg', ' KoleksiUmum gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/koleksiumum');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');

		$koleksiumumUpdate = $this->koleksiumumModel->update($id, array($field => $value));

		if ($koleksiumumUpdate) {
			set_message('toastr_msg', ' KoleksiUmum berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' KoleksiUmum gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/koleksiumum');
	}

	public function export()
	{
		if (!is_allowed('koleksiumum/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$query = $this->koleksiumumModel
			->select('t_koleksi_umum.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_koleksi_umum.created_by', 'left')
			->join('users updated', 'updated.id = t_koleksi_umum.updated_by', 'left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "KoleksiUmum");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul KoleksiUmum");
		$sheet->setCellValue("C2", "Pengarang/Penulis");
		$sheet->setCellValue("D2", "Aktif");
		$sheet->setCellValue("E2", "Created By");
		$sheet->setCellValue("F2", "Updated By");
		$sheet->setCellValue("G2", "Foto Cover");
		$sheet->setCellValue("H2", "Konten Digital");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(10);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);

		$sheet->getStyle('A2:H2')->getFont()->setBold(true)->setSize(12);

		$col = 3;
		$no = 1;
		$i = 1;
		foreach ($results as $row) {
			$sheet->setCellValue("A" . $col, $no);
			$sheet->setCellValue("B" . $col, $row->title);
			$sheet->setCellValue("C" . $col, $row->author);
			$sheet->setCellValue("D" . $col, $row->active);
			$sheet->setCellValue("E" . $col, $row->created_at . ' | ' . strtoupper($row->created_name));
			$sheet->setCellValue("F" . $col, $row->updated_at . ' | ' . strtoupper($row->updated_name));
			$sheet->setCellValue("G" . $col, base_url('uploads/koleksiumum/' . $row->file_image));
			$sheet->setCellValue("H" . $col, base_url('uploads/koleksiumum/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Koleksi Umum';
		$filename = ucwords($subject) . '-' . date('Y-m-d');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function thumb()
	{
		$from = $this->request->getVar('from');
		$to = $this->request->getVar('to');

		for ($i = $from; $i <= $to; $i++) {
			$koleksiumum = $this->koleksiumumModel->find($i);
			$newFileName = $koleksiumum->file_image;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
