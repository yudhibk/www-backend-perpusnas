<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/pegawai', ['namespace' => 'Pegawai\Controllers'], function ($subroutes) {
	/*** Route Update for Pegawai ***/
	$subroutes->add('', 'Pegawai::index');
	$subroutes->add('index', 'Pegawai::index');
	$subroutes->add('detail/(:any)', 'Pegawai::detail/$1');
	$subroutes->add('edit/(:any)', 'Pegawai::edit/$1');
	$subroutes->add('create', 'Pegawai::create');
	$subroutes->add('delete/(:any)', 'Pegawai::delete/$1');
	$subroutes->add('do_init', 'Pegawai::do_init');
	$subroutes->add('do_upload', 'Pegawai::do_upload');
	$subroutes->add('do_delete', 'Pegawai::do_delete');
	$subroutes->add('flip', 'Pegawai::flip');
	$subroutes->add('apply_status/(:any)', 'Pegawai::apply_status/$1');
	$subroutes->add('export', 'Pegawai::export');
	$subroutes->add('thumb', 'Pegawai::thumb');
});

$routes->group('api/pegawai', ['namespace' => 'Pegawai\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Pegawai::index');
	$subroutes->add('index', 'Pegawai::index');
	$subroutes->add('index/(:any)', 'Pegawai::index/$1');
	$subroutes->add('detail/(:any)', 'Pegawai::detail/$1');
	$subroutes->add('show/(:any)', 'Pegawai::show/$1');
	$subroutes->add('create', 'Pegawai::create');
	$subroutes->add('update/(:any)', 'Pegawai::update/$1');
	$subroutes->add('delete/(:any)', 'Pegawai::delete/$1');

	//custom
	$subroutes->add('datatable', 'Pegawai::datatable');
	$subroutes->add('datatable/(:any)', 'Pegawai::datatable/$1');
	$subroutes->add('switch', 'Pegawai::switch');
	$subroutes->add('switch/(:any)', 'Pegawai::switch/$1');
	$subroutes->add('(:any)', 'Pegawai::detail/$1');
});
