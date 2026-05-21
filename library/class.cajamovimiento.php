<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.minutas.php');
require_once('class.minutasusados.php');
require_once('class.unidades.php');
require_once('class.usados.php');
require_once('class.modelos.php');
require_once('class.clientes.php');
require_once('class.minutaspago.php');
require_once('class.facturaspostventas.php');

class CajaMovimiento extends DBAccess 
{
	public $IdCajaMovimiento;
	public $IdCajaDetalle;
	public $IdTipoMovimiento;
	public $IdCajaDestino;
	public $Fecha;
	public $IdEntidad;
	public $Total;
	public $Comentarios;
	public $IdConcepto;
	public $IdUsuario;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaMovimiento			= $arr['IdCajaMovimiento'];
		$this->IdCajaDetalle			= $arr['IdCajaDetalle'];
		$this->IdTipoMovimiento			= $arr['IdTipoMovimiento'];
		$this->IdCajaDestino			= $arr['IdCajaDestino'];	
		$this->IdCajaOrigen				= $arr['IdCajaOrigen'];	
		$this->Fecha					= $arr['Fecha'];
		$this->IdEntidad				= $arr['IdEntidad'];
		$this->Total					= $arr['Total'];	
		$this->Comentarios				= $arr['Comentarios'];
		$this->IdConcepto				= $arr['IdConcepto'];
		$this->IdUsuario				= $arr['IdUsuario'];
	}		
	
	public function GetDetalle()
	{
		$oPagos					= new Pagos();
		$oMinutas				= new Minutas();
		$oMinutasUsados			= new MinutasUsados();
		$oUnidades				= new Unidades();
		$oUsados				= new Usados();
		$oModelos				= new Modelos();
		$oClientes				= new Clientes();
		$oMinutasPago			= new MinutasPago();
		$oArreglos				= new UnidadesArreglos();
		//$oArreglosUsados		= new UsadosArreglos();
		$oFacturasPostVentas	= new FacturasPostVentas();
		$oCajasDetalle			= new CajasDetalles();
		
		if  ($this->IdTipoMovimiento == TiposMovimientosCaja::Pago)
		{
			$oPago = $oPagos->GetById($this->IdEntidad);
			if ($oPago->IdMinuta)
			{
				$oMinuta = $oMinutas->GetById($oPago->IdMinuta);
				$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
				$oModelo = $oModelos->GetById($oUnidad->IdModelo);
				$oCliente = $oClientes->GetById($oMinuta->IdCliente);
				
				return 'Pago Unidad: ' . $oPago->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' - ' . $oPago->Observaciones . ' ';
			}
			else
			{
				$oMinuta = $oMinutasUsados->GetById($oPago->IdMinutaUsado);
				$oUsado = $oUsados->GetById($oMinuta->IdUsado);
				$oCliente = $oClientes->GetById($oMinuta->IdCliente);
				
				return 'Pago Unidad: ' . $oPago->IdMinutaUsado . ' (' . $oUsado->Modelo . ') - Cliente: ' . $oCliente->RazonSocial . ' - ' . $oPago->Observaciones . ' ';
			}
		}
		elseif ($this->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $this->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
		{
			$oCuentaGestoria = $oCuentasGestorias->GetById($this->IdEntidad);
			$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
			$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			$oCliente = $oClientes->GetById($oMinuta->IdCliente);
			
			return 'Nro. Carpeta: ' . $oMinuta->IdMinuta . ' (' . $oModelo->DenominacionComercial . ') - Cliente: ' . $oCliente->RazonSocial . ' ';
		}
/*		elseif ($this->IdTipoMovimiento == TiposMovimientosCaja::PagoProveedores)
		{
			$oMinutaPago = $oMinutasPago->GetById($this->IdEntidad);
			
			return 'Minuta de Pago Nro: ' . $oMinutaPago->IdMinutaPago . ' ' . $oMinutaPago->ObtenerDescripcion() . ' ';
		}
		elseif ($this->IdTipoMovimiento == TiposMovimientosCaja::Arreglos)
		{
			if ($oArreglo = $oArreglos->GetById($this->IdEntidad))
				return 'Arreglo Interno Nro: ' . $oArreglo->IdUnidad . ' - ' . $oArreglo->Detalle . ' ';
			
			if ($oArreglo = $oArreglosUsados->GetById($this->IdEntidad))
				return 'Arreglo Interno Nro: U-' . $oArreglo->IdUsado . ' - ' . $oArreglo->Detalle . ' ';
			
		}*/
		elseif  ($this->IdTipoMovimiento == TiposMovimientosCaja::PagoPV)
		{
			$oFactura = $oFacturasPostVentas->GetById($this->IdEntidad);
			$oCliente = $oClientes->GetById($oFactura->IdCliente);
			return 'Pago de Factura de Posventa Nro: ' . $oFactura->NumeroFactura . ' - ' . $oCliente->RazonSocial . ' ';
		}
		elseif  ($this->IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja)
		{
			$oCajaDestino = $oCajasDetalle->GetById($this->IdCajaDestino);
			if ($this->Total < 0)
				return 'Destino: ' . $oCajaDestino->Nombre . ' - ' . ' ';
			return 'Origen: ' . $oCajaDestino->Nombre . ' - ' . ' ';
		}
		
		return  '';
	}
}

?>