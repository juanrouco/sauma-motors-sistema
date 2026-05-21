<?php

return array(
    'GET' => array(
        '/'          => array('AuthController',     'info'),
        '/verificar' => array('AuthController',     'verificar'),
        '/all'       => array('AllController',      'index'),
        '/clientes'  => array('ClientesController', 'index'),
        '/motos'     => array('MotosController',    'index'),
        '/repuestos' => array('RepuestosController','index'),
    ),
    'POST' => array(
        '/login'     => array('AuthController',     'login'),
    ),
);