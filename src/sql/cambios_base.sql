/*************
* 09/10/2026 *
* Borrado logico de usuarios (portado de aspen-sistema): Delete marca
* Deleted = 1 y el usuario deja de existir para listados, login,
* permisos y suggests, pero conserva sus registros relacionados.
*************/
ALTER TABLE TB_Usuarios
    ADD COLUMN Deleted TINYINT(1) NULL DEFAULT 0;
