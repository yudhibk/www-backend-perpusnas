<?php

namespace Event\Controllers;

use \CodeIgniter\Files\File;

class Event extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;
    protected $eventModel;
    protected $uploadPath;
    protected $modulePath;

    function __construct()
    {

        $this->eventModel = new \Event\Models\EventModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/event/';
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
        if (!is_allowed('event/access')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $query = $this->eventModel
            ->select('t_event.*')
            ->select('c_references.name as category')
            ->join('c_references', 'c_references.id = t_event.category_id', 'left')

            ->select('created.username as created_name')
            ->select('updated.username as updated_name')
            ->join('users created', 'created.id = t_event.created_by', 'left')
            ->join('users updated', 'updated.id = t_event.updated_by', 'left');
        $events = $query->findAll();

        $this->data['title'] = 'Event';
        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['events'] = $events;
        $this->data['redirect'] = base_url('event/index');
        echo view('Event\Views\list', $this->data);
    }

    public function create()
    {
        if (!is_allowed('event/create')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Tambah Event';

        $this->validation->setRule('name', 'Nama', 'required');
        $this->validation->setRule('category_id', 'Kategori', 'required');
        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $slug = url_title($this->request->getPost('name'), '-', TRUE);
            $save_data = [
                'name' => $this->request->getPost('name'),
                'category_id' => $this->request->getPost('category_id'),
                'slug' => $slug,
                'sort' => $this->request->getPost('sort'),
                'host' => $this->request->getPost('host'),
                'place' => $this->request->getPost('place'),
                'pic_name' => $this->request->getPost('pic_name'),
                'pic_phone' => $this->request->getPost('pic_phone'),
                'date_from' => $this->request->getPost('date_from'),
                'date_to' => $this->request->getPost('date_to'),
                'description' => $this->request->getPost('description'),
                'content' => $this->request->getPost('content'),
                'created_by' => user_id(),
            ];

            // Logic Upload
            $file_images = (array) $this->request->getPost('file_image');
            if (count($file_images)) {
                $listed_file = array();
                foreach ($file_images as $uuid => $name) {
                    if (file_exists($this->modulePath . $name)) {
                        $listed_file[] = $name;
                    } else {
                        if (file_exists($this->uploadPath . $name)) {
                            $file = new File($this->uploadPath . $name);
                            $newFileName = $file->getRandomName();
                            $file->move($this->modulePath, $newFileName);
                            $listed_file[] = $newFileName;

                            create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                        }
                    }
                }
                $save_data['file_image'] = implode(',', $listed_file);
            }

            $file_pdfs = (array) $this->request->getPost('file_pdf');
            if (count($file_pdfs)) {
                $listed_file = array();
                foreach ($file_pdfs as $uuid => $name) {
                    if (file_exists($this->uploadPath . $name)) {
                        $file = new File($this->uploadPath . $name);
                        $newFileName = $file->getRandomName();
                        $file->move($this->modulePath, $newFileName);
                        $listed_file[] = $newFileName;
                    }
                }
                $save_data['file_pdf'] = implode(',', $listed_file);
            }

            $newEventId = $this->eventModel->insert($save_data);

            if ($newEventId) {
                add_log('Tambah Event', 'event', 'create', 't_event', $newEventId);
                set_message('toastr_msg', lang('Event.info.successfully_saved'));
                set_message('toastr_type', 'success');
                return redirect()->to('/event');
            } else {
                set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : lang('Event.info.failed_saved'));
                echo view('Event\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('event/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            echo view('Event\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        if (!is_allowed('event/update')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        $this->data['title'] = 'Ubah Event';
        $event = $this->eventModel->find($id);
        $this->data['event'] = $event;

        $this->validation->setRule('name', 'Nama', 'required');
        $this->validation->setRule('category_id', 'Jenis Tokoh', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => (!empty($this->request->getPost('slug'))) ? $this->request->getPost('slug') : $slug,
                    'category_id' => $this->request->getPost('category_id'),
                    'sort' => $this->request->getPost('sort'),
                    'host' => $this->request->getPost('host'),
                    'place' => $this->request->getPost('place'),
                    'pic_name' => $this->request->getPost('pic_name'),
                    'pic_phone' => $this->request->getPost('pic_phone'),
                    'date_from' => $this->request->getPost('date_from'),
                    'date_to' => $this->request->getPost('date_to'),
                    'description' => $this->request->getPost('description'),
                    'content' => $this->request->getPost('content'),
                    'updated_by' => user_id(),
                ];

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
                    $update_data['file_image'] = implode(',', $listed_file);
                }

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
                    $update_data['file_pdf'] = implode(',', $listed_file);
                }

                $eventUpdate = $this->eventModel->update($id, $update_data);

                if ($eventUpdate) {
                    add_log('Ubah Event', 'event', 'edit', 't_event', $id);
                    set_message('toastr_msg', 'Event berhasil diubah');
                    set_message('toastr_type', 'success');
                    return redirect()->to('/event');
                } else {
                    set_message('toastr_msg', 'Event gagal diubah');
                    set_message('toastr_type', 'warning');
                    set_message('message', 'Event gagal diubah');
                    return redirect()->to('/event/edit/' . $id);
                }
            }
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
        $this->data['redirect'] = base_url('event/edit/' . $id);
        echo view('Event\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!is_allowed('event/delete')) {
            set_message('toastr_msg', lang('App.permission.not.have'));
            set_message('toastr_type', 'error');
            return redirect()->to('/dashboard');
        }

        if (!$id) {
            set_message('toastr_msg', 'Sorry you have to provide parameter (id)');
            set_message('toastr_type', 'error');
            return redirect()->to('/event');
        }
        $event = $this->eventModel->find($id);
        $eventDelete = $this->eventModel->delete($id);
        if ($eventDelete) {
            unlink_file($this->modulePath, $event->file_image);
            unlink_file($this->modulePath, 'thumb_' . $event->file_image);
            unlink_file($this->modulePath, $event->file_pdf);

            add_log('Hapus Event', 'event', 'delete', 't_event', $id);
            set_message('toastr_msg', lang('Event.info.successfully_deleted'));
            set_message('toastr_type', 'success');
            return redirect()->to('/event');
        } else {
            set_message('toastr_msg', lang('Event.info.failed_deleted'));
            set_message('toastr_type', 'warning');
            set_message('message', lang('Event.info.failed_deleted'));
            return redirect()->to('/event/delete/' . $id);
        }
    }

    public function apply_status($id)
    {
        $field = $this->request->getVar('field');
        $value = $this->request->getVar('value');

        $eventUpdate = $this->eventModel->update($id, array($field => $value));

        if ($eventUpdate) {
            set_message('toastr_msg', ' Event berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' Event gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/event');
    }

    public function thumb()
    {
        $from = $this->request->getVar('from');
        $to = $this->request->getVar('to');

        for ($i = $from; $i <= $to; $i++) {
            $event = $this->eventModel->find($i);
            $newFileName = $event->file_image;
            if (!file_exists($this->modulePath . '/thumb_' . $newFileName)) {
                create_thumbnail($this->modulePath, $newFileName, 'thumb_', 250);
                echo "success generate thumbnail for ID: " . $i . " <br>";
            } else {
                echo "already exist, failed generate thumbnail for ID: " . $i . " <br>";
            }
        }
    }
}
