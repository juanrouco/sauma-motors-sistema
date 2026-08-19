<?php
/* Suite de pruebas integracion CFMOTO - corre dentro del contenedor sauma_web */

error_reporting(E_ERROR | E_PARSE);

$BASE = 'http://localhost/api';
$pass = 0; $fail = 0; $resultados = array();

function check($nombre, $cond, $detalle = '') {
	global $pass, $fail, $resultados;
	if ($cond) { $pass++; $resultados[] = "[PASS] $nombre"; }
	else { $fail++; $resultados[] = "[FAIL] $nombre" . ($detalle !== '' ? " -- $detalle" : ''); }
}

function http($metodo, $url, $body = null, $token = null) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
	$headers = array('Content-Type: application/json');
	if ($token) $headers[] = 'Authorization: Bearer ' . $token;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, is_string($body) ? $body : json_encode($body));
	$resp = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array($code, json_decode($resp, true), $resp);
}

$db = mysqli_connect('db', 'root', '', 'benelli_com_ar');
mysqli_set_charset($db, 'latin1');
function q($sql) { global $db; $r = mysqli_query($db, $sql); if (!$r) return null; $row = mysqli_fetch_row($r); return $row ? $row[0] : null; }

/* ---- limpieza: deja el estado sembrado como recien creado (re-ejecutable) ---- */
mysqli_query($db, "DELETE FROM tb_tallerunidades WHERE NumeroVin='LCEPCJL4XP1009999'");
mysqli_query($db, "DELETE FROM tb_clientes WHERE DocumentoNumero='99887766'");
mysqli_query($db, "DELETE FROM tb_ordenestrabajo WHERE IdOrdenTrabajo > 700004");
mysqli_query($db, "UPDATE tb_ordenestrabajo SET IdEstadoOrden=9, FechaFin=NULL, Kilometros=1200 WHERE IdOrdenTrabajo=700002");
mysqli_query($db, "UPDATE tb_ordenestrabajo SET IdEstadoOrden=12 WHERE IdOrdenTrabajo=700004");
mysqli_query($db, "UPDATE tb_clientes SET Email='juan.perez@cfmototest.com' WHERE IdCliente=900001");
@unlink('/var/www/html/_recursos/cfmoto/eventos-recibidos.log');
foreach (glob('/var/www/html/_recursos/cfmoto/pendientes/*.json') as $f) @unlink($f);
@unlink('/tmp/mock-recibido.log');
@unlink('/var/www/html/_recursos/cfmoto/api-recibidos-' . date('Ymd') . '.log');
@unlink('/var/www/html/_recursos/cfmoto/api-enviados-' . date('Ymd') . '.log');

echo "=== SUITE A: Autenticacion ===\n";

list($c, $j) = http('POST', "$BASE/login", array('login' => 'apitest', 'password' => 'apitest123'));
check('A1 login valido devuelve 200 y token', $c == 200 && !empty($j['datos']['token']), "code=$c");
$TOKEN = isset($j['datos']['token']) ? $j['datos']['token'] : '';

list($c, $j) = http('POST', "$BASE/login", array('login' => 'apitest', 'password' => 'MAL'));
check('A2 login invalido devuelve 401', $c == 401, "code=$c");

list($c, $j) = http('GET', "$BASE/sync/taller/turnos?id=700001");
check('A3 GET sin token devuelve 401', $c == 401, "code=$c");

list($c, $j) = http('GET', "$BASE/sync/taller/turnos?id=700001", null, 'token.falso.xxx');
check('A4 GET con token invalido devuelve 401', $c == 401, "code=$c");

list($c, $j) = http('GET', "$BASE/");
check('A5 info lista los endpoints nuevos', $c == 200 && isset($j['datos']['endpoints']['POST /api/webhook/taller']), "code=$c");

echo "=== SUITE B: GET ordenes de trabajo ===\n";

