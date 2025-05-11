   // MODULE SUBROUTES START
    $routes->group('__module__', function($routes) {
        $routes->get('/',           '__Module__Controller::index');
        $routes->get('create',      '__Module__Controller::create');
        $routes->post('store',      '__Module__Controller::store');
        $routes->get('edit/(:num)', '__Module__Controller::edit/$1');
        $routes->post('update/(:num)', '__Module__Controller::update/$1');
        $routes->get('delete/(:num)', '__Module__Controller::delete/$1');
        $routes->get('(:num)',      '__Module__Controller::show/$1');
    });
   // MODULE SUBROUTES END
    