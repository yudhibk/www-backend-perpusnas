<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}
$routes->group('cms/rules-guide', ['namespace' => 'RulesGuide\Controllers'], function ($subroutes) {
	/*** Route Update for RulesGuide ***/
	$subroutes->add('', 'RulesGuide::index');
	$subroutes->add('index', 'RulesGuide::index');
	$subroutes->add('detail/(:any)', 'RulesGuide::detail/$1');
	$subroutes->add('create', 'RulesGuide::create');
	$subroutes->add('edit/(:any)', 'RulesGuide::edit/$1');
	$subroutes->add('delete/(:any)', 'RulesGuide::delete/$1');
	$subroutes->add('apply_status/(:any)', 'RulesGuide::apply_status/$1');
	$subroutes->add('do_init', 'RulesGuide::do_init');
	$subroutes->add('do_upload', 'RulesGuide::do_upload');
	$subroutes->add('do_delete', 'RulesGuide::do_delete');
	$subroutes->add('flip', 'RulesGuide::flip');
	$subroutes->add('thumb', 'RulesGuide::thumb');
});

$routes->group('api/cms/rules-guide', ['namespace' => 'RulesGuide\Controllers\Api'], function ($subroutes) {
	/*** Route Update for RulesGuide ***/
	$subroutes->add('', 'RulesGuide::index');
	$subroutes->add('index', 'RulesGuide::index');
	$subroutes->add('detail/(:any)', 'RulesGuide::detail/$1');
	$subroutes->add('create', 'RulesGuide::create');
	$subroutes->add('edit/(:any)', 'RulesGuide::edit/$1');
	$subroutes->add('delete/(:any)', 'RulesGuide::delete/$1');
	$subroutes->add('upload_file', 'RulesGuide::upload_file');
});
