<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('deposit/pengajuan/bahan-pustaka', ['namespace' => 'DepositPengajuanBahanpustaka\Controllers'], function ($subroutes) {
	/*** Route Update for Bahanpustaka ***/
	$subroutes->add('', 'Bahanpustaka::index');
	$subroutes->add('index', 'Bahanpustaka::index');
	$subroutes->add('detail/(:any)', 'Bahanpustaka::detail/$1');
	$subroutes->add('create', 'Bahanpustaka::create');
	$subroutes->add('edit/(:any)', 'Bahanpustaka::edit/$1');
	$subroutes->add('delete/(:any)', 'Bahanpustaka::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Bahanpustaka::apply_status/$1');
	$subroutes->add('do_init', 'Bahanpustaka::do_init');
	$subroutes->add('do_upload', 'Bahanpustaka::do_upload');
	$subroutes->add('do_delete', 'Bahanpustaka::do_delete');
	$subroutes->add('flip', 'Bahanpustaka::flip');
	$subroutes->add('thumb', 'Bahanpustaka::thumb');
});

$routes->group('api/deposit/pengajuan/bahan-pustaka', ['namespace' => 'DepositPengajuanBahanpustaka\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Bahanpustaka ***/
	$subroutes->add('', 'Bahanpustaka::index');
	$subroutes->add('index', 'Bahanpustaka::index');
	$subroutes->add('detail/(:any)', 'Bahanpustaka::detail/$1');
	$subroutes->add('create', 'Bahanpustaka::create');
	$subroutes->add('edit/(:any)', 'Bahanpustaka::edit/$1');
	$subroutes->add('delete/(:any)', 'Bahanpustaka::delete/$1');
	$subroutes->add('upload_file', 'Bahanpustaka::upload_file');
});
