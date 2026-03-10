<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/koleksi', ['namespace' => 'Koleksi\Controllers'], function ($subroutes) {
	/*** Route Update for Koleksi ***/
	$subroutes->add('', 'Koleksi::index');
	$subroutes->add('index', 'Koleksi::index');
	$subroutes->add('detail/(:any)', 'Koleksi::detail/$1');
	$subroutes->add('edit/(:any)', 'Koleksi::edit/$1');
	$subroutes->add('create', 'Koleksi::create');
	$subroutes->add('delete/(:any)', 'Koleksi::delete/$1');
	$subroutes->add('do_init', 'Koleksi::do_init');
	$subroutes->add('do_upload', 'Koleksi::do_upload');
	$subroutes->add('do_delete', 'Koleksi::do_delete');
	$subroutes->add('flip', 'Koleksi::flip');
	$subroutes->add('apply_status/(:any)', 'Koleksi::apply_status/$1');
	$subroutes->add('export', 'Koleksi::export');
	$subroutes->add('thumb', 'Koleksi::thumb');
});

$routes->group('api/koleksi', ['namespace' => 'Koleksi\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Koleksi::index');
	$subroutes->add('index', 'Koleksi::index');
	$subroutes->add('detail/(:any)', 'Koleksi::detail/$1');
	$subroutes->add('show/(:any)', 'Koleksi::show/$1');
	$subroutes->add('create', 'Koleksi::create');
	$subroutes->add('update/(:any)', 'Koleksi::update/$1');
	$subroutes->add('delete/(:any)', 'Koleksi::delete/$1');

	//custom
	$subroutes->add('datatable', 'Koleksi::datatable');
	$subroutes->add('datatable/(:any)', 'Koleksi::datatable/$1');
	$subroutes->add('upload_file', 'Koleksi::upload_file');
});
