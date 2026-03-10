<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/paketinformasi', ['namespace' => 'PaketInformasi\Controllers'], function ($subroutes) {
	/*** Route Update for PaketInformasi ***/
	$subroutes->add('', 'PaketInformasi::index');
	$subroutes->add('index', 'PaketInformasi::index');
	$subroutes->add('detail/(:any)', 'PaketInformasi::detail/$1');
	$subroutes->add('edit/(:any)', 'PaketInformasi::edit/$1');
	$subroutes->add('create', 'PaketInformasi::create');
	$subroutes->add('delete/(:any)', 'PaketInformasi::delete/$1');
	$subroutes->add('do_init', 'PaketInformasi::do_init');
	$subroutes->add('do_upload', 'PaketInformasi::do_upload');
	$subroutes->add('do_delete', 'PaketInformasi::do_delete');
	$subroutes->add('flip', 'PaketInformasi::flip');
	$subroutes->add('apply_status/(:any)', 'PaketInformasi::apply_status/$1');
	$subroutes->add('export', 'PaketInformasi::export');
	$subroutes->add('thumb', 'PaketInformasi::thumb');
});

$routes->group('api/paketinformasi', ['namespace' => 'PaketInformasi\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'PaketInformasi::index');
	$subroutes->add('index', 'PaketInformasi::index');
	$subroutes->add('detail/(:any)', 'PaketInformasi::detail/$1');
	$subroutes->add('show/(:any)', 'PaketInformasi::show/$1');
	$subroutes->add('create', 'PaketInformasi::create');
	$subroutes->add('update/(:any)', 'PaketInformasi::update/$1');
	$subroutes->add('delete/(:any)', 'PaketInformasi::delete/$1');

	//custom
	$subroutes->add('datatable', 'PaketInformasi::datatable');
	$subroutes->add('datatable/(:any)', 'PaketInformasi::datatable/$1');
	$subroutes->add('(:any)', 'PaketInformasi::detail/$1');
});
