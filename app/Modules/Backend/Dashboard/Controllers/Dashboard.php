<?php

namespace Dashboard\Controllers;

class Dashboard extends \App\Controllers\BaseController
{
    protected $auth;
    protected $authorize;

    function __construct()
    {
        $this->auth = \Myth\Auth\Config\Services::authentication();
        $this->authorize = \Myth\Auth\Config\Services::authorization();
        $this->session = \Config\Services::session();

        helper(['text', 'app', 'reference']);
    }

    public function index()
    {
        if (! $this->auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('login');
        }

        $userModel = new \Auth\Models\UserModel();
        $this->data['total_user_active'] = $userModel->countAll();
        $this->data['total_user_inactive'] = $userModel->where('active', 0)->countAllResults();

        $groupModel = new \Auth\Models\GroupModel();
        $this->data['total_group'] = $groupModel->countAll();

        $bannerModel = new \Banner\Models\BannerModel();
        $this->data['total_banner'] = $bannerModel->countAll();

        $beritaModel = new \Berita\Models\BeritaModel();
        $this->data['total_berita'] = $beritaModel->countAll();

        $agendaModel = new \Agenda\Models\AgendaModel();
        $this->data['total_agenda'] = $agendaModel->countAll();

        $pengumumanModel = new \Pengumuman\Models\PengumumanModel();
        $this->data['total_pengumuman'] = $pengumumanModel->countAll();

        $testimoniModel = new \Testimoni\Models\TestimoniModel();
        $this->data['total_testimoni'] = $testimoniModel->countAll();

        $pameranModel = new \Pameran\Models\PameranModel();
        $this->data['total_pameran'] = $pameranModel->countAll();

        $koleksiUmumModel = new \KoleksiUmum\Models\KoleksiUmumModel();
        $this->data['total_koleksi_umum'] = $koleksiUmumModel->countAll();

        $bukuBaruModel = new \BukuBaru\Models\BukuBaruModel();
        $this->data['total_buku_baru'] = $bukuBaruModel->countAll();

        $majalahOnlineModel = new \MajalahOnline\Models\MajalahOnlineModel();
        $this->data['total_majalah_online'] = $majalahOnlineModel->countAll();

        $direktoriModel = new \Direktori\Models\DirektoriModel();
        $this->data['total_direktori'] = $direktoriModel->countAll();

        $kamusModel = new \Kamus\Models\KamusModel();
        $this->data['total_kamus'] = $kamusModel->countAll();

        $this->data['title'] = 'Dashboard';

        echo view('Dashboard\Views\index', $this->data);
    }
}