list($c, $j) = http('GET', "$BASE/sync/taller/turnos?id=700001", null, $TOKEN);
$t = isset($j['turnos'][0]) ? $j['turnos'][0] : array();
check('B1 GET OT puntual devuelve 200 con 1 turno', $c == 200 && count($j['turnos']) == 1, "code=$c");
check('B2 id_externo y numero_orden correctos', $t['id_externo'] == 'OT-700001' && $t['numero_orden'] == 'OT-700001');
check('B3 estado finalizado', $t['estado'] == 'finalizado', 'estado=' . $t['estado']);
check('B4 cliente correcto', $t['cliente']['id_externo'] == 'CLI-900001' && $t['cliente']['nombre'] == 'PEREZ JUAN CFMOTO TEST');
check('B5 telefono cliente en E.164', $t['cliente']['telefono'] == '+541138611119', 'tel=' . $t['cliente']['telefono']);
check('B6 moto VIN completo (prefijo+numero)', $t['moto']['vin'] == 'LCELDGB19P1000123', 'vin=' . $t['moto']['vin']);
check('B7 moto marca/modelo/anio/patente', $t['moto']['marca'] == 'CF MOTO' && $t['moto']['modelo'] == 'CF 450 SR' && $t['moto']['anio'] == 2024 && $t['moto']['patente'] == 'A123BCD');
check('B8 kilometraje_ingreso de la OT', $t['moto']['kilometraje_ingreso'] == 5240, 'km=' . $t['moto']['kilometraje_ingreso']);
check('B9 garantia inicio/fin +24 meses y vigente', $t['garantia']['inicio'] == '2026-02-15' && $t['garantia']['fin'] == '2028-02-15' && $t['garantia']['vigente'] === true);
check('B10 garantia aplica (hay tarea de garantia)', $t['garantia']['aplica_a_este_ingreso'] === true);
check('B11 servicio tipo/razon garantia', $t['servicio']['tipo'] == 'garantia' && $t['servicio']['razon_ingreso'] == 'garantia');
check('B12 trabajos_realizados con 2 tareas', count($t['servicio']['trabajos_realizados']) == 2, 'n=' . count($t['servicio']['trabajos_realizados']));
check('B13 repuestos: 2 items con garantia false', count($t['repuestos']) == 2 && $t['repuestos'][0]['cubierto_por_garantia'] === false);
check('B14 repuestos precio_unitario calculado (8358.68/2)', abs($t['repuestos'][1]['precio_unitario'] - 4179.34) < 0.01, 'pu=' . $t['repuestos'][1]['precio_unitario']);
check('B15 programacion: turno de agenda fecha y hora', $t['programacion']['fecha_turno'] == '2026-08-10' && $t['programacion']['hora_turno'] == '10:00');
check('B16 fecha_ingreso ISO con timezone', strpos($t['programacion']['fecha_ingreso'], '2026-08-10T09:30:00') === 0);
check('B17 finalizada: entrega real seteada, estimada null', strpos($t['programacion']['fecha_entrega_real'], '2026-08-12T16:40:00') === 0 && $t['programacion']['fecha_entrega_estimada'] === null);
check('B18 mecanico asignado', $t['programacion']['mecanico']['id_externo'] == 'MEC-9001' && $t['programacion']['mecanico']['nombre'] == 'Test API');
check('B19 notas: comentarios OT + tabla comentarios', strpos($t['notas_internas'], 'SERVICE DE 5000 KM') !== false && strpos($t['notas_internas'], 'REVISAR CADENA') !== false);
check('B20 adjuntos: imagen con url', count($t['adjuntos']) == 1 && strpos($t['adjuntos'][0]['url'], 'ot700001-ingreso.jpg') !== false);
check('B21 importes: moneda y estado_pago pendiente', $t['importes']['moneda'] == 'ARS' && $t['importes']['estado_pago'] == 'pendiente');
check('B22 importes: total > 0 y repuestos > 0', $t['importes']['total'] > 0 && $t['importes']['repuestos'] > 0, 'total=' . $t['importes']['total'] . ' rep=' . $t['importes']['repuestos']);
check('B23 creado_en de la fecha de la OT', strpos($t['creado_en'], '2026-08-10') === 0);

