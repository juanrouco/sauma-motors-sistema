<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.contratosprendasusados.php');
require_once('class.minutasusadosfinanciacion.php');

class MinutaUsado
{
	public $IdMinuta;
	public $IdUsado;
	public $IdUsuario;
	public $IdCliente;
	public $IdClienteCondominio;
	public $FechaMinuta;
	public $PrecioVenta;
	public $GastosOtorgamiento;
	public $GastosPrenda;
	public $Gastos;
	public $Anticipo;
	public $FinanciacionCapital;
	public $Condominio;
	public $DepositoGarantia;
	public $PlazoPrenda;
	public $Reportado;
	public $Rentas;
	public $IdClienteReventa;
	public $IdUsadoTomado;
	public $EntregaUsado;
	public $IdAcreedor;
	public $Observaciones;
	public $FechaVencimiento;
	public $FechaRetiro;
	public $CedulaAzul;
	
	public function __construct()
	{
		$this->IdMinuta 			= '';
		$this->IdUsado				= '';
		$this->IdUsuario 			= '';
		$this->IdCliente 			= '';
		$this->IdClienteCondominio	= '';
		$this->FechaMinuta 			= '';
		$this->PrecioVenta 			= '';
		$this->GastosOtorgamiento 	= '';
		$this->GastosPrenda 		= '';
		$this->Gastos 		= '';
		$this->Anticipo 			= '';
		$this->FinanciacionCapital 	= '';
		$this->Condominio 			= '';
		$this->DepositoGarantia		= '';
		$this->PlazoPrenda			= '';
		$this->Reportado			= '';
		$this->IdClienteReventa		= '';
		$this->IdUsadoTomado		= '';
		$this->EntregaUsado 		= '';
		$this->IdAcreedor	 		= '';
		$this->Observaciones 		= '';
		$this->FechaVencimiento		= '';
		$this->FechaRetiro			= '';
		$this->CedulaAzul			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->IdUsado				= $arr['IdUsado'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->IdCliente 			= $arr['IdCliente'];
		$this->IdClienteCondominio	= $arr['IdClienteCondominio'];
		$this->FechaMinuta 			= $arr['FechaMinuta'];
		$this->PrecioVenta 			= $arr['PrecioVenta'];
		$this->GastosOtorgamiento 	= $arr['GastosOtorgamiento'];
		$this->GastosPrenda 		= $arr['GastosPrenda'];
		$this->Gastos		 		= $arr['Gastos'];
		$this->PlazoPrenda			= $arr['PlazoPrenda'];
		$this->Anticipo 			= $arr['Anticipo'];
		$this->FinanciacionCapital 	= $arr['FinanciacionCapital'];
		$this->Condominio 			= ((ord($arr['Condominio']) == 1) || ($arr['Condominio'] == 1));
		$this->DepositoGarantia		= $arr['DepositoGarantia'];
		$this->Reportado			= $arr['Reportado'];
		$this->IdClienteReventa		= $arr['IdClienteReventa'];
		$this->IdUsadoTomado		= $arr['IdUsadoTomado'];
		$this->EntregaUsado 		= ((ord($arr['EntregaUsado']) == 1) || ($arr['EntregaUsado'] == 1));
		$this->IdAcreedor	 		= $arr['IdAcreedor'];
		$this->Observaciones	 	= $arr['Observaciones'];
		$this->FechaVencimiento	 	= $arr['FechaVencimiento'];
		$this->FechaRetiro		 	= $arr['FechaRetiro'];
		$this->CedulaAzul		 	= $arr['CedulaAzul'];
	}
	
	
	public function CanDelete()
	{
		/*if ($this->GetFacturaUnidad())
			return false;

		if ($this->GetRemito())
			return false;

		if ($this->GetNotaNoRodamiento())
			return false;

		if ($this->GetOrdenSalida())
			return false;

		if ($this->GetPedidoAccesorios())
			return false;

		if ($this->GetGestoria())
			return false;*/
		
		return true;
	}
	
	public function GetMinutasFinanciacion()
	{
		$MinutasFinanciacion = new MinutasUsadosFinanciacion();
		
		return $MinutasFinanciacion->GetByMinuta($this);
	}
	
	public function GetCostoTotal()
	{
		$oUsados = new Usados();
		$TotalAbonar = $this->PrecioVenta;
		$TotalAbonar += $this->GastosPatentamiento;
		$TotalAbonar += $this->GastosPrenda;
		$TotalAbonar += $this->GastosOtorgamiento;
		$TotalAbonar += $this->Gastos;
		$TotalAbonar += $this->Anticipo;
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinutaUsado($this->IdMinuta);
			
			$oUsado = $arrUsados[0];
			$TotalAbonar += $oUsado->Arreglos;
			if (count($arrUsados) > 1)
			{
				$oUsado2 = $arrUsados[1];
				$TotalAbonar += $oUsado2->Arreglos;
			}
		}
		
		return $TotalAbonar;
	}
	
	public function GetTotalAcreditado()
	{
		$oUsados = new Usados();
		$oContratosPrendas = new ContratosPrendasUsados();
		
		$TotalAcreditado = $this->DepositoGarantia;
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinutaUsado($this->IdMinuta);
			
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
			$TotalAcreditado+= $this->FinanciacionCapital;
			/*$oContratoPrenda = $oContratosPrendas->GetByIdMinuta($this->IdMinuta);
			
			if ($oContratoPrenda && ($oContratoPrenda->IdEstado == EstadosPrendas::Liquidado || $oContratoPrenda->IdEstado == EstadosPrendas::Aprobado))
				$TotalAcreditado += $oContratoPrenda->MontoAcreditado;*/
		}
		
		$TotalAcreditado += $this->GetTotalPagos();
		
		return $TotalAcreditado;
	}
	
	public function GetTotalPagos()
	{
		$oPagos = new Pagos();
		
		$arrPagos = $oPagos->GetByIdMinutaUsado($this->IdMinuta);
		
		$TotalPagos = 0;
		
		foreach ($arrPagos as $oPago)
		{
			$TotalPagos += $oPago->Importe;
		}
		
		return $TotalPagos;
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
	
	public function GetTotalAAbonar()
	{
		$TotalAcreditado = $this->GetTotalDescontable();
		
		$TotalAbonar = $this->GetCostoTotal();
		
		$TotalPagos = $this->GetTotalPagos();
		
		$TotalAbonar -= $TotalAcreditado;
		$TotalAbonar -= $TotalPagos;
		
		return $TotalAbonar;
	}
	
	public function GetTotalDescontable()
	{
		$oUsados = new Usados();
		$oContratosPrendas = new ContratosPrendasUsados();
		
		$TotalAcreditado = $this->DepositoGarantia;
		
		if ($this->EntregaUsado)
		{
			$arrUsados = $oUsados->GetAllByIdMinutaUsado($this->IdMinuta);
			
			$oUsado = $arrUsados[0];
			$TotalAcreditado += $oUsado->Valuacion;
			if (count($arrUsados) > 1)
			{
				$oUsado2 = $arrUsados[1];
				$TotalAcreditado += $oUsado2->Valuacion;
			}
		}
		
		$arrMinutasFinanciacion = $this->GetMinutasFinanciacion();
		
		if ($arrMinutasFinanciacion && count($arrMinutasFinanciacion) > 0)
		{
			foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
				$TotalAcreditado += $oMinutaFinanciacion->Importe;
		}
		
		return $TotalAcreditado;
	}
	
}

?>