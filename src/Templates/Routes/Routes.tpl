<?php

$routes->group('__module__', ['namespace' => 'App\Modules\__Module__\Controllers'], function($routes) {
    
    // Dashboard do módulo
    $routes->get('/', '__Module__Controller::index');

    // Rotas extras
    
});