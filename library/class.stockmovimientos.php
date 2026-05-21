<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulo.php');
require_once('class.articulos.php');
require_once('class.ubicacion.php');
require_once('class.ubicaciones.php');
require_once('class.comprobantes.php');
require_once('class.compra.php');
require_once('class.compras.php');
require_once('class.compradetalles.php');
require_once('class.stockmovimiento.php');
require_once('class.tipoventa.php');
require_once('class.tallerunidades.php');
require_once('class.clientes.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');

class StockMovimientos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ($filter['TipoOperacion'] != null && $filter['TipoOperacion'] != "")
		{	
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($filter['TipoOperacion']);
		}
		
		if ($filter['IdArticulo'] != null && $filter['IdArticulo'] != "")
		{	
			$sql.= " AND ms.IdArticulo = " . DB::Number($filter['IdArticulo']);			
		}
		
		if ($filter['IdUbicacion'] != null && $filter['IdUbicacion'] != "")
		{	
			$sql.= " AND ms.IdUbicacion = " . DB::Number($filter['IdUbicacion']);
		}
		
		/*if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
		{	
			$sql.= " AND ms.Fecha >= " . DB::Date($filter['FechaDesde']);
		}
		
		if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
		{	
			$sql.= " AND ms.Fecha <= " . DB::Date($filter['FechaHasta']);
		}*/
		
		if ($filter['NotOrdenTrabajo'] != null && $filter['NotOrdenTrabajo'] != "")
		{
			$sql.= " AND cp.IdOrdenTrabajoTarea IS NULL";
		}
		
		if ($filter['OrdenTrabajo'] != null && $filter['OrdenTrabajo'] != "")
		{
			
			if ($filter['TipoOperacion'] != TipoVenta::Garantia && $filter['TipoOperacion'] != TipoVenta::VentaInterna && $filter['TipoOperacion'] != TipoVenta::PreEntrega)
			{
				$sql.= " AND ott.IdOrdenTrabajoTarea IS NOT NULL";
				if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
				{	
					$sql.= " AND ((f.Fecha IS NOT NULL AND f.Fecha >= " . DB::Date($filter['FechaDesde']) . ") OR (f.Fecha IS NULL AND ot.FechaInicio >= " . DB::Date($filter['FechaDesde']) . "))";
				}
				
				if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
				{	
					$sql.= " AND ((f.Fecha IS NOT NULL AND f.Fecha <= " . DB::Date($filter['FechaHasta']) . ") OR (f.Fecha IS NULL AND ot.FechaInicio <= " . DB::Date($filter['FechaHasta'] . ' 23:59') . "))";
				}
			}
			else
			{
				if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
				{	
					$sql.= " AND ot.FechaInicio >= " . DB::Date($filter['FechaDesde']);
				}
				
				if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
				{	
					$sql.= " AND ot.FechaInicio <= " . DB::Date($filter['FechaHasta'] . ' 23:59');
				}
			}
		}
		else
		{
			if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
			{	
				$sql.= " AND (f.Fecha IS NULL AND cp.FechaCarga >= " . DB::Date($filter['FechaDesde']) . " OR f.Fecha IS NOT NULL AND f.Fecha >= " . DB::Date($filter['FechaDesde']) . ")";
			}
			
			if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
			{	
				$sql.= " AND (f.Fecha IS NULL AND cp.FechaCarga <= " . DB::Date($filter['FechaHasta']) . " OR f.Fecha IS NOT NULL AND f.Fecha <= " . DB::Date($filter['FechaHasta']) . ")";
				//$sql.= " AND cp.FechaCarga <= " . DB::Date($filter['FechaHasta']);
			}
		}

		return $sql;
	}
	
	public function ParseFilterFacturado(array $filter)
	{
		$sql = '';
		
		if ($filter['TipoOperacion'] != null && $filter['TipoOperacion'] != "")
		{	
			$sql.= " AND ott.IdTipoVenta = " . DB::Number($filter['TipoOperacion']);
		}
		
		if ($filter['IdArticulo'] != null && $filter['IdArticulo'] != "")
		{	
			$sql.= " AND ms.IdArticulo = " . DB::Number($filter['IdArticulo']);			
		}
		
		if ($filter['IdUbicacion'] != null && $filter['IdUbicacion'] != "")
		{	
			$sql.= " AND ms.IdUbicacion = " . DB::Number($filter['IdUbicacion']);
		}
		
		if ($filter['IdTipoPago'] != null && $filter['IdTipoPago'] != "")
		{	
			$sql.= " AND f.IdFacturaPostVenta IN (SELECT IdFacturaPostVenta FROM TB_Pagos where IdFacturaPostVenta IS NOT NULL AND IdTipoPago = " . DB::Number($filter['IdTipoPago']) . ")";
		}
		
		if ($filter['Codigo'] != null && $filter['Codigo'] != "")
		{	
			$sql.= " AND a.Codigo LIKE '%" . DB::StringUnquoted($filter['Codigo']) . "%'";
		}
		
		if ($filter['NotOrdenTrabajo'] != null && $filter['NotOrdenTrabajo'] != "")
		{
			$sql.= " AND ott.IdOrdenTrabajoTarea IS NULL";
		}
		
		if ($filter['OrdenTrabajo'] != null && $filter['OrdenTrabajo'] != "")
		{
			
			$sql.= " AND ott.IdOrdenTrabajoTarea IS NOT NULL AND ot.IdComprobante IS NOT NULL";
			if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
			{	
				$sql.= " AND f.Fecha >= " . DB::Date($filter['FechaDesde']);
			}
				
			if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
			{	
				$sql.= " AND f.Fecha <= " . DB::Date($filter['FechaHasta']);
			}
		}
		else
		{
			if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
			{	
				$sql.= " AND f.Fecha >= " . DB::Date($filter['FechaDesde']);
			}
				
			if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
			{	
				$sql.= " AND f.Fecha <= " . DB::Date($filter['FechaHasta']);
			}
		}

		return $sql;
	}
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";		
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRow['Count'];

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	public function GetPagesCountReporte(Page $oPage, $filter = false)
	{	
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " INNER JOIN TB_Comprobantes c ON c.Numero = ms.Remito AND c.IdTipoComprobante = " . DB::Number(ComprobanteTipos::Remito);
		$sql.= " INNER JOIN TB_Compras cp ON cp.IdRemito = c.IdComprobante";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON cp.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	public function GetPagesCountReporteAjuste(Page $oPage, $filter = false)
	{	
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE (ms.Remito IS NULL OR ms.Remito = '')";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
			
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Nombre ASC, ms.Fecha DESC, ms.IdStockMovimiento DESC";		
//print_r($sql);
		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetAllAjusteReporte(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE (ms.Remito IS NULL OR ms.Remito = '')";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Nombre ASC, ms.Fecha DESC, ms.IdStockMovimiento DESC";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	public function GetAllReporteVentas(array $filter = NULL)
	{
		$sql = "SELECT IdArticulo, Cantidad";
		$sql.= " FROM (SELECT ms.IdArticulo, SUM(ms.Cantidad) AS Cantidad";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdCompra IS NOT NULL";
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY ms.IdArticulo ) AS tblAux";		
		$sql.= " ORDER BY Cantidad ASC";		


		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new stdClass();
			$oStockMovimiento->IdArticulo = $oRow['IdArticulo'];
			$oStockMovimiento->Cantidad = $oRow['Cantidad'];
			
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetAllReporte(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " INNER JOIN TB_Comprobantes c ON c.Numero = ms.Remito AND c.IdTipoComprobante = " . DB::Number(ComprobanteTipos::Remito);
		$sql.= " INNER JOIN TB_Compras cp ON cp.IdRemito = c.IdComprobante";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON cp.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Nombre ASC, ms.Fecha DESC, ms.IdStockMovimiento DESC";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetTotalReporteFacturado(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		$oReporteTotal->CostoTotalTaller = 0;
		$oReporteTotal->CostoTotalChapa = 0;
		$oReporteTotal->CostoTotalPreEntrega = 0;
		$oReporteTotal->CostoTotalAccesorios = 0;
		$oReporteTotal->CostoCompraFord = 0;
		$oReporteTotal->CostoCompraTerceros = 0;
		
		if ($filter['TipoOperacion'] != TipoVenta::Garantia && $filter['TipoOperacion'] != TipoVenta::VentaInterna)
		{
		
			$sql = " SELECT SUM(cd.Cantidad) AS CantidadTotal,";
			$sql.= " SUM(cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota)) AS CostoTotal,";
			$sql.= " SUM(cd.PrecioCompra * cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100)) AS CostoCompraTotal,";
			foreach (Categorias::GetAll() as $oCategoria)
			{
				$sql.= " SUM(IF (ott.IdCategoria = ". $oCategoria['IdCategoria'] . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotal" . $oCategoria['NombreColumna'] . ",";
				$sql.= " SUM(IF (ott.IdCategoria = ".  $oCategoria['IdCategoria'] . ", cd.PrecioCompra * cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100), 0)) AS CostoCompra" . $oCategoria['NombreColumna'] . ",";
				$sql.= " SUM(IF (ott.IdCategoria = ".  $oCategoria['IdCategoria'] . ", cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) , 0)) AS Cantidad" . $oCategoria['NombreColumna'] . ",";
			}
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::ChapaYPintura . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalChapa,";
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::ChapaYPintura . ", cd.PrecioCompra * cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoCompraChapa,";
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::PreEntrega . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalPreEntrega,";
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::PreEntrega . ", cd.PrecioCompra * cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoCompraPreEntrega,";
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::Accesorios . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalAccesorios,";
			//$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::Accesorios . ", cd.PrecioCompra * cd.Cantidad * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoCompraAccesorios,";
			$sql.= " COUNT(cp.IdCompra) AS CantidadCompras,";
			$sql.= " SUM(IF (a.IdProveedor = ". Proveedor::Ford . ", cd.PrecioCompra * cd.Cantidad, 0)) AS CostoCompraFord,";
			$sql.= " SUM(IF (a.IdProveedor <> ". Proveedor::Ford . ", cd.PrecioCompra * cd.Cantidad, 0)) AS CostoCompraTerceros";
			$sql.= " FROM TB_Compras cp";
			$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = cp.IdCompra";
			$sql.= " INNER JOIN TB_Articulos a ON cd.IdArticulo = a.IdArticulo";
			$sql.= " INNER JOIN TB_Ivas i ON a.IdIva = i.IdIva";
			$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON cp.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
			$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
			$sql.= " LEFT JOIN TB_TallerUnidades tu ON ot.IdTallerUnidad = tu.IdTallerUnidad";
			$sql.= " INNER JOIN TB_FacturasPostVentas f ON (ot.IdOrdenTrabajo = f.IdOrdenTrabajo OR (ot.IdOrdenTrabajo IS NULL AND f.IdCompra = cp.IdCompra))";
			$sql.= " WHERE (ot.IdEstadoOrden IS NULL OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
			$sql.= " AND f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL)";
			$sql.= " AND f.NumeroFactura <> '0007-00000000'";
			//$sql.= " 	WHERE (r.IdEstado <> 3;
			//$sql.= " WHERE 1=1";

			if ($filter)
				$sql.= " " . $this->ParseFilterFacturado($filter);
	/*if ($filter['TipoOperacion'] == 2) {
	print_r($sql);exit;}*/
			if ( !($oRes = $this->GetQuery($sql)) )
				return false;
				
			if (!$oRow = $oRes->GetRow())
				return false;

			$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
			$oReporteTotal->CantidadCompras = $oRow['CantidadCompras'];
			$oReporteTotal->CostoTotal = $oRow['CostoTotal'];
			$oReporteTotal->CostoCompraTotal = $oRow['CostoCompraTotal'];
			foreach (Categorias::GetAll() as $oCategoria)
			{
				$VarCosto = 'CostoTotal' . $oCategoria['NombreColumna'];
				$VarCostoCompra = 'CostoCompra' . $oCategoria['NombreColumna'];
				$VarCantidad = 'Cantidad' . $oCategoria['NombreColumna'];
				$oReporteTotal->$VarCosto = $oRow[$VarCosto];
				$oReporteTotal->$VarCostoCompra = $oRow[$VarCostoCompra];
				$oReporteTotal->$VarCantidad = $oRow[$VarCantidad];
			}
			
			$oReporteTotal->CostoCompraFord = $oRow['CostoCompraFord'];
			$oReporteTotal->CostoCompraTerceros = $oRow['CostoCompraTerceros'];
		}
		return $oReporteTotal;		
	}
	
	public function GetTotalReporte(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		$oReporteTotal->CostoTotalTaller = 0;
		$oReporteTotal->CostoTotalChapa = 0;
		$oReporteTotal->CostoTotalPreEntrega = 0;
		$oReporteTotal->CostoTotalAccesorios = 0;
		$oReporteTotal->CostoCompraFord = 0;
		$oReporteTotal->CostoCompraTerceros = 0;
		
		$sql = " SELECT SUM(cd.Cantidad) AS CantidadTotal,";
		$sql.= " SUM(cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota)) AS CostoTotal,";
		$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::Taller . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalTaller,";
		$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::ChapaYPintura . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalChapa,";
		$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::PreEntrega . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalPreEntrega,";
		$sql.= " SUM(IF (ott.IdCategoria = ". Categorias::Accesorios . ", cd.ImporteCompraNeto * IF(cp.IdTipoMovimiento = 1, 1, -1) * (1 - IF (f.Descuentos IS NULL, 0, f.Descuentos) / 100) / (1 + i.Alicuota), 0)) AS CostoTotalAccesorios,";
		$sql.= " COUNT(cp.IdCompra) AS CantidadCompras,";
		$sql.= " SUM(IF (a.IdProveedor = ". Proveedor::Ford . ", cd.PrecioCompra * cd.Cantidad, 0)) AS CostoCompraFord,";
		$sql.= " SUM(IF (a.IdProveedor <> ". Proveedor::Ford . ", cd.PrecioCompra * cd.Cantidad, 0)) AS CostoCompraTerceros";
		$sql.= " FROM TB_Compras cp";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = cp.IdCompra";
		$sql.= " INNER JOIN TB_Articulos a ON cd.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ivas i ON a.IdIva = i.IdIva";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON cp.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_TallerUnidades tu ON ot.IdTallerUnidad = tu.IdTallerUnidad";
		$sql.= " LEFT JOIN TB_FacturasPostVentas f ON (ot.IdOrdenTrabajo = f.IdOrdenTrabajo OR (ot.IdOrdenTrabajo IS NULL AND f.IdCompra = cp.IdCompra))";
		$sql.= " WHERE (ot.IdEstadoOrden IS NULL OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
		$sql.= " AND (f.IdComprobante IS NULL OR f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL))";
		//$sql.= " 	WHERE (r.IdEstado <> 3;
		//$sql.= " WHERE 1=1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
//print_r($sql);exit;
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;

		$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
		$oReporteTotal->CantidadCompras = $oRow['CantidadCompras'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];
		$oReporteTotal->CostoTotalTaller = $oRow['CostoTotalTaller'];
		$oReporteTotal->CostoTotalChapa = $oRow['CostoTotalChapa'];
		$oReporteTotal->CostoTotalPreEntrega = $oRow['CostoTotalPreEntrega'];
		$oReporteTotal->CostoTotalAccesorios = $oRow['CostoTotalAccesorios'];
		$oReporteTotal->CostoCompraFord = $oRow['CostoCompraFord'];
		$oReporteTotal->CostoCompraTerceros = $oRow['CostoCompraTerceros'];

		return $oReporteTotal;		
	}
	
	public function GetAllByUbicacion(Ubicacion $oUbicacion)
	{
		$arr = array();
	
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdUbicacion = " . DB::Number($oUbicacion->IdUbicacion);		
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByArticulo(Articulo $oArticulo)
	{
		$arr = array();
	
		$sql = "SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdArticulo = " . DB::Number($oArticulo->IdArticulo);		
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByCompra(Compra $oCompra)
	{
		$arr = array();
	
		$sql = "SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdCompra = " . DB::Number($oCompra->IdCompra);		
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oStockMovimiento = new StockMovimiento();
			$oStockMovimiento->ParseFromArray($oRow);
			
			array_push($arr, $oStockMovimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdArticuloStock)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdStockMovimiento = " . DB::Number($IdArticuloStock);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oStockMovimiento = new StockMovimiento();
		$oStockMovimiento->ParseFromArray($oRow);

		
		return $oStockMovimiento;		
	}
	
	public function GetByArticuloAndUbicacion($IdArticulo, $IdUbicacion)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdArticulo = " . DB::Number($IdArticulo);	
		$sql.= " AND ms.IdUbicacion = " . DB::Number($IdUbicacion);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oStockMovimiento = new StockMovimiento();
		$oStockMovimiento->ParseFromArray($oRow);

		
		return $oStockMovimiento;		
	}
	
	public function GetByArticuloAndUbicacionAndRemito($IdArticulo, $IdUbicacion, $Remito)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdArticulo = " . DB::Number($IdArticulo);	
		$sql.= " AND ms.IdUbicacion = " . DB::Number($IdUbicacion);
		$sql.= " AND ms.Remito = " . DB::String($Remito);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oStockMovimiento = new StockMovimiento();
		$oStockMovimiento->ParseFromArray($oRow);

		
		return $oStockMovimiento;		
	}
	
	public function GetByArticuloAndUbicacionAndIdCompra($IdArticulo, $IdUbicacion, $IdCompra)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE ms.IdArticulo = " . DB::Number($IdArticulo);	
		$sql.= " AND ms.IdUbicacion = " . DB::Number($IdUbicacion);
		$sql.= " AND ms.IdCompra = " . DB::Number($IdCompra);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oStockMovimiento = new StockMovimiento();
		$oStockMovimiento->ParseFromArray($oRow);

		
		return $oStockMovimiento;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetCountRowsReporte(array $filter = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " INNER JOIN TB_Comprobantes c ON c.Numero = ms.Remito AND c.IdTipoComprobante = " . DB::Number(ComprobanteTipos::Remito);
		$sql.= " INNER JOIN TB_Compras cp ON cp.IdRemito = c.IdComprobante";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON cp.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " LEFT JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetCountRowsReporteAjuste(array $filter = NULL)
	{
		$sql = " SELECT ms.*";
		$sql.= " FROM TB_StockMovimientos ms";
		$sql.= " INNER JOIN TB_Articulos a ON ms.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ms.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE (ms.Remito IS NULL OR ms.Remito = '')";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(StockMovimiento $oStockMovimiento)
	{
		$arr = array
		(
			'IdArticulo'				=> DB::Number($oStockMovimiento->IdArticulo),
			'IdUbicacion'				=> DB::Number($oStockMovimiento->IdUbicacion),
			'Remito'					=> DB::String($oStockMovimiento->Remito),
			'Fecha'						=> DB::Date($oStockMovimiento->Fecha),
			'Cantidad'					=> DB::Number($oStockMovimiento->Cantidad),
			'Observaciones'				=> DB::String($oStockMovimiento->Observaciones),
			'IdCompra'					=> DB::Number($oStockMovimiento->IdCompra)
		);
		return $arr;
	}
	
	public function Create(StockMovimiento $oStockMovimiento)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oStockMovimiento);

		if (!DBAccess::Insert('TB_StockMovimientos', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oStockMovimiento;
	}
	
	
	public function Update(StockMovimiento $oStockMovimiento)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetArrayDB($oStockMovimiento);

		$where = " IdStockMovimiento = " . (int)$oStockMovimiento->IdStockMovimiento;
		
		if (!DBAccess::Update('TB_StockMovimientos', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oStockMovimiento;
	}
	/*
	public function ChangePassword(Proveedor $oProveedor)
	{
		$where = " IdProveedor = " . (int)$oProveedor->IdProveedor;
		
		$arr = array('Contrasenia' => DB::String(md5($oProveedor->Contrasenia)));
		
		if (!DBAccess::Update('TB_Proveedores', $arr, $where))
			return false;
		
		return $oProveedor;
	}
	*/
	
	public function Delete($IdStockMovimiento)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdStockMovimiento = " . DB::Number($IdStockMovimiento);
		if (!DBAccess::Delete('TB_StockMovimientos', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
		
	public function ExportToPDF($outputPdf = '', array $filter = NULL)
	{
		$pdf = new FPDF('P', 'cm', 'A4');
		$arr = array();
		
		$arr = $this->GetAll($filter);
		$paginaActual = 0;
		$cont = 50;
		$x = 0;
		$y = 3;
		
		foreach ($arr as $oProveedor)
		{	  
			if ($cont == 50)
			{
				$pdf->AddPage();
				$paginaActual++;
				$cont = 0;
				$y = 3;

				$pdf->SetFont('Arial','B', 15);
				$pdf->Text($x + 8, $y - 2, "Reporte de Proveedores");

				$pdf->SetFont('Arial','B', 6.5);				
				$pdf->Text($x + 1, $y - 1, "Apellido y Nombre");
				$pdf->Text($x + 4.8, $y - 1, "Proveedor");
				$pdf->Text($x + 7.4, $y - 1, "Cuit / Cuil");
				$pdf->Text($x + 12.5, $y - 1, "Email");
				$pdf->Text($x + 16.6, $y - 1, "Telefono");
				
				$pdf->SetFont('Arial','B', 9);
				$pdf->Text($x + 18.5, $y + 25.6, "Pagina ".$paginaActual);				
			}

			$pdf->SetFont('Arial', 'B', 6.1);
			$pdf->Text($x + 1, $y, $oProveedor->Apellido . ", " . $oProveedor->Nombre);
			$pdf->Text($x + 4.8, $y, $oProveedor->Proveedor);
			$pdf->Text($x + 7.4, $y, $oProveedor->CuitCuil);
			$pdf->Text($x + 12.5, $y, $oProveedor->Email);
			$pdf->Text($x + 16.6, $y, $oProveedor->Telefono);
			
			$cont++;
			$y+=0.5;
		}
		
		$pdf->Output($outputPdf);	
	}
	
	
	public function ExportCsv(array $filter = NULL)
	{
		$oArticulos			= new Articulos();
		$oUbicaciones		= new Ubicaciones();
		$oComprobantes 		= new Comprobantes();
		$oCompras			= new Compras();
		$oCompraDetalles	= new CompraDetalles();
		$oTallerUnidades	= new TallerUnidades();
		$oClientes			= new Clientes();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$arr 			= $this->GetAllReporte($filter);
		$oReporteTotal 	= $this->GetTotalReporte($filter);
		
		$FileName = "reporte.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Datos Totales";
		$csv.= $SaltoLinea;
		$csv.= "Cantidad Total de Repuestos";
		$csv.= $Separador;
		$csv.= $oReporteTotal->CantidadTotal < 0 ? $oReporteTotal->CantidadTotal * -1 : $oReporteTotal->CantidadTotal;
		$csv.= $SaltoLinea;
		$csv.= "Costo Total de Repuestos";
		$csv.= $Separador;
		$csv.= number_format($oReporteTotal->CostoTotal, 2);
		$csv.= $SaltoLinea;
				
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Codigo";
		$csv.= $Separador;
		$csv.= "Articulo";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Remito";
		$csv.= $Separador;
		$csv.= "Cliente/Unidad";
		$csv.= $Separador;
		
		if ($filter['TipoOperacion'] != TipoVenta::Mostrador)
		{
			$csv.= "Nro. OT";		
			$csv.= $Separador;
		}
		
		$csv.= "Cantidad";
		$csv.= $Separador;
		$csv.= "Costo";		
		$csv.= $SaltoLinea;
	
		foreach ($arr as $oStockMovimiento)
		{				
			$oArticulo = $oArticulos->GetById($oStockMovimiento->IdArticulo);
			$oUbicacion	= $oUbicaciones->GetById($oStockMovimiento->IdUbicacion);
			$oRemito = $oComprobantes->GetByNumero(ComprobanteTipos::Remito, $oStockMovimiento->Remito);
			$oCompra = $oCompras->GetByIdRemito($oRemito->IdComprobante);
			
			$UnidadCliente = 'N/C';
			$oCompraDetalle = $oCompraDetalles->GetById($oCompra->IdCompra, $oArticulo->IdArticulo);
			if ($oStockMovimiento->Cantidad <= 0)
			{
				
				if ($oCompra->TipoOperacion == TipoVenta::Mostrador)
				{
					$oCliente = $oClientes->GetById($oCompra->IdCliente);
					if ($oCliente)
						$UnidadCliente		= $oCliente->GetUsuario();
				}
				else
				{
					$oTallerUnidad = $oTallerUnidades->GetById($oCompra->IdTallerUnidad);
					$UnidadCliente		= $oTallerUnidad->Dominio;
				}
			}
		
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oStockMovimiento->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUbicacion->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oStockMovimiento->Remito));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($UnidadCliente));
			$csv.= $Separador;
			if ($filter['TipoOperacion'] != TipoVenta::Mostrador)
			{
				$csv.= str_replace('(\t|\n)','', trim($oCompra->IdOrdenTrabajo));
				$csv.= $Separador;
			}
			$csv.= str_replace('(\t|\n)','', trim($oStockMovimiento->Cantidad < 0 ? ($oStockMovimiento->Cantidad * -1) : $oStockMovimiento->Cantidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCompraDetalle->ImporteCompraNeto));
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function ExportReporteVentasCsv(array $filter = NULL)
	{
		$oArticulos			= new Articulos();
		$oArticulosStock	= new ArticuloStocks();
		$oComprobantes 		= new Comprobantes();
		$oCompras			= new Compras();
		$oCompraDetalles	= new CompraDetalles();
		$oTallerUnidades	= new TallerUnidades();
		$oClientes			= new Clientes();
		
		$arr = $this->GetAllReporteVentas($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
		"Codigo",
		"Repuesto",
		"Stock Actual",
		"Cantidad Vendida"
		);
		
		foreach ($arr as $oStockMovimiento)
		{				
			$oArticulo = $oArticulos->GetById($oStockMovimiento->IdArticulo);
			$oArticuloStock = $oArticulosStock->GetByArticuloAndUbicacion($oStockMovimiento->IdArticulo, Ubicacion::Libertador);
		
			$arrData[] = array(
			trim($oArticulo->Codigo),
			trim($oArticulo->Descripcion),
			trim($oArticuloStock->StockActual),
			trim($oStockMovimiento->Cantidad * -1)
			);		
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'reporte_ventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
		
		return true;	
	}
	
	public function ExportAjustesCsv(array $filter = NULL)
	{
		$oArticulos			= new Articulos();
		$oUbicaciones		= new Ubicaciones();
		$oComprobantes 		= new Comprobantes();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$arr 			= $this->GetAllAjusteReporte($filter);
		
		$FileName = "reporte ajustes.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
						
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Codigo";
		$csv.= $Separador;
		$csv.= "Articulo";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Cantidad";
		$csv.= $Separador;
		$csv.= "Observaciones";		
		$csv.= $SaltoLinea;
	
		foreach ($arr as $oStockMovimiento)
		{				
			$oArticulo = $oArticulos->GetById($oStockMovimiento->IdArticulo);
			$oUbicacion	= $oUbicaciones->GetById($oStockMovimiento->IdUbicacion);
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oStockMovimiento->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUbicacion->Nombre));
			$csv.= $Separador;			
			$csv.= str_replace('(\t|\n)','', trim($oStockMovimiento->Cantidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCompraDetalle->Observaciones));
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>