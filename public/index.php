<?php

require '../vendor/autoload.php';

$router = new Core\Router();
$url = $_SERVER['QUERY_STRING'];

// Routing tale
$router->setRoutes('', ['controller' => 'Contacts', 'action' => 'index']);
$router->setRoutes('contacts', ['controller' => 'Contacts', 'action' => 'index']);
$router->setRoutes('add-new', ['controller' => 'Contacts', 'action' => 'addNew']);
$router->setRoutes('show-contact', ['controller' => 'Contacts', 'action' => 'show']);
$router->setRoutes('edit-contact', ['controller' => 'Contacts', 'action' => 'edit']);
$router->setRoutes('delete-contact', ['controller' => 'Contacts', 'action' => 'delete']);

$router->dispatchRoutes($url);