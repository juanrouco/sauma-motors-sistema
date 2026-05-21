<?php

require_once __DIR__ . '/../helpers/jwt.php';

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../../library'));

require_once __DIR__ . '/../../library/class.usuarios.php';

class AuthController
{
    public function info($body, $query, $params)
    {
        return Response::forGiven(200, true, 'API SAUMA funcionando correctamente.', array(
            'version'   => '1.0',
            'endpoints' => array(
                'POST /api/login'     => 'Autenticar y obtener token JWT',
                'GET  /api/verificar' => 'Verificar validez del token',
            ),
        ));
    }

    /**
     * POST /login
     * Body esperado: { "login": "...", "password": "..." }
     */
    public function login($body, $query, $params)
    {
        $login    = isset($body['login'])    ? trim($body['login'])    : '';
        $password = isset($body['password']) ? trim($body['password']) : '';

        if ($login === '' || $password === '') {
            return Response::forGiven(422, false, 'Los campos "login" y "password" son obligatorios.');
        }

        $oUsuarios = new Usuarios();
        $oUsuario  = $oUsuarios->GetByCredentials($login, $password);

        if (!$oUsuario) {
            return Response::forGiven(401, false, 'Usuario o contrasena incorrectos.');
        }

        $token = JWT::generate(array(
            'id_usuario'  => (int)$oUsuario->IdUsuario,
            'login'       => $oUsuario->Login,
            'id_perfil'   => (int)$oUsuario->IdPerfil,
            'id_sector'   => (int)$oUsuario->IdSector,
        ));

        return Response::forGiven(200, true, 'Login exitoso.', array(
            'token'   => $token,
            'expira'  => JWT::getExpires() . ' segundos',
            'usuario' => array(
                'id'        => (int)$oUsuario->IdUsuario,
                'nombre'    => $oUsuario->Nombre . ' ' . $oUsuario->Apellido,
                'login'     => $oUsuario->Login,
                'email'     => $oUsuario->Email,
                'id_perfil' => (int)$oUsuario->IdPerfil,
            ),
        ));
    }

    /**
     * GET /verificar
     */
    public function verificar($body, $query, $params)
    {
        try {
            $token   = JWT::fromHeader();
            $payload = JWT::validate($token);
        } catch (JWTException $e) {
            return Response::forGiven($e->getCode(), false, $e->getMessage());
        }

        return Response::forGiven(200, true, 'Token valido.', array(
            'token_valido' => true,
            'usuario'      => array(
                'id_usuario' => $payload['id_usuario'],
                'login'      => $payload['login'],
                'id_perfil'  => $payload['id_perfil'],
            ),
            'token_info'   => array(
                'emitido'       => date('Y-m-d H:i:s', $payload['iat']),
                'expira'        => date('Y-m-d H:i:s', $payload['exp']),
                'expira_en_seg' => $payload['exp'] - time(),
            ),
        ));
    }
}