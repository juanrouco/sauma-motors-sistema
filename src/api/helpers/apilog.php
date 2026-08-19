<?php

/**
 * Log detallado del trafico ENTRANTE de la API.
 *
 * Cada request recibido queda registrado con: fecha/hora, IP, metodo, URL,
 * body recibido, codigo HTTP devuelto, cuerpo de la respuesta y duracion.
 * Un archivo por dia: _recursos/cfmoto/api-recibidos-AAAAMMDD.log
 *
 * Sin dependencias de library/ a proposito: se carga en todos los requests
 * de la API (incluido /login) sin costo.
 */
class ApiLog
{
	/* limite por cuerpo logueado, para que un GET gigante no infle el archivo */
	const MaxBody = 500000;

	private static $metodo   = '';
	private static $uri      = '';
	private static $request  = '';
	private static $inicio   = 0;
	private static $logueado = false;

	/* Registra los datos del request apenas llega (la respuesta se loguea al salir) */
	public static function Request($metodo, $uri, $rawBody)
	{
		self::$metodo  = $metodo;
		self::$uri     = $uri;
		self::$inicio  = microtime(true);
		self::$request = self::Redactar((string)$rawBody);
	}

	/* Escribe la entrada completa; la llama Response::send antes de responder */
	public static function Response($codigo, $body)
	{
		if (self::$logueado)
			return;
		self::$logueado = true;

		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
		$ms = self::$inicio ? round((microtime(true) - self::$inicio) * 1000) : 0;

		$linea  = '==== ' . date('Y-m-d H:i:s') . ' ====' . "\n";
		$linea .= '>> ' . self::$metodo . ' ' . self::$uri . ' (ip: ' . $ip . ')' . "\n";
		if (self::$request !== '')
			$linea .= 'REQUEST: ' . self::Truncar(self::$request) . "\n";
		$linea .= '<< HTTP ' . (int)$codigo . ' (' . $ms . ' ms)' . "\n";
		$linea .= 'RESPONSE: ' . self::Truncar((string)$body) . "\n\n";

		self::Escribir($linea);
	}

	/* Censura credenciales para que no queden contrasenas en los logs */
	private static function Redactar($texto)
	{
		return preg_replace('/"password"\s*:\s*"[^"]*"/i', '"password":"***"', $texto);
	}

	private static function Truncar($texto)
	{
		if (strlen($texto) > self::MaxBody)
			return substr($texto, 0, self::MaxBody) . ' ...[TRUNCADO: ' . strlen($texto) . ' bytes en total]';
		return $texto;
	}

	private static function Escribir($linea)
	{
		$path = __DIR__ . '/../../_recursos/cfmoto/';
		if (!is_dir($path))
			@mkdir($path, 0777, true);
		@file_put_contents($path . 'api-recibidos-' . date('Ymd') . '.log', $linea, FILE_APPEND | LOCK_EX);
	}
}
