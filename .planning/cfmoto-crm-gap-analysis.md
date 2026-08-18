# Integración CRM CFMOTO — Análisis de brecha de datos

**Fecha:** 2026-08-14
**Spec de referencia:** `cfmoto-api-sincronizacion.md` (v1) — sincronización de Clientes + Motos y Turnos de Taller.
**Equivalencia de conceptos:** lo que la spec llama *turno* es nuestra **orden de trabajo** (`TB_OrdenesTrabajos`); además existe `TB_Turnos` (agenda del taller, referencia `IdOrdenTrabajo`).

Un dato estructural a favor: el sistema ya separa las tres entidades que pide la spec. La moto del taller vive en `TB_TallerUnidades` (con `IdCliente`, VIN, motor, dominio, concesionario de origen), por lo que podemos sincronizar motos de clientes aunque no las hayamos vendido nosotros.

Leyenda: ✅ tenemos · ⚠️ parcial/derivable · ❌ no tenemos

## 1. Clientes (`POST /api/v1/sync/clientes`)

| Campo CRM | Nuestro dato | Estado |
|---|---|---|
| `id_externo` | `IdCliente` | ✅ |
| `nombre` / `razon_social` | `RazonSocial` + `IdTipoPersona` (física/jurídica) | ✅ |
| `documento` | `DocumentoTipo`/`DocumentoNumero`; CUIT/CUIL en `ClaveFiscalTipo`/`ClaveFiscalNumero` | ✅ |
| `telefono` | `TelefonoCodigoArea` + `Telefono` | ⚠️ normalizar a E.164 |
| `telefono_alternativo` | `TB_ClientesContactos` (contactos con tel/email propios) | ✅ |
| `email` | `Email` | ✅ |
| `direccion` | `DomicilioCalle/Numero/Piso/Dpto/CodigoPostal` + `IdLocalidad` → localidad → provincia | ✅ (ciudad/provincia vía join) |
| `vendedor_asignado` | `IdVendedor` en el cliente | ✅ |
| `tipo` (lead/cliente/prospecto/inactivo) | No existe esa semántica; nuestros tipos son persona física/jurídica | ❌ (todos saldrían como `cliente`) |
| `origen` | Existe el catálogo `OrigenesCliente` (Salón, Facebook, Instagram, Referido, MercadoLibre…) pero no está como campo en `TB_Clientes`; se usa en prospectos/ventas | ⚠️ |
| `fecha_alta`, `actualizado_en` | No hay timestamps de alta ni de modificación en `TB_Clientes` | ❌ |
| `marcas_interes`, `etiquetas`, `acepta_marketing`, `notas`, `activo` | No existen | ❌ |

## 2. Motos

Dos fuentes: `TB_Unidades` (stock / ventas propias) y `TB_TallerUnidades` (motos que ingresan al taller, incluso externas).

| Campo CRM | Nuestro dato | Estado |
|---|---|---|
| `vin` (llave maestra) | `PrefijoVin`+`NumeroVin` (taller) / `NumeroVinPrefijo`+`NumeroVin` (unidades) | ✅ |
| `vin_motor` | `NumeroMotor` | ✅ |
| `marca`, `modelo`, `anio`, `color`, `patente` | `IdMarca`, `Modelo`/`IdModelo`, `ModeloAnio`, `IdColor`, `Dominio`/`Patente` | ✅ |
| `version` | No hay campo separado (a lo sumo dentro de `DenominacionComercial` del modelo) | ❌ |
| `kilometraje` + fecha | Derivable: `Kilometros` de la última OT + su fecha | ⚠️ |
| `propiedad` (fecha compra, nro factura) | `TB_FacturasUnidades` si la vendimos nosotros; `Concesionario` en taller para externas | ⚠️ solo ventas propias |
| `propiedad.tipo_operacion` | Derivable: `0km` si salió de nuestro stock, `desconocido` para las que solo pasan por taller | ⚠️ |
| `estado` (activa/vendida/baja/robada) | `EstadoUnidad` (Stock/Facturado/Entregado…) mapea a activa/vendida; no hay baja ni robada | ⚠️ |
| `garantia` | Solo `FechaInicioGarantia` en `TB_TallerUnidades`. No hay fin, meses, km límite, vigente ni extendida | ❌ crítico — calcular por regla de negocio (inicio + plazo estándar por modelo) |

## 3. Turnos / Órdenes de trabajo (`POST /api/v1/sync/taller/turnos`)

### Lo que tenemos

