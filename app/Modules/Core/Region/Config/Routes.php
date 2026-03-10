<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('api/region', ['namespace' => 'Region\Controllers\Api'], function ($subroutes) {
	$subroutes->get('province', 'Region::get_provinces');
	$subroutes->get('city/(:any)', 'Region::get_cities/$1');
	$subroutes->get('district/(:any)', 'Region::get_districts/$1');
	$subroutes->get('sub_district/(:any)', 'Region::get_sub_districts/$1');

	$subroutes->get('kab_kota', 'Region::get_kab_kota');
	$subroutes->get('kelurahan/(:any)', 'Region::get_kelurahan/$1');
});
