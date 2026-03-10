<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/kamus', ['namespace' => 'Kamus\Controllers'], function ($subroutes) {
	/*** Route Update for Kamus ***/
	$subroutes->add('', 'Kamus::index');
	$subroutes->add('index', 'Kamus::index');
	$subroutes->add('detail/(:any)', 'Kamus::detail/$1');
	$subroutes->add('edit/(:any)', 'Kamus::edit/$1');
	$subroutes->add('create', 'Kamus::create');
	$subroutes->add('delete/(:any)', 'Kamus::delete/$1');
	$subroutes->add('do_init', 'Kamus::do_init');
	$subroutes->add('do_upload', 'Kamus::do_upload');
	$subroutes->add('do_delete', 'Kamus::do_delete');
	$subroutes->add('flip', 'Kamus::flip');
	$subroutes->add('apply_status/(:any)', 'Kamus::apply_status/$1');
	$subroutes->add('export', 'Kamus::export');
	$subroutes->add('thumb', 'Kamus::thumb');
});

$routes->group('api/kamus', ['namespace' => 'Kamus\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Kamus::index');
	$subroutes->add('index', 'Kamus::index');
	$subroutes->add('index/(:any)', 'Kamus::index/$1');
	$subroutes->add('detail/(:any)', 'Kamus::detail/$1');
	$subroutes->add('show/(:any)', 'Kamus::show/$1');
	$subroutes->add('create', 'Kamus::create');
	$subroutes->add('update/(:any)', 'Kamus::update/$1');
	$subroutes->add('delete/(:any)', 'Kamus::delete/$1');

	//custom
	$subroutes->add('datatable', 'Kamus::datatable');
	$subroutes->add('datatable/(:any)', 'Kamus::datatable/$1');
	$subroutes->add('category', 'Kamus::category/id');
	$subroutes->add('upload_file', 'Kamus::upload_file');
});
