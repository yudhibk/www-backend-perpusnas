<?php

namespace Pameran\Controllers;

use CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Pameran\Models\PameranModel;

class Pameran extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $pameranModel;
    protected $uploadPath;
    protected $modulePath;
    protected $db;

    function __construct()
    {


        $this->pameranModel = new PameranModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/pameran/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }

        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();


        if (!$this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }

        helper('adminigniter');
        helper('thumbnail');
        helper('reference');
    }

    public function index()
    {
        if (!is_allowed('cms/pameran/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = ' Pameran';
        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        echo view('Pameran\Views\list', $this->data);
    }

    public function detail(int $id)
    {
        if (!is_allowed('cms/pameran/read')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $pameran = $this->pameranModel->find($id);
        $this->data['title'] = 'Pameran - Detail';
        $this->data['pameran'] = $pameran;
        echo view('Pameran\Views\view', $this->data);
    }

    public function create()
    {
        if (!is_allowed('cms/pameran/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah  Pameran';

        $this->validation->setRule('title', 'Judul Artikel', 'required');
        if (
            $this->request->getPost() &&
            $this->validation->withRequest($this->request)->run()
        ) {
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
                $listed_file = [];
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
                $listed_file = [];
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
                    $meta = [];
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

            $newPameranId = $this->pameranModel->insert($save_data);
            if ($newPameranId) {
                add_log(
                    'Tambah  Pameran',
                    'pameran',
                    'create',
                    't_pameran',
                    $newPameranId
                );
                set_message('toastr_msg', ' Pameran berhasil ditambah');
                set_message('toastr_type', 'success');

                return redirect()->to('/cms/pameran');
            } else {
                set_message(
                    'message',
                    $this->validation->getErrors()
                        ? $this->validation->listErrors()
                        : ' Pameran gagal ditambah'
                );
                echo view('Pameran\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('pameran/create');
            set_message(
                'message',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : $this->session->getFlashdata('message')
            );
            echo view('Pameran\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('cms/pameran/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Pameran';
        $pameran = $this->pameranModel->find($id);
        $this->data['pameran'] = $pameran;

        $this->validation->setRule('title', 'Judul Pameran', 'required');
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
                    $listed_file = [];
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
                    $listed_file = [];
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
                    $del_file_images = (array) $this->request->getPost(
                        'file_image_del'
                    );
                    $old_file_images = array_filter(
                        explode(',', $pameran->file_image)
                    );
                    $merge_file_images = [];
                    if (!empty($del_file_images)) {
                        $dif_file_images = array_diff(
                            $old_file_images,
                            $del_file_images
                        );
                        $merge_file_images = array_merge(
                            $dif_file_images,
                            $new_file_images
                        );
                    } else {
                        $merge_file_images = array_merge(
                            $old_file_images,
                            $new_file_images
                        );
                    }

                    $file_image = implode(',', $merge_file_images);
                    $update_data['file_image'] = $file_image;
                } else {
                    $merge_file_images = [];
                    $del_file_images = (array) $this->request->getPost(
                        'file_image_del'
                    );
                    $old_file_images = array_filter(
                        explode(',', $pameran->file_image)
                    );
                    if (count($del_file_images)) {
                        $dif_file_images = array_diff(
                            $old_file_images,
                            $del_file_images
                        );
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
                        $meta = [];
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

                $pageUpdate = $this->pameranModel->update($id, $update_data);
                if ($pageUpdate) {
                    add_log('Ubah Pameran', 'pameran', 'edit', 't_page', $id);
                    set_message('toastr_msg', 'Pameran berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/cms/pameran');
                } else {
                    set_message('toastr_msg', 'Pameran gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Pameran gagal diubah');
                    return redirect()->to('/cms/pameran');
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors()
            ? $this->validation->listErrors()
            : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('cms/pameran/edit/' . $id);
        echo view('Pameran\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('cms/pameran/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message(
                'toastr_msg',
                'Sorry you have to provide parameter (id)'
            );
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }
        $pameran = $this->pameranModel->find($id);
        $pameranDelete = $this->pameranModel->delete($id);
        if ($pameranDelete) {
            // unlink_file($this->modulePath, $pameran->file_image);
            // unlink_file($this->modulePath, 'thumb_'.$pameran->file_image);
            // unlink_file($this->modulePath, $pameran->file_pdf);

            add_log('Hapus  Pameran', 'pameran', 'delete', 't_pameran', $id);
            set_message('toastr_msg', ' Pameran berhasil dihapus');
            set_message('toastr_type', 'success');
            return redirect()->to('/cms/pameran');
        } else {
            set_message('toastr_msg', ' Pameran gagal dihapus');
            set_message('toastr_type', 'warning');
            return redirect()->to('/cms/pameran');
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $pameranUpdate = $this->pameranModel->update($id, [$field => $value]);

        if ($pameranUpdate) {
            set_message('toastr_msg', ' Pameran berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Pameran gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/cms/pameran');
    }

    public function export()
    {
        if (!is_allowed('cms/pameran/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->pameranModel
            ->select('t_pameran.*')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_pameran.created_by', 'left')
            ->join(
                'users updated',
                'updated.id = t_pameran.updated_by',
                'left'
            );

        $results = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Pameran');
        $sheet
            ->getStyle('A1:H1')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Judul Artikel');
        $sheet->setCellValue('C2', 'Pengarang/Penulis');
        $sheet->setCellValue('D2', 'Aktif');
        $sheet->setCellValue('E2', 'Created By');
        $sheet->setCellValue('F2', 'Updated By');
        $sheet->setCellValue('G2', 'Foto Cover');
        $sheet->setCellValue('H2', 'Konten Digital');

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $sheet
            ->getStyle('A2:H2')
            ->getFont()
            ->setBold(true)
            ->setSize(12);

        $col = 3;
        $no = 1;
        $i = 1;
        foreach ($results as $row) {
            $sheet->setCellValue('A' . $col, $no);
            $sheet->setCellValue('B' . $col, $row->title);
            $sheet->setCellValue('C' . $col, $row->author);
            $sheet->setCellValue('D' . $col, $row->active);
            $sheet->setCellValue(
                'E' . $col,
                $row->created_at . ' | ' . strtoupper($row->created_name)
            );
            $sheet->setCellValue(
                'F' . $col,
                $row->updated_at . ' | ' . strtoupper($row->updated_name)
            );
            $sheet->setCellValue(
                'G' . $col,
                base_url('uploads/pameran/' . $row->file_image)
            );
            $sheet->setCellValue(
                'H' . $col,
                base_url('uploads/pameran/' . $row->file_pdf)
            );

            $col++;
            $no++;
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $subject = 'Pameran';
        $filename = ucwords($subject) . '-' . date('Y-m-d');

        header('Content-Type: application/vnd.ms-excel');
        header(
            'Content-Disposition: attachment;filename="' . $filename . '.xlsx"'
        );
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $pameran = $this->pameranModel->find($i);
            $newFileName = $pameran->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail(
                    $this->modulePath,
                    $newFileName,
                    'thumb_',
                    250
                );
                echo 'success generate thumbnail for ID: ' . $i . ' <br>';
            } else {
                echo 'already exist, failed generate thumbnail for ID: ' .
                    $i .
                    ' <br>';
            }
        }
    }
}