list($c, $j) = http('GET', "$BASE/sync/taller/turnos?id_externo=OT-700002", null, $TOKEN);
$t2 = isset($j['turnos'][0]) ? $j['turnos'][0] : array();
check('B24 acepta id_externo OT-700002', $c == 200 && $t2['id_externo'] == 'OT-700002');
check('B25 aceptada con inicio sin fin = en_proceso', $t2['estado'] == 'en_proceso', 'estado=' . $t2['estado']);
check('B26 sin fecha garantia: campos null', $t2['garantia']['inicio'] === null && $t2['garantia']['vigente'] === null);
check('B27 sin tareas: servicio otro y sin repuestos', $t2['servicio']['tipo'] == 'otro' && count($t2['repuestos']) == 0);
check('B28 sin turno agenda: fecha_turno cae a fecha OT', $t2['programacion']['fecha_turno'] == '2026-08-15');

list($c, $j) = http('GET', "$BASE/sync/taller/turnos?id=999999", null, $TOKEN);
check('B29 OT inexistente devuelve 404', $c == 404, "code=$c");

list($c, $j) = http('GET', "$BASE/sync/taller/turnos", null, $TOKEN);
$ids = array();
if (isset($j['turnos'])) foreach ($j['turnos'] as $tt) $ids[] = $tt['id_externo'];
check('B30 sin filtro: devuelve exactamente las finalizadas', $c == 200 && count($ids) == 2 && in_array('OT-700001', $ids) && in_array('OT-700004', $ids), implode(',', $ids));
check('B31 envelope: origen_sistema y generado_en', $j['origen_sistema'] == 'DMS_CONCESIONARIA' && !empty($j['generado_en']));

echo "=== SUITE C: GET clientes ===\n";

list($c, $j) = http('GET', "$BASE/sync/clientes?orden_id=700001", null, $TOKEN);
$cl = isset($j['clientes'][0]) ? $j['clientes'][0] : array();
check('C1 cliente por orden_id devuelve 200', $c == 200 && count($j['clientes']) == 1, "code=$c");
check('C2 id_externo y nombre', $cl['id_externo'] == 'CLI-900001' && $cl['nombre'] == 'PEREZ JUAN CFMOTO TEST');
check('C3 tipo cliente y persona fisica sin razon_social', $cl['tipo'] == 'cliente' && $cl['razon_social'] === null);
check('C4 documento CUIT prioritario sobre DNI', $cl['documento']['tipo'] == 'CUIT' && $cl['documento']['numero'] == '20345678903');
check('C5 direccion completa con ciudad y provincia', $cl['direccion']['calle'] == 'AV. CORRIENTES 1234' && $cl['direccion']['piso_depto'] == '5 B' && $cl['direccion']['ciudad'] == 'LA PLATA' && $cl['direccion']['provincia'] == 'BUENOS AIRES' && $cl['direccion']['codigo_postal'] == 'C1043');
check('C6 vendedor asignado', $cl['vendedor_asignado']['id_externo'] == 'VEN-9001' && $cl['vendedor_asignado']['nombre'] == 'Test API');
check('C7 fecha_alta = hoy (regla acordada)', $cl['fecha_alta'] == date('Y-m-d'));
check('C8 motos del cliente: 1 (la 800001)', count($cl['motos']) == 1 && $cl['motos'][0]['id_externo'] == 'UNI-800001');
$m = $cl['motos'][0];
check('C9 moto: version repite modelo (regla acordada)', $m['version'] == $m['modelo'] && $m['modelo'] == 'CF 450 SR');
check('C10 moto: kilometraje de la ultima OT', $m['kilometraje'] == 5600 && $m['kilometraje_actualizado_en'] == '2026-08-17', 'km=' . $m['kilometraje'] . ' f=' . $m['kilometraje_actualizado_en']);
check('C11 moto sin IdUnidad: tipo_operacion desconocido', $m['propiedad']['tipo_operacion'] == 'desconocido');
check('C12 marcas_interes derivadas de las motos', in_array('CF MOTO', $cl['marcas_interes']));

