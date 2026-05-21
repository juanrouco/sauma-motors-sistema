<?php

class JWTException extends Exception
{
    public function __construct($message, $code = 401)
    {
        parent::__construct($message, $code);
    }
}

class JWT
{
    private static function secret()
    {
        // ⚠️ Cambiar este valor por una clave secreta propia en produccion
        return getenv('JWT_SECRET') ?: 'sauma_clave_secreta_cambiar_en_produccion';
    }

    public static function getExpires()
    {
        return (int)(getenv('JWT_EXPIRES') ?: 3600);
    }

    private static function b64Encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64Decode($data)
    {
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function generate($payload)
    {
        $header         = self::b64Encode(json_encode(array('alg' => 'HS256', 'typ' => 'JWT')));
        $payload['iat'] = time();
        $payload['exp'] = time() + self::getExpires();
        $body           = self::b64Encode(json_encode($payload));
        $firma          = self::b64Encode(hash_hmac('sha256', "$header.$body", self::secret(), true));
        return "$header.$body.$firma";
    }

    public static function validate($token)
    {
        $partes = explode('.', $token);
        if (count($partes) !== 3) {
            throw new JWTException('Token con formato invalido.', 401);
        }

        list($header, $body, $firmaRecibida) = $partes;
        $firmaEsperada = self::b64Encode(hash_hmac('sha256', "$header.$body", self::secret(), true));

        // Comparacion segura compatible con PHP < 5.6
        if (strlen($firmaEsperada) !== strlen($firmaRecibida)) {
            throw new JWTException('Token con firma invalida.', 401);
        }
        $diff = 0;
        for ($i = 0; $i < strlen($firmaEsperada); $i++) {
            $diff |= ord($firmaEsperada[$i]) ^ ord($firmaRecibida[$i]);
        }
        if ($diff !== 0) {
            throw new JWTException('Token con firma invalida.', 401);
        }

        $payload = json_decode(self::b64Decode($body), true);
        if (!$payload || !isset($payload['exp'])) {
            throw new JWTException('Token malformado.', 401);
        }

        if (time() > $payload['exp']) {
            throw new JWTException('Token expirado.', 401);
        }

        return $payload;
    }

    public static function fromHeader()
    {
        $headers = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $req = apache_request_headers();
            if (isset($req['Authorization'])) {
                $headers = $req['Authorization'];
            } elseif (isset($req['authorization'])) {
                $headers = $req['authorization'];
            }
        }

        if (!$headers || !preg_match('/Bearer\s+(.+)/i', $headers, $m)) {
            throw new JWTException('Token no encontrado en el header Authorization.', 401);
        }

        return $m[1];
    }
}