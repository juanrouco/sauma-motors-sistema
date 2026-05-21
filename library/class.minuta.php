<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.usados.php');
require_once('class.contratosprendas.php');
require_once('class.pedidosaccesorios.php');
require_once('class.minutasfinanciacion.php');
require_once('class.estadounidad.php');
require_once('class.pagos.php');

class Minuta
{
	public $IdMinuta;
	public $IdUnidad;
	public $IdUsuario;
	public $IdCliente;
	public $IdClienteCondominio;
	public $FechaMinuta;
	public $PrecioVenta;
	public $GastosFlete;
	public $GastosPatentamiento;
	public $GastosOtorgamiento;
	public $GastosPrenda;
	public $Circular;
	public $Anticipo;
	public $FinanciacionCapital;
	public $Condominio;
	public $EntregaUsado;
	public $IdUsado;
	public $DepositoGarantia;
	public $PlazoPrenda;
	public $Reportado;
	public $Rentas;
	public $IdClienteReventa;
	public $Saldo;
	public $Alta;
	public $RentasFinal;
	public $ReportadoSeguros;
	public $IdAcreedor;
	public $Observaciones;
	public $FechaVencimiento;
	public $FechaRetiro;
	public $SeguroCompania;
	public $SeguroCobertura;
	public $SeguroValor;
	public $SeguroIdTipoPago;
	public $CedulaAzul;
	public $IdOrigenCliente;
	public $NumeroGarantia;
	public $Interes;
	
