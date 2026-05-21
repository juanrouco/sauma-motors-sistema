<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.modulo.php');
require_once('class.modulopermiso.php');

class ModuloPermisos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ($filter['IdModulo'] != "")
		{	
			$sql.= " AND IdModulo = " . DB::Number($filter['IdModulo']);
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModuloPermisos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdModulo";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModuloPermiso = new ModuloPermiso();
			$oModuloPermiso->ParseFromArray($oRow);
			
			array_push($arr, $oModuloPermiso);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdModulo, $IdPermiso)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModuloPermisos";
		$sql.= " WHERE IdModulo = " . DB::Number($IdModulo);	
		$sql.= " AND IdPermiso = " . DB::Number($IdPermiso);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oModuloPermiso = new ModuloPermiso();
		$oModuloPermiso->ParseFromArray($oRow);
		
		return $oModuloPermiso;		
	}
	

	public function GetAllByModulo(Modulo $oModulo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModuloPermisos";
		$sql.= " WHERE IdModulo = " . DB::Number($oModulo->IdModulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModuloPermiso = new ModuloPermiso();
			$oModuloPermiso->ParseFromArray($oRow);
			
			array_push($arr, $oModuloPermiso);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function CheckPermModulo(Modulo $oModulo, $IdPermiso)
	{
		$sql = "SELECT mp.*";
		$sql.= " FROM TB_ModuloPermisos mp";
		$sql.= " WHERE mp.IdModulo = ".DB::Number($oModulo->IdModulo);
		$sql.= " AND mp.IdPermiso = ".DB::Number($IdPermiso);
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;

		return true;		
	}


	public function Create(ModuloPermiso $oModuloPermiso)
	{
		$arr = array
		(
			'IdModulo' 	=> DB::Number($oModuloPermiso->IdModulo),
			'IdPermiso' => DB::Number($oModuloPermiso->IdPermiso)
		);
	
		if (!$this->Insert('TB_ModuloPermisos', $arr))
			return false;
			
		return $oModuloPermiso;
	}
	
	
	public function Delete($IdModulo, $IdPermiso)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdModulo = " . (int)$IdModulo;
		$where.= " AND IdPermiso = " . (int)$IdPermiso;
		
		if ( !DBAccess::Delete('TB_ModuloPermisos', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	
	public function DeleteByModulo(Modulo $oModulo)
	{
		$where = " IdModulo = " . (int)$oModulo->IdModulo;
		
		if (!DBAccess::Delete('TB_ModuloPermisos', $where))
			return false;
		
		return true;	
	}			
}

?>