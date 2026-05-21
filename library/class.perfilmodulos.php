<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.perfil.php');
require_once('class.perfilmodulo.php');
require_once('class.filter.php');
require_once('class.page.php');

class PerfilModulos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT am.*";
		$sql.= " FROM TB_PerfilModulos am";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT am.*";
		$sql.= " FROM TB_PerfilModulos am";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY IdPerfil";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPerfilModulo = new PerfilModulo();
			$oPerfilModulo->ParseFromArray($oRow);
			
			array_push($arr, $oPerfilModulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByPerfil(Perfil $oPerfil)
	{
		$sql = "SELECT am.*";
		$sql.= " FROM TB_PerfilModulos am";
		$sql.= " WHERE IdPerfi = " . DB::Number($oPerfil->IdPerfil);
		$sql.= " GROUP BY IdModulo";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPerfilModulo = new PerfilModulo();
			$oPerfilModulo->ParseFromArray($oRow);
			
			array_push($arr, $oPerfilModulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdPerfil, $IdModulo)
	{
		$sql = "SELECT am.*";
		$sql.= " FROM TB_PerfilModulos am";
		$sql.= " WHERE am.IdPerfil = ".DB::Number($IdPerfil);	
		$sql.= " AND am.IdModulo = ".DB::Number($IdModulo);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPerfilModulo = new PerfilModulo();
		$oPerfilModulo->ParseFromArray($oRow);

		return $oPerfilModulo;		
	}


	public function Create(PerfilModulo $oPerfilModulo)
	{
		$arr = array
		(
			'IdPerfil'	=> DB::Number($oPerfilModulo->IdPerfil),
			'IdModulo'	=> DB::Number($oPerfilModulo->IdModulo)
		);

		if (!$this->Insert('TB_PerfilModulos', $arr))
			return false;
			
		return $oPerfilModulo;
	}
	
		
	public function Delete($IdPerfil, $IdModulo)
	{
		$where = " IdPerfil = ".DB::Number($IdPerfil);
		$where.= " AND IdModulo = ".DB::Number($IdModulo);
		
		if (!DBAccess::Delete('TB_PerfilModulos', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByPerfil(Perfil $oPerfil)
	{
		$where = " IdPerfil = ".DB::Number($oPerfil->IdPerfil);
		
		if (!DBAccess::Delete('TB_PerfilModulos', $where))
			return false;
		
		return true;	
	}
}

?>