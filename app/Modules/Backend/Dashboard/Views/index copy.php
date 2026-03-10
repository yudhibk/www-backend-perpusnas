<?= $this->extend(config('Core')->layout_backend) ?>
<?= $this->section('page') ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-display1 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Dashboard
                    <div class="page-title-subheading">Dashboard</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url(
                            'auth'
                        ) ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active breadcrumb-item" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="">
		<div class="row">
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-primary">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">User Aktif</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_user_active ??
                                0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-primary">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">User Non Aktif</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_user_inactive ??
                                0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="card mb-3 widget-content bg-primary">
                    <div class="widget-content-wrapper text-white">
                        <div class="widget-content-left">
                            <div class="widget-heading">Group User</div>
                            <div class="widget-subheading"></div>
                        </div>
                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $total_group ??
                                0 ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider mt-0" style="margin-bottom: 30px;"></div>
        

        <div class="row">
			<div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Banner</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_banner ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Berita</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_berita ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Agenda</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_agenda ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4 ">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Pengumuman</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_pengumuman ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4 ">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Testimoni</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_testimoni ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4 ">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Pameran</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_pameran ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Koleksi Umum</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_koleksi_umum ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Buku Baru</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_buku_baru ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Majalah Online</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_majalah_online ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Direktori</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_direktori ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-md-6 col-lg-4 col-xl-4 ">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Kamus</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= $total_kamus ??
                                                0 ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card-hover-shadow-2x mb-3 card bg-night-sky">
                    <div class="rm-border responsive-center text-left card-header">
                        <div>
                            <h5 class="menu-header-title text-capitalize text-dark">Jumlah Kunjungan</h5>
                        </div>
                    </div>
                    <div class="widget-chart widget-chart2 text-left pt-0">
                        <div class="widget-chat-wrapper-outer">
                            <div class="widget-chart-content">
                                <div class="widget-chart-flex">
                                    <div class="widget-numbers">
                                        <div class="widget-chart-flex">
                                            <div class="text-white"><span><?= get_visitor() ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-chart-wrapper widget-chart-wrapper-lg he-auto opacity-10 m-0">
                                <div id="dashboard-sparkline-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?= $this->endSection('page') ?>
