<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajo.php');
require_once('class.modelos.php');
require_once('class.tipoventa.php');
require_once('class.filter.php');
require_once('class.estadosorden.php');
require_once('class.page.php');
require_once('class.usuarios.php');
require_once('excel_export/class.xlsexport.php');


class OrdenesTrabajo extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND ot.Fecha >= " . DB::Date($filter['FechaDesde']);
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND ot.Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaInicio'])) && ($filter['FechaInicio'] != ''))
			$sql.= " AND ot.FechaInicio = " . DB::Date($filter['FechaInicio']);
			
		if ((isset($filter['FechaInicioDesde'])) && ($filter['FechaInicioDesde'] != ''))
			$sql.= " AND ot.FechaInicio >= " . DB::Date($filter['FechaInicioDesde']);
		
		if ((isset($filter['FechaInicioHasta'])) && ($filter['FechaInicioHasta'] != ''))
			$sql.= " AND ot.FechaInicio < " . DB::Date($filter['FechaInicioHasta'] . ' 23:59');
			
		if ((isset($filter['FechaFinDesde'])) && ($filter['FechaFinDesde'] != ''))
			$sql.= " AND ot.FechaFin >= " . DB::Date($filter['FechaFinDesde']);
		
		if ((isset($filter['FechaFinHasta'])) && ($filter['FechaFinHasta'] != ''))
			$sql.= " AND ot.FechaFin < " . DB::Date($filter['FechaFinHasta'] . ' 23:59');
		
		if ((isset($filter['FechaFin'])) && ($filter['FechaFin'] != ''))
			$sql.= " AND ot.FechaFin = " . DB::Date($filter['FechaFin']);
			
		if ((isset($filter['IdUsuarioAsignado'])) && ($filter['IdUsuarioAsignado'] != ''))
			$sql.= " AND ot.IdUsuarioAsignado = " . DB::Number($filter['IdUsuarioAsignado']);

		if ((isset($filter['IdEstadoOrden'])) && ($filter['IdEstadoOrden'] != ''))
			$sql.= " AND ot.IdEstadoOrden = " . DB::Number($filter['IdEstadoOrden']);
			
		if ((isset($filter['EnTaller'])) && ($filter['EnTaller'] != ''))
			$sql.= " AND (ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Aceptada) . " OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Auditoria) . ")";

		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND tu.Dominio LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";
			
		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
			$sql.= " AND tu.Modelo LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%'";
			
		if ((isset($filter['IdTipoVenta'])) && $filter['IdTipoVenta'] != '')
		{
			if (intval($filter['IdTipoVenta']) == 0)
				$sql.= " AND (ot.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number(TipoVenta::OrdenReparacion) . ")";
			else
				$sql.= " AND (ot.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . ")";
		}
		
		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			
		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND tu.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
		
		if ((isset($filter['IdOrdenTrabajo'])) && ($filter['IdOrdenTrabajo'] != ''))		
			$sql.= " AND ot.IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
			
		if ((isset($filter['IdOrdenTrabajoLike'])) && ($filter['IdOrdenTrabajoLike'] != ''))
			$sql.= " AND ot.IdOrdenTrabajo LIKE '%" . DB::Number($filter['IdOrdenTrabajoLike']) . "%'";
			
		if ((isset($filter['RepuestosAsignados'])) && ($filter['RepuestosAsignados'] != ''))
			$sql.= " AND ot.`IdOrdenTrabajo` IN (SELECT IdOrdenTrabajo FROM tb_Compras)";
			
		if ((isset($filter['Bahia'])) && ($filter['Bahia'] != ''))
			$sql.= " AND ot.`Bahia` = " . DB::Bool($filter['Bahia']);
			
		if ((isset($filter['Facturado'])) && ($filter['Facturado'] != ''))
			if ($filter['Facturado'] == 1)
				$sql.= " AND ot.`IdOrdenTrabajo` IN (SELECT IdOrdenTrabajo FROM TB_FacturasPostVentas WHERE IdOrdenTrabajo IS NOT NULL)";
			else
				$sql.= " AND ot.`IdOrdenTrabajo` NOT IN (SELECT IdOrdenTrabajo FROM TB_FacturasPostVentas WHERE IdOrdenTrabajo IS NOT NULL)";
			
		if ((isset($filter['Factura'])) && ($filter['Factura'] != ''))
		{
			if (strpos($filter['Factura'],'-') !== false)
			{
				$f = explode('-', $filter['Factura']);
				$sql.= " AND f.`Prefijo` LIKE '%" . DB::StringUnquoted($f[0]) . "%'";
				$sql.= " AND f.`Numero` LIKE '%" . DB::StringUnquoted($f[1]) . "%'";
			}
			else
			{
				$sql.= " AND (f.`Prefijo` LIKE '%" . DB::StringUnquoted($filter['Factura']) . "%'";
				$sql.= " OR f.`Numero` LIKE '%" . DB::StringUnquoted($filter['Factura']) . "%')";
			}
			
		}

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Comprobantes f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY ot.IdOrdenTrabajo";
		$sql.= " ORDER BY ot.IdOrdenTrabajo DESC, ot.Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajo = new OrdenTrabajo();
			$oOrdenTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrderByIngreso(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Comprobantes f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " WHERE ot.FechaInicio IS NOT NULL";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY ot.IdOrdenTrabajo";
		$sql.= " ORDER BY ot.FechaInicio ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajo = new OrdenTrabajo();
			$oOrdenTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllFacturados()
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " WHERE ot.IdEstadoOrden = 12 and ot.IdComprobante IS NOT NULL";
		$sql.= " ORDER BY ot.Fecha DESC, ot.IdOrdenTrabajo";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajo = new OrdenTrabajo();
			$oOrdenTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllPreentregaByEstado($IdEstadoOrden, $Cantidad = 10)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1=1";
		$sql.= " AND ot.IdEstadoOrden = " . DB::Number($IdEstadoOrden);
		$sql.= " AND (ot.IdTipoVenta = " . DB::Number(TipoVenta::PreEntrega);
		$sql.= " OR ott.IdTipoVenta = " . DB::Number(TipoVenta::PreEntrega) . ")";
		$sql.= " GROUP BY ot.IdOrdenTrabajo";
		$sql.= " ORDER BY ot.Fecha, ot.IdOrdenTrabajo";
		$sql.= " LIMIT " . DB::Number($Cantidad);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajo = new OrdenTrabajo();
			$oOrdenTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}	
	
	public function GetAllByEstado($IdEstadoOrden, $Cantidad = 10)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1=1";
		$sql.= " AND ot.IdEstadoOrden = " . DB::Number($IdEstadoOrden);
		$sql.= " ORDER BY ot.Fecha, ot.IdOrdenTrabajo";
		$sql.= " LIMIT " . DB::Number($Cantidad);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajo = new OrdenTrabajo();
			$oOrdenTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}	
	
	public function GetById($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajo";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajo = new OrdenTrabajo();
		$oOrdenTrabajo->ParseFromArray($oRow);
		
		return $oOrdenTrabajo;		
	}
		
	public function GetLastByIdTallerUnidad($IdTallerUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajo";
		$sql.= " WHERE IdTallerUnidad = " . DB::Number($IdTallerUnidad);	
		$sql.= " AND IdEstadoOrden = " . DB::Number(EstadoOrden::Aceptada);	
		$sql.= " ORDER BY IdOrdenTrabajo";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajo = new OrdenTrabajo();
		$oOrdenTrabajo->ParseFromArray($oRow);
		
		return $oOrdenTrabajo;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " LEFT JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Comprobantes f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY ot.IdOrdenTrabajo";
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(OrdenTrabajo $oOrdenTrabajo)
	{
		$arr = array
		(
			'IdEstadoOrden' 	=> DB::Number($oOrdenTrabajo->IdEstadoOrden),
			'IdTallerUnidad' 	=> DB::Number($oOrdenTrabajo->IdTallerUnidad),
			'Fecha' 			=> DB::Date($oOrdenTrabajo->Fecha),
			'FechaInicio' 		=> DB::Date($oOrdenTrabajo->FechaInicio),
			'FechaFin' 			=> DB::Date($oOrdenTrabajo->FechaFin),
			'IdUsuarioCreacion' => DB::Number($oOrdenTrabajo->IdUsuarioCreacion),
			'IdUsuarioAsignado' => DB::Number($oOrdenTrabajo->IdUsuarioAsignado),
			'Kilometros'		=> DB::Number($oOrdenTrabajo->Kilometros),
			'Comentarios'		=> DB::String($oOrdenTrabajo->Comentarios),
			'IdTipoVenta'		=> DB::Number($oOrdenTrabajo->IdTipoVenta),
			'IdComprobante'		=> DB::Number($oOrdenTrabajo->IdComprobante),
			'Bahia'				=> DB::Bool($oOrdenTrabajo->Bahia)
		);
		return $arr;
	}
	
	public function GetHorasEntreFechas($FechaInicio, $FechaFin)
	{
		$sql = "SELECT SUM(ott.HorasEstimadas) AS HourCount";
		$sql.= " FROM tb_OrdenesTrabajo ot";
		$sql.= " INNER JOIN tb_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo =  ott.IdOrdenTrabajo";
		$sql.= " WHERE ot.FechaFin >= " . DB::Date($FechaInicio);
		$sql.= " AND ot.FechaFin <= " . DB::Date($FechaFin);
		$sql.= " AND (ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Aceptada);
		$sql.= " OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['HourCount'];
		
		return $Count;
	}
	
	public function Create(OrdenTrabajo $oOrdenTrabajo)
	{
		$arr = $this->GetArrayDB($oOrdenTrabajo);
		
		if (!$this->Insert('TB_OrdenesTrabajo', $arr))
			return false;

		/* asignamos el id generado */
		$oOrdenTrabajo->IdOrdenTrabajo = DBAccess::GetLastInsertId();
			
		return $oOrdenTrabajo;
	}
	
	
	public function Update(OrdenTrabajo $oOrdenTrabajo)
	{
		$where = " IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
		
		$arr = $this->GetArrayDB($oOrdenTrabajo);
		
		if (!DBAccess::Update('TB_OrdenesTrabajo', $arr, $where))
			return false;
		
		return $oOrdenTrabajo;
	}

	public function Delete($IdOrdenTrabajo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);

		if (!DBAccess::Delete('TB_OrdenesTrabajo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function GetReporteHoras($fechaDesde, $fechaHasta)
	{
	
	/*if ($IdTipoVenta != TipoVenta::Garantia)
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " AND tu.IdCliente NOT IN (827, 703, 716)";
		else
			$sql.= " 	AND (ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " OR tu.IdCliente IN (827, 703, 716))";*/
	
	
		$sql = "SELECT u.IdUsuario,";
		$sql.= " SUM(TIME_TO_SEC(oth.Tiempo) / 3600) AS Horas,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::Garantia) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasGarantia,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::OrdenReparacion) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasOR,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::PreEntrega) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasPreEntrega,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::VentaInterna) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasVentaInterna,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::ChapaYPintura) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasChapaYPintura,";
		$sql.= " SUM(IF(ott.IdTipoVenta = " . DB::Number(TipoVenta::Accesorios) . ", TIME_TO_SEC(oth.Tiempo) / 3600, 0)) AS HorasAccesorios";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " INNER JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_OrdenTrabajoHitos oth ON ott.IdOrdenTrabajoTarea = oth.IdOrdenTrabajoTarea aND oth.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_Usuarios u ON oth.IdUsuario = u.IdUsuario";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON ot.IdTallerUnidad = tu.IdTallerUnidad";
		$sql.= " WHERE oth.FechaHora >= " . DB::Date($fechaDesde);
		$sql.= " AND oth.FechaHora <= " . DB::Date($fechaHasta . ' 23:59');
		//$sql.= " AND oth.IdOrdenTrabajo IN (SELECT IdOrdenTrabajo FROM TB_FacturasPostVentas where IdComprobante IS NOT NULL and IdOrdenTrabajo IS NOT NULL)";
		$sql.= " GROUP BY u.IdUsuario";
		$sql.= " ORDER BY Horas";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oReporte = new stdClass();
			$oReporte->IdUsuario = $oRow['IdUsuario'];
			$oReporte->Horas = $oRow['Horas'];
			$oReporte->HorasGarantia = $oRow['HorasGarantia'];
			$oReporte->HorasOR = $oRow['HorasOR'];
			$oReporte->HorasPreEntrega = $oRow['HorasPreEntrega'];
			$oReporte->HorasVentaInterna = $oRow['HorasVentaInterna'];
			
			array_push($arr, $oReporte);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetReporteOTFacturadas($IdTipoVenta, $fechaDesde, $fechaHasta, $IdCategoria = null, $IdUsuario = null, $IdAsesor = null)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->TotalManoObra = 0;
		$oReporteTotal->TotalRepuestos = 0;
		$oReporteTotal->CantidadOT = 0;
		
		if ($IdTipoVenta == TipoVenta::OrdenReparacion)
		{
			$sql = "SELECT SUM(Interes + IF (ImporteFranquicia IS NULL, IF (ImporteFranquicia2 IS NULL, (Importe / 1.21 - IF (TotalRepuestos IS NULL, IF (PrecioArticulos IS NULL, 0, PrecioArticulos), TotalRepuestos / 1.21)) * IF (Descuentos IS NULL, 0, (100 - Descuentos) / 100), ManoObraFactura - Interes), ImporteNeto - Interes) * Indice) AS ManoObra, ";
			$sql.= " SUM(IF (PrecioArticulos IS NULL OR ImporteFranquicia IS NOT NULL, 0, IF (ImporteFranquicia2 IS NULL, IF(TotalRepuestos IS NULL, PrecioArticulos, TotalRepuestos/1.21) * IF (Descuentos IS NULL, 0, (100 - Descuentos) / 100) * Indice, ImporteNeto - ManoObraFactura))) AS Repuestos,";
			$sql.= " SUM(IF (CostoArticulos IS NULL OR ImporteFranquicia IS NOT NULL, 0, IF (ImporteFranquicia2 IS NULL, CostoArticulos * Indice, (ImporteNeto - ManoObraFactura) / 1.3))) AS CostoRepuestos";
			$sql.= " FROM";
			$sql.= " (";
			$sql.= " 	SELECT ott.*, IF(oth2.Tiempo IS NULL OR oth.Tiempo IS NULL, 1, oth2.Tiempo / oth.Tiempo) AS Indice, SUM(cd.PrecioCompra * cd.Cantidad * IF(c.IdTipoMovimiento = 1, 1, -1) / (1 + i.Alicuota)) AS CostoArticulos, SUM(cd.ImporteCompraNeto * IF(c.IdTipoMovimiento = 1, 1, -1) / (1 + i.Alicuota)) AS PrecioArticulos, f.Descuentos, f.ImporteNeto, otf.Importe AS ImporteFranquicia, otf2.Importe AS ImporteFranquicia2, IF (fi.ManoObra IS NULL OR ott.Terceros = 1, 0, fi.ManoObra) AS ManoObraFactura, IF (fii.Interes IS NOT NULL, fii.Interes/otc.CantidadTareas, 0) AS Interes";
			$sql.= " 	FROM ";
			$sql.= " 	(";
			$sql.= " 		SELECT aott.*, SUM(IF (com.Total IS NULL, 0, com.Total)) AS PrecioTareaArticulos";
			$sql.= " 		FROM tb_OrdenesTrabajoTareas aott";
			$sql.= " 		LEFT JOIN tb_Compras com ON aott.IdOrdenTrabajoTarea = com.IdOrdenTrabajoTarea";
			$sql.= " 		WHERE aott.Terceros = 0";
			$sql.= " 		GROUP BY aott.IdOrdenTrabajoTarea";
			$sql.= " 	) AS ott";
			$sql.= " 	INNER JOIN tb_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
			$sql.= " 	INNER JOIN tb_FacturasPostVentas f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= "	INNER JOIN (SELECT IdOrdenTrabajo, COUNT(*) AS CantidadTareas FROM tb_OrdenesTrabajoTareas GROUP BY IdOrdenTrabajo) AS otc ON otc.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= " 	LEFT JOIN (SELECT IdFactura, SUM(ImporteNeto) AS ManoObra FROM TB_FacturasItems WHERE IdArticulo IS NULL AND Descripcion <> 'REPUESTOS' GROUP BY IdFactura) AS fi ON f.IdFacturaPostVenta = fi.IdFactura";
			$sql.= " 	LEFT JOIN (SELECT IdFactura, SUM(ImporteNeto) AS Interes FROM TB_FacturasItems WHERE IdArticulo IS NULL AND Descripcion LIKE '%INTERES%' GROUP BY IdFactura) AS fii ON f.IdFacturaPostVenta = fii.IdFactura";
			$sql.= "	LEFT JOIN TB_OrdenesTrabajoFranquicias otf ON otf.IdFactura = f.IdFacturaPostVenta";
			$sql.= "	LEFT JOIN TB_OrdenesTrabajoFranquicias otf2 ON otf2.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= "	LEFT JOIN (";
			$sql.= "		SELECT SUM(Tiempo) AS Tiempo, IdOrdenTrabajoTarea FROM TB_OrdenTrabajoHitos WHERE TipoHito <> 1 GROUP BY IdOrdenTrabajoTarea";
			$sql.= "	) AS oth ON ott.IdOrdenTrabajoTarea = oth.IdOrdenTrabajoTarea";
			$sql.= "	LEFT JOIN (";
			$sql.= "		SELECT SUM(Tiempo) AS Tiempo, IdOrdenTrabajoTarea FROM TB_OrdenTrabajoHitos WHERE TipoHito <> 1";
			if ($IdUsuario)
			{
				$sql.= "		AND IdUsuario = " . DB::Number($IdUsuario);
			}
			$sql.= "		GROUP BY IdOrdenTrabajoTarea";
			$sql.= "	) AS oth2 ON ott.IdOrdenTrabajoTarea = oth2.IdOrdenTrabajoTarea";
			$sql.= " 	LEFT JOIN tb_Compras c ON c.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
			$sql.= " 	LEFT JOIN tb_CompraDetalles cd ON c.IdCompra = cd.IdCompra ";
			$sql.= " 	LEFT JOIN tb_Articulos a ON cd.IdArticulo = a.IdArticulo";
			$sql.= " 	LEFT JOIN tb_Ivas i ON a.IdIva = i.IdIva";
			
			$sql.= " 	WHERE (f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL))";
			$sql.= "	AND f.IdComprobante IS NOT NULL AND f.NumeroFactura <> ''";
			$sql.= " 	AND (ot.IdEstadoOrden IS NULL OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta);
			
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND f.Fecha >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND f.Fecha <= " . DB::Date($fechaHasta);
				
			if ($IdAsesor)
				$sql.= " 	AND ot.IdUsuarioAsignado = " . DB::Number($IdAsesor);
				
			if ($IdCategoria)
				$sql.= " 	AND ott.IdCategoria = " . DB::Number($IdCategoria);
				
			if ($IdUsuario)
			{
				$sql.= " 	AND ott.IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenTrabajoHitos WHERE IdUsuario = " . DB::Number($IdUsuario);
					
				/*if ($fechaDesde && $fechaDesde != '')
					$sql.= " 	AND FechaHora >= " . DB::Date($fechaDesde);
				if ($fechaHasta && $fechaHasta != '')
					$sql.= " 	AND FechaHora <= " . DB::Date($fechaHasta);*/
				$sql.= ")";
			}
			
			$sql.= " 	GROUP BY ott.IdOrdenTrabajoTarea, f.IdFacturaPostVenta";
			$sql.= " ) AS rep";
			
			
			//print_r($sql);exit;
			if ( !($oRes = $this->GetQuery($sql)) )
				return false;
				
			if (!$oRow = $oRes->GetRow())
				return false;
			
			
			$sql = "SELECT SUM(Interes + IF (ImporteFranquicia IS NULL, IF (ImporteFranquicia2 IS NULL, (Importe / 1.21) * IF (Descuentos IS NULL, 0, (100 - Descuentos) / 100), ManoObraFactura - Interes), ImporteNeto - Interes)) AS ManoObra, ";
			$sql.= " SUM(CostoTotal / 1.21) AS Costo";
			$sql.= " FROM";
			$sql.= " (";
			$sql.= " 	SELECT ott.*, f.Descuentos, f.ImporteNeto, otf.Importe AS ImporteFranquicia, otf2.Importe AS ImporteFranquicia2, IF (fi.ManoObra IS NULL OR ott.Terceros = 1, 0, fi.ManoObra) AS ManoObraFactura, IF (fii.Interes IS NOT NULL, fii.Interes/otc.CantidadTareas, 0) AS Interes";
			$sql.= " 	FROM ";
			$sql.= " 	(";
			$sql.= " 		SELECT aott.*";
			$sql.= " 		FROM tb_OrdenesTrabajoTareas aott";
			$sql.= " 		WHERE aott.Terceros = 1";
			$sql.= " 		GROUP BY aott.IdOrdenTrabajoTarea";
			$sql.= " 	) AS ott";
			$sql.= " 	INNER JOIN tb_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
			$sql.= " 	INNER JOIN tb_FacturasPostVentas f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= "	INNER JOIN (SELECT IdOrdenTrabajo, COUNT(*) AS CantidadTareas FROM tb_OrdenesTrabajoTareas GROUP BY IdOrdenTrabajo) AS otc ON otc.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= " 	LEFT JOIN (SELECT IdFactura, SUM(ImporteNeto) AS ManoObra FROM TB_FacturasItems WHERE IdArticulo IS NULL AND Descripcion <> 'REPUESTOS' GROUP BY IdFactura) AS fi ON f.IdFacturaPostVenta = fi.IdFactura";
			$sql.= " 	LEFT JOIN (SELECT IdFactura, SUM(ImporteNeto) AS Interes FROM TB_FacturasItems WHERE IdArticulo IS NULL AND Descripcion LIKE '%INTERES%' GROUP BY IdFactura) AS fii ON f.IdFacturaPostVenta = fii.IdFactura";
			$sql.= "	LEFT JOIN TB_OrdenesTrabajoFranquicias otf ON otf.IdFactura = f.IdFacturaPostVenta";
			$sql.= "	LEFT JOIN TB_OrdenesTrabajoFranquicias otf2 ON otf2.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			
			$sql.= " 	WHERE (f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL))";
			$sql.= "	AND f.IdComprobante IS NOT NULL AND f.NumeroFactura <> ''";
			$sql.= " 	AND (ot.IdEstadoOrden IS NULL OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta);
			
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND f.Fecha >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND f.Fecha <= " . DB::Date($fechaHasta);
				
			if ($IdAsesor)
				$sql.= " 	AND ot.IdUsuarioAsignado = " . DB::Number($IdAsesor);
				
			if ($IdCategoria)
				$sql.= " 	AND ott.IdCategoria = " . DB::Number($IdCategoria);
				
			if ($IdUsuario)
			{
				$sql.= " 	AND ott.IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenTrabajoHitos WHERE IdUsuario = " . DB::Number($IdUsuario);
					
				/*if ($fechaDesde && $fechaDesde != '')
					$sql.= " 	AND FechaHora >= " . DB::Date($fechaDesde);
				if ($fechaHasta && $fechaHasta != '')
					$sql.= " 	AND FechaHora <= " . DB::Date($fechaHasta);*/
				$sql.= ")";
			}
			
			$sql.= " 	GROUP BY ott.IdOrdenTrabajoTarea, f.IdFacturaPostVenta";
			$sql.= " ) AS rep";
			
			
			//print_r($sql);exit;
			if ( !($oRes = $this->GetQuery($sql)) )
				return false;
				
			if (!$oRowT = $oRes->GetRow())
				return false;
				
			$sql = " 	SELECT COUNT(DISTINCT(f.IdOrdenTrabajo)) AS CantidadOT";
			$sql.= " 	FROM TB_OrdenesTrabajoTareas ott";
			$sql.= " 	INNER JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
			$sql.= " 	INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
			$sql.= " 	INNER JOIN TB_FacturasPostVentas f ON f.IdOrdenTrabajo = ot.IdOrdenTrabajo";
			$sql.= " 	WHERE ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado);
			$sql.= " 	AND f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito WHERE IdFactura IS NOT NULL)";
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta);
			
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND f.Fecha >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND f.Fecha <= " . DB::Date($fechaHasta);
			if ($IdUsuario)
			{
				$sql.= "		AND ott.IdOrdenTrabajoTarea IN (SELECT IdOrdenTrabajoTarea FROM TB_OrdenTrabajoHitos WHERE IdUsuario = " . DB::Number($IdUsuario) . ")";
			}
			if ($IdAsesor)
			{
				$sql.= "		AND ot.IdUsuarioAsignado = " . DB::Number($IdAsesor);
			}
			if ($IdCategoria)
				$sql.= " 	AND ott.IdCategoria = " . DB::Number($IdCategoria);
			
			if ( !($oRes1 = $this->GetQuery($sql)) )
				return false;
				
			if (!$oRow1 = $oRes1->GetRow())
				return false;
				
			$oReporteTotal->TotalManoObra = $oRow['ManoObra'];
			$oReporteTotal->TotalTerceros = $oRowT['ManoObra'];
			$oReporteTotal->CostoTerceros = $oRowT['Costo'];
			$oReporteTotal->TotalRepuestos = $oRow['Repuestos'];
			$oReporteTotal->CostoRepuestos = $oRow['CostoRepuestos'];
			$oReporteTotal->CantidadOT = $oRow1['CantidadOT'];
		}
		else
		{
			if (($IdTipoVenta == TipoVenta::Garantia && ($IdCategoria == null || $IdCategoria == Categorias::Taller)) || ($IdTipoVenta == TipoVenta::DaniosYFaltantes  && ($IdCategoria == null || $IdCategoria == Categorias::PreEntrega)))
			{
				
				$sql = 'SELECT SUM(fi.ImporteNeto) AS Repuestos';
				$sql.= ' FROM TB_FacturasPostVentas f';
				$sql.= ' INNER JOIN TB_FacturasItems fi ON f.IdFacturaPostVenta = fi.IdFactura';
				$sql.= ' WHERE f.IdOrdenTrabajo IS NULL';
				$sql.= ' AND f.IdCompra IS NULL';
				$sql.= ' AND f.IdTipoVenta = ' . DB::Number($IdTipoVenta);
				$sql.= ' AND fi.IdArticulo IS NOT NULL';
				$sql.= " AND fi.IdTipo <> 'TER'";
				$sql.= " AND f.IdComprobante IS NOT NULL AND f.NumeroFactura <> ''";
				if ($fechaDesde != '')
					$sql.= ' AND f.Fecha >= ' . DB::Date($fechaDesde);
				if ($fechaHasta != '')
					$sql.= ' AND f.Fecha <= ' . DB::Date($fechaHasta . ' 23:59:59');
				
				if ( !($oRes = $this->GetQuery($sql)) )
					return false;
						
				if (!$oRow = $oRes->GetRow())
					return false;
					
				$oReporteTotal->TotalRepuestos = $oRow['Repuestos'];
			}
			
			if (($IdTipoVenta == TipoVenta::Garantia && ($IdCategoria == null || $IdCategoria == Categorias::Taller)) || ($IdTipoVenta == TipoVenta::DaniosYFaltantes  && ($IdCategoria == null || $IdCategoria == Categorias::ChapaYPintura)))
			{
				
				$sql = 'SELECT SUM(fi.ImporteNeto) AS ManoObra';
				$sql.= ' FROM TB_FacturasPostVentas f';
				$sql.= ' INNER JOIN TB_FacturasItems fi ON f.IdFacturaPostVenta = fi.IdFactura';
				$sql.= ' WHERE f.IdOrdenTrabajo IS NULL';
				$sql.= ' AND f.IdCompra IS NULL';
				$sql.= ' AND f.IdTipoVenta = ' . DB::Number($IdTipoVenta);
				$sql.= ' AND fi.IdArticulo IS NULL';
				$sql.= " AND fi.IdTipo <> 'TER'";
				if ($IdUsuario)
					$sql.= ' AND f.IdOrdenTrabajo IN (SELECT IdOrdenTrabajo FROM TB_OrdenTrabajoHitos where IdUsuario = ' . DB::Number($IdUsuario) . ')';
				
				if ($fechaDesde != '')
					$sql.= ' AND f.Fecha >= ' . DB::Date($fechaDesde);
				if ($fechaHasta != '')
					$sql.= ' AND f.Fecha <= ' . DB::Date($fechaHasta . ' 23:59:59');
				
				if ( !($oRes = $this->GetQuery($sql)) )
					return false;
						
				if (!$oRow = $oRes->GetRow())
					return false;
					
				$oReporteTotal->TotalManoObra = $oRow['ManoObra'];
			}
		}
		return $oReporteTotal;
	}
	
	public function GetReporteOT($IdTipoVenta, $fechaDesde, $fechaHasta)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->TotalManoObra = 0;
		$oReporteTotal->TotalRepuestos = 0;
		$oReporteTotal->CantidadOT = 0;
		
		$sql = "SELECT SUM((Importe - IF (PrecioTareaArticulos IS NULL, 0, PrecioTareaArticulos)) / 1.21) AS ManoObra, SUM(IF (PrecioArticulos IS NULL, 0, PrecioArticulos)) AS Repuestos";
		$sql.= " FROM";
		$sql.= " (";
		$sql.= " 	SELECT ott.*, SUM(cd.ImporteCompraNeto / (1 + i.Alicuota)) AS PrecioArticulos";
		$sql.= " 	FROM ";
		$sql.= " 	(";
		$sql.= " 		SELECT aott.*, SUM(IF (otta.PrecioTotal IS NULL, 0, otta.PrecioTotal)) AS PrecioTareaArticulos";
		$sql.= " 		FROM tb_OrdenesTrabajoTareas aott";
		$sql.= " 		LEFT JOIN tb_OrdenesTrabajoTareasArticulos otta ON aott.IdOrdenTrabajoTarea = otta.IdOrdenTrabajoTarea";
		$sql.= " 		GROUP BY aott.IdOrdenTrabajoTarea";
		$sql.= " 	) AS ott";
		$sql.= " 	INNER JOIN tb_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " 	INNER JOIN tb_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " 	LEFT JOIN tb_Comprobantes f ON f.IdComprobante = ot.IdComprobante";
		$sql.= " 	LEFT JOIN tb_Compras c ON c.IdOrdenTrabajoTarea = ott.IdOrdenTrabajoTarea";
		$sql.= " 	LEFT JOIN tb_CompraDetalles cd ON c.IdCompra = cd.IdCompra ";
		$sql.= " 	LEFT JOIN tb_Articulos a ON cd.IdArticulo = a.IdArticulo AND cd.IdArticulo = a.IdArticulo";
		$sql.= " 	LEFT JOIN tb_Ivas i ON a.IdIva = i.IdIva";
		$sql.= " 	LEFT JOIN tb_Comprobantes r ON c.IdRemito = r.IdComprobante";
		//$sql.= " INNER JOIN TB_Comprobantes c ON c.Numero = ms.Remito AND c.IdTipoComprobante = " . DB::Number(ComprobanteTipos::Remito);
		
		$sql.= " 	WHERE (r.IdEstado <> " . DB::Number(ComprobanteEstados::Anulado) . " OR r.IdEstado IS NULL)";
		$sql.= " 	AND (ot.IdEstadoOrden IS NULL OR ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado) . ")";
		if ($IdTipoVenta != TipoVenta::Garantia)
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " AND tu.IdCliente NOT IN (827, 703, 716, 1101, 1390)";
		else
			$sql.= " 	AND ( tu.IdCliente IN (827, 703, 716, 1101, 1390))";
		if ($IdTipoVenta != TipoVenta::Garantia && $IdTipoVenta != TipoVenta::VentaInterna && $IdTipoVenta != TipoVenta::PreEntrega)
		{
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND f.Fecha >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND f.Fecha <= " . DB::Date($fechaHasta);
		}
		else
		{
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND ot.FechaFin >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND ot.FechaFin <= " . DB::Date($fechaHasta);
		}
		//$sql.= " 	AND (f.IdComprobante IS NULL OR f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito))";
		$sql.= " 	GROUP BY ott.IdOrdenTrabajoTarea";
		$sql.= " ) AS rep";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
			
		$sql = " 	SELECT COUNT(ot.IdOrdenTrabajo) AS CantidadOT";
		$sql.= " 	FROM tb_OrdenesTrabajoTareas ott";
		$sql.= " 	INNER JOIN tb_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " 	INNER JOIN tb_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " 	LEFT JOIN tb_Comprobantes f ON f.IdComprobante = ot.IdComprobante";
		$sql.= " 	WHERE ot.IdEstadoOrden = " . DB::Number(EstadoOrden::Finalizado);
		if ($IdTipoVenta != TipoVenta::Garantia)
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " AND tu.IdCliente NOT IN (827, 703, 716, 1101, 1390) ";
		else
			$sql.= " 	AND (tu.IdCliente IN (827, 703, 716, 1101, 1390) )";
		if ($IdTipoVenta != TipoVenta::Garantia && $IdTipoVenta != TipoVenta::VentaInterna && $IdTipoVenta != TipoVenta::PreEntrega)
		{
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND f.Fecha >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND f.Fecha <= " . DB::Date($fechaHasta);
		}
		else
		{
			if ($fechaDesde && $fechaDesde != '')
				$sql.= " 	AND ot.FechaFin >= " . DB::Date($fechaDesde);
			if ($fechaHasta && $fechaHasta != '')
				$sql.= " 	AND ot.FechaFin <= " . DB::Date($fechaHasta);
		}
		//$sql.= " 	AND (f.IdComprobante IS NULL OR f.IdComprobante NOT IN (SELECT IdFactura FROM TB_NotasCredito))";
		
		if ( !($oRes1 = $this->GetQuery($sql)) )
			return false;
			
		if (!$oRow1 = $oRes1->GetRow())
			return false;
			
		$oReporteTotal->TotalManoObra = $oRow['ManoObra'];
		$oReporteTotal->TotalRepuestos = $oRow['Repuestos'];
		$oReporteTotal->CantidadOT = $oRow1['CantidadOT'];
		
		return $oReporteTotal;
	}
	
	public function GetReporteHorasDetalle($fechaDesde, $fechaHasta, $IdUsuario)
	{
	
	/*if ($IdTipoVenta != TipoVenta::Garantia)
			$sql.= " 	AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " AND tu.IdCliente NOT IN (827, 703, 716)";
		else
			$sql.= " 	AND (ott.IdTipoVenta = " . DB::Number($IdTipoVenta) . " OR tu.IdCliente IN (827, 703, 716))";*/
	
	
		$sql = "SELECT oth.*";
		$sql.= " FROM TB_OrdenTrabajoHitos oth";
		$sql.= " WHERE oth.FechaHora >= " . DB::Date($fechaDesde);
		$sql.= " AND oth.FechaHora <= " . DB::Date($fechaHasta . ' 23:59');
		$sql.= " AND oth.IdUsuario = " . DB::Number($IdUsuario);
		//$sql.= " AND oth.IdOrdenTrabajo IN (SELECT IdOrdenTrabajo FROM TB_FacturasPostVentas where IdComprobante IS NOT NULL and IdOrdenTrabajo IS NOT NULL)";
		$sql.= " ORDER BY Tiempo DESC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoHito = new OrdenTrabajoHito();
			$oOrdenTrabajoHito->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoHito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetReporteHorasExplicado($fechaDesde, $fechaHasta, $IdTipoVenta = null, $IdCategoria = Null)
	{
		$sql = "SELECT SUM(TIME_TO_SEC(oth.Tiempo) / 3600) AS Horas";
		$sql.= " FROM TB_OrdenesTrabajo ot";
		$sql.= " INNER JOIN TB_OrdenesTrabajoTareas ott ON ot.IdOrdenTrabajo = ott.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_OrdenTrabajoHitos oth ON ott.IdOrdenTrabajoTarea = oth.IdOrdenTrabajoTarea aND oth.IdOrdenTrabajo = ot.IdOrdenTrabajo";
		$sql.= " WHERE oth.FechaHora >= " . DB::Date($fechaDesde);
		$sql.= " AND oth.FechaHora <= " . DB::Date($fechaHasta . ' 23:59');
		if ($IdTipoVenta)
			$sql.= " AND ott.IdTipoVenta = " . DB::Number($IdTipoVenta);
		if ($IdCategoria)
			$sql.= " AND ott.IdCategoria = " . DB::Number($IdCategoria);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oReporte = new stdClass();
		$oReporte->Horas = $oRow['Horas'];
				
		return $oReporte;
	}
	
	public function ExportCsv(array $filter = NULL)
	{
		$oTallerUnidades = new TallerUnidades();
		$oClientes = new Clientes();
		$oEstadosOrden = new EstadosOrden();
		$oUsuarios = new Usuarios();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "ordenestrabajo.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrData 	= $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Ingreso";
		$csv.= $Separador;
		$csv.= "Nro. OT";
		$csv.= $Separador;
		$csv.= "Fecha Turno";
		$csv.= $Separador;
		$csv.= "Dominio";
		$csv.= $Separador;
		$csv.= "Modelo";
		$csv.= $Separador;
		$csv.= "Chasis";
		$csv.= $Separador;
		$csv.= "Nro Motor";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Telefono 1";
		$csv.= $Separador;
		$csv.= "Telefono 2";
		$csv.= $Separador;
		$csv.= "Asesor";
		$csv.= $Separador;
		$csv.= "Mecanicos";
		$csv.= $Separador;
		$csv.= "Tipo";
		$csv.= $Separador;
		$csv.= "Estado";
		$csv.= $Separador;
		$csv.= "Salida";
		$csv.= $Separador;
		$csv.= "Total";
		$csv.= $Separador;
		$csv.= "Tareas";
		$csv.= $SaltoLinea;
	
		foreach ($arrData as $oOrdenTrabajo)
		{				
			$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
			$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
			$oEstadoOrden = $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
			$oUsuario = $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
			
			$arrTareas 		= $oOrdenTrabajo->GetAllTareas();
			$arrMecanicos = $oUsuarios->GetAllByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);

			$strTareas = '';
			foreach ($arrTareas as $oTarea)
			{
				if ($strTareas != '')
					$strTareas.= ', ';
				$strTareas.=  utf8_encode($oTarea->Titulo);
			}
			
			$strMecanicos = '';
			foreach ($arrMecanicos as $oUsuarioM)
			{
				if ($strMecanicos != '')
					$strMecanicos.= ', ';
				$strMecanicos.=  utf8_encode($oUsuarioM->Nombre . ' ' . $oUsuarioM->Apellido);
			}
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oOrdenTrabajo->FechaInicio)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajo->IdOrdenTrabajo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oOrdenTrabajo->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Dominio));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Modelo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->NumeroVin));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->NumeroMotor));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->TelefonoCodigoArea . ' ' . $oCliente->Telefono));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->FaxCodigoArea . ' ' . $oCliente->Fax));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsuario->Nombre . ' ' . $oUsuario->Apellido));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($strMecanicos));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajo->Bahia ? 'BAHIA' : 'REGULAR'));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oEstadoOrden->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oOrdenTrabajo->FechaFin)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oOrdenTrabajo->ImporteTotalCalculado(), 2, ',', '')));		
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($strTareas));			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>