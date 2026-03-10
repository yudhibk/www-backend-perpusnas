<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/peraturan', ['namespace' => 'Peraturan\Controllers'], function ($subroutes) {
	/*** Route Update for Peraturan ***/
	$subroutes->add('', 'Peraturan::index');
	$subroutes->add('index', 'Peraturan::index');
	$subroutes->add('detail/(:any)', 'Peraturan::detail/$1');
	$subroutes->add('create', 'Peraturan::create');
	$subroutes->add('edit/(:any)', 'Peraturan::edit/$1');
	$subroutes->add('delete/(:any)', 'Peraturan::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Peraturan::apply_status/$1');
	$subroutes->add('do_init', 'Peraturan::do_init');
	$subroutes->add('do_upload', 'Peraturan::do_upload');
	$subroutes->add('do_delete', 'Peraturan::do_delete');
	$subroutes->add('flip', 'Peraturan::flip');
	$subroutes->add('thumb', 'Peraturan::thumb');
});

$routes->group('api/peraturan', ['namespace' => 'Peraturan\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Peraturan ***/
	$subroutes->add('', 'Peraturan::index');
	$subroutes->add('index', 'Peraturan::index');
	$subroutes->add('detail/(:any)', 'Peraturan::detail/$1');
	$subroutes->add('create', 'Peraturan::create');
	$subroutes->add('edit/(:any)', 'Peraturan::edit/$1');
	$subroutes->add('delete/(:any)', 'Peraturan::delete/$1');
	$subroutes->add('upload_file', 'Peraturan::upload_file');
});
