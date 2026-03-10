<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/agenda', ['namespace' => 'Agenda\Controllers'], function ($subroutes) {
	/*** Route Update for Agenda ***/
	$subroutes->add('', 'Agenda::index');
	$subroutes->add('index', 'Agenda::index');
	$subroutes->add('detail/(:any)', 'Agenda::detail/$1');
	$subroutes->add('edit/(:any)', 'Agenda::edit/$1');
	$subroutes->add('create', 'Agenda::create');
	$subroutes->add('delete/(:any)', 'Agenda::delete/$1');
	$subroutes->add('do_init', 'Agenda::do_init');
	$subroutes->add('do_upload', 'Agenda::do_upload');
	$subroutes->add('do_delete', 'Agenda::do_delete');
	$subroutes->add('flip', 'Agenda::flip');
	$subroutes->add('apply_status/(:any)', 'Agenda::apply_status/$1');
	$subroutes->add('export', 'Agenda::export');
	$subroutes->add('thumb', 'Agenda::thumb');
});

$routes->group('api/agenda', ['namespace' => 'Agenda\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Agenda::index');
	$subroutes->add('index', 'Agenda::index');
	$subroutes->add('index/(:any)', 'Agenda::index/$1');
	$subroutes->add('detail/(:any)', 'Agenda::detail/$1');
	$subroutes->add('show/(:any)', 'Agenda::show/$1');
	$subroutes->add('create', 'Agenda::create');
	$subroutes->add('update/(:any)', 'Agenda::update/$1');
	$subroutes->add('delete/(:any)', 'Agenda::delete/$1');

	//custom
	$subroutes->add('datatable', 'Agenda::datatable');
	$subroutes->add('datatable/(:any)', 'Agenda::datatable/$1');
	$subroutes->add('upload_file', 'Agenda::upload_file');
});
