<?php

error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array(
            'ok'      => false,
            'mensaje' => 'Error fatal: ' . $error['message'],
            'archivo' => $error['file'],
            'linea'   => $error['line'],
        ));
    }
});

if (!defined('undefined')) { define('undefined', null); }

require_once __DIR__ . '/helpers/Response.php';

Response::headers();

// ── Parseo de la ruta ────────────────────────────────────────────────────────
if (!empty($_SERVER['PATH_INFO'])) {
    $ruta = rtrim($_SERVER['PATH_INFO'], '/') ?: '/';
} else {
    $uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri  = rtrim($uri, '/');
    $ruta = '/' . ltrim(preg_replace('#^.*?/api(?:/index\.php)?#', '', $uri), '/');
    if ($ruta === '') $ruta = '/';
}

$metodo = $_SERVER['REQUEST_METHOD'];

// ── Registro de rutas ────────────────────────────────────────────────────────
$routes = require __DIR__ . '/routes/routes.php';

if (!isset($routes[$metodo]) || !isset($routes[$metodo][$ruta])) {
    Response::send(array(
        'ok'                    => false,
        'mensaje'               => 'Endpoint "' . $ruta . '" no encontrado.',
        'endpoints_disponibles' => array(
            'POST /api/login',
            'GET  /api/verificar',
        ),
    ), 404);
}

list($controllerClass, $action) = $routes[$metodo][$ruta];

// ── Carga del controller ─────────────────────────────────────────────────────
require_once __DIR__ . '/controllers/' . strtolower($controllerClass) . '.php';

// ── Preparacion de argumentos ────────────────────────────────────────────────
$body = array();
if ($metodo !== 'GET' && $metodo !== 'DELETE') {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        Response::send(array('ok' => false, 'mensaje' => 'El body debe ser JSON valido.'), 400);
    }
    $body = $body ? $body : array();
}

$query  = $_GET;
$params = array();

// ── Invocacion del controller ────────────────────────────────────────────────
$controller = new $controllerClass();

try {
    $result = $controller->$action($body, $query, $params);
} catch (Exception $e) {
    Response::send(array('ok' => false, 'mensaje' => 'Error interno del servidor.'), 500);
}

// ── Procesamiento del retorno ────────────────────────────────────────────────
if (is_int($result)) {
    http_response_code($result);
    exit;
}

if (is_array($result) && count($result) === 2 && is_int($result[0]) && is_array($result[1])) {
    Response::send($result[1], $result[0]);
}

Response::send($result, 200);
