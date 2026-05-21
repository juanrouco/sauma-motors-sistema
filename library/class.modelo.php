<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.origen.php');
require_once('class.tiposmodelo.php');

class Modelo
{
	const PathCsvBaseBack	= '../_recursos/modelos/modelos_base.xls';
	const PathCsvImportBack	= '../_recursos/modelos/';
	
	public $IdModelo;
	public $IdTipoCombustible;
	public $IdTipoModelo;
	public $IdCategoriaModelo;
	public $IdMarcaVehiculo;
	public $IdMarcaMotor;
	public $IdMarcaChasis;
	public $IdTipoVehiculo;
	public $IdTipoCarroceria;
	public $IdTipoUso;
	public $IdDestinoVehiculo;
	public $NumeroVinPrefijo;
	public $CodigoComercial;
	public $DenominacionModelo;
	public $DenominacionComercial;
	public $Origen;
	public $Anio;
	public $Peso;
	public $PrecioPublicoNeto;
	public $PrecioPublicoTotalIva;	
	public $Precio1;
	public $PrecioCompra;
	public $FleteFormularios;	
	public $MesPrecioTotal;
	public $Patentamiento;
	public $Ganancia1;
	public $VentaPrecio;
	public $VentaGastosFlete;
	public $VentaGastosPatentamiento;
	public $Ganancia2;	
	public $Iva;
	public $Prenda;
	public $Otorgamiento;
	public $ImpuestoInterno;
	public $Precio2;
	public $Flete;
	public $BonificacionExtra;
	public $DescuentoReventa;
	public $Cilindrada;
	public $ReventaPrecio;
	public $Electrolito;
	public $GTIN;
	public $Cufe;
	public $Obsoleto;
	
	public function __construct()
	{
		$this->IdModelo 					= '';
		$this->IdTipoCombustible			= '';
		$this->IdTipoModelo					= '';
		$this->IdTipoCategoriaModelo		= '';
		$this->IdMarcaVehiculo 				= '';
		$this->IdMarcaMotor 				= '';
		$this->IdMarcaChasis 				= '';
		$this->IdTipoVehiculo				= '';
		$this->IdTipoCarroceria				= '';
		$this->IdTipoUso					= '';
		$this->IdDestinoVehiculo			= '';
		$this->NumeroVinPrefijo 			= '';
		$this->CodigoComercial 				= '';
		$this->DenominacionModelo 			= '';
		$this->DenominacionComercial 		= '';
		$this->Anio 						= '';
		$this->Peso 						= '';
		$this->PrecioPublicoNeto 			= '';
		$this->PrecioPublicoTotalIva 		= '';		
		$this->Precio1			 			= '';
		$this->PrecioCompra		 			= '';
		$this->MesPrecioTotal 				= '';
		$this->Patentamiento		 		= '';
		$this->Ganancia1			 		= '';		
		$this->FleteFormularios 			= '';		
		$this->Ganancia2	 				= '';		
		$this->Iva 							= '';
		$this->Prenda 						= '';
		$this->Otorgamiento 				= '';
		$this->ImpuestoInterno				= '';
		$this->Precio2	 					= '';
		$this->Flete		 				= '';
		$this->BonificacionExtra 			= '';
		$this->DescuentoReventa				= '';
		$this->RecuperoBonificacion			= '';
		$this->Cilindrada					= '';
		$this->ReventaPrecio				= '';
		$this->Electrolito					= '';
		$this->GTIN							= '';
		$this->Cufe							= '';
		$this->Obsoleto 					= '0';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdModelo 					= $arr['IdModelo'];
		$this->IdTipoCombustible			= $arr['IdTipoCombustible'];
		$this->IdTipoModelo					= $arr['IdTipoModelo'];
		$this->IdCategoriaModelo			= $arr['IdCategoriaModelo'];
		$this->IdMarcaVehiculo 				= $arr['IdMarcaVehiculo'];
		$this->IdMarcaMotor 				= $arr['IdMarcaMotor'];
		$this->IdMarcaChasis 				= $arr['IdMarcaChasis'];
		$this->IdTipoVehiculo				= $arr['IdTipoVehiculo'];
		$this->IdTipoCarroceria				= $arr['IdTipoCarroceria'];
		$this->IdTipoUso					= $arr['IdTipoUso'];
		$this->IdDestinoVehiculo			= $arr['IdDestinoVehiculo'];
		$this->NumeroVinPrefijo 			= stripslashes($arr['NumeroVinPrefijo']);
		$this->CodigoComercial 				= stripslashes($arr['CodigoComercial']);
		$this->DenominacionModelo 			= stripslashes($arr['DenominacionModelo']);
		$this->DenominacionComercial 		= stripslashes($arr['DenominacionComercial']);
		$this->Anio 						= $arr['Anio'];
		$this->Peso 						= $arr['Peso'];
		$this->PrecioPublicoNeto 			= $arr['PrecioPublicoNeto'];
		$this->PrecioPublicoTotalIva 		= $arr['PrecioPublicoTotalIva'];		
		$this->Precio1 						= $arr['Precio1'];
		$this->MesPrecioTotal 				= $arr['MesPrecioTotal'];
		$this->Patentamiento		 		= $arr['Patentamiento'];
		$this->Ganancia1 					= $arr['Ganancia1'];		
		$this->FleteFormularios 			= $arr['FleteFormularios'];		
		$this->Ganancia2 					= $arr['Ganancia2'];		
		$this->Iva 							= $arr['Iva'];
		$this->Prenda 						= $arr['Prenda'];
		$this->Otorgamiento 				= $arr['Otorgamiento'];
		$this->ImpuestoInterno				= $arr['ImpuestoInterno'];
		$this->Precio2	 					= $arr['Precio2'];
		$this->Flete		 				= $arr['Flete'];
		$this->BonificacionExtra 			= $arr['BonificacionExtra'];
		$this->DescuentoReventa				= $arr['DescuentoReventa'];
		$this->RecuperoBonificacion			= $arr['RecuperoBonificacion'];	
		$this->PrecioCompra					= $arr['PrecioCompra'];	
		$this->Cilindrada					= $arr['Cilindrada'];	
		$this->ReventaPrecio				= $arr['ReventaPrecio'];	
		$this->Electrolito					= $arr['Electrolito'];	
		$this->GTIN							= $arr['GTIN'];	
		$this->Cufe							= $arr['Cufe'];	
		$this->Obsoleto						= $arr['Obsoleto'];

		/* determinamos el origen del modelo segun los dos primeros caracteres del prefijo de numero vin */
		if (substr($this->NumeroVinPrefijo, 0, 2) == '8A')
		{
			$this->Origen = Origen::Nacional;
		}
		else
		{
			$this->Origen = Origen::Importado;
		}
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllUnidades())
			return false;
		
