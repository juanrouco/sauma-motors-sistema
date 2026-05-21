<?php 

require_once('class.dbaccess.php');
require_once('class.compra.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.ordenestrabajo.php');
require_once('class.ordenestrabajotareas.php');
require_once('class.articulos.php');
require_once('class.categorias.php');
require_once('class.clientes.php');
require_once('class.tallerunidades.php');
require_once('excel_export/class.xlsexport.php');

class Compras extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1=1';
		
		if ((isset($filter['IdCompra'])) && ($filter['IdCompra'] != ''))
			$sql.= " AND IdCompra = " . DB::Number($filter['IdCompra']);
			
		if ((isset($filter['FechaCargaDesde'])) && ($filter['FechaCargaDesde'] != ''))
			$sql.= " AND FechaCarga >= " . DB::Date($filter['FechaCargaDesde']);
			
		if ((isset($filter['FechaCargaHasta'])) && ($filter['FechaCargaHasta'] != ''))
			$sql.= " AND FechaCarga <= " . DB::Date($filter['FechaCargaHasta']);
			
		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND IdCliente = " . DB::Number($filter['IdCliente']);
			
		if ((isset($filter['IdTallerUnidad'])) && ($filter['IdTallerUnidad'] != ''))
			$sql.= " AND IdTallerUnidad = " . DB::Number($filter['IdTallerUnidad']);
			
		if ((isset($filter['IdOrdenTrabajo'])) && ($filter['IdOrdenTrabajo'] != ''))
			$sql.= " AND IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
			
		if ((isset($filter['IdFactura'])) && ($filter['IdFactura'] != ''))
			$sql.= " AND IdFactura = " . DB::Number($filter['IdFactura']);
			
		if ((isset($filter['IdRemito'])) && ($filter['IdRemito'] != ''))
			$sql.= " AND IdRemito = " . DB::Number($filter['IdRemito']);
			
		if ((isset($filter['TipoOperacion'])) && ($filter['TipoOperacion'] != ''))
			$sql.= " AND TipoOperacion = " . DB::Number($filter['TipoOperacion']);
			
		if ((isset($filter['IdTipoMovimiento'])) && ($filter['IdTipoMovimiento'] != ''))
			$sql.= " AND IdTipoMovimiento = " . DB::Number($filter['IdTipoMovimiento']);
			
		if ((isset($filter['CodigoRepuesto'])) && ($filter['CodigoRepuesto'] != ''))
			$sql.= " AND IdCompra IN (SELECT IdCompra FROM TB_CompraDetalles cd INNER JOIN TB_Articulos a ON a.IdArticulo = cd.IdArticulo WHERE a.Codigo LIKE '%" . DB::StringUnquoted($filter['CodigoRepuesto']) . "%')";
			
		if ((isset($filter['IdTipoVenta'])) && ($filter['IdTipoVenta'] != ''))
			$sql.= " AND IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenesTrabajoTareas WHERE IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . ")";

		
			
		if ((isset($filter['Facturado'])) && ($filter['Facturado'] != ''))
		{
			if ($filter['Facturado'] == '1')
			{
				$sql.= " AND IdCompra IN (SELECT IdCompra FROM TB_FacturasPostVentas WHERE IdCompra IS NOT NULL)";
			}
			else
			{
				$sql.= " AND IdCompra NOT IN (SELECT IdCompra FROM TB_FacturasPostVentas WHERE IdCompra IS NOT NULL)";
			}
			
		}
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCompra DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompra = new Compra();
			$oCompra->ParseFromArray($oRow);
			
			array_push($arr, $oCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllVI(array $filter = NULL)
	{
		$sql = "SELECT * FROM (SELECT SUM(cd.Cantidad * IF(c.IdTipoMovimiento = 2, -1, 1)) as CantidadTotal, cd.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON c.IdCompra = cd.IdCompra";
		$sql.= " LEFT JOIN TB_FacturasPostVentas f ON (f.IdCompra = c.IdCompra OR f.IdOrdenTrabajo = c.IdOrdenTrabajo)";
		$sql.= " LEFT JOIN TB_Comprobantes com ON f.IdComprobante = com.IdComprobante";
		$sql.= ($filter) ? $this->ParseFilter($filter) : " WHERE 1";
		$sql.= " AND (c.IdFactura IS NULL OR c.IdFactura <> -1)";
		$sql.= " AND (f.IdComprobante IS NOT NULL) AND (com.IdEstado = 2)";
		$sql.= " GROUP BY cd.IdArticulo) AS Aux";
		$sql.= " ORDER BY CantidadTotal DESC";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompraDetalle = new CompraDetalle();
			$oCompraDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oCompraDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function ParseFilterReporte(array $filter)
	{
		$sql = ' WHERE 1=1';
		
		if ((isset($filter['FechaCargaDesde'])) && ($filter['FechaCargaDesde'] != ''))
			$sql.= " AND FechaCarga >= " . DB::Date($filter['FechaCargaDesde']);
			
		if ((isset($filter['FechaCargaHasta'])) && ($filter['FechaCargaHasta'] != ''))
			$sql.= " AND FechaCarga <= " . DB::Date($filter['FechaCargaHasta']);
			
		if ((isset($filter['IdTipoVenta'])) && ($filter['IdTipoVenta'] != ''))
		{
			$sql.= " AND IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenesTrabajoTareas WHERE IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . ")";
			$sql.= " AND IdOrdenTrabajo IS NOT NULL";
		}

		return $sql;
	}


	public function GetAllReporte(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= ($filter) ? $this->ParseFilterReporte($filter) : "";
		$sql.= " ORDER BY IdOrdenTrabajo ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompra = new Compra();
			$oCompra->ParseFromArray($oRow);
			
			array_push($arr, $oCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function ParseFilterReporteRepuestosAsignados(array $filter)
	{
		$sql = ' WHERE 1=1';
		
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND ((f.Fecha IS NOT NULL AND f.Fecha >= " . DB::Date($filter['FechaDesde']) . ") OR (f.Fecha IS NULL AND ot.FechaFin >= " . DB::Date($filter['FechaDesde']) . "))";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
		$sql.= " AND ((f.Fecha IS NOT NULL AND f.Fecha <= " . DB::Date($filter['FechaHasta']) . ") OR (f.Fecha IS NULL AND ot.FechaFin <= " . DB::Date($filter['FechaHasta']) . "))";
			
		if ((isset($filter['IdTipoVenta'])) && ($filter['IdTipoVenta'] != ''))
		{
			$sql.= " AND c.IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenesTrabajoTareas WHERE IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . ")";
			$sql.= " AND c.IdOrdenTrabajo IS NOT NULL";
		}	
		if ((isset($filter['IdCategoria'])) && ($filter['IdCategoria'] != ''))
		{
			$sql.= " AND c.IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenesTrabajoTareas WHERE IdCategoria = " . DB::Number($filter['IdCategoria']) . ")";
			$sql.= " AND c.IdOrdenTrabajo IS NOT NULL";
		}

		return $sql;
	}


	public function GetAllReporteRepuestosAsignados(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON c.IdCompra = cd.IdCompra";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot ON c.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_FacturasPostVentas f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= ($filter) ? $this->ParseFilterReporteRepuestosAsignados($filter) : "";
		$sql.= " ORDER BY ot.FechaFin DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompraDetalle = new CompraDetalle();
			$oCompraDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oCompraDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	

	public function GetCountRowsReporteRepuestosAsignados(array $filter = NULL)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON c.IdCompra = cd.IdCompra";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot ON c.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_FacturasPostVentas f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= ($filter) ? $this->ParseFilterReporteRepuestosAsignados($filter) : "";
		$sql.= " ORDER BY ot.FechaFin DESC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}


	public function GetById($IdCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= " WHERE IdCompra = " . DB::Number($IdCompra);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCompra = new Compra();
		$oCompra->ParseFromArray($oRow);
		
		return $oCompra;		
	}
	
	public function GetByIdRemito($IdRemito)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= " WHERE IdRemito = " . DB::Number($IdRemito);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCompra = new Compra();
		$oCompra->ParseFromArray($oRow);
		
		return $oCompra;		
	}	
	
	public function GetByOrdenTrabajo($oOrdenTrabajo)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " INNER JOIN TB_OrdenesTrabajoTareas ott ON c.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " LEFT JOIN TB_Comprobantes cs ON cs.IdComprobante = c.IdRemito";
		$sql.= " WHERE c.IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
		$sql.= " AND (cs.IdEstado <> " . DB::Number(ComprobanteEstados::Anulado);
		$sql.= " OR cs.IdEstado IS NULL)";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompra = new Compra();
			$oCompra->ParseFromArray($oRow);

			array_push($arr, $oCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}	
	
	public function GetByOrdenTrabajoTarea($oOrdenTrabajoTarea)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " LEFT JOIN TB_Comprobantes cs ON cs.IdComprobante = c.IdRemito";
		$sql.= " WHERE c.IdOrdenTrabajoTarea = " . DB::Number($oOrdenTrabajoTarea->IdOrdenTrabajoTarea);
		$sql.= " AND (cs.IdEstado <> " . DB::Number(ComprobanteEstados::Anulado);
		$sql.= " OR cs.IdEstado IS NULL)";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompra = new Compra();
			$oCompra->ParseFromArray($oRow);

			array_push($arr, $oCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}	
	
	public function GetNextNumeroVale()
	{
		$sql = "SELECT MAX(c.NumeroVale) AS Vale";
		$sql.= " FROM TB_Compras c";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$NumeroVale = intval($oRow['Vale']);
		$NumeroVale++;
		
		return $NumeroVale;		
	}	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	public function GetCountRowsVI(array $filter = NULL)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM TB_Compras c";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON c.IdCompra = cd.IdCompra";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY cd.IdArticulo";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Compra $oCompra)
	{
		$arr = array(
			'FechaCarga' 					=> DB::Date($oCompra->FechaCarga),
			'IdUbicacion' 					=> DB::Number($oCompra->IdUbicacion),
			'TipoOperacion' 				=> DB::String($oCompra->TipoOperacion),
			'IdFactura'						=> DB::Number($oCompra->IdFactura),
			'IdRemito'						=> DB::Number($oCompra->IdRemito),
			'IdCliente'						=> DB::Number($oCompra->IdCliente),
			'IdTallerUnidad'				=> DB::Number($oCompra->IdTallerUnidad),
			'IdOrdenTrabajo'				=> DB::Number($oCompra->IdOrdenTrabajo),
			'Transporte'					=> DB::String($oCompra->Transporte),
			'TransporteClaveFiscalTipo'		=> DB::String($oCompra->TransporteClaveFiscalTipo),
			'TransporteClaveFiscalNumero'	=> DB::String($oCompra->TransporteClaveFiscalNumero),
			'IdCuponDescuento'				=> DB::Number($oCompra->IdCuponDescuento),
			'IdOrdenTrabajoTarea'			=> DB::Number($oCompra->IdOrdenTrabajoTarea),
			'IdNotaCredito'					=> DB::Number($oCompra->IdNotaCredito),
			'IdTipoMovimiento'				=> DB::Number($oCompra->IdTipoMovimiento),
			'Total'							=> DB::Number($oCompra->Total),
			'Iva21'							=> DB::Number($oCompra->Iva21),
			'Iva10'							=> DB::Number($oCompra->Iva10),
			'PercepcionIIBB'				=> DB::Number($oCompra->PercepcionIIBB)
		);
		
		if (!$this->Insert('TB_Compras', $arr))
			return false;

		/* asignamos el id generado */
		$oCompra->IdCompra = DBAccess::GetLastInsertId();
			
		return $oCompra;
	}
	
	public function Update(Compra $oCompra)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array(
			'IdNotaCredito'					=> DB::Number($oCompra->IdNotaCredito),
			'IdRemito'						=> DB::Number($oCompra->IdRemito),
			'IdFactura'						=> DB::Number($oCompra->IdFactura),
			'Total'							=> DB::Number($oCompra->Total),
			'Iva21'							=> DB::Number($oCompra->Iva21),
			'Iva10'							=> DB::Number($oCompra->Iva10),
			'PercepcionIIBB'				=> DB::Number($oCompra->PercepcionIIBB),
			'NumeroVale'					=> DB::Number($oCompra->NumeroVale)
		);

		$where = " IdCompra = " . (int)$oCompra->IdCompra;
		
		if (!DBAccess::Update('TB_Compras', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oCliente;
	}
	
	
	public function Delete($IdCompra)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCompra = " . DB::Number($IdCompra);

		if (!DBAccess::Delete('TB_CompraDetalles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_Compras', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Compras";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	
	
	public function ExportCsv(array $filter = NULL)
	{
		$oOrdenesTrabajo		= new OrdenesTrabajo();
		$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$oArticulos				= new Articulos();
		$oCompras				= new Compras();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "repuestos cargos internos.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrData 	= $this->GetAllReporteRepuestosAsignados($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Repuesto";
		$csv.= $Separador;
		$csv.= "Cantidad";
		$csv.= $Separador;
		$csv.= "Precio";
		$csv.= $Separador;
		$csv.= "Nro. OT";
		$csv.= $Separador;
		$csv.= "Dominio";
		$csv.= $Separador;
		$csv.= "Modelo";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Sector";
		$csv.= $SaltoLinea;
	
		foreach ($arrData as $oCompraDetalle)
		{				
			$oCompra 			= $this->GetById($oCompraDetalle->IdCompra);
			$oOrdenTrabajo		= $oOrdenesTrabajo->GetById($oCompra->IdOrdenTrabajo);
			$oOrdenTrabajoTarea	= $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
			$oTallerUnidad 		= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
			$oCliente 			= $oClientes->GetById($oTallerUnidad->IdCliente);
			$oArticulo 			= $oArticulos->GetById($oCompraDetalle->IdArticulo);
			$oCategoria 		= Categorias::GetById($oOrdenTrabajoTarea->IdCategoria);
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oOrdenTrabajo->FechaFin)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo . '- ' . $oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCompraDetalle->Cantidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCompraDetalle->ImporteCompraNeto));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajo->IdOrdenTrabajo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Dominio));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Modelo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCategoria['Nombre']));	
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	
	
	public function ExportsVICsv(array $filter = NULL)
	{
		$oOrdenesTrabajo		= new OrdenesTrabajo();
		$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
		$oTallerUnidades		= new TallerUnidades();
		$oClientes				= new Clientes();
		$oArticulos				= new Articulos();
		$oCompras				= new Compras();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "repuestos vendidos.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrData 	= $this->GetAllVI($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Codigo";
		$csv.= $Separador;
		$csv.= "Repuesto";
		$csv.= $Separador;
		$csv.= "Cantidad";
		$csv.= $SaltoLinea;
	
		foreach ($arrData as $oCompraDetalle)
		{				
			$oCompra 			= $this->GetById($oCompraDetalle->IdCompra);
			$oArticulo 			= $oArticulos->GetById($oCompraDetalle->IdArticulo);
			
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCompraDetalle->CantidadTotal));
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>