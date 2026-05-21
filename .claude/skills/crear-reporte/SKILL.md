---
name: crear-reporte
description: Crear paginas de reporte para el DMS legacy (PHP 5.6 + MySQL 5.7). Genera el archivo de reporte con filtros, tabla de resultados, totales, y opcionalmente exportacion a XLS.
---

# Crear reporte para el sistema de concesionaria

## Antes de empezar

Pregunta al usuario:

1. **Que entidad/datos muestra el reporte** (ej: movimientos de stock, ordenes de trabajo, facturacion)
2. **Que filtros necesita** (ej: fecha desde/hasta, ubicacion, modelo, proveedor)
3. **Que columnas/totales muestra** (ej: costos por proveedor, cantidades, rentabilidad)
4. **Si necesita exportacion a XLS/CSV**
5. **Que permiso de `src/inc_perms.php` aplica** (ej: `PERM_ARTI_LIST`, `PERM_TALL_REPORTES`)

## Arquitectura

Cada reporte se compone de hasta 3 archivos:

| Archivo | Template | Funcion |
|---------|----------|---------|
| `src/_admin_/{entidad}_reporte.php` | `templates/reporte.md` | Pagina principal con filtros y tabla |
| `src/_admin_/{entidad}_reporte_exportar.php` | `templates/exportar.md` | Exportacion CSV/XLS (opcional) |
| `src/library/class.{entidades}.php` | `templates/metodo-consulta.md` | Metodo(s) de consulta SQL |

## Proceso

1. Leer el template `templates/reporte.md` y generar el archivo del reporte reemplazando los placeholders segun lo que pidio el usuario.
2. Leer el template `templates/metodo-consulta.md` y agregar los metodos necesarios en la clase de acceso a datos existente (o crear la clase si no existe, siguiendo las convenciones de `CLAUDE.md`).
3. Si el usuario pidio exportacion, leer `templates/exportar.md` y generar el archivo exportador.
4. Verificar sintaxis PHP con: `docker exec sauma_web php -l /var/www/html/_admin_/{archivo}.php`

## Reglas obligatorias

### Seguridad
- **SIEMPRE** usar `isset()` al leer `$_REQUEST`. Sin esto, la primera carga genera PHP Notices.
- **SIEMPRE** usar `intval()` para campos numericos (IDs, paginas).
- **SIEMPRE** escapar valores en HTML con `htmlspecialchars()` en atributos `value=""` y en URLs.
- Usar `DB::Number()`, `DB::Date()`, `DB::String()` en queries para sanitizar valores.

### Performance
- Instanciar SOLO las clases que se usan. Cada `new Clase()` que extiende `DBAccess` puede inicializar una conexion.
- Para excluir registros, preferir `LEFT JOIN ... WHERE campo IS NULL` sobre `NOT IN (SELECT ...)`. El LEFT JOIN permite a MySQL usar indices (~20% mejora medida en benchmarks).
- Proteger TODA division con check de denominador != 0.

### JavaScript
- Usar `$j` para jQuery (NO `$`, que es Prototype.js).
- Los date pickers usan `tcal`: `new tcal({'formname': 'frmData', 'controlname': 'NombreCampo'})`.
- Para modales usar `$j('#modal-popup').dialog({...})`.

### Formato
- Montos en formato argentino: `$<?= number_format($monto, 2, ',', '.') ?>` (miles con punto, decimal con coma).
- Clases CSS del sistema: `bordeGris`, `bordeGrisFondo`, `bordeGrisTitulo`, `TituloRubro`, `tituloPagina`, `tituloMenu`, `camporFormularioSuggest`, `botonBasico`.
- Separadores entre filas: `background="images/linea_punteada.gif"`.
- Hover en filas: `onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''"`.
- NO olvidar `</head>` antes de `<body>`.

## Reportes existentes como referencia

| Reporte | Archivo | Patron |
|---------|---------|--------|
| Stock movimientos totales | `stockmovimientos_totales_reporte.php` | Totales agrupados por tipo, loop de filas, rentabilidad |
| Ordenes de trabajo | `ordenestrabajo_reporte.php` | Totales por tipo + detalle expandible en modal |
| Articulos | `articulos_reporte.php` | Listado paginado con filtros multiples |
| Contabilidad | `contabilidad_reporte.php` | Paginado con `ReceiveArray()` para filtros complejos |
| Facturacion | `reportesfacturacion.php` | Reporte con sub-detalle y PDF |
