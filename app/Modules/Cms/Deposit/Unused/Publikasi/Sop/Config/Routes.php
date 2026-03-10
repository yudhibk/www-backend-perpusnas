<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('deposit/publikasi/sop', ['namespace' => 'DepositPublikasiSop\Controllers'], function ($subroutes) {
	/*** Route Update for Sop ***/
	$subroutes->add('', 'Sop::index');
	$subroutes->add('index', 'Sop::index');
	$subroutes->add('detail/(:any)', 'Sop::detail/$1');
	$subroutes->add('create', 'Sop::create');
	$subroutes->add('edit/(:any)', 'Sop::edit/$1');
	$subroutes->add('delete/(:any)', 'Sop::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Sop::apply_status/$1');
	$subroutes->add('do_init', 'Sop::do_init');
	$subroutes->add('do_upload', 'Sop::do_upload');
	$subroutes->add('do_delete', 'Sop::do_delete');
	$subroutes->add('flip', 'Sop::flip');
	$subroutes->add('thumb', 'Sop::thumb');
});

$routes->group('api/deposit/publikasi/sop', ['namespace' => 'DepositPublikasiSop\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Sop ***/
	$subroutes->add('', 'Sop::index');
	$subroutes->add('index', 'Sop::index');
	$subroutes->add('detail/(:any)', 'Sop::detail/$1');
	$subroutes->add('create', 'Sop::create');
	$subroutes->add('edit/(:any)', 'Sop::edit/$1');
	$subroutes->add('delete/(:any)', 'Sop::delete/$1');
	$subroutes->add('upload_file', 'Sop::upload_file');
});
