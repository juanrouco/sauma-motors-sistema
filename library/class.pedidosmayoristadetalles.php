<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.pedidomayorista.php');
require_once('class.pedidomayoristadetalle.php');

class PedidosMayoristaDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayoristaDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPedidoMayorista DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoMayoristaDetalle = new PedidoMayoristaDetalle();
			$oPedidoMayoristaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoMayoristaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByPedidoMayorista(PedidoMayorista $oPedidoMayorista)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayoristaDetalles";
		$sql.= " WHERE IdPedidoMayorista = " . DB::Number($oPedidoMayorista->IdPedidoMayorista);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoMayoristaDetalle = new PedidoMayoristaDetalle();
			$oPedidoMayoristaDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoMayoristaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdPedidoMayorista, $IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayoristaDetalles";
		$sql.= " WHERE IdPedidoMayorista = " . DB::Number($IdPedidoMayorista);	
		$sql.= " AND IdMinuta = " . DB::Number($IdMinuta);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPedidoMayoristaDetalle = new PedidoMayoristaDetalle();
		$oPedidoMayoristaDetalle->ParseFromArray($oRow);
		
		return $oPedidoMayoristaDetalle;		
	}
	

	public function Create(PedidoMayoristaDetalle $oPedidoMayoristaDetalle)
	{
		$arr = array
		(
			'IdPedidoMayorista' 	=> DB::Number($oPedidoMayoristaDetalle->IdPedidoMayorista),
			'IdMinuta' 		=> DB::Number($oPedidoMayoristaDetalle->IdMinuta)
		);
	
		if (!$this->Insert('TB_PedidosMayoristaDetalles', $arr))
			return false;
			
		return $oPedidoMayoristaDetalle;
	}
	

	public function Update(PedidoMayoristaDetalle $oPedidoMayoristaDetalle)
	{
		$where = " IdPedidoMayorista = " . (int)$oPedidoMayoristaDetalle->IdPedidoMayorista;
		$where.= " AND IdMinuta = " . (int)$oPedidoMayoristaDetalle->IdMinuta;

		
		if (!DBAccess::Update('TB_PedidosMayoristaDetalles', $arr, $where))
			return false;
			
		return $oPedidoMayoristaDetalle;
	}

	
	public function Delete($IdPedidoMayorista, $IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoMayorista = " . (int)$IdPedidoMayorista;
		$where.= " AND IdMinuta = " . (int)$IdMinuta;
		
		if ( !DBAccess::Delete('TB_PedidosMayoristaDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteByIdPedidoMayorista($IdPedidoMayorista)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoMayorista = " . (int)$IdPedidoMayorista;
		
		if ( !DBAccess::Delete('TB_PedidosMayoristaDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>