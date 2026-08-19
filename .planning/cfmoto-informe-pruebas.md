# Integración CFMOTO — Informe de pruebas

**Fecha:** 2026-08-18 (actualizado 2026-08-19 con log de tráfico)
**Resultado global:** ✅ **99 de 99 pruebas automatizadas en verde** (suite re-ejecutada dos veces para verificar idempotencia).

## Log de tráfico (agregado 2026-08-19)

Todo el tráfico de la integración queda auditado en archivos diarios dentro de `src/_recursos/cfmoto/`:

- **`api-recibidos-AAAAMMDD.log`** — cada request que entra a la API: fecha/hora, IP, método, URL, body recibido, código HTTP devuelto, respuesta completa y duración en ms. Incluye errores (401/404/422/500 y fatales). La contraseña del login queda censurada (`"password":"***"`).
- **`api-enviados-AAAAMMDD.log`** — cada webhook que nosotros disparamos: URL destino, JSON enviado, código HTTP de ellos, su respuesta textual y duración. Los fallos quedan con el error de conexión.

Cuerpos truncados a 500 KB por entrada para que un GET masivo no infle el archivo. Suite G (8 pruebas) valida ambos logs.

## Entorno

- Contenedores locales `sauma_web` (PHP 5.6 + Apache) y `sauma_db` (MySQL 5.7).
- Base `benelli_com_ar` con **estructura completa** (198 tablas, extraída de la base de Aspen) + **datos reales parciales** del dump `bu-data.sql` (8.465 clientes, 4.523 artículos, comprobantes) + **datos de prueba sembrados** para las tablas que el dump no trajo (ver "Limitaciones").
- Suite: `tests-cfmoto.php` (91 asserts), ejecutada dentro del contenedor contra la API real por HTTP, con verificación directa en MySQL de cada efecto.
- Los webhooks salientes se probaron contra un **servidor mock local** — no se envió ningún dato de prueba al servidor real de CFMOTO.

## Cobertura (6 suites)

### A — Autenticación (5/5)
Login OK y fallido, requests sin token, token inválido, listado de endpoints.

### B — GET órdenes de trabajo (31/31)
- OT puntual por `?id=` y por `?id_externo=OT-...`; inexistente → 404.
- Sin filtro → devuelve exactamente las finalizadas.
- Payload validado campo por campo contra la spec: id_externo, estado (finalizado / en_proceso derivado / pendiente), cliente con teléfono E.164, VIN completo (prefijo+número), marca/modelo/año/patente, kilometraje de ingreso, garantía (inicio + 24 meses, vigente, aplica si hay tarea de garantía), servicio (tipo/razón derivados, trabajos realizados), repuestos (con precio unitario calculado y marca de garantía), programación (fecha/hora del turno de agenda, ingreso ISO 8601 con timezone, entrega real solo si finalizada), mecánico, notas internas (comentarios OT + tabla), adjuntos (imágenes con URL), importes (moneda, estado de pago derivado de lo facturado).
- Casos borde: OT sin turno de agenda, sin tareas, unidad sin fecha de garantía.

### C — GET clientes (17/17)
- Por `?orden_id=` (número u `OT-...`), por `?id=` (`CLI-...`); sin parámetros → 422; orden inexistente → 404.
- Payload validado: documento (CUIT prioritario sobre DNI), dirección completa con ciudad/provincia vía joins de localidad, vendedor, `fecha_alta` = hoy (regla acordada), motos del cliente con `version` = modelo (regla acordada), kilometraje de la última OT, `tipo_operacion` (0km si salió de nuestro stock / desconocido), marcas de interés derivadas.

### D — POST clientes (14/14)
- Alta de cliente nuevo con moto: verificado en base (nombre con acentos/ñ en latin1, calle y altura separadas, teléfono normalizado, taller unidad creada por VIN con marca resuelta por nombre y garantía).
- Idempotencia: reenvío idéntico actualiza sin duplicar (1 cliente, 1 moto).
- Update por id_externo (cambio de email verificado en base).
- Errores: cliente nuevo sin nombre (`NOMBRE_OBLIGATORIO`), moto sin VIN (`VIN_OBLIGATORIO`), body inválido → 400.
- Roundtrip de encoding: "Ángel Rodríguez Peña" entra por JSON UTF-8, se guarda en latin1 y vuelve intacto en UTF-8.

