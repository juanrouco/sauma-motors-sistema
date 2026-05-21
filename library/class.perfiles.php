<?php

require_once('class.dbaccess.php');
require_once('class.perfil.php');
require_once('class.filter.php');
require_once('class.page.php');

class Perfiles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}

	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT p.*";
		$sql.= " FROM TB_Perfiles p";
		$sql.= " ORDER BY IdPerfil";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPerfil = new Perfil();
			$oPerfil->ParseFromArray($oRow);
			
			array_push($arr, $oPerfil);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdPerfil)
	{
		$sql = "SELECT p.*";
		$sql.= " FROM TB_Perfiles p";
		$sql.= " WHERE p.IdPerfil = ".DB::Number($IdPerfil);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPerfil = new Perfil();
		$oPerfil->ParseFromArray($oRow);
		
		return $oPerfil;
	}
	
	
	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Perfiles";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPerfil = new Perfil();
		$oPerfil->ParseFromArray($oRow);
		
		return $oPerfil;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Perfiles";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
		
	public function Create(Perfil $oPerfil)
	{
		$arr = array
		(
			'Codigo' => DB::String($oPerfil->Codigo),
			'Nombre' => DB::String($oPerfil->Nombre)
		);
		
		if (!$this->Insert('TB_Perfiles', $arr))
			return false;

		/* asignamos el id generado */
		$oPerfil->IdPerfil = DBAccess::GetLastInsertId();
			
		return $oPerfil;
	}
	
	
	public function Update(Perfil $oPerfil)
	{
		$where = " IdPerfil = " . DB::Number($oPerfil->IdPerfil);
		
		$arr = array
		(
			'Codigo' => DB::String($oPerfil->Codigo),
			'Nombre' => DB::String($oPerfil->Nombre)
		);
		
		if (!DBAccess::Update('TB_Perfiles', $arr, $where))
			return false;
		
		return $oPerfil;
	}
	

	public function Delete($IdPerfil)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPerfil = " . DB::Number($IdPerfil);
		if (!DBAccess::Delete('TB_Perfiles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}
?>