<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Unidad
{
	const PathCsvBaseBack	= '../_recursos/unidades/unidades_base.xls';
	const PathCsvImportBack	= '../_recursos/unidades/';
	
	const PathImageBig		= '../_recursos/unidades/imagenes/big/';
	const PathImageThumb	= '../_recursos/unidades/imagenes/thumb/';
	const PathFile			= '../_recursos/unidades/archivos/';

	public $IdUnidad;
	public $IdModelo;
	public $IdUbicacion;
	public $IdColor;
	public $IdPlanillaRecepcion;
	public $IdMinutaPago;
	public $IdPlanillaCompra;
	public $IdReporteFacturacion;
	public $CodigoComercial;
	public $NumeroVinPrefijo;
	public $NumeroVin;
	public $NumeroMotor;
	public $NumeroChasis;
	public $Anio;
	public $Patente;
	public $FechaFacturaCompra;
	public $NumeroFacturaCompra;
	public $ImporteCompraNeto;
	
	public $Iva10;
	public $Iva21;
	public $PercepcionIVA;
	public $PercepcionIB;
	public $PercepcionGanancias;
	public $NoGrabado;
	public $ImpuestoInterno;
	public $ImpuestoInternoD;
	
	public $ImporteCompraBruto;
	public $CodigoLlaves;
	public $CodigoRadio;
	public $Cancelada;
	public $Verificado;
	public $Certificado;
	public $Lavado;
	public $FechaRetiro;
	public $IdEstado;
	public $NumeroPedido;	
	public $IdClientePlan;	
	public $Pisado;	
	public $Comentarios;	
	public $Plan;	
	public $VentaEspecial;	
	public $IdProveedor;	
	public $FechaPedidoFactura;	
	public $FechaRecepcionFactura;	
	public $FechaPatentamiento;	
	public $Consignacion;	
	public $Observaciones;	
	public $DNRPA;	
	public $LugarPatentamiento;	
	public $NumeroCertificado;	
	public $Marcha;	
	public $FechaMarchaVencimiento;	
	public $Conforme;	
	public $PrecioUnidad;	
	
	public function __construct()
	{
		$this->IdUnidad 			= '';
		$this->IdModelo				= '';
		$this->IdUbicacion 			= '';
		$this->IdColor 				= '';
		$this->IdPlanillaRecepcion 	= '';
		$this->IdPlanillaCompra 	= '';
		$this->IdMinutaPago		 	= '';
		$this->IdReporteFacturacion = '';
		$this->CodigoComercial 		= '';
		$this->NumeroVinPrefijo 	= '';
		$this->NumeroVin 			= '';
		$this->NumeroMotor 			= '';
		$this->NumeroChasis 		= '';
		$this->Anio 				= '';
		$this->Patente 				= '';
		$this->FechaFacturaCompra 	= '';
		$this->NumeroFacturaCompra 	= '';
		$this->ImporteCompraNeto 	= '';
		$this->Iva10 				= '';
		$this->Iva21 				= '';
		$this->PercepcionIVA	 	= '';
		$this->PercepcionIB		 	= '';
		$this->PercepcionGanancias 	= '';
		$this->NoGrabado		 	= '';
		$this->ImpuestoInterno	 	= '';
		$this->ImpuestoInternoD	 	= '';
		$this->ImporteCompraBruto 	= '';
		$this->CodigoLlaves 		= '';
		$this->CodigoRadio 			= '';
		$this->Cancelada 			= '';
		$this->Verificado 			= '';
		$this->Certificado 			= '';
		$this->Lavado 				= '';
		$this->FechaRetiro 			= '';
		$this->IdEstado 			= '';
		$this->NumeroPedido			= '';
		$this->IdClientePlan		= '';
		$this->Pisado				= '';
		$this->Comentarios			= '';
		$this->Plan					= '';
		$this->VentaEspecial		= '';
		$this->IdProveedor			= '';
		$this->FechaPedidoFactura	= '';
		$this->FechaRecepcionFactura= '';
		$this->FechaPatentamiento	= '';
		$this->Consignacion			= '';
		$this->Observaciones		= '';
		$this->DNRPA				= '';
		$this->LugarPatentamiento	= '';
		$this->NumeroCertificado	= '';
		$this->Marcha				= '';
		$this->FechaMarchaVencimiento	= '';
		$this->Conforme				= '';
		$this->PrecioUnidad			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUnidad 			= $arr['IdUnidad'];
		$this->IdModelo				= $arr['IdModelo'];
		$this->IdUbicacion 			= $arr['IdUbicacion'];
		$this->IdColor 				= $arr['IdColor'];
		$this->IdPlanillaRecepcion 	= $arr['IdPlanillaRecepcion'];
		$this->IdPlanillaCompra 	= $arr['IdPlanillaCompra'];
		$this->IdMinutaPago		 	= $arr['IdMinutaPago'];
		$this->IdReporteFacturacion	= $arr['IdReporteFacturacion'];
		$this->CodigoComercial 		= $arr['CodigoComercial'];
		$this->NumeroVin 			= $arr['NumeroVin'];
		$this->NumeroVinPrefijo 	= $arr['NumeroVinPrefijo'];
		$this->NumeroMotor 			= $arr['NumeroMotor'];
		$this->NumeroChasis 		= $arr['NumeroChasis'];
		$this->Anio 				= $arr['Anio'];
		$this->Patente 				= $arr['Patente'];
		$this->FechaFacturaCompra 	= $arr['FechaFacturaCompra'];
		$this->NumeroFacturaCompra 	= $arr['NumeroFacturaCompra'];
		$this->ImporteCompraNeto 	= $arr['ImporteCompraNeto'];
		$this->Iva10 				= $arr['Iva10'];
		$this->Iva21 				= $arr['Iva21'];
		$this->PercepcionIVA	 	= $arr['PercepcionIVA'];
		$this->PercepcionIB		 	= $arr['PercepcionIB'];
		$this->PercepcionGanancias 	= $arr['PercepcionGanancias'];
		$this->NoGrabado		 	= $arr['NoGrabado'];
		$this->ImporteCompraBruto 	= $arr['ImporteCompraBruto'];
		$this->ImporteNotaCredito 	= $arr['ImporteNotaCredito'];
		$this->ImpuestoInterno	 	= $arr['ImpuestoInterno'];
		$this->ImpuestoInternoD 	= $arr['ImpuestoInternoD'];
		$this->CodigoLlaves 		= $arr['CodigoLlaves'];
		$this->CodigoRadio 			= $arr['CodigoRadio'];		
		$this->Cancelada 			= ($arr['Cancelada'] == 1);
		$this->Verificado 			= ($arr['Verificado'] == 1);
		$this->Certificado 			= ($arr['Certificado'] == 1);
		$this->Reparacion 			= ($arr['Reparacion'] == 1);
		$this->Lavado 				= ($arr['Lavado'] == 1);
		$this->FechaRetiro 			= $arr['FechaRetiro'];
		$this->IdEstado 			= $arr['IdEstado'];
		$this->NumeroPedido			= $arr['NumeroPedido'];
		$this->FechaArriboEstimada 	= $arr['FechaArriboEstimada'];
		$this->IdClientePlan 		= $arr['IdClientePlan'];
		$this->Pisado		 		= $arr['Pisado'];
		$this->Comentarios	 		= $arr['Comentarios'];
		$this->Plan 				= ($arr['Plan'] == 1);
		$this->VentaEspecial		= ($arr['VentaEspecial'] == 1);
		$this->IdProveedor			= $arr['IdProveedor'];
		$this->FechaPedidoFactura	= $arr['FechaPedidoFactura'];
		$this->FechaRecepcionFactura= $arr['FechaRecepcionFactura'];
		$this->FechaPatentamiento	= $arr['FechaPatentamiento'];
		$this->Consignacion			= $arr['Consignacion'];
		$this->Observaciones		= $arr['Observaciones'];
		$this->DNRPA				= $arr['DNRPA'];
		$this->LugarPatentamiento	= $arr['LugarPatentamiento'];
		$this->NumeroCertificado	= $arr['NumeroCertificado'];
		$this->Marcha				= ($arr['Marcha'] == 1);
		$this->FechaMarchaVencimiento	= $arr['FechaMarchaVencimiento'];
		$this->Conforme				= ($arr['Conforme'] == 1);
		$this->PrecioUnidad			= $arr['PrecioUnidad'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetMinuta())
			return false;
		
		return true;
	}
	
	
	public function GetMinuta()
	{
		$Minutas = new Minutas();
		
		return $Minutas->GetByUnidad($this);
	}
}

?>