<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('flip', ['namespace' => 'Flip\Controllers'], function ($subroutes) {
	/*** Route Update for Flip ***/
	$subroutes->add('', 'Flip::index');
});