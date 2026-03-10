<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('deposit/publication', ['namespace' => 'DepositPublication\Controllers'], function ($subroutes) {
	/*** Route Update for Publication ***/
	$subroutes->add('', 'Publication::index');
	$subroutes->add('index', 'Publication::index');
	$subroutes->add('detail/(:any)', 'Publication::detail/$1');
	$subroutes->add('create', 'Publication::create');
	$subroutes->add('edit/(:any)', 'Publication::edit/$1');
	$subroutes->add('delete/(:any)', 'Publication::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Publication::apply_status/$1');
	$subroutes->add('do_init', 'Publication::do_init');
	$subroutes->add('do_upload', 'Publication::do_upload');
	$subroutes->add('do_delete', 'Publication::do_delete');
	$subroutes->add('flip', 'Publication::flip');
	$subroutes->add('thumb', 'Publication::thumb');
});

$routes->group('api/deposit/publication', ['namespace' => 'DepositPublication\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Publication ***/
	$subroutes->add('', 'Publication::index');
	$subroutes->add('index', 'Publication::index');
	$subroutes->add('detail/(:any)', 'Publication::detail/$1');
	$subroutes->add('create', 'Publication::create');
	$subroutes->add('edit/(:any)', 'Publication::edit/$1');
	$subroutes->add('delete/(:any)', 'Publication::delete/$1');
	$subroutes->add('upload_file', 'Publication::upload_file');
});
