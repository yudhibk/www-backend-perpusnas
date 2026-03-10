<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('deposit/pengajuan/naskah-kuno', ['namespace' => 'DepositPengajuanNaskahkuno\Controllers'], function ($subroutes) {
	/*** Route Update for Naskahkuno ***/
	$subroutes->add('', 'Naskahkuno::index');
	$subroutes->add('index', 'Naskahkuno::index');
	$subroutes->add('detail/(:any)', 'Naskahkuno::detail/$1');
	$subroutes->add('create', 'Naskahkuno::create');
	$subroutes->add('edit/(:any)', 'Naskahkuno::edit/$1');
	$subroutes->add('delete/(:any)', 'Naskahkuno::delete/$1');
	$subroutes->add('apply_status/(:any)', 'Naskahkuno::apply_status/$1');
	$subroutes->add('do_init', 'Naskahkuno::do_init');
	$subroutes->add('do_upload', 'Naskahkuno::do_upload');
	$subroutes->add('do_delete', 'Naskahkuno::do_delete');
	$subroutes->add('flip', 'Naskahkuno::flip');
	$subroutes->add('thumb', 'Naskahkuno::thumb');
});

$routes->group('api/deposit/pengajuan/naskah-kuno', ['namespace' => 'DepositPengajuanNaskahkuno\Controllers\Api'], function ($subroutes) {
	/*** Route Update for Naskahkuno ***/
	$subroutes->add('', 'Naskahkuno::index');
	$subroutes->add('index', 'Naskahkuno::index');
	$subroutes->add('detail/(:any)', 'Naskahkuno::detail/$1');
	$subroutes->add('create', 'Naskahkuno::create');
	$subroutes->add('edit/(:any)', 'Naskahkuno::edit/$1');
	$subroutes->add('delete/(:any)', 'Naskahkuno::delete/$1');
	$subroutes->add('upload_file', 'Naskahkuno::upload_file');
});
