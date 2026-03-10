<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/pengumuman', ['namespace' => 'Pengumuman\Controllers'], function ($subroutes) {
	/*** Route Update for Pengumuman ***/
	$subroutes->add('', 'Pengumuman::index');
	$subroutes->add('index', 'Pengumuman::index');
	$subroutes->add('detail/(:any)', 'Pengumuman::detail/$1');
	$subroutes->add('edit/(:any)', 'Pengumuman::edit/$1');
	$subroutes->add('create', 'Pengumuman::create');
	$subroutes->add('delete/(:any)', 'Pengumuman::delete/$1');
	$subroutes->add('do_init', 'Pengumuman::do_init');
	$subroutes->add('do_upload', 'Pengumuman::do_upload');
	$subroutes->add('do_delete', 'Pengumuman::do_delete');
	$subroutes->add('flip', 'Pengumuman::flip');
	$subroutes->add('apply_status/(:any)', 'Pengumuman::apply_status/$1');
	$subroutes->add('export', 'Pengumuman::export');
	$subroutes->add('thumb', 'Pengumuman::thumb');
});

$routes->group('api/pengumuman', ['namespace' => 'Pengumuman\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Pengumuman::index');
	$subroutes->add('index', 'Pengumuman::index');
	$subroutes->add('detail/(:any)', 'Pengumuman::detail/$1');
	$subroutes->add('show/(:any)', 'Pengumuman::show/$1');
	$subroutes->add('create', 'Pengumuman::create');
	$subroutes->add('update/(:any)', 'Pengumuman::update/$1');
	$subroutes->add('delete/(:any)', 'Pengumuman::delete/$1');

	//custom
	$subroutes->add('datatable', 'Pengumuman::datatable');
	$subroutes->add('datatable/(:any)', 'Pengumuman::datatable/$1');
	$subroutes->add('upload_file', 'Pengumuman::upload_file');
});
