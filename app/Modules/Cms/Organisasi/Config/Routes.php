<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/organisasi', ['namespace' => 'Organisasi\Controllers'], function ($subroutes) {
	/*** Route Update for Organisasi ***/
	$subroutes->add('', 'Organisasi::index');
	$subroutes->add('index', 'Organisasi::index');
	$subroutes->add('detail/(:any)', 'Organisasi::detail/$1');
	$subroutes->add('edit/(:any)', 'Organisasi::edit/$1');
	$subroutes->add('create', 'Organisasi::create');
	$subroutes->add('delete/(:any)', 'Organisasi::delete/$1');
	$subroutes->add('do_init', 'Organisasi::do_init');
	$subroutes->add('do_upload', 'Organisasi::do_upload');
	$subroutes->add('do_delete', 'Organisasi::do_delete');
	$subroutes->add('flip', 'Organisasi::flip');
	$subroutes->add('apply_status/(:any)', 'Organisasi::apply_status/$1');
	$subroutes->add('export', 'Organisasi::export');
	$subroutes->add('thumb', 'Organisasi::thumb');
});

$routes->group('api/organisasi', ['namespace' => 'Organisasi\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Organisasi::index');
	$subroutes->add('index', 'Organisasi::index');
	$subroutes->add('detail/(:any)', 'Organisasi::detail/$1');
	$subroutes->add('show/(:any)', 'Organisasi::show/$1');
	$subroutes->add('create', 'Organisasi::create');
	$subroutes->add('update/(:any)', 'Organisasi::update/$1');
	$subroutes->add('delete/(:any)', 'Organisasi::delete/$1');

	//custom
	$subroutes->add('datatable', 'Organisasi::datatable');
	$subroutes->add('datatable/(:any)', 'Organisasi::datatable/$1');
	$subroutes->add('(:any)', 'Organisasi::detail/$1');
});
