<?php

class Response
{
    public static function headers()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    public static function send($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function forGiven($code, $ok, $mensaje, $datos = null)
    {
        $body = array('ok' => (bool)$ok, 'mensaje' => $mensaje);
        if ($datos !== null) {
            $body['datos'] = $datos;
        }
        return array((int)$code, $body);
    }
}
