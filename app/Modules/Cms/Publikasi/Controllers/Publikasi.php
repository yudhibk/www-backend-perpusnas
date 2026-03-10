<?php

namespace Publikasi\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Publikasi\Models\PublikasiModel;

class Publikasi extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $publikasiModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{

		$this->publikasiModel = new PublikasiModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/publikasi/';
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
		if (!is_allowed('cms/publikasi/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Publikasi';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('Publikasi\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('cms/publikasi/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$publikasi = $this->publikasiModel->find($id);
		$this->data['title'] = 'Publikasi - Detail';
		$this->data['publikasi'] = $publikasi;
		echo view('Publikasi\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('cms/publikasi/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah  Publikasi Baru';

		$this->validation->setRule('title', 'Judul Buku Baru', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'year' => $this->request->getPost('year'),
				'source' => $this->request->getPost('source'),
				'publish_date' => $this->request->getPost('publish_date'),
				'created_by' => user_id(),
			];

			if (is_member('admin')) {
				$channel = $this->request->getPost('channel');
				if (!empty($channel)) {
					$save_data['channel'] = $channel;
				}
			} else {
				$group = get_group();
				$save_data['channel'] = $group->name;
			}

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

			// Proses upload PDF
			$pdfFile = $this->request->getFile('file_pdf');
			
			if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
				$newName = $pdfFile->getRandomName();
				$pdfFile->move($this->modulePath, $newName);
				$save_data['file_pdf'] = $newName;
			}

			$newPublikasiId = $this->publikasiModel->insert($save_data);
			if ($newPublikasiId) {
				add_log('Tambah  Buku Baru', 'publikasi', 'create', 't_buku_baru', $newPublikasiId);
				set_message('toastr_msg', ' Publikasi berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/publikasi');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' Publikasi gagal ditambah');
				echo view('Publikasi\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('publikasi/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Publikasi\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('cms/publikasi/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Halaman';
		$publikasi = $this->publikasiModel->find($id);
		$this->data['publikasi'] = $publikasi;

		$this->validation->setRule('title', 'Judul Halaman', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$title = $this->request->getPost('title');
				$update_data = [
					'title' => $title,
					'slug' => slugify($title),
					'category' => $this->request->getPost('category'),
					'description' => $this->request->getPost('description'),
					'content' => $this->request->getPost('content'),
					'year' => $this->request->getPost('year'),
					'source' => $this->request->getPost('source'),
					'publish_date' => $this->request->getPost('publish_date'),
					'language' => $this->request->getPost('language') ?? 'id',
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
				$files = (array) $this->request->getPost('file_pdf');
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

					$new_file_pdfs = $listed_file;
					$del_file_pdfs = (array) $this->request->getPost('file_pdf_del');
					$old_file_pdfs = array_filter(explode(',', $publikasi->file_pdf));
					$merge_file_pdfs = array();
					if (!empty($del_file_pdfs)) {
						$dif_file_pdfs = array_diff($old_file_pdfs, $del_file_pdfs);
						$merge_file_pdfs = array_merge($dif_file_pdfs, $new_file_pdfs);
					} else {
						$merge_file_pdfs = array_merge($old_file_pdfs, $new_file_pdfs);
					}

					$file_pdf = implode(',', $merge_file_pdfs);
					$update_data['file_pdf'] = $file_pdf;
				} else {
					$merge_file_pdfs = array();
					$del_file_pdfs = (array) $this->request->getPost('file_pdf_del');
					$old_file_pdfs = array_filter(explode(',', $publikasi->file_pdf));
					if (count($del_file_pdfs)) {
						$dif_file_pdfs = array_diff($old_file_pdfs, $del_file_pdfs);
						$file_pdf = implode(',', $dif_file_pdfs);
						$update_data['file_pdf'] = $file_pdf;

						foreach ($del_file_pdfs as $row) {
							unlink_file($this->modulePath, $row);
						}
					}
				}
				$pageUpdate = $this->publikasiModel->update($id, $update_data);
				if ($pageUpdate) {
					add_log('Ubah Halaman', 'publikasi', 'edit', 't_page', $id);
					set_message('toastr_msg', 'Page berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/publikasi');
				} else {
					set_message('toastr_msg', 'Page gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Page gagal diubah');
					return redirect()->to('/cms/publikasi');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/publikasi/edit/' . $id);
		echo view('Publikasi\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('publikasi/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$publikasi = $this->publikasiModel->find($id);
		$publikasiDelete = $this->publikasiModel->delete($id);
		if ($publikasiDelete) {
			// unlink_file($this->modulePath, $publikasi->images);
			// unlink_file($this->modulePath, 'thumb_'.$publikasi->images);

			add_log('Hapus  Buku Baru', 'publikasi', 'delete', 't_buku_baru', $id);
			set_message('toastr_msg', ' Publikasi berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/publikasi');
		} else {
			set_message('toastr_msg', ' Publikasi gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/publikasi');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');

		$publikasiUpdate = $this->publikasiModel->update($id, array($field => $value));

		if ($publikasiUpdate) {
			set_message('toastr_msg', ' Publikasi berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Publikasi gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/publikasi');
	}

	public function export()
	{
		if (!is_allowed('publikasi/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$query = $this->publikasiModel
			->select('t_buku_baru.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_buku_baru.created_by', 'left')
			->join('users updated', 'updated.id = t_buku_baru.updated_by', 'left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "Publikasi");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul Publikasi");
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
			$sheet->setCellValue("G" . $col, base_url('uploads/publikasi/' . $row->file_pdf));
			$sheet->setCellValue("H" . $col, base_url('uploads/publikasi/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Publikasi';
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
			$publikasi = $this->publikasiModel->find($i);
			$newFileName = $publikasi->file_pdf;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
