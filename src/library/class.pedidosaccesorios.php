<?php 

require_once('class.dbaccess.php');
require_once('class.pedidoaccesorios.php');
require_once('class.pedidosaccesoriositems.php');
require_once('class.filter.php');
require_once('class.page.php');

class PedidosAccesorios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND m.IdUnidad = " . DB::Number($filter['IdUnidad']);

		if ((isset($filter['IdUsado'])) && ($filter['IdUsado'] != ''))
			$sql.= " AND m.IdUsado = " . DB::Number($filter['IdUsado']);
		
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND pa.Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND pa.Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT pa.*";
		$sql.= " FROM TB_PedidosAccesorios pa";
		$sql.= " LEFT JOIN TB_Minutas m ON pa.IdMinuta = m.IdMinuta";
		$sql.= " LEFT JOIN TB_MinutasUsados mu ON pa.IdMinutaUsado = mu.IdMinuta";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY pa.IdPedido DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoAccesorios = new PedidoAccesorios();
			$oPedidoAccesorios->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoAccesorios);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesorios";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoAccesorios = new PedidoAccesorios();
		$oPedidoAccesorios->ParseFromArray($oRow);
		
		return $oPedidoAccesorios;		
	}
	
	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesorios";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoAccesorios = new PedidoAccesorios();
		$oPedidoAccesorios->ParseFromArray($oRow);
		
		return $oPedidoAccesorios;		
	}
	

	public function GetByMinutaUsado(MinutaUsado $oMinutaUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesorios";
		$sql.= " WHERE IdMinutaUsado = " . DB::Number($oMinutaUsado->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoAccesorios = new PedidoAccesorios();
		$oPedidoAccesorios->ParseFromArray($oRow);
		
		return $oPedidoAccesorios;		
	}
	
	public function GetByIdMinutaUsado($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesorios";
		$sql.= " WHERE IdMinutaUsado = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoAccesorios = new PedidoAccesorios();
		$oPedidoAccesorios->ParseFromArray($oRow);
		
		return $oPedidoAccesorios;		
	}


	public function GetById($IdPedido)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosAccesorios";
		$sql.= " WHERE IdPedido = " . DB::Number($IdPedido);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoAccesorios = new PedidoAccesorios();
		$oPedidoAccesorios->ParseFromArray($oRow);
		
		return $oPedidoAccesorios;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT pa.*";
		$sql.= " FROM TB_PedidosAccesorios pa";
		$sql.= " INNER JOIN TB_Minutas m ON pa.IdMinuta = m.IdMinuta";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PedidoAccesorios $oPedidoAccesorios)
	{
		$arr = array
		(
			'IdMinuta' 		=> DB::Number($oPedidoAccesorios->IdMinuta),
			'IdMinutaUsado'	=> DB::Number($oPedidoAccesorios->IdMinutaUsado),
			'Fecha' 		=> DB::Date($oPedidoAccesorios->Fecha),
			'Accesorios' 	=> DB::String($oPedidoAccesorios->Accesorios)
		);
		
		if (!$this->Insert('TB_PedidosAccesorios', $arr))
			return false;

		/* asignamos el id generado */
		$oPedidoAccesorios->IdPedido = DBAccess::GetLastInsertId();
			
		return $oPedidoAccesorios;
	}
	

	public function Update(PedidoAccesorios $oPedidoAccesorios)
	{
		$where = " IdPedido = " . DB::Number($oPedidoAccesorios->IdPedido);
		
		$arr = array('Accesorios' => DB::String($oPedidoAccesorios->Accesorios));
		
		if (!DBAccess::Update('TB_PedidosAccesorios', $arr, $where))
			return false;
		
		return $oSector;
	}

	
	public function Delete($IdPedido)
	{
		if (!DBAccess::$db->Begin())
			return false;
		
		$oPedidosAccesoriosItems = new PedidosAccesoriosItems();
		$oPedidosAccesoriosItems->DeleteByPedidoAccesorio($IdPedido);
		
		$where = " IdPedido = " . DB::Number($IdPedido);

		if (!DBAccess::Delete('TB_PedidosAccesorios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>