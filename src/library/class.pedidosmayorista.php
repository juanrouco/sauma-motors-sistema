<?php 

require_once('class.dbaccess.php');
require_once('class.pedidomayorista.php');
require_once('class.filter.php');
require_once('class.page.php');

class PedidosMayorista extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdPedidoMayorista'])) && ($filter['IdPedidoMayorista'] != ''))
			$sql.= " AND IdPedidoMayorista = " . DB::Number($filter['IdPedidoMayorista']);
			
		if ((isset($filter['FechaPedidoMayorista'])) && ($filter['FechaPedidoMayorista'] != ''))
			$sql.= " AND FechaPedidoMayorista = " . DB::Number($filter['FechaPedidoMayorista']);
		
		if ((isset($filter['FechaPedidoMayoristaHasta'])) && ($filter['FechaPedidoMayoristaHasta'] != ''))
			$sql.= " AND FechaPedidoMayorista <= " . DB::Number($filter['FechaPedidoMayoristaHasta']);
			
		if ((isset($filter['FechaPedidoMayoristaDesde'])) && ($filter['FechaPedidoMayoristaDesde'] != ''))
			$sql.= " AND FechaPedidoMayorista >= " . DB::Number($filter['FechaPedidoMayoristaDesde']);
		
		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND IdCliente IN (SELECT IdCliente FROM TB_Clientes where RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%')";

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayorista";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPedidoMayorista DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPedidoMayorista = new PedidoMayorista();
			$oPedidoMayorista->ParseFromArray($oRow);
			
			array_push($arr, $oPedidoMayorista);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdPedidoMayorista)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayorista";
		$sql.= " WHERE IdPedidoMayorista = " . DB::Number($IdPedidoMayorista);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoMayorista = new PedidoMayorista();
		$oPedidoMayorista->ParseFromArray($oRow);
		
		return $oPedidoMayorista;		
	}
	

	public function GetByIdCliente($IdCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayorista";
		$sql.= " WHERE IdCliente = " . DB::Number($IdCliente);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPedidoMayorista = new PedidoMayorista();
		$oPedidoMayorista->ParseFromArray($oRow);
		
		return $oPedidoMayorista;		
	}


	public function GetNextId()
	{
		$sql = "SELECT MAX(IdPedidoMayorista) AS IdPedidoMayorista";
		$sql.= " FROM TB_PedidosMayorista";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$IdPedidoMayorista = $oRow['IdPedidoMayorista'];
		$IdPedidoMayorista++;
		
		return $IdPedidoMayorista;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PedidosMayorista";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PedidoMayorista $oPedidoMayorista)
	{
		$arr = array
		(
			'IdCliente' 			=> DB::Number($oPedidoMayorista->IdCliente),
			'FechaPedidoMayorista' 	=> DB::Date($oPedidoMayorista->FechaPedidoMayorista),
			'Observaciones' 		=> DB::String($oPedidoMayorista->Observaciones),
			'IdEstado' 				=> DB::Number($oPedidoMayorista->IdEstado)
		);
		
		if (!$this->Insert('TB_PedidosMayorista', $arr))
			return false;

		/* asignamos el id generado */
		$oPedidoMayorista->IdPedidoMayorista = DBAccess::GetLastInsertId();
			
		return $oPedidoMayorista;
	}
	
	
	public function Update(PedidoMayorista $oPedidoMayorista)
	{
		$where = " IdPedidoMayorista = " . DB::Number($oPedidoMayorista->IdPedidoMayorista);
		
		$arr = array
		(
			'IdCliente' 			=> DB::Number($oPedidoMayorista->IdCliente),
			'FechaPedidoMayorista' 	=> DB::Date($oPedidoMayorista->FechaPedidoMayorista),
			'Observaciones' 		=> DB::String($oPedidoMayorista->Observaciones),
			'IdEstado' 				=> DB::Number($oPedidoMayorista->IdEstado)
		);
		
		if (!DBAccess::Update('TB_PedidosMayorista', $arr, $where))
			return false;
		
		return $oPedidoMayorista;
	}
	

	public function Delete($IdPedidoMayorista)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPedidoMayorista = " . DB::Number($IdPedidoMayorista);

		if (!DBAccess::Delete('TB_PedidoMayoristaDetalles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_PedidosMayorista', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>