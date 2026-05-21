<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.gestoria.php');
require_once('class.gestoriasocio.php');

class GestoriaSocios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaSocios";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdGestoria, Porcentaje DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoriaSocio = new GestoriaSocio();
			$oGestoriaSocio->ParseFromArray($oRow);
			
			array_push($arr, $oGestoriaSocio);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByGestoria(Gestoria $oGestoria)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaSocios";
		$sql.= " WHERE IdGestoria = " . DB::Number($oGestoria->IdGestoria);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoriaSocio = new GestoriaSocio();
			$oGestoriaSocio->ParseFromArray($oRow);
			
			array_push($arr, $oGestoriaSocio);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdSocio)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaSocios";
		$sql.= " WHERE IdGestoriaSocio = " . DB::Number($IdSocio);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oGestoriaSocio = new GestoriaSocio();
		$oGestoriaSocio->ParseFromArray($oRow);

		return $oGestoriaSocio;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaSocios";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(GestoriaSocio $oGestoriaSocio)
	{
		$arr = array
		(
			'IdGestoria'		=> DB::Number($oGestoriaSocio->IdGestoria),
			'IdCliente'			=> DB::Number($oGestoriaSocio->IdCliente),
			'Porcentaje'		=> DB::Number($oGestoriaSocio->Porcentaje)
		);
		return $arr;
	}
	
	public function Create(GestoriaSocio $oGestoriaSocio)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oGestoriaSocio);

		if (!DBAccess::Insert('TB_GestoriaSocios', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oGestoriaSocio->IdGestoriaSocio = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oGestoriaSocio;
	}
	
	
	public function Update(GestoriaSocio $oGestoriaSocio)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetArrayDB($oGestoriaSocio);

		$where = " IdGestoriaSocio = " . (int)$oGestoriaSocio->IdGestoriaSocio;
		
		if (!DBAccess::Update('TB_GestoriaSocios', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oGestoriaSocio;
	}
	
	
	public function Delete($IdGestoriaSocio)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdGestoriaSocio = " . DB::Number($IdGestoriaSocio);

		if (!DBAccess::Delete('TB_GestoriaSocios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	


	public function DeleteByGestoria(Gestoria $oGestoria)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdGestoria = " . DB::Number($oGestoria->IdGestoria);

		if (!DBAccess::Delete('TB_GestoriaSocios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>