list($c, $j) = http('GET', "$BASE/sync/clientes?orden_id=OT-700004", null, $TOKEN);
$cl2 = isset($j['clientes'][0]) ? $j['clientes'][0] : array();
check('C13 cliente real por orden con id_externo OT-', $c == 200 && $cl2['id_externo'] == 'CLI-153080');
$motos2 = $cl2['motos'];
$m0km = null;
foreach ($motos2 as $mm) if ($mm['id_externo'] == 'UNI-800002') $m0km = $mm;
check('C14 moto con IdUnidad: tipo_operacion 0km', $m0km && $m0km['propiedad']['tipo_operacion'] == '0km');

list($c, $j) = http('GET', "$BASE/sync/clientes?id=CLI-900001", null, $TOKEN);
check('C15 cliente por id CLI-', $c == 200 && $j['clientes'][0]['id_externo'] == 'CLI-900001');

list($c, $j) = http('GET', "$BASE/sync/clientes", null, $TOKEN);
check('C16 sin parametros devuelve 422', $c == 422, "code=$c");

list($c, $j) = http('GET', "$BASE/sync/clientes?orden_id=999999", null, $TOKEN);
check('C17 orden inexistente devuelve 404', $c == 404, "code=$c");

echo "=== SUITE D: POST clientes (alta / actualizacion) ===\n";

$nuevoCliente = array(
	'id_externo' => 'CRM-NUEVO-001',
	'tipo' => 'cliente',
	'nombre' => 'Ángel Rodríguez Peña',
	'documento' => array('tipo' => 'DNI', 'numero' => '99887766'),
	'telefono' => '+5491144556677',
	'email' => 'angel.test@cfmoto.com',
	'direccion' => array('calle' => 'Av. Santa Fe 4321', 'piso_depto' => '2A', 'ciudad' => 'CABA', 'codigo_postal' => 'C1425', 'pais' => 'AR'),
	'motos' => array(array(
		'id_externo' => 'CRM-MOTO-001',
		'vin' => 'LCEPCJL4XP1009999',
		'vin_motor' => '450MT9999',
		'marca' => 'CF MOTO',
		'modelo' => 'CF 450 MT',
		'anio' => 2025,
		'patente' => 'AH111JJ',
		'garantia' => array('inicio' => '2026-08-01'),
	)),
);

list($c, $j) = http('POST', "$BASE/sync/clientes", array('origen_sistema' => 'CFMOTO_WEB', 'clientes' => array($nuevoCliente)), $TOKEN);
check('D1 alta cliente nuevo: creados=1 sin errores', $c == 200 && $j['creados'] == 1 && count($j['errores']) == 0, json_encode($j));
$idNuevo = q("SELECT IdCliente FROM tb_clientes WHERE DocumentoNumero='99887766'");
check('D2 cliente existe en la base', $idNuevo > 0, "id=$idNuevo");
check('D3 nombre guardado en latin1 con acentos y enie', q("SELECT RazonSocial FROM tb_clientes WHERE IdCliente=$idNuevo") == utf8_decode('Ángel Rodríguez Peña'));
check('D4 direccion parseada: calle y altura separadas', q("SELECT DomicilioCalle FROM tb_clientes WHERE IdCliente=$idNuevo") == 'Av. Santa Fe' && q("SELECT DomicilioNumero FROM tb_clientes WHERE IdCliente=$idNuevo") == '4321');
check('D5 telefono guardado sin prefijo pais', q("SELECT Telefono FROM tb_clientes WHERE IdCliente=$idNuevo") == '91144556677');
$tuNueva = q("SELECT IdTallerUnidad FROM tb_tallerunidades WHERE NumeroVin='LCEPCJL4XP1009999'");
check('D6 moto creada como taller unidad por VIN', $tuNueva > 0, "id=$tuNueva");
check('D7 moto: marca resuelta por nombre y garantia', q("SELECT IdMarca FROM tb_tallerunidades WHERE IdTallerUnidad=$tuNueva") == 40 && q("SELECT FechaInicioGarantia FROM tb_tallerunidades WHERE IdTallerUnidad=$tuNueva") == '2026-08-01');

