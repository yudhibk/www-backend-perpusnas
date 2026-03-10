<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('event', ['namespace' => 'Event\Controllers'], function ($subroutes) {
	/*** Route Update for Event ***/
	$subroutes->add('', 'Event::index');
	$subroutes->add('index', 'Event::index');
	$subroutes->add('detail/(:any)', 'Event::detail/$1');
	$subroutes->add('create', 'Event::create');
	$subroutes->add('edit/(:any)', 'Event::edit/$1');
	$subroutes->add('delete/(:any)', 'Event::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Event::apply_status/$1');
	$subroutes->add('do_init', 'Event::do_init');
	$subroutes->add('do_upload', 'Event::do_upload');
	$subroutes->add('do_delete', 'Event::do_delete');
	$subroutes->add('flip', 'Event::flip');
	$subroutes->add('thumb', 'Event::thumb');
});

$routes->group('api/event', ['namespace' => 'Event\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Event ***/
	$subroutes->add('detail/(:any)', 'Event::detail/$1');
	$subroutes->add('create', 'Event::create');
	$subroutes->add('edit/(:any)', 'Event::edit/$1');
	$subroutes->add('delete/(:any)', 'Event::delete/$1');
	$subroutes->add('upload_file', 'Event::upload_file');
});