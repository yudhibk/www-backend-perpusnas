<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/publikasi', ['namespace' => 'Publikasi\Controllers'], function ($subroutes) {
	/*** Route Update for Publikasi ***/
	$subroutes->add('', 'Publikasi::index');
	$subroutes->add('index', 'Publikasi::index');
	$subroutes->add('detail/(:any)', 'Publikasi::detail/$1');
	$subroutes->add('edit/(:any)', 'Publikasi::edit/$1');
	$subroutes->add('create', 'Publikasi::create');
	$subroutes->add('delete/(:any)', 'Publikasi::delete/$1');
	$subroutes->add('do_init', 'Publikasi::do_init');
	$subroutes->add('do_upload', 'Publikasi::do_upload');
	$subroutes->add('do_delete', 'Publikasi::do_delete');
	$subroutes->add('flip', 'Publikasi::flip');
	$subroutes->add('apply_status/(:any)', 'Publikasi::apply_status/$1');
	$subroutes->add('export', 'Publikasi::export');
	$subroutes->add('thumb', 'Publikasi::thumb');
});

$routes->group('api/publikasi', ['namespace' => 'Publikasi\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Publikasi::index');
	$subroutes->add('index', 'Publikasi::index');
	$subroutes->add('index/(:any)', 'Publikasi::index/$1');
	$subroutes->add('detail/(:any)', 'Publikasi::detail/$1');
	$subroutes->add('show/(:any)', 'Publikasi::show/$1');
	$subroutes->add('create', 'Publikasi::create');
	$subroutes->add('update/(:any)', 'Publikasi::update/$1');
	$subroutes->add('delete/(:any)', 'Publikasi::delete/$1');

	//custom
	$subroutes->add('datatable', 'Publikasi::datatable');
	$subroutes->add('datatable/(:any)', 'Publikasi::datatable/$1');
	$subroutes->add('upload_file', 'Publikasi::upload_file');
});
