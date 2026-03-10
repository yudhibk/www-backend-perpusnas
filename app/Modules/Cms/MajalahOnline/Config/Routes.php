<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/majalahonline', ['namespace' => 'MajalahOnline\Controllers'], function ($subroutes) {
	/*** Route Update for MajalahOnline ***/
	$subroutes->add('', 'MajalahOnline::index');
	$subroutes->add('index', 'MajalahOnline::index');
	$subroutes->add('index/(:any)', 'MajalahOnline::index/$1');
	$subroutes->add('edition', 'MajalahOnline::edition');
	$subroutes->add('detail/(:any)', 'MajalahOnline::detail/$1');
	$subroutes->add('edit/(:any)', 'MajalahOnline::edit/$1');
	$subroutes->add('create', 'MajalahOnline::create');
	$subroutes->add('delete/(:any)', 'MajalahOnline::delete/$1');
	$subroutes->add('do_init', 'MajalahOnline::do_init');
	$subroutes->add('do_upload', 'MajalahOnline::do_upload');
	$subroutes->add('do_delete', 'MajalahOnline::do_delete');
	$subroutes->add('flip', 'MajalahOnline::flip');
	$subroutes->add('apply_status/(:any)', 'MajalahOnline::apply_status/$1');
	$subroutes->add('export', 'MajalahOnline::export');
	$subroutes->add('thumb', 'MajalahOnline::thumb');
});

$routes->group('api/majalahonline', ['namespace' => 'MajalahOnline\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'MajalahOnline::index');
	$subroutes->add('index', 'MajalahOnline::index');
	$subroutes->add('index/(:any)', 'MajalahOnline::index/$1');
	$subroutes->add('detail/(:any)', 'MajalahOnline::detail/$1');
	$subroutes->add('show/(:any)', 'MajalahOnline::show/$1');
	$subroutes->add('create', 'MajalahOnline::create');
	$subroutes->add('update/(:any)', 'MajalahOnline::update/$1');
	$subroutes->add('delete/(:any)', 'MajalahOnline::delete/$1');

	//custom
	$subroutes->add('datatable', 'MajalahOnline::datatable');
	$subroutes->add('datatable/(:any)', 'MajalahOnline::datatable/$1');
	$subroutes->add('datatable_edition', 'MajalahOnline::datatable_edition');
	$subroutes->add('datatable_edition/(:any)', 'MajalahOnline::datatable_edition/$1');
	$subroutes->add('edition', 'MajalahOnline::edition');
	$subroutes->add('edition/(:any)', 'MajalahOnline::edition/$1');
	$subroutes->add('upload_file', 'MajalahOnline::upload_file');
});
