<?php

require_once('class.dbaccess.php');
require_once('class.modulo.php');
require_once('class.filter.php');
require_once('class.page.php');

class Modulos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modulos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModulo = new Modulo();
			$oModulo->ParseFromArray($oRow);
			
			array_push($arr, $oModulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdModulo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modulos";
		$sql.= " WHERE IdModulo = ".DB::Number($IdModulo);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oModulo = new Modulo();
		$oModulo->ParseFromArray($oRow);
		
		return $oModulo;
	}


	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modulos";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModulo = new Modulo();
		$oModulo->ParseFromArray($oRow);
		
		return $oModulo;		
	}
	

	public function GetLastInsertId()
	{
		$sql = "SELECT IdModulo";
		$sql.= " FROM TB_Modulos m";
		$sql.= " ORDER BY IdModulo DESC";
		$sql.= " LIMIT 1";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$LastInsertId = $oRow['IdModulo'];

		return $LastInsertId;
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modulos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}


	public function Create(Modulo $oModulo)
	{
		$arr = array
		(
			'Codigo' => DB::String($oModulo->Codigo),
			'Nombre' => DB::String($oModulo->Nombre)
		);
	
		if (!$this->Insert('TB_Modulos', $arr))
			return false;

		/* asignamos el id generado */
		$oModulo->IdModulo = DBAccess::GetLastInsertId();
			
		return $oModulo;
	}
	
	
	public function Update(Modulo $oModulo)
	{
		$where = " IdModulo = " . (int)$oModulo->IdModulo;

		$arr = array
		(
			'Codigo' => DB::String($oModulo->Codigo),
			'Nombre' => DB::String($oModulo->Nombre)
		);
				
		if (!DBAccess::Update('TB_Modulos', $arr, $where))
			return false;
		
		return $oModulo;
	}
	

	public function Delete($IdModulo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdModulo = " . (int)$IdModulo;

		if (!DBAccess::Delete('TB_PerfilModulos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_ModuloPermisos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_Modulos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>