### E — POST turnos / webhook taller (14/14)
- VIN obligatorio (`VIN_OBLIGATORIO`) y VIN inexistente (`VIN_NO_ENCONTRADO`, con el VIN en el mensaje) — igual que el ejemplo de la spec.
- Alta de OT desde el CRM: estado mapeado (confirmado→Aceptada), fecha de turno+hora → FechaInicio, kilometraje, comentarios con descripción y falla en latin1, respuesta con `asignaciones` (el `OT-XXXXXX` nuestro para que el CRM re-mapee).
- Update de OT existente: finalizado → estado 12 + FechaFin de `fecha_entrega_real`; la OT aparece luego entre las finalizadas del GET.
- Webhook `/webhook/taller` con bloque `evento`: procesa la primera vez, ignora el mismo `evento.id` repetido.

### F — Webhooks salientes y cola (10/10)
- `CFMoto::Enviar` a mock local: 200 y el mock recibió el JSON exacto.
- URL caída: devuelve false y **encola** el envío en `_recursos/cfmoto/pendientes/`.
- `ReenviarPendientes()` (y el script `cfmoto_reintentos.php`): reenvía y limpia la cola.
- Builders de payload por CLI (mismo camino que usan los hooks de `_admin_`), ids inexistentes sin excepción, log de actividad, moto sin VIN → `vin: null`.

### Extras
- `php -l` (PHP 5.6 real) en los 20 archivos nuevos/modificados: limpio.
- Las 11 páginas de `_admin_` modificadas responden 302 al login (ninguna rompe con 500).

## Bugs encontrados y corregidos DURANTE las pruebas

1. **`.htaccess` de la API mal nombrado** (`.htaccess.htaccess`): las rutas daban 404 de Apache. Se creó `src/api/.htaccess`.
2. **`ClienteContactos::GetAllByCliente()` no existía** pero `Cliente::GetAllContactos()` lo invoca → fatal error (bug legacy preexistente; volteaba el GET de clientes). Se implementó el método en `class.clientecontactos.php`.
3. **`OrdenTrabajoImagenes::GetAllByIdOrdenTrabajo()` joinea `tblOrdenesTrabajo`, tabla inexistente** → siempre falla (bug legacy). La integración pasó a usar `GetAllByOrdenTrabajo()`, que sí funciona.
4. **Kilometraje de la moto**: `GetLastByIdTallerUnidad()` legacy solo considera OTs *Aceptadas*. Se agregó `OrdenesTrabajo::GetUltimaByIdTallerUnidad()` (cualquier estado).
5. **Altas por API fallaban por columnas NOT NULL** (`IdVendedor`, `IdTipoIva`, `Empresa`, `Email` en clientes; `NumeroMotor`, `FechaInicioGarantia` en unidades): ahora se completan con defaults (vendedor = usuario del token, IVA consumidor final).
6. **Entorno dev**: a `sauma_db` le faltaban `lower_case_table_names=1` (sin eso el código no encuentra las tablas `TB_`) y charset de servidor `latin1` (con utf8 los acentos entraban como `?`). Corregido en `docker-compose.yml`; también se deshabilitó `ONLY_FULL_GROUP_BY`.

Ya conocidos de antes (esquivados, no tocados): `OrdenTrabajo::ImporteRepuestos()` roto (se usa `ImporteRepuestosCalculado()`).

## Limitaciones de esta corrida

- El dump `bu-data.sql` vino **cortado** (43 tablas de ~180, hasta la "L"): órdenes de trabajo, taller unidades, turnos, usuarios, marcas y tipos de IVA se probaron con **datos sembrados realistas**, no reales. Cuando llegue el dump completo (exportar todo **menos `tb_logsfacturaelectronica`**), conviene re-correr la suite: `docker cp tests-cfmoto.php sauma_web:/tmp/ && docker exec sauma_web php /tmp/tests-cfmoto.php`.
- Los webhooks salientes no se dispararon contra el servidor real de CFMOTO (a propósito). El primer disparo real se puede verificar en `src/_recursos/cfmoto/cfmoto-AAAAMM.log`.
- Los errores SQL del layer legacy salen como texto plano (no JSON) en la respuesta — comportamiento del `class.db.php` histórico.

## Pendientes para producción

1. Darle a CFMOTO un usuario de API (los receptores exigen el JWT de `/api/login`).
2. Configurar el cron de reintentos: `*/5 * * * * php /ruta/al/sitio/cfmoto_reintentos.php`.
3. Actualizar `Config::UrlSitio` cuando haya URL pública (hoy las URLs de adjuntos salen con IP de LAN).
4. Subir también `src/api/.htaccess` (además de los archivos nuevos) — sin él la API da 404.
