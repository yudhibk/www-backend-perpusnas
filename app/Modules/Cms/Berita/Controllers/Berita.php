<?php

namespace Berita\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Berita extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $beritaModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{
		$this->beritaModel = new \Berita\Models\BeritaModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/berita/';
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
		if (!is_allowed('cms/berita/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Berita';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('Berita\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('cms/berita/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/home');
		}

		$berita = $this->beritaModel->find($id);
		$this->data['title'] = 'Berita - Detail';
		$this->data['berita'] = $berita;
		echo view('Berita\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('cms/berita/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah  Berita';

		$this->validation->setRule('title', 'Judul Berita', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'category_sub' => $this->request->getPost('category_sub'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'en_content' => $this->request->getPost('en_content'),
				'publish_date' => $this->request->getPost('publish_date'),
				'language' => $this->request->getPost('language') ?? 'id',
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

			if (is_admin()) {
				$index_arr = $this->request->getPost('index');
				if (!empty($index_arr)) {
					$meta = array();
					foreach ($index_arr as $index => $value) {
						$meta[] = [
							'key' => $this->request->getPost('key')[$value],
							'value' => $this->request->getPost('value')[$value],
						];
					}
					if (!empty($meta)) {
						$save_data['meta'] = json_encode($meta);
					}
				}
			}

			$newBeritaId = $this->beritaModel->insert($save_data);
			if ($newBeritaId) {
				add_log('Tambah  Berita', 'berita', 'create', 't_berita', $newBeritaId);
				set_message('toastr_msg', ' Berita berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/berita');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' Berita gagal ditambah');
				echo view('Berita\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('berita/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Berita\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('cms/berita/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Berita';
		$berita = $this->beritaModel->find($id);
		$this->data['berita'] = $berita;

		$this->validation->setRule('title', 'Judul Berita', 'required');
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
					'publish_date' => $this->request->getPost('publish_date'),
					'language' => $this->request->getPost('language') ?? 'id',
					'updated_by' => user_id(),
				];

				if (is_member('admin')) {
					$channel = $this->request->getPost('channel');
					if (!empty($channel)) {
						$update_data['channel'] = $channel;
					}
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
					$old_file_images = array_filter(explode(',', $berita->file_image));
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
					$old_file_images = array_filter(explode(',', $berita->file_image));
					if (count($del_file_images)) {
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$file_image = implode(',', $dif_file_images);
						$update_data['file_image'] = $file_image;

						foreach ($del_file_images as $row) {
							unlink_file($this->modulePath, $row);
						}
					}
				}

				if (is_admin()) {
					$index_arr = $this->request->getPost('index');
					if (!empty($index_arr)) {
						$meta = array();
						foreach ($index_arr as $index => $value) {
							$meta[] = [
								'key' => $this->request->getPost('key')[$value],
								'value' => $this->request->getPost('value')[$value],
							];
						}
						if (!empty($meta)) {
							$update_data['meta'] = json_encode($meta);
						}
					}
				}
				$pageUpdate = $this->beritaModel->update($id, $update_data);
				if ($pageUpdate) {
					add_log('Ubah Berita', 'berita', 'edit', 't_page', $id);
					set_message('toastr_msg', 'Page berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/berita');
				} else {
					set_message('toastr_msg', 'Page gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Page gagal diubah');
					return redirect()->to('/cms/berita');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/berita/edit/' . $id);
		echo view('Berita\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('cms/berita/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$berita = $this->beritaModel->find($id);
		$beritaDelete = $this->beritaModel->delete($id);
		if ($beritaDelete) {
			unlink_file($this->modulePath, $berita->file_cover);
			unlink_file($this->modulePath, $berita->file_image);

			add_log('Hapus  Berita', 'berita', 'delete', 't_berita', $id);
			set_message('toastr_msg', ' Berita berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/berita');
		} else {
			set_message('toastr_msg', ' Berita gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/berita');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');

		$beritaUpdate = $this->beritaModel->update($id, array($field => $value));

		if ($beritaUpdate) {
			set_message('toastr_msg', ' Berita berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Berita gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/berita');
	}

	public function export()
	{
		if (!is_allowed('cms/berita/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$query = $this->beritaModel
			->select('t_berita.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_berita.created_by', 'left')
			->join('users updated', 'updated.id = t_berita.updated_by', 'left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "Berita");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul Berita");
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
			$sheet->setCellValue("G" . $col, base_url('uploads/berita/' . $row->file_image));
			$sheet->setCellValue("H" . $col, base_url('uploads/berita/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Berita';
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
			$berita = $this->beritaModel->find($i);
			$newFileName = $berita->file_image;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