list($c, $j) = http('POST', "$BASE/sync/clientes", array('clientes' => array($nuevoCliente)), $TOKEN);
check('D8 re-envio identico: actualizados=1, no duplica', $c == 200 && $j['actualizados'] == 1 && $j['creados'] == 0);
check('D9 idempotencia: sigue habiendo 1 cliente y 1 moto', q("SELECT COUNT(*) FROM tb_clientes WHERE DocumentoNumero='99887766'") == 1 && q("SELECT COUNT(*) FROM tb_tallerunidades WHERE NumeroVin='LCEPCJL4XP1009999'") == 1);

list($c, $j) = http('POST', "$BASE/sync/clientes", array('clientes' => array(array(
	'id_externo' => 'CLI-900001', 'email' => 'nuevo.mail@cfmototest.com'))), $TOKEN);
check('D10 update por id_externo: cambia el email', $c == 200 && $j['actualizados'] == 1 && q("SELECT Email FROM tb_clientes WHERE IdCliente=900001") == 'nuevo.mail@cfmototest.com');

list($c, $j) = http('POST', "$BASE/sync/clientes", array('clientes' => array(array('id_externo' => 'CRM-SINNOMBRE', 'email' => 'x@x.com'))), $TOKEN);
check('D11 cliente nuevo sin nombre: error NOMBRE_OBLIGATORIO', $j['errores'][0]['codigo'] == 'NOMBRE_OBLIGATORIO');

list($c, $j) = http('POST', "$BASE/sync/clientes", array('clientes' => array(array(
	'id_externo' => 'CLI-900001', 'motos' => array(array('id_externo' => 'X-1', 'modelo' => 'SIN VIN'))))), $TOKEN);
check('D12 moto sin VIN: error VIN_OBLIGATORIO', $j['errores'][0]['codigo'] == 'VIN_OBLIGATORIO');

list($c, $j) = http('POST', "$BASE/sync/clientes", array('otracosa' => 1), $TOKEN);
check('D13 body sin array clientes devuelve 400', $c == 400, "code=$c");

list($c, $j, $raw) = http('GET', "$BASE/sync/clientes?id=$idNuevo", null, $TOKEN);
check('D14 roundtrip encoding: el nombre vuelve en UTF-8 correcto', strpos($raw, 'Rodr') !== false && $j['clientes'][0]['nombre'] == 'Ángel Rodríguez Peña', $j['clientes'][0]['nombre']);

echo "=== SUITE E: POST turnos (VIN obligatorio) ===\n";

list($c, $j) = http('POST', "$BASE/sync/taller/turnos", array('turnos' => array(array('id_externo' => 'OT-CRM-1', 'estado' => 'confirmado'))), $TOKEN);
check('E1 turno sin moto.vin: VIN_OBLIGATORIO', $j['errores'][0]['codigo'] == 'VIN_OBLIGATORIO', json_encode($j));

list($c, $j) = http('POST', "$BASE/sync/taller/turnos", array('turnos' => array(array(
	'id_externo' => 'OT-CRM-2', 'estado' => 'confirmado', 'moto' => array('vin' => 'ZZZZZZZZZZZZZZ999')))), $TOKEN);
check('E2 VIN inexistente: VIN_NO_ENCONTRADO', $j['errores'][0]['codigo'] == 'VIN_NO_ENCONTRADO');
check('E3 mensaje incluye el VIN', strpos($j['errores'][0]['mensaje'], 'ZZZZZZZZZZZZZZ999') !== false);

