<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/tentang', ['namespace' => 'Tentang\Controllers'], function ($subroutes) {
	/*** Route Update for Tentang ***/
	$subroutes->add('', 'Tentang::index');
	$subroutes->add('index', 'Tentang::index');
	$subroutes->add('detail/(:any)', 'Tentang::detail/$1');
	$subroutes->add('create', 'Tentang::create');
	$subroutes->add('edit/(:any)', 'Tentang::edit/$1');
	$subroutes->add('delete/(:any)', 'Tentang::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Tentang::apply_status/$1');
	$subroutes->add('do_init', 'Tentang::do_init');
	$subroutes->add('do_upload', 'Tentang::do_upload');
	$subroutes->add('do_delete', 'Tentang::do_delete');
	$subroutes->add('flip', 'Tentang::flip');
	$subroutes->add('thumb', 'Tentang::thumb');
});

$routes->group('api/tentang', ['namespace' => 'Tentang\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Tentang ***/
	$subroutes->add('', 'Tentang::index');
	$subroutes->add('index', 'Tentang::index');
	$subroutes->add('detail/(:any)', 'Tentang::detail/$1');
	$subroutes->add('create', 'Tentang::create');
	$subroutes->add('edit/(:any)', 'Tentang::edit/$1');
	$subroutes->add('delete/(:any)', 'Tentang::delete/$1');
	$subroutes->add('upload_file', 'Tentang::upload_file');
});
