<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('deposit/laporan/pengadaan', ['namespace' => 'DepositLaporanpengadaan\Controllers'], function ($subroutes) {
	/*** Route Update for Laporanpengadaan ***/
	$subroutes->add('', 'Laporanpengadaan::index');
	$subroutes->add('index', 'Laporanpengadaan::index');
	$subroutes->add('detail/(:any)', 'Laporanpengadaan::detail/$1');
	$subroutes->add('create', 'Laporanpengadaan::create');
	$subroutes->add('edit/(:any)', 'Laporanpengadaan::edit/$1');
	$subroutes->add('delete/(:any)', 'Laporanpengadaan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Laporanpengadaan::apply_status/$1');
	$subroutes->add('do_init', 'Laporanpengadaan::do_init');
	$subroutes->add('do_upload', 'Laporanpengadaan::do_upload');
	$subroutes->add('do_delete', 'Laporanpengadaan::do_delete');
	$subroutes->add('flip', 'Laporanpengadaan::flip');
	$subroutes->add('thumb', 'Laporanpengadaan::thumb');
});

$routes->group('api/deposit/laporan/pengadaan', ['namespace' => 'DepositLaporanpengadaan\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Laporanpengadaan ***/
	$subroutes->add('', 'Laporanpengadaan::index');
	$subroutes->add('index', 'Laporanpengadaan::index');
	$subroutes->add('detail/(:any)', 'Laporanpengadaan::detail/$1');
	$subroutes->add('create', 'Laporanpengadaan::create');
	$subroutes->add('edit/(:any)', 'Laporanpengadaan::edit/$1');
	$subroutes->add('delete/(:any)', 'Laporanpengadaan::delete/$1');
	$subroutes->add('upload_file', 'Laporanpengadaan::upload_file');
});