| Campo CRM | Nuestro dato | Estado |
|---|---|---|
| `id_externo` / `numero_orden` | `IdOrdenTrabajo` (y `IdTurno` para agenda) | ✅ |
| `cliente`, `moto` | Vía `IdTallerUnidad` (VIN, motor, marca, modelo, año, dominio, `IdCliente`) | ✅ |
| `moto.kilometraje_ingreso` | `OT.Kilometros` | ✅ |
| `programacion.fecha_turno` / `hora_turno` | `TB_Turnos.Fecha` / `FechaInicio` | ✅ |
| `programacion.fecha_ingreso` | `OT.FechaInicio` | ✅ |
| `programacion.fecha_entrega_real` | `OT.FechaFin` | ✅ |
| `programacion.fecha_entrega_estimada` | No hay campo propio | ⚠️ |
| `programacion.mecanico` | `IdUsuarioAsignado` → usuarios | ✅ |
| `servicio.descripcion` / `trabajos_realizados` | Tareas de la OT (`Titulo`, `Descripcion`, `IdCodigoTrabajo`) | ✅ |
| `garantia.aplica_a_este_ingreso` / `repuestos[].cubierto_por_garantia` | Derivable exacto: cada tarea tiene `IdTipoVenta`, existe `TipoVenta::Garantia` | ✅ |
| `repuestos[]` | `OrdenTrabajoTareaArticulo` (`IdArticulo`, `Cantidad`, `PrecioTotal`) + `Articulo` (`Codigo`, `Descripcion`) | ✅ |
| `importes` (mano de obra, repuestos, total) | `TotalMO`, `TotalRepuestos`, `ImporteTotal()`; impuestos vía factura postventa | ✅ |
| `importes.estado_pago` | Derivable (facturas postventa + notas de crédito; existe `ImporteFacturado()`), sin campo directo | ⚠️ |
| `notas_internas` | `OT.Comentarios` + `TB_OrdenesTrabajoComentarios` | ✅ |
| `adjuntos[]` | `TB_OrdenesTrabajoImagenes` — los archivos existen en `_recursos/ordentrabajo/imagenes/` pero no hay URLs públicas | ⚠️ |
| `creado_en` | `OT.Fecha` | ✅ |

### Lo que no tenemos

| Campo CRM | Detalle | Estado |
|---|---|---|
| `estado` | Nuestro `EstadoOrden` = {Presupuesto, Aceptada, Rechazado, Finalizado, Auditoría}. Mapean: pendiente≈Presupuesto, confirmado≈Aceptada (+`Turno.Reconfirmado`), cancelado≈Rechazado, finalizado ✅, `en_proceso` derivable (FechaInicio sin FechaFin). No existen `esperando_repuesto`, `entregado` (distinto de finalizado) ni `no_asistio` | ❌ |
| `servicio.tipo` (`service_5000km`…) y `servicio.razon_ingreso` | Sin campos categóricos; requiere tabla de mapeo desde códigos de trabajo (`preentrega`/`garantia` salen de `TipoVenta`) | ❌ |
| `servicio.falla_reportada` / `servicio.diagnostico` | Solo `Comentarios` genéricos, sin campos separados | ❌ |
| `garantia.numero_reclamo` | No existe | ❌ |
| `actualizado_en` | No hay timestamp de última modificación | ❌ |

## 4. Transversal — lo más importante para la integración

1. **No hay `actualizado_en` en ninguna tabla** — no existen timestamps de modificación ni mecanismo de eventos. Afecta directamente los webhooks bidireccionales (sección 4 de la spec): hoy no hay forma de saber "qué cambió" ni de disparar un POST al guardar. Habría que instrumentar los puntos de guardado (módulos AJAX / ABMs) o agregar columnas + triggers.
2. **Encoding**: la base es `latin1` y la spec exige UTF-8 — conversión en todo payload saliente y entrante.
3. **Punto a favor**: ya tenemos `src/api/` con router + JWT; los endpoints receptores que pide la sección 4.2 (POST contactos y taller) se montan ahí con poco esfuerzo.
4. **Idempotencia**: cubierta — `IdCliente`, `IdOrdenTrabajo` y VIN sirven como `id_externo` estables.

## Resumen

La operación de taller y los datos duros de cliente/moto están casi completos. Falta:

- **(a)** la capa "CRM" del cliente (origen, etiquetas, marketing, lead/prospecto),
- **(b)** la garantía más allá de la fecha de inicio,
- **(c)** los vocabularios categóricos de estado de OT y tipo/razón de servicio,
- **(d)** la infraestructura de eventos con timestamps para los webhooks — **el faltante más estructural**.
