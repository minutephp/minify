<?php

/** @var Router $router */
use Minute\Model\Permission;
use Minute\Routing\Router;

$router->get('/admin/minify', null, 'admin', 'm_configs[type] as configs')
       ->setReadPermission('configs', 'admin')->setDefault('type', 'minify');
$router->post('/admin/minify', null, 'admin', 'm_configs as configs')
       ->setAllPermissions('configs', 'admin');

$router->get('/admin/minify/reset', 'Admin/Minify/Reset', 'admin')
       ->setDefault('_noView', true);

$router->get('/admin/minify/truncate', 'Admin/Minify/Truncate', 'admin')
       ->setDefault('_noView', true);

$router->get('/static/cache/{version}/{name}', 'Minify/Content.php', false);
/*, 'm_minified_data[name] as minified')
       ->setReadPermission('minified', Permission::EVERYONE)->setDefault('_noView', true)
       ->addConstraint('minified', function ($builder) use ($router) {
           $self = $router->getLastMatchingRoute();
           $builder->where('version', '=', $self->getDefault('version'));
       });*/