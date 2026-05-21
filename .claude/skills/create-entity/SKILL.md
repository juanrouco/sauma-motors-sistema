---
name: create-entity
description: Crea una entidad completa del DMS (clase entidad, clase de acceso a datos y tabla MySQL) siguiendo las convenciones del proyecto legacy. Usar cuando se necesite una nueva entidad, modelo o tabla.
argument-hint: [NombreEntidad]
disable-model-invocation: true
allowed-tools: Read, Write, Edit, Bash, Grep, Glob
---

# Crear Entidad: $ARGUMENTS

Generar los 3 componentes de la entidad siguiendo estrictamente las convenciones del proyecto.

## 1. Identificar nombres

A partir del nombre recibido en `$ARGUMENTS`, derivar:

- **Singular**: PascalCase (ej: `NuevaEntidad`)
- **Plural**: PascalCase (ej: `NuevaEntidades`)
- **Archivo entidad**: `class.{singular_minusculas}.php`
- **Archivo datos**: `class.{plural_minusculas}.php`
- **Tabla**: `TB_{Plural}`
- **Primary Key**: `Id{Singular}`

## 2. Crear tabla MySQL

Generar el script SQL y agregarlo a `src/sql/cambios-basedatos.sql`:

```sql
CREATE TABLE TB_{Plural} (
    Id{Singular} INT AUTO_INCREMENT PRIMARY KEY,
    -- campos según requerimientos
    -- Usar VARCHAR para textos, con largos razonables
    -- Usar FK con CONSTRAINT para relaciones
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

**Reglas SQL:**
- Engine siempre `InnoDB`
- Charset siempre `latin1`
- Primary key: `Id{Singular}` con `AUTO_INCREMENT`
- Foreign keys: nombrar como `FK_{Tabla}_{Campo}`
- Ejecutar: `docker exec -i sauma_db mysql -uroot benelli_com_ar < src/sql/cambios-basedatos.sql`

## 3. Crear clase entidad

Archivo: `src/library/class.{singular_minusculas}.php`

- Una propiedad pública por cada columna de la tabla
- Constructor que inicializa todos los valores por defecto (0 para IDs, '' para strings, null para opcionales)
- Sin métodos adicionales, solo atributos

Ver [examples/acreedor-example.md](examples/acreedor-example.md) para el ejemplo completo de referencia.

## 4. Crear clase de acceso a datos

Archivo: `src/library/class.{plural_minusculas}.php`

- Extender `DBAccess`
- `require_once` del archivo de entidad y `class.dbaccess.php`
- Métodos obligatorios: `GetAll($filtro)`, `GetById($id)`, `Insert($entity)`, `Update($entity)`, `Delete($id)`
- Queries directas con nombres de tabla y campos (no hay ORM)
- Usar `$this->GetQuery()` para SELECT, `$this->Insert()`, `$this->Update()`, `$this->Delete()` heredados de DBAccess

Ver [examples/acreedor-example.md](examples/acreedor-example.md) para el ejemplo completo de referencia.

## 5. Verificaciones finales

- [ ] El archivo de entidad está en `src/library/` con nombre correcto
- [ ] El archivo de acceso a datos está en `src/library/` con nombre correcto
- [ ] La clase de datos extiende `DBAccess`
- [ ] Los `require_once` apuntan a los archivos correctos
- [ ] La tabla usa `ENGINE=InnoDB DEFAULT CHARSET=latin1`
- [ ] La primary key sigue la convención `Id{Singular}`
- [ ] El script SQL se agregó a `src/sql/cambios-basedatos.sql`

## 6. Siguiente paso

Preguntar al usuario si desea crear las páginas ABM para esta entidad (puede invocar `/create-abm-pages {Plural}`).
