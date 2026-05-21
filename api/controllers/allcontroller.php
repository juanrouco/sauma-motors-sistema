<?php

require_once __DIR__ . '/../helpers/jwt.php';
require_once __DIR__ . '/clientescontroller.php';
require_once __DIR__ . '/repuestoscontroller.php';
require_once __DIR__ . '/motoscontroller.php';

class AllController
{
    /**
     * GET /all
     * Requiere: Authorization: Bearer {token}
     * Devuelve: clientes + motos + repuestos en una sola respuesta.
     *
     * Formato de respuesta:
     *   {
     *     clientes:  [...],
     *     motos:     [...],
     *     repuestos: [...]
     *   }
     */
    public function index($body, $query, $params)
    {
        try {
            $token = JWT::fromHeader();
            JWT::validate($token);
        } catch (JWTException $e) {
            return Response::forGiven($e->getCode(), false, $e->getMessage());
        }

        $oClientes  = new ClientesController();
        $oMotos     = new MotosController();
        $oRepuestos = new RepuestosController();

        $resClientes  = $oClientes->index($body, $query, $params);
        $resMotos     = $oMotos->index($body, $query, $params);
        $resRepuestos = $oRepuestos->index($body, $query, $params);

        // Si alguno fallo, propagamos el error
        $errores = array();
        if ($resClientes[0]  !== 200) $errores[] = 'clientes';
        if ($resMotos[0]     !== 200) $errores[] = 'motos';
        if ($resRepuestos[0] !== 200) $errores[] = 'repuestos';

        if (!empty($errores)) {
            return Response::forGiven(
                500,
                false,
                'Error al obtener datos de: ' . implode(', ', $errores)
            );
        }

        return Response::forGiven(200, true, 'Datos obtenidos correctamente.', array(
            'clientes'  => isset($resClientes[1]['datos'])  ? $resClientes[1]['datos']  : array(),
            'motos'     => isset($resMotos[1]['datos'])     ? $resMotos[1]['datos']     : array(),
            'repuestos' => isset($resRepuestos[1]['datos']) ? $resRepuestos[1]['datos'] : array(),
        ));
    }
}