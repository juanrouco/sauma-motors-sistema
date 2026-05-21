<?php 

require_once('class.dbaccess.php');
require_once('class.pedidorepuesto.php');
require_once('class.filter.php');
require_once('class.page.php');

class PedidosRepuestos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['IdOrdenTrabajo']) && $filter['IdOrdenTrabajo'] != '')
			$sql.= ' AND IdOrdenTrabajo = ' . DB::Number($filter['IdOrdenTrabajo']);
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= ' AND IdUsuario = ' . DB::Number($filter['IdUsuario']);
		
		if (isset($filter['IdModalidad']) && $filter['IdModalidad'] != '')
			$sql.= ' AND IdModalidad = ' . DB::Number($filter['IdModalidad']);
		
		if (isset($filter['IdPedidoRepuesto']) && $filter['IdPedidoRepuesto'] != '')
			$sql.= ' AND pr.IdPedidoRepuesto = ' . DB::Number($filter['IdPedidoRepuesto']);
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '')
			$sql.= ' AND Fecha >= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '')
			$sql.= ' AND Fecha <= ' . DB::Date($filter['FechaHasta'] . ' 23:59');
		
		if (isset($filter['Vencido']) && $filter['Vencido'] != '')
		{
			if ($filter['Vencido'] == '1')
				$sql.= ' AND IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE Recibido = 0 AND FechaVencimiento <= ' . DB::Date(date('d-m-Y H:i')) . ')';
			elseif ($filter['Vencido'] == '0')
				$sql.= ' AND IdPedidoRepuesto NOT IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE Recibido = 0 AND FechaVencimiento <= ' . DB::Date(date('d-m-Y H:i')) . ')';
		}
		
		if (isset($filter['Pedido']) && $filter['Pedido'] != '')
		{
			if ($filter['Pedido'] == '1')
				$sql.= ' AND (pr.IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE FechaPedido IS NOT NULL))';
			elseif ($filter['Pedido'] == '0')
				$sql.= ' AND (pr.IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE FechaPedido IS NULL))';
		}
		
		if (isset($filter['Recibido']) && $filter['Recibido'] != '')
		{
			if ($filter['Recibido'] == '1')
				$sql.= ' AND (pr.IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE Recibido = 1))';
			elseif ($filter['Recibido'] == '0')
				$sql.= ' AND (pr.IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE Recibido = 0))';
		}
		
		if (isset($filter['Aprobado']) && $filter['Aprobado'] != '')
			$sql.= " AND Aprobado = " . DB::Bool($filter['Aprobado']);
		
		return $sql;
	}
	public function ParseFilterReporte(array $filter, $sqlAux)
	{
		$sql = '';
		
		if (isset($filter['IdOrdenTrabajo']) && $filter['IdOrdenTrabajo'] != '')
			$sql.= ' AND pr.IdOrdenTrabajo = ' . DB::Number($filter['IdOrdenTrabajo']);
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= ' AND pr.IdUsuario = ' . DB::Number($filter['IdUsuario']);
		
		if (isset($filter['IdModalidad']) && $filter['IdModalidad'] != '')
			$sql.= ' AND pr.IdModalidad = ' . DB::Number($filter['IdModalidad']);
		
		if (isset($filter['IdPedidoRepuesto']) && $filter['IdPedidoRepuesto'] != '')
			$sql.= ' AND pr.IdPedidoRepuesto = ' . DB::Number($filter['IdPedidoRepuesto']);
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '')
			$sql.= ' AND sm.Fecha >= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '')
			$sql.= ' AND sm.Fecha <= ' . DB::Date($filter['FechaHasta'] . ' 23:59');
		
		if (isset($filter['OTCreado']) && $filter['OTCreado'] != '')
			if ($filter['OTCreado'] == '1')
				$sql.= ' AND ot.IdOrdenTrabajo IS NOT NULL AND ot.IdOrdenTrabajo <> pr.IdOrdenTrabajo';
			else
				$sql.= ' AND (ot.IdOrdenTrabajo IS NULL OR ot.IdOrdenTrabajo = pr.IdOrdenTrabajo)';
		
		if (isset($filter['TurnoCreado']) && $filter['TurnoCreado'] != '')
			if ($filter['TurnoCreado'] == '1')
				$sql.= ' AND t.IdTurno IS NOT NULL';
			else
				$sql.= ' AND t.IdTurno IS NULL';
		
		if (isset($filter['ArticuloAsignado']) && $filter['ArticuloAsignado'] != '')
			if ($filter['ArticuloAsignado'] == '1')
				$sql.= ' AND aux.IdArticulo IS NOT NULL';
			else
				$sql.= ' AND aux.IdArticulo IS NULL AND prd.IdPedidoRepuestoDetalle NOT IN (SELECT IdPedidoRepuestoDetalle FROM (' . $sqlAux . 'AND aux.IdArticulo IS NOT NULL) AS aux2)';
		
		return $sql;
	}

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPedidoRepuesto DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuesto = new PedidoRepuesto();
			$oPedidoRepuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetAllOrderedByEstado(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM (";
		$sql.= "SELECT *, IF(IdPedidoRepuesto IN (SELECT IdPedidoRepuesto FROM TB_PedidosRepuestosDetalles WHERE Recibido = 0 AND FechaVencimiento <= " . DB::Date(date('d-m-Y H:i')) . "), 1, 0) AS Vencido";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= ") AS Aux ORDER BY Aux.Vencido DESC, IdPedidoRepuesto ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuesto = new PedidoRepuesto();
			$oPedidoRepuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetReporte(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT prd.*, sm.Fecha AS FechaRecepcion, IF (ot.IdOrdenTrabajo IS NULL, 0, IF (ot.IdOrdenTrabajo = pr.IdOrdenTrabajo, 0, ot.IdOrdenTrabajo)) AS OTCreado, IF (t.IdTurno IS NULL, 0, 1) AS TurnoCreado, IF (aux.IdArticulo IS NULL, 0, 1) AS ArticuloAsignado";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= " INNER JOIN TB_PedidosRepuestosDetalles prd ON pr.IdPedidoRepuesto = prd.IdPedidoRepuesto";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.Dominio = pr.Dominio";
		$sql.= " INNER JOIN TB_StockMovimientos sm ON sm.IdArticulo = prd.IdArticulo";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdTallerUnidad = tu.IdTallerUnidad AND (ot.FechaInicio >= sm.Fecha OR ot.IdOrdenTrabajo = pr.IdOrdenTrabajo)";
		$sql.= " LEFT JOIN TB_Turnos t ON t.IdTallerUnidad = tu.IdTallerUnidad AND t.Fecha >= sm.Fecha";
		$sql.= " LEFT JOIN (";
		$sql.= " SELECT c.IdOrdenTrabajo, cd.IdArticulo, ot2.IdTallerUnidad, c.FechaCarga AS FechaInicio FROM TB_Compras c";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot2 ON ot2.IdOrdenTrabajo = c.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = c.IdCompra) AS aux";
		$sql.= " ON aux.IdTallerUnidad = ot.IdTallerUnidad AND (aux.FechaInicio >= sm.Fecha OR aux.IdOrdenTrabajo = pr.IdOrdenTrabajo) AND aux.IdArticulo = prd.IdArticulo";
		$sql.= " WHERE pr.IdOrdenTrabajo IS NOT NULL";
		$sql.= " AND tu.IdTallerUnidad IN (SELECT IdTallerUnidad FROM TB_OrdenesTrabajo)";
		$sql.= " AND prd.Recibido = 1";
		$sql.= " AND sm.Fecha >= prd.FechaPedido";
		$sql.= " AND sm.Cantidad > 0";
		$sql.= " AND pr.Dominio <> ''";
		$sql.= ($filter) ? $this->ParseFilterReporte($filter, $sql) : "";
		$sql.= " GROUP BY prd.IdPedidoRepuestoDetalle";
		$sql.= " ORDER BY sm.Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
			$oPedidoRepuestoDetalle->ParseFromArray($oRow);
			
			$oReporte = new stdClass();
			$oReporte->PedidoRepuestoDetalle	= $oPedidoRepuestoDetalle;
			$oReporte->FechaRecepcion 			= $oRow['FechaRecepcion'];
			$oReporte->OTCreado					= $oRow['OTCreado'];
			$oReporte->TurnoCreado				= $oRow['TurnoCreado'];
			$oReporte->ArticuloAsignado			= $oRow['ArticuloAsignado'];
			
			array_push($arr, $oReporte);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetReporteTotal(array $filter = NULL)
	{
		$sql = "SELECT prd.*, sm.Fecha AS FechaRecepcion, IF (ot.IdOrdenTrabajo IS NULL, 0, IF (ot.IdOrdenTrabajo = pr.IdOrdenTrabajo, 0, ot.IdOrdenTrabajo)) AS OTCreado, IF (t.IdTurno IS NULL, 0, 1) AS TurnoCreado, IF (aux.IdArticulo IS NULL, 0, 1) AS ArticuloAsignado";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= " INNER JOIN TB_PedidosRepuestosDetalles prd ON pr.IdPedidoRepuesto = prd.IdPedidoRepuesto";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.Dominio = pr.Dominio";
		$sql.= " INNER JOIN TB_StockMovimientos sm ON sm.IdArticulo = prd.IdArticulo";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdTallerUnidad = tu.IdTallerUnidad AND (ot.FechaInicio >= sm.Fecha OR ot.IdOrdenTrabajo = pr.IdOrdenTrabajo)";
		$sql.= " LEFT JOIN TB_Turnos t ON t.IdTallerUnidad = tu.IdTallerUnidad AND t.Fecha >= sm.Fecha";
		$sql.= " LEFT JOIN (";
		$sql.= " SELECT c.IdOrdenTrabajo, cd.IdArticulo, ot2.IdTallerUnidad, c.FechaCarga AS FechaInicio FROM TB_Compras c";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot2 ON ot2.IdOrdenTrabajo = c.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = c.IdCompra) AS aux";
		$sql.= " ON aux.IdTallerUnidad = ot.IdTallerUnidad AND (aux.FechaInicio >= sm.Fecha OR aux.IdOrdenTrabajo = pr.IdOrdenTrabajo) AND aux.IdArticulo = prd.IdArticulo";
		$sql.= " WHERE pr.IdOrdenTrabajo IS NOT NULL";
		$sql.= " AND tu.IdTallerUnidad IN (SELECT IdTallerUnidad FROM TB_OrdenesTrabajo)";
		$sql.= " AND prd.Recibido = 1";
		$sql.= " AND sm.Fecha >= prd.FechaPedido";
		$sql.= " AND sm.Cantidad > 0";
		$sql.= " AND pr.Dominio <> ''";
		$sql.= ($filter) ? $this->ParseFilterReporte($filter, $sql) : "";
		$sql.= " GROUP BY prd.IdPedidoRepuestoDetalle";
		$sql = "SELECT SUM(Cantidad * Precio) AS Total FROM (" . $sql . ") AS Aux3";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!$oRow = $oRes->GetRow())
			return false;	
		
		$oReporte = new stdClass();
		$oReporte->Total 			= $oRow['Total'];
		
		return $oReporte;
	}
	
	public function GetCostoTotal(array $filter = NULL)
	{
		$sql = "SELECT SUM(prd.Precio) AS CostoTotal";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= " INNER JOIN TB_PedidosRepuestosDetalles prd ON pr.IdPedidoRepuesto = prd.IdPedidoRepuesto";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!($oRow = $oRes->GetRow()))
			return false;	
		
		return $oRow['CostoTotal'];
	}

	public function GetById($IdPedidoRepuesto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestos";
		$sql.= " WHERE IdPedidoRepuesto = " . DB::Number($IdPedidoRepuesto);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoRepuesto = new PedidoRepuesto();
		$oPedidoRepuesto->ParseFromArray($oRow);
		
		return $oPedidoRepuesto;		
	}

	public function GetByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestos";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuesto = new PedidoRepuesto();
			$oPedidoRepuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetCountRowsReporte(array $filter = NULL)
	{
		$sql = "SELECT prd.*, sm.Fecha AS FechaRecepcion, IF (ot.IdOrdenTrabajo IS NULL, 0, IF (ot.IdOrdenTrabajo = pr.IdOrdenTrabajo, 0, ot.IdOrdenTrabajo)) AS OTCreado, IF (t.IdTurno IS NULL, 0, 1) AS TurnoCreado, IF (aux.IdArticulo IS NULL, 0, 1) AS ArticuloAsignado";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= " INNER JOIN TB_PedidosRepuestosDetalles prd ON pr.IdPedidoRepuesto = prd.IdPedidoRepuesto";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.Dominio = pr.Dominio";
		$sql.= " INNER JOIN TB_StockMovimientos sm ON sm.IdArticulo = prd.IdArticulo";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdTallerUnidad = tu.IdTallerUnidad AND (ot.FechaInicio >= sm.Fecha OR ot.IdOrdenTrabajo = pr.IdOrdenTrabajo)";
		$sql.= " LEFT JOIN TB_Turnos t ON t.IdTallerUnidad = tu.IdTallerUnidad AND t.Fecha >= sm.Fecha";
		$sql.= " LEFT JOIN (";
		$sql.= " SELECT c.IdOrdenTrabajo, cd.IdArticulo, ot2.IdTallerUnidad, c.FechaCarga AS FechaInicio FROM TB_Compras c";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot2 ON ot2.IdOrdenTrabajo = c.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = c.IdCompra) AS aux";
		$sql.= " ON aux.IdTallerUnidad = ot.IdTallerUnidad AND (aux.FechaInicio >= sm.Fecha OR aux.IdOrdenTrabajo = pr.IdOrdenTrabajo) AND aux.IdArticulo = prd.IdArticulo";
		$sql.= " WHERE pr.IdOrdenTrabajo IS NOT NULL";
		$sql.= " AND tu.IdTallerUnidad IN (SELECT IdTallerUnidad FROM TB_OrdenesTrabajo)";
		$sql.= " AND prd.Recibido = 1";
		$sql.= " AND sm.Fecha >= prd.FechaPedido";
		$sql.= " AND sm.Cantidad > 0";
		$sql.= " AND pr.Dominio <> ''";
		$sql.= ($filter) ? $this->ParseFilterReporte($filter, $sql) : "";
		$sql.= " GROUP BY prd.IdPedidoRepuestoDetalle";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetPagesCount(Page $oPage, $filter = false)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		$Count = ceil($CountRows / $oPage->Size);
		
		return $Count;
	}

	
	public function GetPagesCountReporte(Page $oPage, $filter = false)
	{
		$sql = "SELECT prd.*, sm.Fecha AS FechaRecepcion, IF (ot.IdOrdenTrabajo IS NULL, 0, IF (ot.IdOrdenTrabajo = pr.IdOrdenTrabajo, 0, ot.IdOrdenTrabajo)) AS OTCreado, IF (t.IdTurno IS NULL, 0, 1) AS TurnoCreado, IF (aux.IdArticulo IS NULL, 0, 1) AS ArticuloAsignado";
		$sql.= " FROM TB_PedidosRepuestos pr";
		$sql.= " INNER JOIN TB_PedidosRepuestosDetalles prd ON pr.IdPedidoRepuesto = prd.IdPedidoRepuesto";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.Dominio = pr.Dominio";
		$sql.= " INNER JOIN TB_StockMovimientos sm ON sm.IdArticulo = prd.IdArticulo";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdTallerUnidad = tu.IdTallerUnidad AND (ot.FechaInicio >= sm.Fecha OR ot.IdOrdenTrabajo = pr.IdOrdenTrabajo)";
		$sql.= " LEFT JOIN TB_Turnos t ON t.IdTallerUnidad = tu.IdTallerUnidad AND t.Fecha >= sm.Fecha";
		$sql.= " LEFT JOIN (";
		$sql.= " SELECT c.IdOrdenTrabajo, cd.IdArticulo, ot2.IdTallerUnidad, c.FechaCarga AS FechaInicio FROM TB_Compras c";
		$sql.= " INNER JOIN TB_OrdenesTrabajo ot2 ON ot2.IdOrdenTrabajo = c.IdOrdenTrabajo";
		$sql.= " INNER JOIN TB_CompraDetalles cd ON cd.IdCompra = c.IdCompra) AS aux";
		$sql.= " ON aux.IdTallerUnidad = ot.IdTallerUnidad AND (aux.FechaInicio >= sm.Fecha OR aux.IdOrdenTrabajo = pr.IdOrdenTrabajo) AND aux.IdArticulo = prd.IdArticulo";
		$sql.= " WHERE pr.IdOrdenTrabajo IS NOT NULL";
		$sql.= " AND tu.IdTallerUnidad IN (SELECT IdTallerUnidad FROM TB_OrdenesTrabajo)";
		$sql.= " AND prd.Recibido = 1";
		$sql.= " AND sm.Fecha >= prd.FechaPedido";
		$sql.= " AND sm.Cantidad > 0";
		$sql.= " AND pr.Dominio <> ''";
		$sql.= ($filter) ? $this->ParseFilterReporte($filter, $sql) : "";
		$sql.= " GROUP BY prd.IdPedidoRepuestoDetalle";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		$Count = ceil($CountRows / $oPage->Size);
		
		return $Count;
	}
	
	private function GetArrayDB(PedidoRepuesto $oPedidoRepuesto)
	{
		$arr = array
		(
			'Fecha' 			=> DB::Date($oPedidoRepuesto->Fecha),
			'IdUsuario'			=> DB::Number($oPedidoRepuesto->IdUsuario),
			'IdSector'			=> DB::Number($oPedidoRepuesto->IdSector),
			'IdOrdenTrabajo'	=> DB::Number($oPedidoRepuesto->IdOrdenTrabajo),
			'Dominio'			=> DB::String($oPedidoRepuesto->Dominio),
			'IdModalidad'		=> DB::Number($oPedidoRepuesto->IdModalidad),
			'Aprobado'			=> DB::Bool($oPedidoRepuesto->Aprobado),
			'IdUsuarioAprobado'	=> DB::Number($oPedidoRepuesto->IdUsuarioAprobado),
			'FechaVencimiento'	=> DB::Date($oPedidoRepuesto->FechaVencimiento),
			'FechaAprobado'		=> DB::Date($oPedidoRepuesto->FechaAprobado),
			'FechaPedido'		=> DB::Date($oPedidoRepuesto->FechaPedido),
			'IdUsuarioGenerador'=> DB::Number($oPedidoRepuesto->IdUsuarioGenerador),
			'IdUsuarioPedido'	=> DB::Number($oPedidoRepuesto->IdUsuarioPedido)
		);
		
		return $arr;
	}
	
	public function Create(PedidoRepuesto $oPedidoRepuesto)
	{
		$arr = $this->GetArrayDB($oPedidoRepuesto);
		
		if (!$this->Insert('TB_PedidosRepuestos', $arr))
			return false;

		/* asignamos el id generado */
		$oPedidoRepuesto->IdPedidoRepuesto = DBAccess::GetLastInsertId();
			
		return $oPedidoRepuesto;
	}
	
	
	public function Update(PedidoRepuesto $oPedidoRepuesto)
	{
		$where = " IdPedidoRepuesto = " . DB::Number($oPedidoRepuesto->IdPedidoRepuesto);
		
		$arr = $this->GetArrayDB($oPedidoRepuesto);
		
		if (!DBAccess::Update('TB_PedidosRepuestos', $arr, $where))
			return false;
		
		return $oPedidoRepuesto;
	}
	

	public function Delete($IdPedidoRepuesto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoRepuesto = " . DB::Number($IdPedidoRepuesto);

		if (!DBAccess::Delete('TB_PedidosRepuestos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>