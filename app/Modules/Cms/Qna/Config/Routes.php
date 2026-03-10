<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('qna', ['namespace' => 'Qna\Controllers'], function ($subroutes) {
	/*** Route Update for Qna ***/
	$subroutes->add('', 'Qna::index');
	$subroutes->add('index', 'Qna::index');
	$subroutes->add('detail/(:any)', 'Qna::detail/$1');
	$subroutes->add('edit/(:any)', 'Qna::edit/$1');
	$subroutes->add('create', 'Qna::create');
	$subroutes->add('delete/(:any)', 'Qna::delete/$1');
	$subroutes->add('do_init', 'Qna::do_init');
	$subroutes->add('do_upload', 'Qna::do_upload');
	$subroutes->add('do_delete', 'Qna::do_delete');
	$subroutes->add('flip', 'Qna::flip');
	$subroutes->add('apply_status/(:any)', 'Qna::apply_status/$1');
	$subroutes->add('export', 'Qna::export');
	$subroutes->add('thumb', 'Qna::thumb');
});

$routes->group('api/qna', ['namespace' => 'Qna\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('index', 'Qna::index');
	$subroutes->add('show/(:any)', 'Qna::show/$1');
	$subroutes->add('create', 'Qna::create');
	$subroutes->add('update/(:any)', 'Qna::update/$1');
	$subroutes->add('delete/(:any)', 'Qna::delete/$1');

	//custom
	$subroutes->add('datatable', 'Qna::datatable');
	$subroutes->add('upload_file', 'Qna::upload_file');
});
