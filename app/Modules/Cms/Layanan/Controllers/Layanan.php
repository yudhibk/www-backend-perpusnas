<?php

namespace Layanan\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Layanan\Models\LayananModel;

class Layanan extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $layananModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {

        $this->layananModel = new LayananModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/layanan/';
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
        if (!is_allowed('layanan/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' Layanan';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        echo view('Layanan\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('layanan/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/home');
        }

        $layanan = $this->layananModel->find($id);
        $this->data['title'] = 'Layanan - Detail';
        $this->data['layanan'] = $layanan;
        echo view('Layanan\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('layanan/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Layanan';
        $slug = $this->request->getVar('slug');

        $this->validation->setRule('title', 'Judul Layanan', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $title = $this->request->getPost('title');
            $save_data = [
                'title' => $title,
                'slug' => slugify($title),
                'category' => 'Layanan',
                'category_sub' => $this->request->getPost('category_sub'),
                'sort' => $this->request->getPost('sort'),
                'url' => $this->request->getPost('url'),
                'description' => $this->request->getPost('description'),
                'content' => $this->request->getPost('content'),
                'created_by' => user_id(),
            ];
            // Logic Upload
            $files = (array) $this->request->getPost('file_cover');
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
                $save_data['file_cover'] = implode(',', $listed_file);
            }

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
                $save_data['file_image'] = implode(',', $listed_file);
            }

            $newPageId = $this->layananModel->insert($save_data);
            if ($newPageId) {
                add_log('Tambah Layanan', 'layanan', 'create', 't_page', $newPageId);
                set_message('toastr_msg', 'Layanan berhasil ditambah');
                set_message('toastr_type', 'success');
                return redirect()->to('/cms/layanan');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Page.info.failed_saved'));
                echo view('Layanan\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('layanan/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Layanan\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('layanan/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Layanan';
        $layanan = $this->layananModel->find($id);
        $this->data['layanan'] = $layanan;

        $this->validation->setRule('title', 'Judul Layanan', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $title = $this->request->getPost('title');
                $update_data = [
                    'title' => $title,
                    'slug' => slugify($title),
                    'category_sub' => $this->request->getPost('category_sub'),
                    'sort' => $this->request->getPost('sort'),
                    'url' => $this->request->getPost('url'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

                // Logic Upload
                $file_covers = (array) $this->request->getPost('file_cover');
                if (count($file_covers)) {
                    $listed_file = array();
                    foreach ($file_covers as $uuid => $name) {
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
                $file_images = (array) $this->request->getPost('file_image');
                $listed_file = [];
                $del_file_images = (array) $this->request->getPost('file_image_del');
                $old_file_images = $layanan->file_image ? array_filter(explode(',', $layanan->file_image)) : [];

                // Process new file images if any
                if (count($file_images)) {
                    foreach ($file_images as $uuid => $name) {
                        // Check if file exists in module path or upload path
                        if (file_exists($this->modulePath . $name)) {
                            $listed_file[] = $name;
                        } elseif (file_exists($this->uploadPath . $name)) {
                            // Move the file to the module path
                            $file = new File($this->uploadPath . $name);
                            $newFileName = $file->getRandomName();
                            $file->move($this->modulePath, $newFileName);
                            $listed_file[] = $newFileName;
                        }
                    }

                    // Merge old and new file images
                    $merge_file_images = array_merge($old_file_images, $listed_file);
                } else {
                    // If no new files, just delete files
                    $merge_file_images = $old_file_images;
                    if (count($del_file_images)) {
                        // Remove deleted files from the list
                        $merge_file_images = array_diff($merge_file_images, $del_file_images);

                        // Delete the files physically from the module path
                        foreach ($del_file_images as $file) {
                            unlink_file($this->modulePath, $file);
                        }
                    }
                }

                // Update the file images field in the database
                $file_image = implode(',', $merge_file_images);
                $update_data['file_image'] = $file_image;

                // Perform the database update
                $pageUpdate = $this->layananModel->update($id, $update_data);

                if ($pageUpdate) {
                    add_log('Ubah Layanan', 'layanan', 'edit', 't_page', $id);
                    set_message('toastr_msg', 'Page berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/layanan');
                } else {
                    set_message('toastr_msg', 'Page gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Page gagal diubah');
                    return redirect()->to('/cms/layanan');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/layanan/edit/' . $id);
        echo view('Layanan\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('layanan/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $layanan = $this->layananModel->find($id);
        $layananDelete = $this->layananModel->delete($id);
        if ($layananDelete) {
            unlink_file($this->modulePath, $layanan->file_image);
            unlink_file($this->modulePath, 'thumb_' . $layanan->file_image);
            unlink_file($this->modulePath, $layanan->file_pdf);

            add_log('Hapus  Layanan', 'layanan', 'delete', 't_layanan', $id);
            set_message('toastr_msg', ' Layanan berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/layanan');
        } else {
            set_message('toastr_msg', ' Layanan gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/layanan');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');
        $layanan = $this->layananModel->find($id);

        $layananUpdate = $this->layananModel->update($id, array($field => $value));

        if ($layananUpdate) {
            set_message('toastr_msg', ' Layanan berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Layanan gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/layanan');
    }

    public function export()
    {
        if (!is_allowed('layanan/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->layananModel
            ->select('t_layanan.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_layanan.created_by', 'left')
            ->join('users updated', 'updated.id = t_layanan.updated_by', 'left');

        $results = $query->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue("A1", "Layanan");
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
            $sheet->setCellValue("G" . $col, base_url('uploads/layanan/' . $row->file_image));
            $sheet->setCellValue("H" . $col, base_url('uploads/layanan/' . $row->file_pdf));

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Layanan';
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
            $layanan = $this->layananModel->find($i);
            $newFileName = $layanan->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
