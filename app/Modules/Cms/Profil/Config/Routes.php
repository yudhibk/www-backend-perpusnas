<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/profil', ['namespace' => 'Profil\Controllers'], function ($subroutes) {
	/*** Route Update for Profil ***/
	$subroutes->add('', 'Profil::index');
	$subroutes->add('index', 'Profil::index');
	$subroutes->add('detail/(:any)', 'Profil::detail/$1');
	$subroutes->add('edit/(:any)', 'Profil::edit/$1');
	$subroutes->add('create', 'Profil::create');
	$subroutes->add('delete/(:any)', 'Profil::delete/$1');
	$subroutes->add('do_init', 'Profil::do_init');
	$subroutes->add('do_upload', 'Profil::do_upload');
	$subroutes->add('do_delete', 'Profil::do_delete');
	$subroutes->add('flip', 'Profil::flip');
	$subroutes->add('apply_status/(:any)', 'Profil::apply_status/$1');
	$subroutes->add('export', 'Profil::export');
	$subroutes->add('thumb', 'Profil::thumb');
});

$routes->group('api/profil', ['namespace' => 'Profil\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Profil::index');
	$subroutes->add('index', 'Profil::index');
	$subroutes->add('index/(:any)', 'Profil::index/$1');
	$subroutes->add('detail/(:any)', 'Profil::detail/$1');
	$subroutes->add('show/(:any)', 'Profil::show/$1');
	$subroutes->add('create', 'Profil::create');
	$subroutes->add('update/(:any)', 'Profil::update/$1');
	$subroutes->add('delete/(:any)', 'Profil::delete/$1');

	//custom
	$subroutes->add('datatable', 'Profil::datatable');
	$subroutes->add('datatable/(:any)', 'Profil::datatable/$1');
	$subroutes->add('(:any)', 'Profil::detail/$1');
});
