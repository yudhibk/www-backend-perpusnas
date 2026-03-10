<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('survey', ['namespace' => 'Survey\Controllers'], function ($subroutes) {
	/*** Route Update for Survey ***/
	$subroutes->add('', 'Survey::index');
	$subroutes->add('index', 'Survey::index');
	$subroutes->add('detail/(:any)', 'Survey::detail/$1');
	$subroutes->add('edit/(:any)', 'Survey::edit/$1');
	$subroutes->add('create', 'Survey::create');
	$subroutes->add('delete/(:any)', 'Survey::delete/$1');
	$subroutes->add('do_init', 'Survey::do_init');
	$subroutes->add('do_upload', 'Survey::do_upload');
	$subroutes->add('do_delete', 'Survey::do_delete');
	$subroutes->add('flip', 'Survey::flip');
	$subroutes->add('apply_status/(:any)', 'Survey::apply_status/$1');
	$subroutes->add('export', 'Survey::export');
	$subroutes->add('report', 'Survey::report');
	$subroutes->add('responden', 'Survey::responden');
});

$routes->group('api/survey', ['namespace' => 'Survey\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('index', 'Survey::index');
	$subroutes->add('show/(:any)', 'Survey::show/$1');
	$subroutes->add('create', 'Survey::create');
	$subroutes->add('update/(:any)', 'Survey::update/$1');
	$subroutes->add('delete/(:any)', 'Survey::delete/$1');

	//custom
	$subroutes->add('datatable', 'Survey::datatable');
	$subroutes->add('upload_file', 'Survey::upload_file');
});
