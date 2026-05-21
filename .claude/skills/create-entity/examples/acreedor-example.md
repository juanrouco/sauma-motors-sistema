# Ejemplo completo: Entidad Acreedor

Este archivo sirve como referencia del patrón a seguir para toda nueva entidad.

## Clase Entidad (`src/library/class.acreedor.php`)

```php
<?php
class Acreedor
{
    public $IdAcreedor;
    public $IdTipoPersona;
    public $IdNacionalidad;
    public $NumeroInscripcion;
    public $RazonSocial;
    public $DomicilioCalle;
    public $DomicilioNumero;
    public $DomicilioPiso;
    public $DomicilioDpto;
    public $DomicilioIdLocalidad;
    public $TelefonoCodigoArea;
    public $Telefono;
    public $DocumentoTipo;
    public $DocumentoNumero;
    public $Email;

    public function __construct()
    {
        $this->IdAcreedor = 0;
        $this->IdTipoPersona = 0;
        $this->IdNacionalidad = 0;
        $this->NumeroInscripcion = '';
        $this->RazonSocial = '';
        $this->DomicilioCalle = '';
        $this->DomicilioNumero = '';
        $this->DomicilioPiso = '';
        $this->DomicilioDpto = '';
        $this->DomicilioIdLocalidad = 0;
        $this->TelefonoCodigoArea = '';
        $this->Telefono = '';
        $this->DocumentoTipo = 0;
        $this->DocumentoNumero = '';
        $this->Email = '';
    }
}
?>
```

## Clase Acceso a Datos (`src/library/class.acreedores.php`)

```php
<?php
require_once("class.acreedor.php");
require_once("class.dbaccess.php");

class Acreedores extends DBAccess
{
    public function GetAll($filtro = null)
    {
        $sql = "SELECT * FROM TB_Acreedores WHERE 1";
        if ($filtro) {
            $sql .= $filtro->GetSQL();
        }
        return $this->GetQuery($sql);
    }

    public function GetById($id)
    {
        $sql = "SELECT * FROM TB_Acreedores WHERE IdAcreedor = $id";
        return $this->GetQuery($sql);
    }

    public function Insert($entity)
    {
        return $this->Insert('TB_Acreedores', [
            'IdTipoPersona' => $entity->IdTipoPersona,
            'IdNacionalidad' => $entity->IdNacionalidad,
            'NumeroInscripcion' => $entity->NumeroInscripcion,
            'RazonSocial' => $entity->RazonSocial,
            'DomicilioCalle' => $entity->DomicilioCalle,
            'DomicilioNumero' => $entity->DomicilioNumero,
            'DomicilioPiso' => $entity->DomicilioPiso,
            'DomicilioDpto' => $entity->DomicilioDpto,
            'DomicilioIdLocalidad' => $entity->DomicilioIdLocalidad,
            'TelefonoCodigoArea' => $entity->TelefonoCodigoArea,
            'Telefono' => $entity->Telefono,
            'DocumentoTipo' => $entity->DocumentoTipo,
            'DocumentoNumero' => $entity->DocumentoNumero,
            'Email' => $entity->Email,
        ]);
    }

    public function Update($entity)
    {
        return $this->Update('TB_Acreedores', [
            'IdTipoPersona' => $entity->IdTipoPersona,
            'IdNacionalidad' => $entity->IdNacionalidad,
            'NumeroInscripcion' => $entity->NumeroInscripcion,
            'RazonSocial' => $entity->RazonSocial,
            'DomicilioCalle' => $entity->DomicilioCalle,
            'DomicilioNumero' => $entity->DomicilioNumero,
            'DomicilioPiso' => $entity->DomicilioPiso,
            'DomicilioDpto' => $entity->DomicilioDpto,
            'DomicilioIdLocalidad' => $entity->DomicilioIdLocalidad,
            'TelefonoCodigoArea' => $entity->TelefonoCodigoArea,
            'Telefono' => $entity->Telefono,
            'DocumentoTipo' => $entity->DocumentoTipo,
            'DocumentoNumero' => $entity->DocumentoNumero,
            'Email' => $entity->Email,
        ], "IdAcreedor = {$entity->IdAcreedor}");
    }

    public function Delete($id)
    {
        return $this->Delete('TB_Acreedores', "IdAcreedor = $id");
    }
}
?>
```

## Tabla MySQL (`TB_Acreedores`)

```sql
CREATE TABLE `tb_acreedores` (
    `IdAcreedor` smallint(6) NOT NULL AUTO_INCREMENT,
    `IdTipoPersona` tinyint(4) NOT NULL,
    `IdNacionalidad` smallint(6) DEFAULT NULL,
    `NumeroInscripcion` varchar(64) DEFAULT NULL,
    `RazonSocial` varchar(128) NOT NULL,
    `DomicilioCalle` varchar(128) DEFAULT NULL,
    `DomicilioNumero` varchar(8) DEFAULT NULL,
    `DomicilioPiso` char(2) DEFAULT NULL,
    `DomicilioDpto` char(2) DEFAULT NULL,
    `DomicilioIdLocalidad` smallint(6) DEFAULT NULL,
    `TelefonoCodigoArea` varchar(12) DEFAULT NULL,
    `Telefono` varchar(16) DEFAULT NULL,
    `DocumentoTipo` tinyint(4) DEFAULT NULL,
    `DocumentoNumero` varchar(16) DEFAULT NULL,
    `Email` varchar(128) DEFAULT NULL,
    PRIMARY KEY (`IdAcreedor`),
    KEY `FK_Acreedores_IdNacionalidad` (`IdNacionalidad`),
    CONSTRAINT `FK_Acreedores_IdNacionalidad`
        FOREIGN KEY (`IdNacionalidad`) REFERENCES `tb_paises` (`IdPais`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```

## Notas del patrón

- La clase entidad es un DTO puro: solo propiedades públicas y constructor con defaults
- La clase de datos extiende `DBAccess` y usa sus métodos heredados
- `GetAll` siempre acepta un `$filtro` opcional que usa `$filtro->GetSQL()`
- Los métodos de escritura reciben el objeto entidad completo
- No hay prepared statements: los valores se pasan directamente en el array
