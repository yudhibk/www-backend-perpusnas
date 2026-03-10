<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/histori-direktur', ['namespace' => 'Historidirektur\Controllers'], function ($subroutes) {
	/*** Route Update for Historidirektur ***/
	$subroutes->add('', 'Historidirektur::index');
	$subroutes->add('index', 'Historidirektur::index');
	$subroutes->add('detail/(:any)', 'Historidirektur::detail/$1');
	$subroutes->add('create', 'Historidirektur::create');
	$subroutes->add('edit/(:any)', 'Historidirektur::edit/$1');
	$subroutes->add('delete/(:any)', 'Historidirektur::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Historidirektur::apply_status/$1');
	$subroutes->add('do_init', 'Historidirektur::do_init');
	$subroutes->add('do_upload', 'Historidirektur::do_upload');
	$subroutes->add('do_delete', 'Historidirektur::do_delete');
	$subroutes->add('flip', 'Historidirektur::flip');
	$subroutes->add('thumb', 'Historidirektur::thumb');
});

$routes->group('api/cms/histori-direktur', ['namespace' => 'Historidirektur\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Historidirektur ***/
	$subroutes->add('', 'Historidirektur::index');
	$subroutes->add('index', 'Historidirektur::index');
	$subroutes->add('detail/(:any)', 'Historidirektur::detail/$1');
	$subroutes->add('create', 'Historidirektur::create');
	$subroutes->add('edit/(:any)', 'Historidirektur::edit/$1');
	$subroutes->add('delete/(:any)', 'Historidirektur::delete/$1');
	$subroutes->add('upload_file', 'Historidirektur::upload_file');
});
