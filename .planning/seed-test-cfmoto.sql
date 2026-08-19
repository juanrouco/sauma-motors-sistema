-- Datos de prueba integracion CFMOTO (ids altos para no chocar con datos reales)
SET NAMES latin1;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO tb_paises (IdPais, Nombre) VALUES (1, 'ARGENTINA')
ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre);

INSERT INTO tb_marcas (IdMarca, Codigo, Nombre, Imagen) VALUES
  (40, 'CFM', 'CF MOTO', ''),
  (41, 'BEN', 'BENELLI', '')
ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre);

INSERT INTO tb_provincias (IdProvincia, IdPais, Nombre) VALUES (2, 1, 'BUENOS AIRES')
ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre);

INSERT INTO tb_localidades (IdLocalidad, IdPartido, IdProvincia, IdPais, Nombre, CodigoPostal) VALUES
  (5000, 0, 2, 1, 'LA PLATA', '1900')
ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre);

INSERT INTO tb_usuarios (IdUsuario, IdSector, IdPerfil, Nombre, Apellido, Email, Login, Password, Deleted) VALUES
  (9001, 1, 1, 'Test', 'API', 'api@cfmoto.test', 'apitest', MD5('apitest123'), 0)
ON DUPLICATE KEY UPDATE Login = VALUES(Login);

INSERT INTO tb_clientes (IdCliente, IdTipoPersona, RazonSocial, DomicilioCalle, DomicilioNumero,
  DomicilioPiso, DomicilioDpto, DomicilioIdLocalidad, DomicilioCodigoPostal,
  TelefonoCodigoArea, Telefono, DocumentoTipo, DocumentoNumero,
  ClaveFiscalTipo, ClaveFiscalNumero, Email, IdVendedor, IdTipoIva) VALUES
  (900001, 1, 'PEREZ JUAN CFMOTO TEST', 'AV. CORRIENTES', '1234', '5', 'B', 5000, 'C1043',
   '11', '38611119', 1, '34567890', 1, '20345678903', 'juan.perez@cfmototest.com', 9001, 3)
ON DUPLICATE KEY UPDATE RazonSocial = VALUES(RazonSocial);

-- 800001: unidad completa del cliente de prueba (VIN con prefijo, garantia vigente)
-- 800002: unidad de cliente real, VIN completo en NumeroVin, con IdUnidad (0km), sin garantia
-- 800003: unidad SIN VIN (para probar validaciones)
INSERT INTO tb_tallerunidades (IdTallerUnidad, IdMarca, IdColor, Modelo, ModeloAnio, IdCliente,
  Dominio, PrefijoVin, NumeroVin, NumeroMotor, FechaInicioGarantia, Concesionario, IdUnidad) VALUES
  (800001, 40, 1, 'CF 450 SR', 2024, 900001, 'A123BCD', 'LCE', 'LDGB19P1000123', '191QA0123456', '2026-02-15', 'SAUMA', NULL),
  (800002, 40, 2, 'CF 800 MT', 2025, 153080, 'AD456EF', '', 'LCELDGB19P1000456', '283MW0654321', NULL, 'CFMOTO BS AS', 123),
  (800003, 41, 1, 'TRK 502',   2023, 153081, 'AC789GH', '', '', 'BJ500654321', NULL, 'SAUMA', NULL)
ON DUPLICATE KEY UPDATE Modelo = VALUES(Modelo);

-- OTs: 700001 finalizada con todo; 700002 en proceso; 700003 presupuesto; 700004 finalizada simple
INSERT INTO tb_ordenestrabajo (IdOrdenTrabajo, IdEstadoOrden, IdTallerUnidad, Fecha, FechaInicio, FechaFin,
  IdUsuarioCreacion, IdUsuarioAsignado, Kilometros, Comentarios, IdTipoVenta, IdCliente) VALUES
  (700001, 12, 800001, '2026-08-10', '2026-08-10 09:30:00', '2026-08-12 16:40:00', 9001, 9001, 5240, 'SERVICE DE 5000 KM SEGUN MANUAL', 2, 900001),
  (700002, 9,  800002, '2026-08-15', '2026-08-15 10:00:00', NULL,                  9001, 9001, 1200, 'RUIDO EN MOTOR A ALTAS RPM', 2, 153080),
  (700003, 10, 800001, '2026-08-17', '2026-08-20 09:00:00', '2026-08-20 17:00:00', 9001, 9001, 5600, 'PRESUPUESTO CAMBIO DE CUBIERTAS', 2, 900001),
  (700004, 12, 800002, '2026-07-01', '2026-07-01 08:30:00', '2026-07-01 18:15:00', 9001, 9001, 500,  'SERVICE 500 KM', 2, 153080)
ON DUPLICATE KEY UPDATE IdEstadoOrden = VALUES(IdEstadoOrden);

INSERT INTO tb_turnos (IdTurno, IdEstadoOrden, IdTallerUnidad, Fecha, FechaInicio, IdUsuarioCreacion,
  IdUsuarioAsignado, Kilometros, IdOrdenTrabajo, IdCliente) VALUES
  (600001, 9, 800001, '2026-08-10', '2026-08-10 10:00:00', 9001, 9001, 5240, 700001, 900001)
ON DUPLICATE KEY UPDATE Fecha = VALUES(Fecha);

-- Tareas de la OT finalizada: una comun con repuestos, una de garantia
INSERT INTO tb_ordenestrabajotareas (IdOrdenTrabajoTarea, IdOrdenTrabajo, Importe, Titulo, Descripcion,
  IdTipoVenta, IdEstado, Tarea, TotalMO, TotalRepuestos) VALUES
  (30001, 700001, 45000.00, 'CAMBIO DE ACEITE Y FILTRO', 'CAMBIO DE ACEITE 10W40 Y FILTRO ORIGINAL', 2, 2, 'REALIZADO OK', 26527.27, 18472.73),
  (30002, 700001, 15000.00, 'AJUSTE CADENA DE DISTRIBUCION', 'CADENA CON JUEGO FUERA DE TOLERANCIA', 3, 2, 'REALIZADO EN GARANTIA', 15000.00, 0.00)
ON DUPLICATE KEY UPDATE Titulo = VALUES(Titulo);

INSERT INTO tb_ordenestrabajotareasarticulos (IdOrdenTrabajoTarea, IdArticulo, PrecioTotal, Cantidad) VALUES
  (30001, 5844, 5114.05, 1),
  (30001, 5845, 8358.68, 2)
ON DUPLICATE KEY UPDATE PrecioTotal = VALUES(PrecioTotal);

INSERT INTO tb_ordentrabajocomentarios (IdOrdenTrabajoComentario, IdOrdenTrabajo, Comentarios, IdUsuario, Fecha) VALUES
  (40001, 700001, 'REVISAR CADENA DE DISTRIBUCION EN PROXIMO SERVICE', 9001, '2026-08-12 16:00:00'),
  (40002, 700001, 'CLIENTE AVISADO POR TELEFONO', 9001, '2026-08-12 16:30:00')
ON DUPLICATE KEY UPDATE Comentarios = VALUES(Comentarios);

INSERT INTO tblordentrabajoimagenes (IdImagen, IdOrdenTrabajo, Imagen, Epigrafe, Orden) VALUES
  (50001, 700001, 'ot700001-ingreso.jpg', 'ESTADO DE LLEGADA', 1)
ON DUPLICATE KEY UPDATE Imagen = VALUES(Imagen);
