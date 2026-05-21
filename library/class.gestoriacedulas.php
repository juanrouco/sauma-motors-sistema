<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.gestoria.php');
require_once('class.gestoriacedula.php');

class GestoriaCedulas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaCedulas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Apellido, Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoriaCedula = new GestoriaCedula();
			$oGestoriaCedula->ParseFromArray($oRow);
			
			array_push($arr, $oGestoriaCedula);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByGestoria(Gestoria $oGestoria)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaCedulas";
		$sql.= " WHERE IdGestoria = " . DB::Number($oGestoria->IdGestoria);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoriaCedula = new GestoriaCedula();
			$oGestoriaCedula->ParseFromArray($oRow);
			
			array_push($arr, $oGestoriaCedula);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByTipoDocumento(TipoDocumento $oTipoDocumento)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_GestoriaCedulas";
		$sql.= " WHERE DocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoriaCedula = new GestoriaCedula();
			$oGestoriaCedula->ParseFromArray($oRow);
			
			array_push($arr, $oGestoriaCedula);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetById($IdCedula)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaCedulas";
		$sql.= " WHERE IdCedula = " . DB::Number($IdCedula);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oGestoriaCedula = new GestoriaCedula();
		$oGestoriaCedula->ParseFromArray($oRow);

		return $oGestoriaCedula;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_GestoriaCedulas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(GestoriaCedula $oGestoriaCedula)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdGestoria'		=> DB::Number($oGestoriaCedula->IdGestoria),
			'Nombre'			=> DB::String($oGestoriaCedula->Nombre),
			'Apellido'			=> DB::String($oGestoriaCedula->Apellido),
			'DocumentoTipo'		=> DB::Number($oGestoriaCedula->DocumentoTipo),
			'DocumentoNumero'	=> DB::String($oGestoriaCedula->DocumentoNumero)
		);

		if (!DBAccess::Insert('TB_GestoriaCedulas', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oGestoriaCedula->IdCedula = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oGestoriaCedula;
	}
	
	
	public function Update(GestoriaCedula $oGestoriaCedula)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdGestoria'		=> DB::Number($oGestoriaCedula->IdGestoria),
			'Nombre'			=> DB::String($oGestoriaCedula->Nombre),
			'Apellido'			=> DB::String($oGestoriaCedula->Apellido),
			'DocumentoTipo'		=> DB::Number($oGestoriaCedula->DocumentoTipo),
			'DocumentoNumero'	=> DB::String($oGestoriaCedula->DocumentoNumero)
		);

		$where = " IdCedula = " . (int)$oGestoriaCedula->IdCedula;
		
		if (!DBAccess::Update('TB_GestoriaCedulas', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oGestoriaCedula;
	}
	
	
	public function Delete($IdCedula)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCedula = " . DB::Number($IdCedula);

		if (!DBAccess::Delete('TB_GestoriaCedulas', $where))
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

		if (!DBAccess::Delete('TB_GestoriaCedulas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>