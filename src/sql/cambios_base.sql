/*************
* 09/10/2026 *
* Borrado logico de usuarios (portado de aspen-sistema): Delete marca
* Deleted = 1 y el usuario deja de existir para listados, login,
* permisos y suggests, pero conserva sus registros relacionados.
*************/
ALTER TABLE TB_Usuarios
    ADD COLUMN Deleted TINYINT(1) NULL DEFAULT 0;

/*************
* 09/10/2026 *
* Baja logica de los usuarios marcados en el listado. Cada UPDATE exige
* nro. + apellido: si alguno no coincide, afecta 0 filas y no toca a
* ningun otro usuario (verificar la cuenta de filas afectadas = 25).
*************/
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 12 AND Apellido LIKE 'ABOGNAIN%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 49 AND Apellido LIKE 'BARRI%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 47 AND Apellido LIKE 'BERMOLEN%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 30 AND Apellido LIKE 'BERRO GARCIA%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 23 AND Apellido LIKE 'BONALDO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 40 AND Apellido LIKE 'DUFFEK%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 28 AND Apellido LIKE 'FERNANDEZ%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 20 AND Apellido LIKE 'FLORES%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 27 AND Apellido LIKE 'FRAILE%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 33 AND Apellido LIKE 'GARZILAZO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 55 AND Apellido LIKE 'GOMEZ%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 10 AND Apellido LIKE 'GROSSO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 17 AND Apellido LIKE 'IOVIERO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 42 AND Apellido LIKE 'KRAFCHUK%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 34 AND Apellido LIKE 'MAXIAS%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 44 AND Apellido LIKE 'MIGENZ%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 36 AND Apellido LIKE 'MONTERO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 22 AND Apellido LIKE 'NOSTRO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 64 AND Apellido LIKE 'ORTIZ%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 3 AND Apellido LIKE 'PESCE%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 11 AND Apellido LIKE 'RIVERO%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 31 AND Apellido LIKE 'VIEGELMANN%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 13 AND Apellido LIKE 'VILLALBA%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 29 AND Apellido LIKE 'VILLALBA%';
UPDATE TB_Usuarios SET Deleted = 1 WHERE IdUsuario = 51 AND Apellido LIKE 'ZAPATERRA%';
