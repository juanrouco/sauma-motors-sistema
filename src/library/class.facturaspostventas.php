<?php 

require_once('class.dbaccess.php');
require_once('class.facturapostventa.php');
require_once('class.formaspago.php');
require_once('class.planescuotas.php');
require_once('class.filter.php');
require_once('class.cajas.php');
require_once('class.cajasdetalles.php');
require_once('class.pagos.php');
require_once('class.cajasmovimientos.php');
require_once('class.page.php');

class FacturasPostVentas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1=1';
		
		if ((isset($filter['IdFacturaPostVenta'])) && ($filter['IdFacturaPostVenta'] != ''))
			$sql.= " AND IdFacturaPostVenta = " . DB::Number($filter['IdFacturaPostVenta']);
			
		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND IdCliente = " . DB::Number($filter['IdCliente']);
			
		if ((isset($filter['IdTallerUnidad'])) && ($filter['IdTallerUnidad'] != ''))
			$sql.= " AND IdTallerUnidad = " . DB::Number($filter['IdTallerUnidad']);
			
		if ((isset($filter['IdFactura'])) && ($filter['IdFactura'] != ''))
			$sql.= " AND IdFactura = " . DB::Number($filter['IdFactura']);
			
		if ((isset($filter['IdFormaPago'])) && ($filter['IdFormaPago'] != ''))
			$sql.= " AND IdFormaPago = " . DB::Number($filter['IdFormaPago']);
			
		if ((isset($filter['IdPlanCuota'])) && ($filter['IdPlanCuota'] != ''))
			$sql.= " AND IdPlanCuota = " . DB::Number($filter['IdPlanCuota']);

		if ((isset($filter['NumeroComprobante'])) && ($filter['NumeroComprobante'] != ''))
			$sql.= " AND NumeroFactura LIKE '%" . DB::StringUnquoted($filter['NumeroComprobante']) . "%'";

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND IdCliente IN (SELECT IdCliente FROM TB_Clientes WHERE RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%')";

		if ((isset($filter['Cuil'])) && ($filter['Cuil'] != ''))
			$sql.= " AND IdCliente IN (SELECT IdCliente FROM TB_Clientes WHERE ClaveFiscalNumero LIKE '%" . DB::StringUnquoted($filter['Cuil']) . "%')";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['IdUsuarioAsignado'])) && ($filter['IdUsuarioAsignado'] != ''))
			$sql.= " AND IdOrdenTrabajo IN (SELECT IdOrdenTrabajo FROM TB_OrdenesTrabajo WHERE IdUsuarioAsignado = " . DB::Number($filter['IdUsuarioAsignado']) . ")";
			
		if ((isset($filter['IdCategoria'])) && ($filter['IdCategoria'] != ''))
			$sql.= " AND IdOrdenTrabajo IN (SELECT IdOrdenTrabajo FROM TB_OrdenesTrabajoTareas WHERE IdCategoria = " . DB::Number($filter['IdCategoria']) . ")";
			
		if ((isset($filter['CuentaCorriente'])) && ($filter['CuentaCorriente'] != ''))
			$sql.= " AND (TotalPago IS NULL OR TotalPago < ImporteBruto)";
			
		if ((isset($filter['NoAnulado'])) && ($filter['NoAnulado'] != ''))
			$sql.= " AND IdComprobante IN (SELECT IdComprobante FROM TB_Comprobantes WHERE IdEstado <> 3)";
			
		if ((isset($filter['IdFormaPago'])) && ($filter['IdFormaPago'] != ''))
			$sql.= " AND IdFormaPago = " . DB::Number($filter['IdFormaPago']);
			
		if ((isset($filter['OT'])) && ($filter['OT'] == '1'))
			$sql.= " AND IdOrdenTrabajo IS NOT NULL";
			
		if ((isset($filter['OT'])) && ($filter['OT'] == '2'))
			$sql.= " AND IdOrdenTrabajo IS NULL";
			
		if ((isset($filter['Saldo'])) && ($filter['Saldo'] != ''))
		{
			if ($filter['Saldo'] == 1)
			{
				$sql.= " AND IdFacturaPostVenta IN (SELECT f.IdFacturaPostVenta";
				$sql.= " FROM TB_FacturasPostVentas f";
				$sql.= " INNER JOIN (SELECT IdFacturaPostVenta, SUM(Importe) AS Total FROM TB_Pagos GROUP BY IdFacturaPostVenta) p ON f.IdFacturaPostVenta = p.IdFacturaPostVenta";
				$sql.= " WHERE f.ImporteBruto - p.Total > 0)";
			}
			else
			{
				$sql.= " AND IdFacturaPostVenta IN (SELECT f.IdFacturaPostVenta";
				$sql.= " FROM TB_FacturasPostVentas f";
				$sql.= " INNER JOIN (SELECT IdFacturaPostVenta, SUM(Importe) AS Total FROM TB_Pagos GROUP BY IdFacturaPostVenta) p ON f.IdFacturaPostVenta = p.IdFacturaPostVenta";
				$sql.= " WHERE f.ImporteBruto - p.Total <= 0)";
			}
		}
			
		if ((isset($filter['Tarjeta'])) && ($filter['Tarjeta'] != ''))
			$sql.= " AND (IdFormaPago = 2 OR IdFormaPago = 3) AND IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL)";
			
		return $sql;
	}

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaPostVenta = new FacturaPostVenta();
			$oFacturaPostVenta->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaPostVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetTotal(array $filter = NULL)
	{
		$sql = "SELECT SUM(ImporteBruto) AS Total";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		return $oRow['Total'];
	}


	public function GetById($IdFacturaPostVenta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= " WHERE IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaPostVenta = new FacturaPostVenta();
		$oFacturaPostVenta->ParseFromArray($oRow);
		
		return $oFacturaPostVenta;		
	}


	public function GetByIdComprobante($IdComprobante)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= " WHERE IdComprobante = " . DB::Number($IdComprobante);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaPostVenta = new FacturaPostVenta();
		$oFacturaPostVenta->ParseFromArray($oRow);
		
		return $oFacturaPostVenta;		
	}
	
	public function GetByOrdenTrabajo($oOrdenTrabajo)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM TB_FacturasPostVentas c";
		$sql.= " WHERE c.IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaPostVenta = new FacturaPostVenta();
			$oFacturaPostVenta->ParseFromArray($oRow);

			array_push($arr, $oFacturaPostVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetByCompra($oCompra)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM TB_FacturasPostVentas c";
		$sql.= " WHERE c.IdCompra = " . DB::Number($oCompra->IdCompra);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaPostVenta = new FacturaPostVenta();
			$oFacturaPostVenta->ParseFromArray($oRow);

			array_push($arr, $oFacturaPostVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	public function GetByOrdenTrabajoNoImpresas($oOrdenTrabajo)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM TB_FacturasPostVentas c";
		$sql.= " WHERE c.IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
		$sql.= " AND c.IdComprobante IS NULL";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaPostVenta = new FacturaPostVenta();
			$oFacturaPostVenta->ParseFromArray($oRow);

			array_push($arr, $oFacturaPostVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(FacturaPostVenta $oFacturaPostVenta)
	{
		$arr = array(
			'IdOrdenTrabajo'				=> DB::Number($oFacturaPostVenta->IdOrdenTrabajo),
			'IdCompra' 						=> DB::Number($oFacturaPostVenta->IdCompra),
			'ImporteNeto'					=> DB::Number($oFacturaPostVenta->ImporteNeto),
			'ImporteBruto'					=> DB::Number($oFacturaPostVenta->ImporteBruto),
			'Iva21'							=> DB::Number($oFacturaPostVenta->Iva21),
			'Iva10'							=> DB::Number($oFacturaPostVenta->Iva10),
			'Descuentos'					=> DB::Number($oFacturaPostVenta->Descuentos),
			'PercepcionIIBB'				=> DB::Number($oFacturaPostVenta->PercepcionIIBB),
			'IdCliente'						=> DB::Number($oFacturaPostVenta->IdCliente),
			'Fecha'							=> DB::Date($oFacturaPostVenta->Fecha),
			'IdComprobante'					=> DB::Number($oFacturaPostVenta->IdComprobante),
			'NumeroFactura'					=> DB::String($oFacturaPostVenta->NumeroFactura),
			'Comentarios'					=> DB::String($oFacturaPostVenta->Comentarios),
			'TotalPago'						=> DB::Number($oFacturaPostVenta->TotalPago),
			'FechaPago'						=> DB::Date($oFacturaPostVenta->FechaPago),
			'IdFormaPago'					=> DB::Number($oFacturaPostVenta->IdFormaPago),
			'IdPlanCuota'					=> DB::Number($oFacturaPostVenta->IdPlanCuota)
		);
		return $arr;
	}
	
	public function Create(FacturaPostVenta $oFacturaPostVenta)
	{
		$arr = $this->GetArrayDB($oFacturaPostVenta);
		
		if (!$this->Insert('TB_FacturasPostVentas', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaPostVenta->IdFacturaPostVenta = DBAccess::GetLastInsertId();
		
		//$this->CrearMovimientoPago($oFacturaPostVenta);
			
		return $oFacturaPostVenta;
	}
	
	public function Update(FacturaPostVenta $oFacturaPostVenta)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetArrayDB($oFacturaPostVenta);

		$where = " IdFacturaPostVenta = " . (int)$oFacturaPostVenta->IdFacturaPostVenta;
		
		if (!DBAccess::Update('TB_FacturasPostVentas', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oCliente;
	}
	
	
	public function Delete($IdFacturaPostVenta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFactura = " . DB::Number($IdFacturaPostVenta);

		if (!DBAccess::Delete('TB_FacturasItems', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		
		
		$where = " IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);
		
		if (!DBAccess::Delete('TB_FacturasPostVentas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}

	private function CrearMovimientoPago($oFactura)
	{
		$oCajasMovimientos = new CajasMovimientos();
		$oCajaMovimiento = new CajaMovimiento();
		
		$Crear = false;
		
		if ($oFactura->IdFormaPago == FormaPago::Efectivo)
		{			
			$oCajaMovimiento->IdCajaDetalle = 8;
			//$oCajaMovimiento->IdCajaDetalle = 1;
			$Crear = true;
		}
		elseif ($oFactura->IdFormaPago == FormaPago::MercadoPago)
		{
			$oCajaMovimiento->IdCajaDetalle = 11;
			$Crear = true;
		}
		/*elseif ($oFactura->IdFormaPago == FormaPago::Cheque)
		{
			$oCajaMovimiento->IdCajaDetalle = 2;
			$Crear = true;
		}*/
		
		if ($Crear)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::PagoPV;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oFactura->IdFacturaPostVenta;
			$oCajaMovimiento->Total = $oFactura->ImporteBruto;
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
	
	public function EliminarMovimientoPago($IdFactura)
	{
		$oFactura = $this->GetById($IdFactura);
		
		$oCajasMovimientos = new CajasMovimientos();
		if ($oFactura->IdFormaPago == FormaPago::Efectivo || /*$oFactura->IdFormaPago == FormaPago::Cheque ||*/ $oFactura->IdFormaPago == FormaPago::MercadoPago)
		{
			if ($oCajaMovimiento = $oCajasMovimientos->GetByIdEntidad(TiposMovimientosCaja::PagoPV, $IdFactura))
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
				$oCajaMovimientoSalida->Comentarios = 'Se anula factura posventa: ' . $oCajaMovimiento->GetDetalle() . ' - ' . $oCajaMovimiento->Comentarios;
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
	
	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasPostVentas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$oComprobantes = new Comprobantes();
		$oClientes = new Clientes();
		
		$arrFacturas = $this->GetAll($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIT", 
			'NUMERO OT',
			'NETO',
			'IVA 10,5%',
			'IVA 21%',
			'PERC: IIBB',
			'IMPORTE',
			'ANULADA');
		
		foreach ($arrFacturas as $oFactura)
		{
			$oComprobante = $oComprobantes->GetById($oFactura->IdComprobante);
			$oCliente = $oClientes->GetById($oFactura->IdCliente);
			
			$arrData[] = array(
				trim(CambiarFecha($oFactura->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				trim($oFactura->IdOrdenTrabajo ? $oFactura->IdOrdenTrabajo : 'NC'),
				trim(number_format($oFactura->ImporteNeto, 2, '.', '')),
				trim(number_format($oFactura->Iva10, 2, '.', '')),
				trim(number_format($oFactura->Iva21, 2, '.', '')),
				trim(number_format($oFactura->PercepcionIIBB, 2, '.', '')),
				trim(number_format($oFactura->ImporteBruto, 2, '.', '')),
				trim($oComprobante->IdEstado == ComprobanteEstados::Anulado ? 'SI' : 'NO')
				);
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'facturas postventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ExportReportePagosCsv(array $filter = NULL)
	{
		$oComprobantes = new Comprobantes();
		$oClientes = new Clientes();
		$oCompras = new Compras();
		$oPagos = new Pagos();
		$oOrdenesTrabajo = new OrdenesTrabajo();
		
		$arrFacturas = $this->GetAll($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"NUMERO", 
			"FACTURA", 
			"CUIT", 
			'PRECIO VENTA',
			'MANO DE OBRA',
			'REPUESTOS',
			'COSTO REPUESTOS',
			'GANANCIA REPUESTOS',
			'ANULADA',
			'EFECTIVO',
			'TARJETA CREDITO',
			'TARJETA DEBITO',
			'MERCADO PAGO',
			'PENDIENTE');
		
		foreach ($arrFacturas as $oFacturaPostVenta)
		{
			$oCliente = $oClientes->GetById($oFacturaPostVenta->IdCliente);
			$oComprobante = $oComprobantes->GetById($oFacturaPostVenta->IdComprobante);
			$TotalMO = 0;
			$TotalRep = 0;
			$CostoRep = 0;
			$Efectivo = 0;
			$Visa = 0;
			$AMEX = 0;
			$Cheque = 0;
			$Transferencia = 0;
			$MP = 0;
						
			$Efectivo = $oPagos->GetTotalIdFacturaPostVentaIdTipoPago($oFacturaPostVenta->IdFacturaPostVenta, TipoPago::Efectivo);
			$Credito = $oPagos->GetTotalIdFacturaPostVentaIdTipoPago($oFacturaPostVenta->IdFacturaPostVenta, TipoPago::Credito);
			$Debito = $oPagos->GetTotalIdFacturaPostVentaIdTipoPago($oFacturaPostVenta->IdFacturaPostVenta, TipoPago::Debito);
			$MP = $oPagos->GetTotalIdFacturaPostVentaIdTipoPago($oFacturaPostVenta->IdFacturaPostVenta, TipoPago::MercadoPago);
				
			$Total = $Efectivo + $Credito + $Debito + $MP;
			$Pendiente = $oFacturaPostVenta->ImporteBruto - $Total;
			
			if ($oFacturaPostVenta->IdCompra)
			{
				$TotalRep = $oFacturaPostVenta->ImporteBruto;
				$oCompra = $oCompras->GetById($oFacturaPostVenta->IdCompra);
				$CostoRep = $oCompra->Costo();
			}
			else
			{
				$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oFacturaPostVenta->IdOrdenTrabajo);
				$TotalMO = $oOrdenTrabajo->ImporteManoObraCalculado();
				$TotalRep = $oOrdenTrabajo->ImporteRepuestosCalculado();
				$CostoRep = $oOrdenTrabajo->CostoRepuestosCalculado();
			}
			
			$arrData[] = array(
				trim(CambiarFecha($oFactura->Fecha)), 
				trim(ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) . ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) . ' ' .$oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				trim(number_format($oFacturaPostVenta->ImporteBruto, 2, '.', '')),
				trim(number_format($TotalMO, 2, '.', '')),
				trim(number_format($TotalRep, 2, '.', '')),
				trim(number_format($CostoRep, 2, '.', '')),
				trim(number_format($TotalRep - $CostoRep, 2, '.', '')),
				trim($oComprobante->IdEstado == ComprobanteEstados::Anulado ? 'SI' : 'NO'),
				trim(number_format($Efectivo, 2, '.', '')),
				trim(number_format($Credito, 2, '.', '')),
				trim(number_format($Debito, 2, '.', '')),
				trim(number_format($MP, 2, '.', '')),
				trim(number_format($Pendiente, 2, '.', ''))
				);
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'facturas postventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ExportReporteCuotasCsv(array $filter = NULL)
	{
		$oComprobantes 	= new Comprobantes();
		$oClientes		= new Clientes();
		$oFormasPago 	= new FormasPago();
		$oPlanesCuotas	= new PlanesCuotas();
		
		$arrFacturas = $this->GetAll($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIT", 
			'NUMERO OT',
			'IMPORTE',
			'TARJETA',
			'CUOTAS');
		
		foreach ($arrFacturas as $oFactura)
		{
			$oComprobante = $oComprobantes->GetById($oFactura->IdComprobante);
			$oCliente = $oClientes->GetById($oFactura->IdCliente);
			$oFormaPago = $oFormasPago->GetById($oFactura->IdFormaPago);
			$oPlanCuota = $oPlanesCuotas->GetById($oFactura->IdPlanCuota);
			
			$arrData[] = array(
				trim(CambiarFecha($oFactura->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				trim($oFactura->IdOrdenTrabajo ? $oFactura->IdOrdenTrabajo : 'NC'),
				trim(number_format($oFactura->ImporteBruto, 2, '.', '')),
				trim($oFormaPago->Nombre),
				trim($oPlanCuota->Nombre)
				);
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'pagos tarjeta';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>