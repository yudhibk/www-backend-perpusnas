<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/bukubaru', ['namespace' => 'BukuBaru\Controllers'], function ($subroutes) {
	/*** Route Update for BukuBaru ***/
	$subroutes->add('', 'BukuBaru::index');
	$subroutes->add('index', 'BukuBaru::index');
	$subroutes->add('detail/(:any)', 'BukuBaru::detail/$1');
	$subroutes->add('edit/(:any)', 'BukuBaru::edit/$1');
	$subroutes->add('create', 'BukuBaru::create');
	$subroutes->add('delete/(:any)', 'BukuBaru::delete/$1');
	$subroutes->add('do_init', 'BukuBaru::do_init');
	$subroutes->add('do_upload', 'BukuBaru::do_upload');
	$subroutes->add('do_delete', 'BukuBaru::do_delete');
	$subroutes->add('flip', 'BukuBaru::flip');
	$subroutes->add('apply_status/(:any)', 'BukuBaru::apply_status/$1');
	$subroutes->add('export', 'BukuBaru::export');
	$subroutes->add('thumb', 'BukuBaru::thumb');
});

$routes->group('api/bukubaru', ['namespace' => 'BukuBaru\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'BukuBaru::index');
	$subroutes->add('index', 'BukuBaru::index');
	$subroutes->add('detail/(:any)', 'BukuBaru::detail/$1');
	$subroutes->add('show/(:any)', 'BukuBaru::show/$1');
	$subroutes->add('create', 'BukuBaru::create');
	$subroutes->add('update/(:any)', 'BukuBaru::update/$1');
	$subroutes->add('delete/(:any)', 'BukuBaru::delete/$1');

	//custom
	$subroutes->add('datatable', 'BukuBaru::datatable');
	$subroutes->add('datatable/(:any)', 'BukuBaru::datatable/$1');
	$subroutes->add('upload_file', 'BukuBaru::upload_file');
});