	public function __construct()
	{
		$this->IdMinuta 			= '';
		$this->IdUnidad				= '';
		$this->IdUsuario 			= '';
		$this->IdCliente 			= '';
		$this->IdClienteCondominio	= '';
		$this->FechaMinuta 			= '';
		$this->PrecioVenta 			= '';
		$this->GastosFlete 			= '';
		$this->GastosPatentamiento 	= '';
		$this->GastosOtorgamiento 	= '';
		$this->GastosPrenda 		= '';
		$this->Circular 			= '';
		$this->Anticipo 			= '';
		$this->FinanciacionCapital 	= '';
		$this->Condominio 			= '';
		$this->EntregaUsado 		= '';
		$this->IdUsado 				= '';
		$this->DepositoGarantia		= '';
		$this->PlazoPrenda			= '';
		$this->Reportado			= '';
		$this->Rentas				= '';
		$this->IdClienteReventa		= '';
		$this->Saldo				= '';
		$this->Alta					= '';
		$this->RentasFinal			= '';
		$this->ReportadoSeguros		= '';
		$this->IdAcreedor			= '';
		$this->Observaciones		= '';
		$this->FechaVencimiento		= '';
		$this->FechaRetiro			= '';
		$this->SeguroCompania		= '';
		$this->SeguroCobertura		= '';
		$this->SeguroValor			= '';
		$this->SeguroIdTipoPago		= '';
		$this->CedulaAzul			= '';
		$this->IdOrigenCliente		= '';
		$this->NumeroGarantia		= '';
		$this->Interes				= 0;
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->IdUnidad				= $arr['IdUnidad'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->IdClienteCondominio	= $arr['IdClienteCondominio'];
		$this->FechaMinuta 			= $arr['FechaMinuta'];
		$this->PrecioVenta 			= $arr['PrecioVenta'];
		$this->GastosFlete 			= $arr['GastosFlete'];
		$this->GastosPatentamiento 	= $arr['GastosPatentamiento'];
		$this->GastosOtorgamiento 	= $arr['GastosOtorgamiento'];
		$this->GastosPrenda 		= $arr['GastosPrenda'];
		$this->PlazoPrenda			= $arr['PlazoPrenda'];
		$this->Circular 			= $arr['Circular'];
		$this->Anticipo 			= $arr['Anticipo'];
		$this->FinanciacionCapital 	= $arr['FinanciacionCapital'];
		$this->Condominio 			= ((ord($arr['Condominio']) == 1) || ($arr['Condominio'] == 1));
		$this->EntregaUsado 		= ((ord($arr['EntregaUsado']) == 1) || ($arr['EntregaUsado'] == 1));
		$this->IdUsado 				= $arr['IdUsado'];
		$this->DepositoGarantia		= $arr['DepositoGarantia'];
		$this->Reportado			= $arr['Reportado'];
		$this->Rentas				= $arr['Rentas'];
		$this->IdClienteReventa		= $arr['IdClienteReventa'];
		$this->Saldo				= $arr['Saldo'];
		$this->Alta					= $arr['Alta'];
		$this->RentasFinal			= $arr['RentasFinal'];
		$this->ReportadoSeguros		= $arr['ReportadoSeguros'];
		$this->IdAcreedor			= $arr['IdAcreedor'];
		$this->Observaciones		= $arr['Observaciones'];
		$this->FechaVencimiento		= $arr['FechaVencimiento'];
		$this->FechaRetiro			= $arr['FechaRetiro'];
		$this->SeguroCompania		= $arr['SeguroCompania'];
		$this->SeguroCobertura		= $arr['SeguroCobertura'];
		$this->SeguroValor			= $arr['SeguroValor'];
		$this->SeguroIdTipoPago		= $arr['SeguroIdTipoPago'];
		$this->CedulaAzul	 		= ((ord($arr['CedulaAzul']) == 1) || ($arr['CedulaAzul'] == 1));
		$this->IdOrigenCliente		= $arr['IdOrigenCliente'];
		$this->NumeroGarantia		= $arr['NumeroGarantia'];
		$this->Interes				= $arr['Interes'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetFacturaUnidad())
			return false;

		if ($this->GetRemito())
			return false;

		if ($this->GetNotaNoRodamiento())
			return false;

		if ($this->GetOrdenSalida())
			return false;

		/*if ($this->GetPedidoAccesorios())
			return false;*/

		if ($this->GetGestoria())
			return false;
		
		return true;
	}
	
	
	public function GetFacturaUnidad()
	{
		$FacturaUnidades = new FacturaUnidades();
		
		return $FacturaUnidades->GetByMinuta($this);
	}


	public function GetRemito()
	{
		$Remitos = new Remitos();
		
		return $Remitos->GetByMinuta($this);
	}


	public function GetNotaNoRodamiento()
	{
		$NotasNoRodamiento = new NotasNoRodamiento();
		
		return $NotasNoRodamiento->GetByMinuta($this);
	}


	public function GetOrdenSalida()
	{
		$OrdenesSalida = new OrdenesSalida();
		
		return $OrdenesSalida->GetByMinuta($this);
	}


	public function GetPedidoAccesorios()
	{
		$PedidosAccesorios = new PedidosAccesorios();
		
		return $PedidosAccesorios->GetByMinuta($this);
	}
	
	public function GetMinutasFinanciacion()
	{
		$MinutasFinanciacion = new MinutasFinanciacion();
		
		return $MinutasFinanciacion->GetByMinuta($this);
	}


	public function GetGestoria()
	{
		$Gestorias = new Gestorias();
		
		return $Gestorias->GetByMinuta($this);
	}
	
	public function GetTotalAcreditado()
	{
		$oUsados = new Usados();
		$oContratosPrendas = new ContratosPrendas();
		
		$TotalAcreditado = $this->DepositoGarantia;
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinuta($this->IdMinuta);
			
			$oUsado = $arrUsados[0];
			if ($oUsado->IdUbicacion != Ubicacion::Transito)
				$TotalAcreditado += $oUsado->Valuacion;
			if (count($arrUsados) > 1)
			{
				$oUsado2 = $arrUsados[1];
				
				if ($oUsado2->IdUbicacion != Ubicacion::Transito)
					$TotalAcreditado += $oUsado2->Valuacion;
			}
		}
		
		if (($this->FinanciacionCapital != '') && ($this->FinanciacionCapital != '0'))
		{
			//$TotalAcreditado += $this->FinanciacionCapital;
			/*$oContratoPrenda = $oContratosPrendas->GetByIdMinuta($this->IdMinuta);
			if ($oContratoPrenda && ($oContratoPrenda->IdEstado == EstadosPrendas::Liquidado || $oContratoPrenda->IdEstado == EstadosPrendas::Aprobado))
				$TotalAcreditado += $oContratoPrenda->MontoAcreditado;*/
		}
		
		$TotalAcreditado += $this->GetTotalPagos();
		
		return $TotalAcreditado;
	}
	
