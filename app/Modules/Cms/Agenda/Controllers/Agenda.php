<?php

namespace Agenda\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Agenda extends \App\Controllers\BaseController
{
	protected $auth;
	protected $authorize;
	protected $agendaModel;
	protected $uploadPath;
	protected $modulePath;
	protected $db;

	function __construct()
	{
		$this->agendaModel = new \Agenda\Models\AgendaModel();
		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/agenda/';
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
		if (!is_allowed('cms/agenda/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = ' Agenda';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('Agenda\Views\list', $this->data);
	}

	public function detail(int $id)
	{
		if (!is_allowed('cms/agenda/read')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$agenda = $this->agendaModel->find($id);
		$this->data['title'] = 'Agenda - Detail';
		$this->data['agenda'] = $agenda;
		echo view('Agenda\Views\view', $this->data);
	}

	public function create()
	{
		if (!is_allowed('cms/agenda/create')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Tambah  Agenda';

		$this->validation->setRule('title', 'Judul Artikel', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'category_sub' => $this->request->getPost('category_sub'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'contact' => $this->request->getPost('contact'),
				'publish_date' => $this->request->getPost('publish_date'),
				'date_from' => $this->request->getPost('date_from'),
				'date_to' => $this->request->getPost('date_to'),
				'place' => $this->request->getPost('place'),
				'address' => $this->request->getPost('address'),
				'organizer' => $this->request->getPost('organizer'),
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

			$newAgendaId = $this->agendaModel->insert($save_data);
			if ($newAgendaId) {
				add_log('Tambah  Agenda', 'agenda', 'create', 't_agenda', $newAgendaId);
				set_message('toastr_msg', ' Agenda berhasil ditambah');
				set_message('toastr_type', 'success');
				return redirect()->to('/cms/agenda');
			} else {
				set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' Agenda gagal ditambah');
				echo view('Agenda\Views\add', $this->data);
			}
		} else {
			$this->data['redirect'] = base_url('agenda/create');
			set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
			echo view('Agenda\Views\add', $this->data);
		}
	}

	public function edit(int $id = null)
	{
		if (!is_allowed('cms/agenda/update')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$this->data['title'] = 'Ubah Agenda';
		$agenda = $this->agendaModel->find($id);
		$this->data['agenda'] = $agenda;

		$this->validation->setRule('title', 'Judul Agenda', 'required');
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
					'contact' => $this->request->getPost('contact'),
					'publish_date' => $this->request->getPost('publish_date'),
					'date_from' => $this->request->getPost('date_from'),
					'date_to' => $this->request->getPost('date_to'),
					'place' => $this->request->getPost('place'),
					'address' => $this->request->getPost('address'),
					'organizer' => $this->request->getPost('organizer'),
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
					$update_data['file_image'] = implode(',', $listed_file);
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
				$pageUpdate = $this->agendaModel->update($id, $update_data);
				if ($pageUpdate) {
					add_log('Ubah Agenda', 'agenda', 'edit', 't_page', $id);
					set_message('toastr_msg', 'Agenda berhasil diubah');
					set_message('toastr_type', 'success');
					return redirect()->to('/cms/agenda');
				} else {
					set_message('toastr_msg', 'Agenda gagal diubah');
					set_message('toastr_type', 'warning');
					set_message('message', 'Agenda gagal diubah');
					return redirect()->to('/cms/agenda');
				}
			}
		}

		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		$this->data['redirect'] = base_url('cms/agenda/edit/' . $id);
		echo view('Agenda\Views\update', $this->data);
	}

	public function delete(int $id = 0)
	{
		if (!is_allowed('cms/agenda/delete')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		if (!$id) {
			set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}
		$agenda = $this->agendaModel->find($id);
		$agendaDelete = $this->agendaModel->delete($id);
		if ($agendaDelete) {
			// unlink_file($this->modulePath, $agenda->file_image);
			// unlink_file($this->modulePath, 'thumb_'.$agenda->file_image);
			// unlink_file($this->modulePath, $agenda->file_pdf);

			add_log('Hapus  Agenda', 'agenda', 'delete', 't_agenda', $id);
			set_message('toastr_msg', ' Agenda berhasil dihapus');
			set_message('toastr_type', 'success');
			return redirect()->to('/cms/agenda');
		} else {
			set_message('toastr_msg', ' Agenda gagal dihapus');
			set_message('toastr_type', 'warning');
			return redirect()->to('/cms/agenda');
		}
	}

	public function apply_status($id)
	{
		$field = $this->request->getVar('field');
		$value = $this->request->getVar('value');

		$agendaUpdate = $this->agendaModel->update($id, array($field => $value));

		if ($agendaUpdate) {
			set_message('toastr_msg', ' Agenda berhasil diubah');
			set_message('toastr_type', 'success');
		} else {
			set_message('toastr_msg', ' Agenda gagal diubah');
			set_message('toastr_type', 'warning');
		}
		return redirect()->to('/cms/agenda');
	}

	public function export()
	{
		if (!is_allowed('cms/agenda/access')) {
			set_message('toastr_msg', lang('App.permission.not.have'));
			set_message('toastr_type', 'error');
			return redirect()->to('/dashboard');
		}

		$query = $this->agendaModel
			->select('t_agenda.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created', 'created.id = t_agenda.created_by', 'left')
			->join('users updated', 'updated.id = t_agenda.updated_by', 'left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "Agenda");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul Artikel");
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
			$sheet->setCellValue("G" . $col, base_url('uploads/agenda/' . $row->file_image));
			$sheet->setCellValue("H" . $col, base_url('uploads/agenda/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Agenda';
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
			$agenda = $this->agendaModel->find($i);
			$newFileName = $agenda->file_image;
			if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: " . $i . " <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
			}
		}
	}
}
