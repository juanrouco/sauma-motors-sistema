<?php 

require_once('class.dbaccess.php');
require_once('class.pagos.php');
require_once('class.minutas.php');
require_once('class.minutasusados.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.usuarios.php');
require_once('class.pago.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.planillasrecepcion.php');
require_once('class.clientetipos.php');
require_once('class.comprobantes.php');
require_once('class.cajas.php');
require_once('class.cajasdetalles.php');
require_once('class.cajasmovimientos.php');
require_once('class.cajasdetallesdefault.php');
require_once('class.session.php');
require_once('class.filter.php');
require_once('class.page.php');

class Pagos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['IdFacturaPostVenta'])) && ($filter['IdFacturaPostVenta'] != ''))
			$sql.= " AND IdFacturaPostVenta = " . DB::Number($filter['IdFacturaPostVenta']);

		if ((isset($filter['IdMinutaUsado'])) && ($filter['IdMinutaUsado'] != ''))
			$sql.= " AND IdMinutaUsado = " . DB::Number($filter['IdMinutaUsado']);

		if ((isset($filter['IdTipoPago'])) && ($filter['IdTipoPago'] != ''))
			$sql.= " AND IdTipoPago = " . DB::Number($filter['IdTipoPago']);

		if ((isset($filter['Interno'])) && ($filter['Interno'] != ''))
		{
			$sql.= " AND (IdMinutaUsado = " . DB::Number($filter['Interno']);
			$sql.= " OR IdMinuta = " . DB::Number($filter['Interno']) . ")";
		}

		if ((isset($filter['NumeroCheque'])) && ($filter['NumeroCheque'] != ''))
			$sql.= " AND NumeroCheque LIKE '%" . DB::StringUnquoted($filter['NumeroCheque']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['Pago'])) && ($filter['Pago'] != ''))
			if ($filter['Pago'] != '0')
			$sql.= " AND Pago = " . DB::Bool($filter['Pago']);
		else
			$sql.= " AND (Pago = " . DB::Bool($filter['Pago']) . " OR Pago IS NULL)";
			
		if ((isset($filter['NumeroRecibo'])) && ($filter['NumeroRecibo'] != ''))
			$sql.= " AND NumeroRecibo LIKE '%" . DB::String($filter['NumeroRecibo']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPago DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetTotales(array $filter = NULL)
	{
		$sql = "SELECT COUNT(IdPago) AS Cantidad, SUM(Importe) AS Valuacion";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!($oRow = $oRes->GetRow()))	
			return false;
		
		$oResultado = new stdClass();
		$oResultado->Cantidad = $oRow['Cantidad'];
		$oResultado->Valuacion = $oRow['Valuacion'];
		
		return $oResultado;
	}

	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY FechaDeposito ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE IdPago = " . DB::Number($IdPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPago = new Pago();
		$oPago->ParseFromArray($oRow);
		
		return $oPago;		
	}
	

	public function GetByNumeroRecibo($NumeroRecibo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE NumeroRecibo = " . DB::String($NumeroRecibo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPago = new Pago();
		$oPago->ParseFromArray($oRow);
		
		return $oPago;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	

	public function GetByIdFacturaPostVenta($IdFacturaPostVenta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	

	public function GetTotalIdMinutaIdTipoPago($IdMinuta, $IdTipoPago)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND (fu.IdTipoPago = " . DB::Number($IdTipoPago);
		if ($IdTipoPago == TipoPago::Efectivo)
			$sql.= " OR (fu.Pago = 1 AND fu.IdTipoPago = " . DB::Number(TipoPago::Pagare) . ")";
		$sql.= ")";
		if ($IdTipoPago == TipoPago::Pagare)
			$sql.= " AND (fu.Pago = 0 OR fu.Pago IS NULL)";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetByIdMinutaIdAcreedor($IdMinuta, $IdAcreedor)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND fu.IdAcreedor = " . DB::Number($IdAcreedor);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetByIdMinutaUsadoIdAcreedor($IdMinuta, $IdAcreedor)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinutaUsado = " . DB::Number($IdMinuta);
		$sql.= " AND fu.IdAcreedor = " . DB::Number($IdAcreedor);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetTotalIdMinutaUsadoIdTipoPago($IdMinuta, $IdTipoPago)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinutaUsado = " . DB::Number($IdMinuta);
		$sql.= " AND (fu.IdTipoPago = " . DB::Number($IdTipoPago);
		if ($IdTipoPago == TipoPago::Efectivo)
			$sql.= " OR (fu.Pago = 1 AND fu.IdTipoPago = " . DB::Number(TipoPago::Pagare) . ")";
		$sql.= ")";
		if ($IdTipoPago == TipoPago::Pagare)
			$sql.= " AND fu.Pago = 0";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetTotalIdFacturaPostVentaIdTipoPago($IdFacturaPostVenta, $IdTipoPago)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);
		$sql.= " AND (fu.IdTipoPago = " . DB::Number($IdTipoPago);
		if ($IdTipoPago == TipoPago::Efectivo)
			$sql.= " OR (fu.Pago = 1 AND fu.IdTipoPago = " . DB::Number(TipoPago::Pagare) . ")";
		$sql.= ")";
		if ($IdTipoPago == TipoPago::Pagare)
			$sql.= " AND fu.Pago = 0";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetTotalIdMinutaIdTipoPagoIdAcreedor($IdMinuta, $IdTipoPago, $IdAcreedor)
	{
		$sql = "SELECT SUM(fu.Importe) AS Total";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND fu.IdTipoPago = " . DB::Number($IdTipoPago);
		$sql.= " AND fu.IdAcreedor = " . DB::Number($IdAcreedor);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;	
		
		return $oRow['Total'];	
	}
	

	public function GetByIdMinutaUsado($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_Pagos fu";
		$sql.= " WHERE fu.IdMinutaUsado = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	
	public function GetByIdPagoMayorista($IdPagoMayorista)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE IdPagoMayorista = " . DB::Number($IdPagoMayorista);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPago = new Pago();
			$oPago->ParseFromArray($oRow);
			
			array_push($arr, $oPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	
	public function GetByIdPagoMayoristaAndIdMinuta($IdPagoMayorista, $IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE IdPagoMayorista = " . DB::Number($IdPagoMayorista);
		$sql.= " And IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())	
			return false;
		
		$oPago = new Pago();
		$oPago->ParseFromArray($oRow);
		
		return $oPago;	
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Pagos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Pago $oPago)
	{
		$arr = array
		(
			'IdMinuta' 				=> DB::Number($oPago->IdMinuta),
			'Fecha' 				=> DB::Date($oPago->Fecha),
			'NumeroCheque' 			=> DB::String($oPago->NumeroCheque),
			'BancoDesde' 			=> DB::String($oPago->BancoDesde),
			'BancoDestino' 			=> DB::String($oPago->BancoDestino),
			'IdCajaDetalle' 		=> DB::Number($oPago->IdCajaDetalle),
			'Cliente' 				=> DB::String($oPago->Cliente),
			'FechaEmision' 			=> DB::Date($oPago->FechaEmision),
			'FechaDeposito'			=> DB::Date($oPago->FechaDeposito),
			'Importe' 				=> DB::Number($oPago->Importe),
			'IdTipoPago'	 		=> DB::Number($oPago->IdTipoPago),
			'Observaciones' 		=> DB::String($oPago->Observaciones),
			'IdPagoMayorista'		=> DB::Number($oPago->IdPagoMayorista),
			'IdMinutaUsado'			=> DB::Number($oPago->IdMinutaUsado),
			'Pago'					=> DB::Number($oPago->Pago),
			'NumeroRecibo'			=> DB::String($oPago->NumeroRecibo),
			'IdAcreedor'			=> DB::Number($oPago->IdAcreedor),
			'Cuotas'				=> DB::Number($oPago->Cuotas),
			'IdFacturaPostVenta'	=> DB::Number($oPago->IdFacturaPostVenta)
		);
		
		return $arr;
	}
	
	public function Create(Pago $oPago)
	{
		$arr = $this->GetArrayDB($oPago);
		
		if (!$this->Insert('TB_Pagos', $arr))
			return false;

		/* asignamos el id generado */
		$oPago->IdPago = DBAccess::GetLastInsertId();
		
		if ($oPago->IdFacturaPostVenta)
			$this->CrearMovimientoPagoPV($oPago, $oPago->IdFacturaPostVenta);
		else
			$this->CrearMovimientoPago($oPago);
			
		return $oPago;
	}
	
	public function Update(Pago $oPago)
	{
		$where = " IdPago = " . DB::Number($oPago->IdPago);
		
		$arr = $this->GetArrayDB($oPago);
		
		if (!DBAccess::Update('TB_Pagos', $arr, $where))
			return false;
		
		if ($oPago->IdFacturaPostVenta)
		{
			$this->EliminarMovimientoPagoPV($oPago, $oPago->IdFacturaPostVenta);
			$this->CrearMovimientoPagoPV($oPago, $oPago->IdFacturaPostVenta);
		}
		else
		{
			$this->EliminarMovimientoPago($oPago->IdPago);
			$this->CrearMovimientoPago($oPago);
		}
		
		return $oPago;
	}
	
	public function UpdatePago(Pago $oPago)
	{
		$where = " IdPago = " . DB::Number($oPago->IdPago);
		
		$arr = $this->GetArrayDB($oPago);
		
		if (!DBAccess::Update('TB_Pagos', $arr, $where))
			return false;
		
		return $oPago;
	}
	
	
	public function Delete($IdPago)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPago = " . DB::Number($IdPago);
		$oPago = $this->GetById($IdPago);
		
		if ($oPago->IdFacturaPostVenta)
		{
			$this->EliminarMovimientoPagoPV($oPago, $oPago->IdFacturaPostVenta);
		}
		else
		{
			$this->EliminarMovimientoPago($IdPago);
		}
		
		if (!DBAccess::Delete('TB_Pagos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	
	public function DeleteByIdMinuta($IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMinuta = " . DB::Number($IdMinuta);

		if (!DBAccess::Delete('TB_Pagos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	
	public function DeleteByIdMinutaUsado($IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMinutaUsado = " . DB::Number($IdMinuta);

		if (!DBAccess::Delete('TB_Pagos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	
	
	
	public function EliminarMovimientoPagoPV($oPago, $IdFactura)
	{
		$oFacturasPostVentas = new FacturasPostVentas();
		$oFactura = $oFacturasPostVentas->GetById($IdFactura);
		
		$oCajasMovimientos = new CajasMovimientos();
		$oCajaMovimiento = new CajaMovimiento();
		
		if ($this->AsignarCajaDetallePV($oCajaMovimiento, $oPago, $oFactura))
		{
			if ($oCajaMovimiento = $oCajasMovimientos->GetByIdEntidad(TiposMovimientosCaja::PagoPV, $IdFactura, $oCajaMovimiento->IdCajaDetalle))
			{
				$oCajaMovimiento->Comentarios = $oCajaMovimiento->GetDetalle() . ' - ' . $oCajaMovimiento->Comentarios;
				$oCajasMovimientos->Update($oCajaMovimiento);
				
				$Importe = $oCajaMovimiento->Total;
				
				$oCajaMovimientoSalida = new CajaMovimiento();
				$oCajaMovimientoSalida->IdCajaDetalle = $oCajaMovimiento->IdCajaDetalle;
				$oCajaMovimientoSalida->IdTipoMovimiento = TiposMovimientosCaja::Egreso;
				$oCajaMovimientoSalida->Fecha = date('Y-m-d H:i:s');
				$oCajaMovimientoSalida->IdEntidad = $oPago->IdPago;
				$oCajaMovimientoSalida->Total = $Importe * -1;
				$oCajaMovimientoSalida->Comentarios = 'Se elimina el pago de postventa: ' . $oCajaMovimiento->GetDetalle() . ' - ' . $oCajaMovimiento->Comentarios;
				$oCajasMovimientos->Create($oCajaMovimientoSalida);
			
				$oCajasDetalles = new CajasDetalles();
				$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
				$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
				$oCajaDetalle->Total -= $Importe;
				$oCajasDetalles->Update($oCajaDetalle);
					
				$oCajas = new Cajas();
				$oCaja = $oCajas->GetById(1);
				$oCaja->TotalRendir -= $Importe;
				$oCaja->TotalDetalles -= $Importe;
				$oCajas->Update($oCaja);
			}
		}
	}
	

	private function AsignarCajaDetallePV(&$oCajaMovimiento, $oPago, $oFactura)
	{
		$Crear = false;
		$oCajasDetallesDefault = new CajasDetallesDefault();
		$oUsuario = Session::GetCurrentUser();
		
		if ($oPago->IdTipoPago == TipoPago::DepositoEfectivo || $oPago->IdTipoPago == TipoPago::Transferencia || $oPago->IdTipoPago == TipoPago::DepositoCheque)
		{
			$oCajaMovimiento->IdCajaDetalle = 23; // $oPago->IdCajaDetalle;
			$Crear = true;
		}
		elseif ($oCajaDetalleDefault = $oCajasDetallesDefault->GetById($oPago->IdTipoPago, $oUsuario->IdUbicacion)) 
		{
			if ($oFactura->IdOrdenTrabajo)
				$oCajaMovimiento->IdCajaDetalle = $oCajaDetalleDefault->IdCajaTaller;
			else
				$oCajaMovimiento->IdCajaDetalle = $oCajaDetalleDefault->IdCajaRepuestos;
			$Crear = true;
		}
		return $Crear;
	}

	private function CrearMovimientoPagoPV($oPago, $IdFactura)
	{
		$oFacturasPostVentas = new FacturasPostVentas();
		$oCajasMovimientos = new CajasMovimientos();
		$oCajaMovimiento = new CajaMovimiento();
		
		$oFactura = $oFacturasPostVentas->GetById($IdFactura);
		
		$Crear = $this->AsignarCajaDetallePV($oCajaMovimiento, $oPago, $oFactura);		
		
		if ($Crear)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::PagoPV;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oFactura->IdFacturaPostVenta;
			$oCajaMovimiento->Total = $oPago->Importe;
			$oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}
	}

	private function AsignarCajaDetalle(&$oCajaMovimiento, $oPago)
	{
		$Crear = false;
		$oCajasDetallesDefault = new CajasDetallesDefault();
		$oUsuario = Session::GetCurrentUser();
		
		if ($oPago->IdTipoPago == TipoPago::DepositoEfectivo || $oPago->IdTipoPago == TipoPago::Transferencia || $oPago->IdTipoPago == TipoPago::DepositoCheque)
		{
			$oCajaMovimiento->IdCajaDetalle = $oPago->IdCajaDetalle;
			$Crear = true;
		}
		elseif ($oCajaDetalleDefault = $oCajasDetallesDefault->GetById($oPago->IdTipoPago, $oUsuario->IdUbicacion)) 
		{
			if (($oPago->IdTipoPago == TipoPago::Pagare && $oPago->Pago == '1') || $oPago->IdTipoPago != TipoPago::Pagare)
			{
				$oCajaMovimiento->IdCajaDetalle = $oCajaDetalleDefault->IdCajaAdministracion;
				$Crear = true;
			}
		}
		
		return $Crear;
	}

	private function CrearMovimientoPago($oPago)
	{
		$oCajasMovimientos = new CajasMovimientos();
		$oCajaMovimiento = new CajaMovimiento();
		
		$Crear = $this->AsignarCajaDetalle($oCajaMovimiento, $oPago);
		//$Crear = false;
						
		if ($Crear)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::Pago;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oPago->IdPago;
			$oCajaMovimiento->Total = $oPago->Importe;
			$oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}
	}	
	
	private function EliminarMovimientoPago($IdPago)
	{
		$oPago = $this->GetById($IdPago);
		
		$oCajasMovimientos = new CajasMovimientos();
		if ($oPago->IdTipoPago == TipoPago::Efectivo || $oPago->IdTipoPago == TipoPago::TodoPago || $oPago->IdTipoPago == TipoPago::MercadoPago || $oPago->IdTipoPago == TipoPago::DepositoEfectivo || $oPago->IdTipoPago == TipoPago::Transferencia || $oPago->IdTipoPago == TipoPago::DepositoCheque || $oPago->IdTipoPago == TipoPago::Cheque || ($oPago->IdTipoPago == TipoPago::Pagare && $oPago->Pago == '1'))
		{
			if ($oCajaMovimiento = $oCajasMovimientos->GetByIdEntidad(TiposMovimientosCaja::Pago, $IdPago))
			{
				$oCajaMovimiento->Comentarios = $oCajaMovimiento->GetDetalle() . ' - ' . $oCajaMovimiento->Comentarios;
				$oCajasMovimientos->Update($oCajaMovimiento);
				
				$Importe = $oCajaMovimiento->Total;
				
				$oCajaMovimientoSalida = new CajaMovimiento();
				$oCajaMovimientoSalida->IdCajaDetalle = $oCajaMovimiento->IdCajaDetalle;
				$oCajaMovimientoSalida->IdTipoMovimiento = TiposMovimientosCaja::Egreso;
				$oCajaMovimientoSalida->Fecha = date('Y-m-d H:i:s');
				$oCajaMovimientoSalida->IdEntidad = $oPago->IdPago;
				$oCajaMovimientoSalida->Total = $Importe * -1;
				$oCajaMovimientoSalida->Comentarios = 'Se borra pago: ' . $oCajaMovimiento->GetDetalle() . ' - ' . $oCajaMovimiento->Comentarios;
				$oCajasMovimientos->Create($oCajaMovimientoSalida);
			
				$oCajasDetalles = new CajasDetalles();
				$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
				$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
				$oCajaDetalle->Total -= $Importe;
				$oCajasDetalles->Update($oCajaDetalle);
					
				$oCajas = new Cajas();
				$oCaja = $oCajas->GetById(1);
				$oCaja->TotalRendir -= $Importe;
				$oCaja->TotalDetalles -= $Importe;
				$oCajas->Update($oCaja);
			}
		}
	}
	
	public function ExportCsv(array $filter = NULL)
	{
		$oMinutas				= new Minutas();
		$oMinutasUsados			= new MinutasUsados();
		$oClientes				= new Clientes();
		$oUsuarios				= new Usuarios();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "pagos.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrPagos = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Nro. Interno";
		$csv.= $Separador;
		$csv.= "Tipo Pago";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Vendedor";
		$csv.= $Separador;
		$csv.= "Pagado";
		$csv.= $Separador;
		$csv.= "Observaciones";
		$csv.= $Separador;
		$csv.= "Importe";		
		$csv.= $SaltoLinea;
	
		foreach ($arrPagos as $oPago)
		{				
			$Interno = $oPago->IdMinuta;
			if (!$Interno)
				$Interno = 'U-' . $oPago->IdMinutaUsado;
						
			if (!$oMinuta = $oMinutas->GetById($oPago->IdMinuta))
				$oMinuta = $oMinutasUsados->GetById($oPago->IdMinutaUsado);
						
			$oUsuario	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oCliente	= $oClientes->GetById($oMinuta->IdCliente);
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oPago->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($Interno));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(TipoPago::GetById($oPago->IdTipoPago)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsuario->Nombre . ' ' . $oUsuario->Apellido));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oPago->Pago ? 'SI' : 'NO'));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oPago->Observaciones));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oPago->Importe, 2, ',', '.')));
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>