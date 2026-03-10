<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/layanan', ['namespace' => 'Layanan\Controllers'], function ($subroutes) {
	/*** Route Update for Layanan ***/
	$subroutes->add('', 'Layanan::index');
	$subroutes->add('index', 'Layanan::index');
	$subroutes->add('detail/(:any)', 'Layanan::detail/$1');
	$subroutes->add('edit/(:any)', 'Layanan::edit/$1');
	$subroutes->add('create', 'Layanan::create');
	$subroutes->add('delete/(:any)', 'Layanan::delete/$1');
	$subroutes->add('do_init', 'Layanan::do_init');
	$subroutes->add('do_upload', 'Layanan::do_upload');
	$subroutes->add('do_delete', 'Layanan::do_delete');
	$subroutes->add('flip', 'Layanan::flip');
	$subroutes->add('apply_status/(:any)', 'Layanan::apply_status/$1');
	$subroutes->add('export', 'Layanan::export');
	$subroutes->add('thumb', 'Layanan::thumb');
});

$routes->group('api/layanan', ['namespace' => 'Layanan\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Layanan::index');
	$subroutes->add('index', 'Layanan::index');
	$subroutes->add('index/(:any)', 'Layanan::index/$1');
	$subroutes->add('detail/(:any)', 'Layanan::detail/$1');
	$subroutes->add('show/(:any)', 'Layanan::show/$1');
	$subroutes->add('create', 'Layanan::create');
	$subroutes->add('update/(:any)', 'Layanan::update/$1');
	$subroutes->add('delete/(:any)', 'Layanan::delete/$1');

	//custom
	$subroutes->add('datatable', 'Layanan::datatable');
	$subroutes->add('datatable/(:any)', 'Layanan::datatable/$1');
	$subroutes->add('(:any)', 'Layanan::detail/$1');
});
