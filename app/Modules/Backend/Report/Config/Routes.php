<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('report', ['namespace' => 'Report\Controllers'], function ($subroutes) {
	/*** Route Update for Report ***/
	$subroutes->add('', 'Report::index');
	$subroutes->add('index', 'Report::index');
	$subroutes->add('visitor', 'Report::visitor');
	$subroutes->add('visitor_export', 'Report::visitor_export');
});

$routes->group('api/report', ['namespace' => 'Report\Controllers\Api'], function ($subroutes) {
	//crud
	$subroutes->add('', 'Report::index');
	$subroutes->add('index', 'Report::index');

	//custom
	$subroutes->add('visitor_datatable', 'Report::visitor_datatable');
	$subroutes->add('visitor_datatable/(:any)/(:any)', 'Report::visitor_datatable/$1/$2');
});