	public function GetTotalDescontable()
	{
		$oUsados = new Usados();
		$oContratosPrendas = new ContratosPrendas();
		
		$TotalAcreditado = $this->DepositoGarantia;
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinuta($this->IdMinuta);
			
			$oUsado = $arrUsados[0];
			$TotalAcreditado += $oUsado->Valuacion;
			if (count($arrUsados) > 1)
			{
				$oUsado2 = $arrUsados[1];
				$TotalAcreditado += $oUsado2->Valuacion;
			}
		}
		
		$arrMinutasFinanciacion = $this->GetMinutasFinanciacion();
		
		if (false && $arrMinutasFinanciacion && count($arrMinutasFinanciacion) > 0)
		{
			foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
				$TotalAcreditado += $oMinutaFinanciacion->Importe;
		}
		
		return $TotalAcreditado;
	}
	
	public function GetTotalPagos()
	{
		$oPagos = new Pagos();
		
		$arrPagos = $oPagos->GetByIdMinuta($this->IdMinuta);
		
		$TotalPagos = 0;
		
		foreach ($arrPagos as $oPago)
		{
			$TotalPagos += $oPago->Importe;
		}
		
		return $TotalPagos;
	}
	
	public function GetTotalAccesorios()
	{
		$oPedidosAccesorios = new PedidosAccesorios();
	
		$TotalAccesorios = 0;
		if ($oPedidoAccesorio = $oPedidosAccesorios->GetByMinuta($this))
		{
			$arrItems = $oPedidoAccesorio->GetAllItems();
			
			foreach($arrItems as $oPedidoAccesorioItem)
			{
				$TotalAccesorios += $oPedidoAccesorioItem->Importe;
			}
		}
		
		return $TotalAccesorios;
	}
	
	public function GetCostoTotal()
	{
		$TotalAbonar = $this->PrecioVenta;
		$TotalAbonar += $this->GastosPatentamiento;
		$TotalAbonar += $this->GastosPrenda;
		$TotalAbonar += $this->GastosOtorgamiento;
		$TotalAbonar += $this->GastosFlete;
		$TotalAbonar += $this->Alta;
		$TotalAbonar += $this->Interes;
		if ($this->RentasFinal > 0)
			$TotalAbonar += $this->RentasFinal;
		else
			$TotalAbonar += $this->Rentas;
			
		$oUsados = new Usados();
		$oContratosPrendas = new ContratosPrendas();
		
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinuta($this->IdMinuta);
			
			$oUsado = $arrUsados[0];
			$TotalAbonar += $oUsado->Arreglos;
			if (count($arrUsados) > 1)
			{
				$oUsado2 = $arrUsados[1];
				$TotalAbonar += $oUsado2->Arreglos;
			}
		}
		
		$TotalAccesorios = $this->GetTotalAccesorios();
		//$TotalAbonar += $TotalAccesorios;
		
		return $TotalAbonar;
	}
	
	public function GetTotalAAbonar()
	{
		$TotalAcreditado = $this->GetTotalDescontable();
		
		$TotalAbonar = $this->GetCostoTotal();
		
		$TotalPagos = $this->GetTotalPagos();
		
		$TotalAbonar -= $TotalAcreditado;
		$TotalAbonar -= $TotalPagos;
		
		return $TotalAbonar;
	}
	
	public function GetTotalPendiente()
	{
		$TotalAcreditado = $this->GetTotalAcreditado();
		
		$TotalAbonar = $this->GetCostoTotal();
		//$TotalPagos = $this->GetTotalPagos();
		
		$TotalAbonar -= $TotalAcreditado;
		//$TotalAbonar -= $TotalPagos;
		
		return $TotalAbonar;
	}
}

?>