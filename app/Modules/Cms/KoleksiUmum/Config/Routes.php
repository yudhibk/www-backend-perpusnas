<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/koleksiumum', ['namespace' => 'KoleksiUmum\Controllers'], function ($subroutes) {
	/*** Route Update for KoleksiUmum ***/
	$subroutes->add('', 'KoleksiUmum::index');
	$subroutes->add('index', 'KoleksiUmum::index');
	$subroutes->add('detail/(:any)', 'KoleksiUmum::detail/$1');
	$subroutes->add('edit/(:any)', 'KoleksiUmum::edit/$1');
	$subroutes->add('create', 'KoleksiUmum::create');
	$subroutes->add('delete/(:any)', 'KoleksiUmum::delete/$1');
	$subroutes->add('do_init', 'KoleksiUmum::do_init');
	$subroutes->add('do_upload', 'KoleksiUmum::do_upload');
	$subroutes->add('do_delete', 'KoleksiUmum::do_delete');
	$subroutes->add('flip', 'KoleksiUmum::flip');
	$subroutes->add('apply_status/(:any)', 'KoleksiUmum::apply_status/$1');
	$subroutes->add('export', 'KoleksiUmum::export');
	$subroutes->add('thumb', 'KoleksiUmum::thumb');
});

$routes->group('api/koleksiumum', ['namespace' => 'KoleksiUmum\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'KoleksiUmum::index');
	$subroutes->add('index', 'KoleksiUmum::index');
	$subroutes->add('detail/(:any)', 'KoleksiUmum::detail/$1');
	$subroutes->add('show/(:any)', 'KoleksiUmum::show/$1');
	$subroutes->add('create', 'KoleksiUmum::create');
	$subroutes->add('update/(:any)', 'KoleksiUmum::update/$1');
	$subroutes->add('delete/(:any)', 'KoleksiUmum::delete/$1');

	//custom
	$subroutes->add('datatable', 'KoleksiUmum::datatable');
	$subroutes->add('datatable/(:any)', 'KoleksiUmum::datatable/$1');
	$subroutes->add('upload_file', 'KoleksiUmum::upload_file');
});
