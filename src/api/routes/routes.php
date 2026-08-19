<?php

return array(
    'GET' => array(
        '/'                   => array('AuthController',          'info'),
        '/verificar'          => array('AuthController',          'verificar'),
        '/all'                => array('AllController',           'index'),
        '/clientes'           => array('ClientesController',      'index'),
        '/motos'              => array('MotosController',         'index'),
        '/repuestos'          => array('RepuestosController',     'index'),

        /* Integracion CFMOTO (spec cfmoto-api-sincronizacion v1) */
        '/sync/clientes'      => array('CfmotoClientesController', 'get'),
        '/sync/taller/turnos' => array('CfmotoOrdenesController',  'get'),
    ),
    'POST' => array(
        '/login'              => array('AuthController',          'login'),

        /* Integracion CFMOTO: sync por lotes */
        '/sync/clientes'      => array('CfmotoClientesController', 'post'),
        '/sync/taller/turnos' => array('CfmotoOrdenesController',  'post'),

        /* Integracion CFMOTO: webhooks entrantes (mismo formato + bloque evento) */
        '/webhook/contactos'  => array('CfmotoClientesController', 'post'),
        '/webhook/taller'     => array('CfmotoOrdenesController',  'post'),
    ),
);
