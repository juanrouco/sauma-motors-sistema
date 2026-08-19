<?php

/**
 * Reintenta los webhooks CFMOTO que fallaron (encolados en
 * _recursos/cfmoto/pendientes/). Pensado para correr por cron o a mano:
 *
 *   docker exec sauma_web php /var/www/html/cfmoto_reintentos.php
 *
 * Sugerido en crontab (backoff simple, cada 5 minutos):
 *   *\/5 * * * * php /var/www/html/cfmoto_reintentos.php
 */

/* solo CLI: no debe ser accesible via web */
if (php_sapi_name() !== 'cli')
{
	header('HTTP/1.1 403 Forbidden');
	exit('Solo ejecutable por linea de comandos.');
}

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/library');

require_once(dirname(__FILE__) . '/library/class.cfmoto.php');

$enviados = CFMoto::ReenviarPendientes();
echo date('Y-m-d H:i:s') . ' - Webhooks reenviados: ' . $enviados . "\n";

?>