list($c, $j) = http('POST', "$BASE/sync/taller/turnos", array('turnos' => array(array(
	'id_externo' => 'OT-CRM-3',
	'estado' => 'confirmado',
	'moto' => array('vin' => 'LCELDGB19P1000456', 'kilometraje_ingreso' => 2500),
	'programacion' => array('fecha_turno' => '2026-08-25', 'hora_turno' => '10:00'),
	'servicio' => array('tipo' => 'service_1000km', 'descripcion' => 'Service de 1000 km', 'falla_reportada' => 'Vibración en el manubrio'),
))), $TOKEN);
check('E4 turno nuevo con VIN valido: creados=1', $c == 200 && $j['creados'] == 1 && count($j['errores']) == 0, json_encode($j));
$otNueva = isset($j['asignaciones'][0]['id_asignado']) ? $j['asignaciones'][0]['id_asignado'] : '';
check('E5 asignaciones devuelve el id nuestro', strpos($otNueva, 'OT-') === 0, "asignado=$otNueva");
$idOtNueva = (int)ltrim(substr($otNueva, 3), '0');
check('E6 OT en base: estado Aceptada(9) y unidad correcta', q("SELECT IdEstadoOrden FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=$idOtNueva") == 9 && q("SELECT IdTallerUnidad FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=$idOtNueva") == 800002);
check('E7 OT en base: kilometros y fecha de ingreso', q("SELECT Kilometros FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=$idOtNueva") == 2500 && strpos(q("SELECT FechaInicio FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=$idOtNueva"), '2026-08-25 10:00') === 0);
check('E8 OT en base: comentarios con descripcion y falla en latin1', strpos(q("SELECT Comentarios FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=$idOtNueva"), utf8_decode('Vibración')) !== false);

list($c, $j) = http('POST', "$BASE/sync/taller/turnos", array('turnos' => array(array(
	'id_externo' => 'OT-700002',
	'estado' => 'finalizado',
	'moto' => array('vin' => 'LCELDGB19P1000456', 'kilometraje_ingreso' => 1250),
	'programacion' => array('fecha_entrega_real' => '2026-08-18T16:40:00-03:00'),
))), $TOKEN);
check('E9 update OT existente: actualizados=1', $c == 200 && $j['actualizados'] == 1, json_encode($j));
check('E10 OT 700002 finalizada en base con FechaFin', q("SELECT IdEstadoOrden FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=700002") == 12 && strpos(q("SELECT FechaFin FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=700002"), '2026-08-18 16:40') === 0);
check('E11 kilometraje actualizado', q("SELECT Kilometros FROM tb_ordenestrabajo WHERE IdOrdenTrabajo=700002") == 1250);

list($c, $j) = http('GET', "$BASE/sync/taller/turnos", null, $TOKEN);
$ids = array(); foreach ($j['turnos'] as $tt) $ids[] = $tt['id_externo'];
check('E12 la 700002 ahora aparece entre las finalizadas', in_array('OT-700002', $ids), implode(',', $ids));

$evento = array('evento' => array('id' => uniqid('evt_test_'), 'tipo' => 'taller.orden_actualizada', 'ocurrido_en' => date('c')),
	'turnos' => array(array('id_externo' => 'OT-700004', 'estado' => 'finalizado', 'moto' => array('vin' => 'LCELDGB19P1000456'))));
list($c, $j) = http('POST', "$BASE/webhook/taller", $evento, $TOKEN);
check('E13 webhook con evento nuevo: procesado', $c == 200 && $j['actualizados'] == 1, json_encode($j));
list($c, $j) = http('POST', "$BASE/webhook/taller", $evento, $TOKEN);
check('E14 mismo evento.id repetido: ignorado (idempotencia)', $c == 200 && $j['creados'] == 0 && $j['actualizados'] == 0 && strpos($j['mensaje'], 'ya procesado') !== false, json_encode($j));

echo "=== SUITE F: webhooks salientes y cola de reintentos ===\n";

set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/library');
require_once '/var/www/html/library/class.cfmoto.php';

/* mock local que registra lo recibido */
$ok = CFMoto::Enviar('http://127.0.0.1:9999/webhook/taller', array('prueba' => 'mock', 'valor' => 123), false);
$recibido = @file_get_contents('/tmp/mock-recibido.log');
check('F1 Enviar a mock local: 200 OK', $ok === true);
check('F2 el mock recibio el JSON', strpos($recibido, '"prueba":"mock"') !== false);

/* fallo -> encola pendiente */
$pendDir = '/var/www/html/_recursos/cfmoto/pendientes/';
$antes = count(glob($pendDir . '*.json'));
$ok = CFMoto::Enviar('http://127.0.0.1:9998/no-existe', array('prueba' => 'fallo'), true);
$despues = count(glob($pendDir . '*.json'));
check('F3 Enviar a URL caida devuelve false', $ok === false);
check('F4 el envio fallido queda encolado en pendientes/', $despues == $antes + 1, "antes=$antes despues=$despues");

