<?php if (!isset($routes)) {
	$routes = \Config\Services::routes(true);
}

$routes->group('/auth', ['namespace' => 'Auth\Controllers\Api'], function ($subroutes) {
    /** @var RouteCollection $routes */
    $subroutes->get('', 'Auth::index');
    $subroutes->get('index', 'Auth::index');
    $subroutes->post('register', 'Auth::register');
    $subroutes->post('register/reseller', 'Auth::register/reseller');
    $subroutes->post('register/buyer', 'Auth::register/buyer');
    $subroutes->post('login', 'Auth::login');
    $subroutes->get('activate/(:any)', 'Auth::activate/$1');
    $subroutes->post('verify/(:any)', 'Auth::verify/$1');
    $subroutes->post('resend_email', 'Auth::resend_email');
    $subroutes->post('forget_password', 'Auth::forget_password');
    $subroutes->post('reset_password/(:any)', 'Auth::reset_password/$1');

	$subroutes->post('login_oauth', 'Auth::login_oauth');
	$subroutes->post('fast_login/(:any)', 'Auth::fast_login/$1');
});

$routes->group('/auth', ['namespace' => 'Auth\Controllers\Api', 'filter' => 'auth'], function ($subroutes) {
    /** @var RouteCollection $routes */
    $subroutes->get('', 'Auth::index');
    $subroutes->get('profile', 'Auth::profile');
    $subroutes->post('profile', 'Auth::update_profile');
    $subroutes->post('change_password', 'Auth::change_password');
	$subroutes->post('upload_avatar', 'Auth::upload_avatar');
	$subroutes->post('upload_cover', 'Auth::upload_cover');

	$subroutes->post('identity', 'Auth::update_identity');
    $subroutes->post('bank', 'Auth::update_bank');
});
