<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.cliente.php');
require_once('class.clientemigracion.php');

class ClientesMigracion extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ($filter['IdCliente'] != "")
		{	
			$sql.= " AND IdCliente = " . DB::Number($filter['IdCliente']);
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClientesMigracion";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCliente";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oClienteMigracion = new ClienteMigracion();
			$oClienteMigracion->ParseFromArray($oRow);
			
			array_push($arr, $oClienteMigracion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdCliente, $IdAntiguo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClientesMigracion";
		$sql.= " WHERE IdCliente = " . DB::Number($IdCliente);	
		$sql.= " AND IdAntiguo = " . DB::Number($IdAntiguo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oClienteMigracion = new ClienteMigracion();
		$oClienteMigracion->ParseFromArray($oRow);
		
		return $oClienteMigracion;		
	}
	
	public function GetByIdAntiguo($IdAntiguo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClientesMigracion";
		$sql.= " WHERE IdAntiguo = " . DB::Number($IdAntiguo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oClienteMigracion = new ClienteMigracion();
		$oClienteMigracion->ParseFromArray($oRow);
		
		return $oClienteMigracion;		
	}
	

	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClientesMigracion";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oClienteMigracion = new ClienteMigracion();
			$oClienteMigracion->ParseFromArray($oRow);
			
			array_push($arr, $oClienteMigracion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function Create(ClienteMigracion $oClienteMigracion)
	{
		$arr = array
		(
			'IdCliente' 	=> DB::Number($oClienteMigracion->IdCliente),
			'IdAntiguo' 	=> DB::Number($oClienteMigracion->IdAntiguo)
		);
	
		if (!$this->Insert('TB_ClientesMigracion', $arr))
			return false;
			
		return $oClienteMigracion;
	}
	
	
	public function Delete($IdCliente, $IdAntiguo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCliente = " . (int)$IdCliente;
		$where.= " AND IdAntiguo = " . (int)$IdAntiguo;
		
		if ( !DBAccess::Delete('TB_ClientesMigracion', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	
	public function DeleteByCliente(Cliente $oCliente)
	{
		$where = " IdCliente = " . (int)$oCliente->IdCliente;
		
		if (!DBAccess::Delete('TB_ClientesMigracion', $where))
			return false;
		
		return true;	
	}			
}

?>