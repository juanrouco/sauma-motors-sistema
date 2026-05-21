<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.perfil.php');
require_once('class.perfilpermiso.php');
require_once('class.filter.php');
require_once('class.page.php');

class PerfilPermisos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT ap.*";
		$sql.= " FROM TB_PerfilPermisos ep";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT ap.*";
		$sql.= " FROM TB_PerfilPermisos ep";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY IdPerfil";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPerfilPermiso = new PerfilPermiso();
			$oPerfilPermiso->ParseFromArray($oRow);
			
			array_push($arr, $oPerfilPermiso);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByPerfil(Perfil $oPerfil)
	{
		$sql = "SELECT ap.*";
		$sql.= " FROM TB_PerfilPermisos ap";
		$sql.= " WHERE IdPerfil = " . DB::Number($oPerfil->IdPerfil);
		$sql.= " GROUP BY IdPermiso";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPerfilPermiso = new PerfilPermiso();
			$oPerfilPermiso->ParseFromArray($oRow);
			
			array_push($arr, $oPerfilPermiso);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdPerfil, $IdPermiso)
	{
		$sql = "SELECT ap.*";
		$sql.= " FROM TB_PerfilPermisos ap";
		$sql.= " WHERE ap.IdPerfil = " . DB::Number($IdPerfil);	
		$sql.= " AND ap.IdPermiso = " . DB::Number($IdPermiso);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPerfilPermiso = new PerfilPermiso();
		$oPerfilPermiso->ParseFromArray($oRow);

		return $oPerfilPermiso;		
	}


	public function Create(PerfilPermiso $oPerfilPermiso)
	{
		$arr = array
		(
			'IdPerfil'	=> DB::Number($oPerfilPermiso->IdPerfil),
			'IdPermiso'	=> DB::Number($oPerfilPermiso->IdPermiso)
		);

		if (!$this->Insert('TB_PerfilPermisos', $arr))
			return false;
			
		return $oPerfilPermiso;
	}
	
		
	public function Delete($IdPerfil, $IdPermiso)
	{
		$where = " IdPerfil = " . DB::Number($IdPerfil);
		$where.= " AND IdPermiso = " . DB::Number($IdPermiso);
		
		if (!DBAccess::Delete('TB_PerfilPermisos', $where))
			return false;
		
		return true;	
	}
	
	
	public function DeleteByPerfil(Perfil $oPerfil)
	{
		$where = " IdPerfil = ".DB::Number($oPerfil->IdPerfil);
		
		if (!DBAccess::Delete('TB_PerfilPermisos', $where))
			return false;
		
		return true;	
	}
}

?>