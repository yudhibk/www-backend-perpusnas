<?php if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}
$routes->group('cms/pameran', ['namespace' => 'Pameran\Controllers'], function (
    $subroutes
) {
    /*** Route Update for Pameran ***/
    $subroutes->add('', 'Pameran::index');
    $subroutes->add('index', 'Pameran::index');
    $subroutes->add('detail/(:any)', 'Pameran::detail/$1');
    $subroutes->add('edit/(:any)', 'Pameran::edit/$1');
    $subroutes->add('create', 'Pameran::create');
    $subroutes->add('delete/(:any)', 'Pameran::delete/$1');
    $subroutes->add('do_init', 'Pameran::do_init');
    $subroutes->add('do_upload', 'Pameran::do_upload');
    $subroutes->add('do_delete', 'Pameran::do_delete');
    $subroutes->add('flip', 'Pameran::flip');
    $subroutes->add('apply_status/(:any)', 'Pameran::apply_status/$1');
    $subroutes->add('export', 'Pameran::export');
    $subroutes->add('thumb', 'Pameran::thumb');
});

$routes->group(
    'api/pameran',
    ['namespace' => 'Pameran\Controllers\Api'],
    function ($subroutes) {
        //crud
        $subroutes->add('', 'Pameran::index');
        $subroutes->add('index', 'Pameran::index');
        $subroutes->add('index/(:any)', 'Pameran::index/$1');
        $subroutes->add('detail/(:any)', 'Pameran::detail/$1');
        $subroutes->add('show/(:any)', 'Pameran::show/$1');
        $subroutes->add('create', 'Pameran::create');
        $subroutes->add('update/(:any)', 'Pameran::update/$1');
        $subroutes->add('delete/(:any)', 'Pameran::delete/$1');

        //custom
        $subroutes->add('datatable', 'Pameran::datatable');
        $subroutes->add('datatable/(:any)', 'Pameran::datatable/$1');
        $subroutes->add('upload_file', 'Pameran::upload_file');
    }
);
