<?php

namespace Testimoni\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Testimoni\Models\TestimoniModel;

class Testimoni extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $testimoniModel;
    protected $uploadPath;
    protected $modulePath;
	protected $db;
    
    function __construct()
    {

        $this->testimoniModel = new TestimoniModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/testimoni/';
if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        

        if (! $this->auth->check() )
		{
			$this->session->set('redirect_url', current_url() );
			return redirect()->route('login');
		} 

		helper('adminigniter');
		helper('thumbnail');
		helper('reference');
    }

    public function index()
    {
        if (!is_allowed('cms/testimoni/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' Testimoni';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('Testimoni\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('cms/testimoni/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $testimoni = $this->testimoniModel->find($id);
        $this->data['title'] = 'Testimoni - Detail';
        $this->data['testimoni'] = $testimoni;
        echo view('Testimoni\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/testimoni/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah  Testimoni';

        $this->validation->setRule('title', 'Judul Testimoni', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$title = $this->request->getPost('title');
			$save_data = [
				'title' => $title,
				'slug' => slugify($title),
				'category' => $this->request->getPost('category'),
				'language' => $this->request->getPost('language'),
				'description' => $this->request->getPost('description'),
				'content' => $this->request->getPost('content'),
				'publish_date' => $this->request->getPost('publish_date'),
				'created_by' => user_id(),
			];

			// Logic Upload
			$files = (array) $this->request->getPost('file_cover');
			// dd($files);
			if (count($files)) {
				$listed_file = array();
				// dd($listed_file);
				foreach ($files as $uuid => $name) {
					if (file_exists($this->modulePath . $name)) {
						$listed_file[] = $name;
						dd($listed_file);
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

			if(is_admin()){
				$index_arr = $this->request->getPost('index');
				if(!empty($index_arr)){
					$meta = array();
					foreach ($index_arr as $index => $value){
						$meta[] = [
							'key' => $this->request->getPost('key')[$value],
							'value' => $this->request->getPost('value')[$value],
						];
					}
					if(!empty($meta)){
						$save_data['meta'] = json_encode($meta);
					}
				}
			}

            $newTestimoniId = $this->testimoniModel->insert($save_data);

            if ($newTestimoniId) {
                add_log('Tambah  Testimoni', 'testimoni', 'create', 't_testimoni', $newTestimoniId);
                set_message('toastr_msg', ' Testimoni berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/testimoni');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : ' Testimoni gagal ditambah');
                echo view('Testimoni\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('testimoni/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Testimoni\Views\add', $this->data);
        }
    }

	public function edit(int $id = null)
    {
        if (!is_allowed('cms/testimoni/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Testimoni';
        $testimoni = $this->testimoniModel->find($id);
        $this->data['testimoni'] = $testimoni;

		$this->validation->setRule('title', 'Judul Testimoni', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
				$title = $this->request->getPost('title');
                $update_data = [
					'title' => $title,
					'slug' => slugify($title),
					'category' => $this->request->getPost('category'),
					'language' => $this->request->getPost('language'),
                    'description' => $this->request->getPost('description'),
					'content' => $this->request->getPost('content'),
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
					$old_file_images = array_filter(explode(',',$testimoni->file_image));
					$merge_file_images = array();
					if(!empty($del_file_images)){
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$merge_file_images = array_merge($dif_file_images, $new_file_images);	
					} else {
						$merge_file_images = array_merge($old_file_images, $new_file_images);
					}
	
					$file_image = implode(',',$merge_file_images);
					$update_data['file_image'] = $file_image;
				} else {
					$del_file_images = (array) $this->request->getPost('file_image_del');
					$old_file_images = $testimoni->file_image ? array_filter(explode(',', $testimoni->file_image)) : [];
					if (count($del_file_images)) {
						$dif_file_images = array_diff($old_file_images, $del_file_images);
						$file_image = implode(',',$dif_file_images);
						$update_data['file_image'] = $file_image;

						foreach($del_file_images as $row){
							unlink_file($this->modulePath, $row);
						}
					}
				}

				if(is_admin()){
					$index_arr = $this->request->getPost('index');
					if(!empty($index_arr)){
						$meta = array();
						foreach ($index_arr as $index => $value){
							$meta[] = [
								'key' => $this->request->getPost('key')[$value],
								'value' => $this->request->getPost('value')[$value],
							];
						}
						if(!empty($meta)){
							$update_data['meta'] = json_encode($meta);
						}
					}
				}
		                $pageUpdate = $this->testimoniModel->update($id, $update_data);
                if ($pageUpdate) {
                    add_log('Ubah Testimoni', 'testimoni', 'edit', 't_page', $id);
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/testimoni');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
					return redirect()->to('/cms/testimoni');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/testimoni/edit/' . $id);
        echo view('Testimoni\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/testimoni/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
		$testimoni= $this->testimoniModel->find($id);
        $testimoniDelete = $this->testimoniModel->delete($id);
        if ($testimoniDelete) {
			unlink_file($this->modulePath, $testimoni->file_cover);
			unlink_file($this->modulePath, $testimoni->file_image);

            add_log('Hapus  Testimoni', 'testimoni', 'delete', 't_testimoni', $id);
            set_message('toastr_msg', ' Testimoni berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/testimoni');
        } else {
            set_message('toastr_msg', ' Testimoni gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/testimoni');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $testimoniUpdate = $this->testimoniModel->update($id, array($field => $value));

        if ($testimoniUpdate) {
            set_message('toastr_msg', ' Testimoni berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Testimoni gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/testimoni');
    }

	public function export()
    {
		if (!is_allowed('cms/testimoni/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

		$query = $this->testimoniModel
			->select('t_testimoni.*')

			->select('created.username as created_name')
			->select('updated.username as updated_name')
			->join('users created','created.id = t_testimoni.created_by','left')
			->join('users updated','updated.id = t_testimoni.updated_by','left');

		$results = $query->findAll();
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:H1');
		$sheet->setCellValue("A1", "Testimoni");
		$sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

		$sheet->setCellValue("A2", "No");
		$sheet->setCellValue("B2", "Judul Testimoni");
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
			$sheet->setCellValue("A".$col, $no);
			$sheet->setCellValue("B".$col, $row->title);
			$sheet->setCellValue("C".$col, $row->author);
			$sheet->setCellValue("D".$col, $row->active);
			$sheet->setCellValue("E".$col, $row->created_at.' | '.strtoupper($row->created_name));
			$sheet->setCellValue("F".$col, $row->updated_at.' | '.strtoupper($row->updated_name));
			$sheet->setCellValue("G".$col, base_url('uploads/testimoni/' . $row->file_image));
			$sheet->setCellValue("H".$col, base_url('uploads/testimoni/' . $row->file_pdf));

			$col++;
			$no++;
			$i++;
		}

		$writer = new Xlsx($spreadsheet);
		$subject = 'Testimoni';
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

		for ($i=$from; $i <= $to ; $i++) { 
			$testimoni= $this->testimoniModel->find($i);
			$newFileName = $testimoni->file_image;
			if(!file_exists($this->modulePath.'/thumb_'.$newFileName)){
				create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
				echo "success generate thumbnail for ID: ".$i." <br>";
			} else {
				echo "already exist, failed generate thumbnail for ID: ".$i." <br>";
			}
		}
    }
}
