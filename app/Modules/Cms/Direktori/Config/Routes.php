<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/direktori', ['namespace' => 'Direktori\Controllers'], function ($subroutes) {
	/*** Route Update for Direktori ***/
	$subroutes->add('', 'Direktori::index');
	$subroutes->add('index', 'Direktori::index');
	$subroutes->add('detail/(:any)', 'Direktori::detail/$1');
	$subroutes->add('edit/(:any)', 'Direktori::edit/$1');
	$subroutes->add('create', 'Direktori::create');
	$subroutes->add('delete/(:any)', 'Direktori::delete/$1');
	$subroutes->add('do_init', 'Direktori::do_init');
	$subroutes->add('do_upload', 'Direktori::do_upload');
	$subroutes->add('do_delete', 'Direktori::do_delete');
	$subroutes->add('flip', 'Direktori::flip');
	$subroutes->add('apply_status/(:any)', 'Direktori::apply_status/$1');
	$subroutes->add('export', 'Direktori::export');
	$subroutes->add('thumb', 'Direktori::thumb');
});

$routes->group('api/direktori', ['namespace' => 'Direktori\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Direktori::index');
	$subroutes->add('index', 'Direktori::index');
	$subroutes->add('index/(:any)', 'Direktori::index/$1');
	$subroutes->add('detail/(:any)', 'Direktori::detail/$1');
	$subroutes->add('show/(:any)', 'Direktori::show/$1');
	$subroutes->add('create', 'Direktori::create');
	$subroutes->add('update/(:any)', 'Direktori::update/$1');
	$subroutes->add('delete/(:any)', 'Direktori::delete/$1');

	//custom
	$subroutes->add('datatable', 'Direktori::datatable');
	$subroutes->add('datatable/(:any)', 'Direktori::datatable/$1');
	$subroutes->add('category', 'Direktori::category/id');
	$subroutes->add('category/(:any)', 'Direktori::category/$1');
	$subroutes->add('upload_file', 'Direktori::upload_file');
});