		return true;
	}
	
	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByModelo($this);
	}
	
	
	public function GetTipoModelo()
	{
		$oTiposModelo = new TiposModelo();
		
		return $oTiposModelo->GetById($this->IdTipoModelo);
	}
	
	public function CalcularValores() 
	{
		$this->PrecioPublicoTotalIva = $this->PrecioPublicoNeto * (1 + ($this->Iva / 100));
		$this->Patentamiento = $this->PrecioPublicoTotalIva * 0.01 + 1000;
		
		$this->Precio1 = $this->Precio1Iva /* + $this->FleteFormularios*/ + $this->Patentamiento + $this->ImpuestoInterno;
		$this->Precio1 -= ($this->Precio2 + $this->Flete + $this->BonificacionExtra);
		
		$this->Ganancia2 = $this->Precio1Iva /* + $this->FleteFormularios*/ + $this->ImpuestoInterno;
		$this->Ganancia2 -= ($this->Precio2 + $this->Flete + $this->BonificacionExtra) * (1 - ($this->RecuperoBonificacion / 100));
		$this->Ganancia2 *= (1 - $this->DescuentoReventa / 100);
	}
	
	public function GetPrecioEfectivo()
	{
		if ($this->Precio1 && ($this->IdTipoModelo == 39 || $this->IdTipoModelo == 40))
			return $this->Precio1;
		if ($this->Precio1)
			return $this->Precio1 + $this->FleteFormularios;
		return 0;
	}
	
	public function GetPrecioCredito()
	{
		if ($this->Precio2 && ($this->IdTipoModelo == 39 || $this->IdTipoModelo == 40))
			return $this->Precio2;
		if ($this->Precio2)
			return $this->Precio2 + $this->FleteFormularios;
		return 0;
	}
}

?>