<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.pedidosrepuestos.php');
require_once('class.pedidorepuestodetalle.php');

class PedidosRepuestosDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		
		$sql = '';
		
		if (isset($filter['IdOrdenTrabajo']) && $filter['IdOrdenTrabajo'] != '')
			$sql.= ' AND pr.IdOrdenTrabajo = ' . DB::Number($filter['IdOrdenTrabajo']);
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= ' AND pr.IdUsuario = ' . DB::Number($filter['IdUsuario']);
		
		if (isset($filter['Codigo']) && $filter['Codigo'] != '')
			$sql.= " AND prd.IdArticulo IN (SELECT IdArticulo FROM TB_Articulos WHERE Codigo LIKE '%" . DB::StringUnquoted($filter['Codigo']) . "%')";
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '')
			$sql.= ' AND pr.Fecha >= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '')
			$sql.= ' AND pr.Fecha <= ' . DB::Date($filter['FechaHasta'] . ' 23:59');
		
		if (isset($filter['Vencido']) && $filter['Vencido'] != '')
		{
			if ($filter['Vencido'] == '1')
				$sql.= ' AND prd.Recibido = 0 AND prd.FechaVencimiento <= ' . DB::Date(date('d-m-Y H:i'));
		}
		
		if (isset($filter['Aprobado']) && $filter['Aprobado'] != '')
			$sql.= " AND pr.Aprobado = " . DB::Bool($filter['Aprobado']);
		
		if (isset($filter['Recibido']) && $filter['Recibido'] != '')
			$sql.= " AND prd.Recibido = " . DB::Bool($filter['Recibido']);
		
		if (isset($filter['Pedido']) && $filter['Pedido'] != '')
		{
			if ($filter['Pedido'] == '1')
				$sql.= ' AND prd.FechaPedido IS NOT NULL';
			elseif ($filter['Pedido'] == '0')
				$sql.= ' AND prd.FechaPedido IS NULL';
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT prd.*";
		$sql.= " FROM TB_PedidosRepuestosDetalles prd";
		$sql.= " INNER JOIN TB_PedidosRepuestos pr ON prd.IdPedidoRepuesto = pr.IdPedidoRepuesto";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY prd.IdPedidoRepuesto DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
			$oPedidoRepuestoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuestoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT prd.*";
		$sql.= " FROM TB_PedidosRepuestosDetalles prd";
		$sql.= " INNER JOIN TB_PedidosRepuestos pr ON prd.IdPedidoRepuesto = pr.IdPedidoRepuesto";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	public function GetAllByPedidoRepuesto(PedidoRepuesto $oPedidoRepuesto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestosDetalles";
		$sql.= " WHERE IdPedidoRepuesto = " . DB::Number($oPedidoRepuesto->IdPedidoRepuesto);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
			$oPedidoRepuestoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuestoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	public function GetAllPendientesByArticulo(Articulo $oArticulo)
	{
		$sql = "SELECT prd.*";
		$sql.= " FROM TB_PedidosRepuestosDetalles prd";
		$sql.= " INNER JOIN TB_PedidosRepuestos pr ON prd.IdPedidoRepuesto = pr.IdPedidoRepuesto";
		$sql.= " WHERE prd.IdArticulo = " . DB::Number($oArticulo->IdArticulo);
		$sql.= " AND prd.Recibido = 0";
		$sql.= " AND prd.FechaPedido IS NOT NULL";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
			$oPedidoRepuestoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuestoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	public function GetAllByIdPedidoRepuesto($IdPedidoRepuesto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestosDetalles";
		$sql.= " WHERE IdPedidoRepuesto = " . DB::Number($IdPedidoRepuesto);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
			$oPedidoRepuestoDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoRepuestoDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	
	public function GetPagesCount(Page $oPage, $filter = false)
	{
		$sql = "SELECT prd.*";
		$sql.= " FROM TB_PedidosRepuestosDetalles prd";
		$sql.= " INNER JOIN TB_PedidosRepuestos pr ON prd.IdPedidoRepuesto = pr.IdPedidoRepuesto";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		$Count = ceil($CountRows / $oPage->Size);
		
		return $Count;
	}

	public function GetById($IdPedidoRepuesto, $IdArticulo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestosDetalles";
		$sql.= " WHERE IdPedidoRepuesto = " . DB::Number($IdPedidoRepuesto);	
		$sql.= " AND IdArticulo = " . DB::Number($IdArticulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
		$oPedidoRepuestoDetalle->ParseFromArray($oRow);
		
		return $oPedidoRepuestoDetalle;		
	}

	public function GetByIdIncrement($IdPedidoRepuestoDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosRepuestosDetalles";
		$sql.= " WHERE IdPedidoRepuestoDetalle = " . DB::Number($IdPedidoRepuestoDetalle);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPedidoRepuestoDetalle = new PedidoRepuestoDetalle();
		$oPedidoRepuestoDetalle->ParseFromArray($oRow);
		
		return $oPedidoRepuestoDetalle;		
	}
	
	public function Create(PedidoRepuestoDetalle $oPedidoRepuestoDetalle)
	{
		$arr = array
		(
			'IdPedidoRepuesto' 	=> DB::Number($oPedidoRepuestoDetalle->IdPedidoRepuesto),
			'IdArticulo' 		=> DB::Number($oPedidoRepuestoDetalle->IdArticulo),
			'Precio' 			=> DB::Number($oPedidoRepuestoDetalle->Precio),
			'Cantidad' 			=> DB::Number($oPedidoRepuestoDetalle->Cantidad),
			'IdCargo'			=> DB::Number($oPedidoRepuestoDetalle->IdCargo),
			'NumeroSap'			=> DB::String($oPedidoRepuestoDetalle->NumeroSap),
			'Recibido'			=> DB::Bool($oPedidoRepuestoDetalle->Recibido),
			'FechaPedido'		=> DB::Date($oPedidoRepuestoDetalle->FechaPedido),
			'FechaVencimiento'	=> DB::Date($oPedidoRepuestoDetalle->FechaVencimiento)
		);
	
		if (!$this->Insert('TB_PedidosRepuestosDetalles', $arr))
			return false;
			
		return $oPedidoRepuestoDetalle;
	}
	

	public function Update(PedidoRepuestoDetalle $oPedidoRepuestoDetalle)
	{
		$where = " IdPedidoRepuestoDetalle = " . (int)$oPedidoRepuestoDetalle->IdPedidoRepuestoDetalle;		

		$arr = array
		(
			'IdPedidoRepuesto' 	=> DB::Number($oPedidoRepuestoDetalle->IdPedidoRepuesto),
			'IdArticulo' 		=> DB::Number($oPedidoRepuestoDetalle->IdArticulo),
			'Precio' 			=> DB::Number($oPedidoRepuestoDetalle->Precio),
			'Cantidad' 			=> DB::Number($oPedidoRepuestoDetalle->Cantidad),
			'IdCargo'			=> DB::Number($oPedidoRepuestoDetalle->IdCargo),
			'NumeroSap'			=> DB::String($oPedidoRepuestoDetalle->NumeroSap),
			'Recibido'			=> DB::Bool($oPedidoRepuestoDetalle->Recibido),
			'FechaPedido'		=> DB::Date($oPedidoRepuestoDetalle->FechaPedido),
			'FechaVencimiento'	=> DB::Date($oPedidoRepuestoDetalle->FechaVencimiento)
		);

		if (!DBAccess::Update('TB_PedidosRepuestosDetalles', $arr, $where))
			return false;
			
		return $oPedidoRepuestoDetalle;
	}
	
	public function Delete($IdPedidoRepuestoDetalle)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoRepuestoDetalle = " . (int)$IdPedidoRepuestoDetalle;		
		
		if ( !DBAccess::Delete('TB_PedidosRepuestosDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteAll($IdPedidoRepuesto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoRepuesto = " . (int)$IdPedidoRepuesto;		
		
		if ( !DBAccess::Delete('TB_PedidosRepuestosDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>