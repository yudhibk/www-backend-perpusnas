<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/testimoni', ['namespace' => 'Testimoni\Controllers'], function ($subroutes) {
	/*** Route Update for Testimoni ***/
	$subroutes->add('', 'Testimoni::index');
	$subroutes->add('index', 'Testimoni::index');
	$subroutes->add('detail/(:any)', 'Testimoni::detail/$1');
	$subroutes->add('edit/(:any)', 'Testimoni::edit/$1');
	$subroutes->add('create', 'Testimoni::create');
	$subroutes->add('delete/(:any)', 'Testimoni::delete/$1');
	$subroutes->add('do_init', 'Testimoni::do_init');
	$subroutes->add('do_upload', 'Testimoni::do_upload');
	$subroutes->add('do_delete', 'Testimoni::do_delete');
	$subroutes->add('flip', 'Testimoni::flip');
	$subroutes->add('apply_status/(:any)', 'Testimoni::apply_status/$1');
	$subroutes->add('export', 'Testimoni::export');
	$subroutes->add('thumb', 'Testimoni::thumb');
});

$routes->group('api/testimoni', ['namespace' => 'Testimoni\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Testimoni::index');
	$subroutes->add('index', 'Testimoni::index');
	$subroutes->add('detail/(:any)', 'Testimoni::detail/$1');
	$subroutes->add('show/(:any)', 'Testimoni::show/$1');
	$subroutes->add('create', 'Testimoni::create');
	$subroutes->add('update/(:any)', 'Testimoni::update/$1');
	$subroutes->add('delete/(:any)', 'Testimoni::delete/$1');

	//custom
	$subroutes->add('datatable', 'Testimoni::datatable');
	$subroutes->add('datatable/(:any)', 'Testimoni::datatable/$1');
	$subroutes->add('upload_file', 'Testimoni::upload_file');
});
