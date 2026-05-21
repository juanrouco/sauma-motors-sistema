<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Usado
{
	public $IdUsado;
	public $IdMarca;
	public $IdColor;
	public $Modelo;
	public $ModeloAnio;
	public $Kilometraje;
	public $Valuacion;
	public $Dominio;
	public $IdEstado;
	public $IdUbicacion;
	public $NumeroVinPrefijo;
	public $NumeroVin;
	public $NumeroMotor;
	public $NumeroChasis;
	public $Pisado;	
	public $Comentarios;
	public $IdMarcaMotor;
	public $IdMarcaChasis;
	public $IdTipoModelo;
	public $PrecioVenta;
	public $IdProveedor;
	public $IdCliente;
	public $IdMinuta;
	public $IdMinutaUsado;
	public $Arreglos;
	public $Observaciones;
	public $Info;
	public $EntregaTitulo;
	public $EntregaCedula;
	public $Entrega08;
	public $EntregaInformeDominio;
	public $Entrega13I;
	public $EntregaVerificacionBomberos;
	public $EntregaPatentes;
	public $EntregaManualLlaves;
	public $EntregaManual;
	public $EntregaClaveFiscal;
	public $IdMinutaEspera;
	public $PrecioVenta2;
	public $FechaRetiro;
	public $Consignacion;
	
	public function __construct()
	{
		$this->IdUsado 			= '';
		$this->IdMarca 			= '';
		$this->IdColor 			= '';
		$this->Modelo			= '';
		$this->ModeloAnio 		= '';
		$this->Kilometraje 		= '';
		$this->Valuacion 		= '';
		$this->Dominio	 		= '';
		$this->IdEstado	 		= '';
		$this->IdUbicacion 		= '';
		$this->NumeroVinPrefijo = '';
		$this->NumeroVin 		= '';
		$this->NumeroMotor 		= '';
		$this->NumeroChasis 	= '';
		$this->Pisado			= '';
		$this->Comentarios		= '';
		$this->IdMarcaMotor		= '';
		$this->IdMarcaChasis	= '';
		$this->IdTipoModelo		= '';
		$this->PrecioVenta		= '';
		$this->IdProveedor		= '';
		$this->IdCliente		= '';
		$this->IdMinuta			= '';
		$this->IdMinutaUsado	= '';
		$this->Arreglos			= '';
		$this->Observaciones	= '';
		$this->Info				= '';
		$this->EntregaTitulo	= '';
		$this->EntregaCedula	= '';
		$this->Entrega08	= '';
		$this->EntregaInformeDominio	= '';
		$this->Entrega13I	= '';
		$this->EntregaVerificacionBomberos	= '';
		$this->EntregaPatentes	= '';
		$this->EntregaManualLlaves	= '';
		$this->EntregaManual		= '';
		$this->EntregaClaveFiscal	= '';
		$this->IdMinutaEspera		= '';
		$this->PrecioVenta2			= '';
		$this->FechaRetiro			= '';
		$this->Consignacion			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUsado 			= $arr['IdUsado'];
		$this->IdMarca 			= $arr['IdMarca'];
		$this->IdColor 			= $arr['IdColor'];
		$this->Modelo			= $arr['Modelo'];
		$this->ModeloAnio 		= $arr['ModeloAnio'];
		$this->Kilometraje 		= $arr['Kilometraje'];
		$this->Valuacion 		= $arr['Valuacion'];
		$this->Dominio	 		= $arr['Dominio'];
		$this->IdEstado	 		= $arr['IdEstado'];
		$this->IdUbicacion 		= $arr['IdUbicacion'];
		$this->NumeroVinPrefijo = $arr['NumeroVinPrefijo'];
		$this->NumeroVin 		= $arr['NumeroVin'];
		$this->NumeroMotor 		= $arr['NumeroMotor'];
		$this->NumeroChasis 	= $arr['NumeroChasis'];
		$this->Pisado		 	= $arr['Pisado'];
		$this->Comentarios	 	= $arr['Comentarios'];
		$this->IdMarcaMotor	 	= $arr['IdMarcaMotor'];
		$this->IdMarcaChasis 	= $arr['IdMarcaChasis'];
		$this->IdTipoModelo	 	= $arr['IdTipoModelo'];
		$this->PrecioVenta	 	= $arr['PrecioVenta'];
		$this->IdProveedor	 	= $arr['IdProveedor'];
		$this->IdCliente	 	= $arr['IdCliente'];
		$this->IdMinuta		 	= $arr['IdMinuta'];
		$this->IdMinutaUsado 	= $arr['IdMinutaUsado'];
		$this->Arreglos	 		= $arr['Arreglos'];
		$this->Observaciones 	= $arr['Observaciones'];
		$this->Info		 		= $arr['Info'];
		$this->EntregaTitulo	= $arr['EntregaTitulo'];
		$this->EntregaCedula	= $arr['EntregaCedula'];
		$this->Entrega08		= $arr['Entrega08'];
		$this->EntregaInformeDominio	= $arr['EntregaInformeDominio'];
		$this->Entrega13I	= $arr['Entrega13I'];
		$this->EntregaVerificacionBomberos	= $arr['EntregaVerificacionBomberos'];
		$this->EntregaPatentes	= $arr['EntregaPatentes'];
		$this->EntregaManualLlaves	= $arr['EntregaManualLlaves'];
		$this->EntregaManual		= $arr['EntregaManual'];
		$this->EntregaClaveFiscal	= $arr['EntregaClaveFiscal'];
		$this->IdMinutaEspera	= $arr['IdMinutaEspera'];
		$this->PrecioVenta2		= $arr['PrecioVenta2'];
		$this->FechaRetiro		= $arr['FechaRetiro'];
		$this->Consignacion		= $arr['Consignacion'];
	}
}

?>