/* reintento: apuntamos el pendiente al mock y reenviamos */
$archivos = glob($pendDir . '*.json');
$ultimo = $archivos[count($archivos) - 1];
$contenido = json_decode(file_get_contents($ultimo), true);
$contenido['url'] = 'http://127.0.0.1:9999/webhook/taller';
file_put_contents($ultimo, json_encode($contenido));
$enviados = CFMoto::ReenviarPendientes();
check('F5 ReenviarPendientes reenvia y limpia la cola', $enviados >= 1 && !file_exists($ultimo), "enviados=$enviados");

/* payload builders directos (mismo camino que usan los hooks de _admin_) */
$oClientes = new Clientes();
$p = CFMoto::PayloadCliente($oClientes->GetById(900001));
check('F6 PayloadCliente directo: estructura valida', $p['id_externo'] == 'CLI-900001' && is_array($p['motos']) && json_encode($p) !== false);
$oOrdenes = new OrdenesTrabajo();
$p = CFMoto::PayloadTurno($oOrdenes->GetById(700001));
check('F7 PayloadTurno directo: estructura valida', $p['id_externo'] == 'OT-700001' && json_encode($p) !== false);
check('F8 Notificar con id inexistente: false sin excepcion', CFMoto::NotificarCliente(99999999, 'contacto.creado') === false);
$logHoy = @file_get_contents('/var/www/html/_recursos/cfmoto/cfmoto-' . date('Ym') . '.log');
check('F9 el log de la integracion registra actividad', strpos($logHoy, 'OK POST') !== false && strpos($logHoy, 'FALLO POST') !== false);

/* unidad sin VIN: el payload la muestra con vin null (dato faltante -> null) */
$oTU = new TallerUnidades();
$p = CFMoto::PayloadMoto($oTU->GetById(800003));
check('F10 moto sin VIN en payload: vin null', $p['vin'] === null);

echo "=== SUITE G: log de trafico (recibidos y enviados) ===\n";

$logRecibidos = @file_get_contents('/var/www/html/_recursos/cfmoto/api-recibidos-' . date('Ymd') . '.log');
check('G1 log de recibidos: existe y registra los GET', strpos($logRecibidos, 'GET /api/sync/taller/turnos') !== false);
check('G2 log de recibidos: registra el body de los POST', strpos($logRecibidos, 'LCEPCJL4XP1009999') !== false);
check('G3 log de recibidos: registra la respuesta y el codigo', strpos($logRecibidos, '<< HTTP 200') !== false && strpos($logRecibidos, 'RESPONSE: {') !== false);
check('G4 log de recibidos: registra tambien los errores (401/404/422)', strpos($logRecibidos, '<< HTTP 401') !== false && strpos($logRecibidos, '<< HTTP 404') !== false);
check('G5 log de recibidos: password del login censurada', strpos($logRecibidos, '"password":"***"') !== false && strpos($logRecibidos, 'apitest123') === false);

$logEnviados = @file_get_contents('/var/www/html/_recursos/cfmoto/api-enviados-' . date('Ymd') . '.log');
check('G6 log de enviados: registra URL destino y body enviado', strpos($logEnviados, '>> POST http://127.0.0.1:9999/webhook/taller') !== false && strpos($logEnviados, 'ENVIADO: {"prueba":"mock"') !== false);
check('G7 log de enviados: registra la respuesta del otro lado', strpos($logEnviados, 'RESPUESTA: {"ok":true}') !== false);
check('G8 log de enviados: los fallos quedan con el error', strpos($logEnviados, '<< HTTP 0') !== false && strpos($logEnviados, 'ERROR:') !== false);

echo "\n=== RESULTADO ===\n";
foreach ($resultados as $r) echo $r . "\n";
echo "\nTOTAL: " . ($pass + $fail) . " pruebas | PASS: $pass | FAIL: $fail\n";
exit($fail > 0 ? 1 : 0);
