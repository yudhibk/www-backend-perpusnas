<?php

namespace MajalahOnline\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use MajalahOnline\Models\MajalahOnlineModel;

class MajalahOnline extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $majalahonlineModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{

		$this->majalahonlineModel = new MajalahOnlineModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/majalahonline/';
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

	public function edition()
	{
		if (!is_allowed('majalahonline/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Majalah Online';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('MajalahOnline\Views\edition', $this->data);
	}

	public function index($edition = null)
	{
		if (!is_allowed('majalahonline/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Majalah Online';
		$this->data['edition'] = $edition;
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('MajalahOnline\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('majalahonline/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$majalahonline = $this->majalahonlineModel->find($id);
		$this->data['title'] = 'MajalahOnline - Detail';
		$this->data['majalahonline'] = $majalahonline;
		echo view('MajalahOnline\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('majalahonline/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah  Majalah Online';

		$this->validation->setRule('title', 'Judul Majalah Online', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'edition' => $this->request->getPost('edition'),
				'subject' => $this->request->getPost('subject'),
				'author' => $this->request->getPost('author'),
				'source' => $this->request->getPost('source'),
				'keyword' => $this->request->getPost('keyword'),
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

			$save_data_id = $this->majalahonlineModel->insert($save_data);
			if ($save_data_id) {
				add_log('Tambah  Majalah Online', 'majalahonline', 'create', 't_majalah_online', $save_data_id);
				set_message('toastr_msg', 'Majalah Online berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/majalahonline');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : 'Majalah Online gagal ditambah');
				echo view('MajalahOnline\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('majalahonline/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('MajalahOnline\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('majalahonline/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Majalah Online';
		$majalahonline = $this->majalahonlineModel->find($id);
		$this->data['majalahonline'] = $majalahonline;

		$this->validation->setRule('title', 'Judul Majalah Online', 'required');
		if ($this->request->getPost()) {
			if ($this->validation->withRequest($this->request)->run()) {
				$title = $this->request->getPost('title');
				$update_data = [
					'title' => $title,
					'slug' => slugify($title),
					'category' => $this->request->getPost('category'),
					'description' => $this->request->getPost('description'),
					'content' => $this->request->getPost('content'),
					'edition' => $this->request->getPost('edition'),
					'subject' => $this->request->getPost('subject'),
					'author' => $this->request->getPost('author'),
					'source' => $this->request->getPost('source'),
					'keyword' => $this->request->getPost('keyword'),
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
					$old_file_images = array_filter(explode(',', $majalahonline->file_image));
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
					$old_file_images = array_filter(explode(',', $majalahonline->file_image));
					if (count($del_file_images)) {
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$file_image = implode(',', $dif_file_images);
						$update_data['file_image'] = $file_image;

						foreach ($del_file_images as $row) {
							unlink_file($this->modulePath, $row);
						}
					}
				}
				$update_data_id = $this->majalahonlineModel->update($id, $update_data);
				if ($update_data_id) {
					add_log('Ubah Majalah Online', 'majalahonline', 'edit', 't_page', $id);
					set_message('toastr_msg', 'Page berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/majalahonline');
				} else {
					set_message('toastr_msg', 'Page gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Page gagal diubah');
					return redirect()->to('/cms/majalahonline');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/majalahonline/edit/' . $id);
		echo view('MajalahOnline\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('majalahonline/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$majalahonline = $this->majalahonlineModel->find($id);
		$majalahonlineDelete = $this->majalahonlineModel->delete($id);
		if ($majalahonlineDelete) {
			// unlink_file($this->modulePath, $majalahonline->images);
			// unlink_file($this->modulePath, 'thumb_'.$majalahonline->images);

			add_log('Hapus  Majalah Online', 'majalahonline', 'delete', 't_majalah_online', $id);
			set_message('toastr_msg', 'Majalah Online berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/majalahonline');
		} else {
			set_message('toastr_msg', 'Majalah Online gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/majalahonline');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');

		$majalahonlineUpdate = $this->majalahonlineModel->update($id, array($field => $value));

		if ($majalahonlineUpdate) {
			set_message('toastr_msg', 'Majalah Online berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', 'Majalah Online gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/majalahonline');
	}

	public function export()
	{
		if (!is_allowed('majalahonline/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$query = $this->majalahonlineModel
			->select('t_majalah_online.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_majalah_online.created_by', 'left')
			->join('users updated', 'updated.id = t_majalah_online.updated_by', 'left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "MajalahOnline");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul MajalahOnline");
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
			$sheet->setCellValue("G" . $col, base_url('uploads/majalahonline/' . $row->file_image));
			$sheet->setCellValue("H" . $col, base_url('uploads/majalahonline/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Majalah Online';
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
			$majalahonline = $this->majalahonlineModel->find($i);
			$newFileName = $majalahonline->file_image;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
