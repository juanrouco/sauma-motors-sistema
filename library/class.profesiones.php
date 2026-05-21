<?php 

require_once('class.dbaccess.php');
require_once('class.profesion.php');
require_once('class.filter.php');
require_once('class.page.php');

class Profesiones extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Profesiones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oProfesion = new Profesion();
			$oProfesion->ParseFromArray($oRow);
			
			array_push($arr, $oProfesion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdProfesion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Profesiones";
		$sql.= " WHERE IdProfesion = " . DB::Number($IdProfesion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oProfesion = new Profesion();
		$oProfesion->ParseFromArray($oRow);
		
		return $oProfesion;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Profesiones";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oProfesion = new Profesion();
		$oProfesion->ParseFromArray($oRow);
		
		return $oProfesion;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Profesiones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Profesion $oProfesion)
	{
		$arr = array
		(
			'Nombre' => DB::String($oProfesion->Nombre),
			'Codigo' => DB::String($oProfesion->Codigo)
		);
		
		if (!$this->Insert('TB_Profesiones', $arr))
			return false;

		/* asignamos el id generado */
		$oProfesion->IdProfesion = DBAccess::GetLastInsertId();
			
		return $oProfesion;
	}
	
	
	public function Update(Profesion $oProfesion)
	{
		$where = " IdProfesion = " . DB::Number($oProfesion->IdProfesion);
		
		$arr = array
		(
			'Nombre' => DB::String($oProfesion->Nombre),
			'Codigo' => DB::String($oProfesion->Codigo)
		);
		
		if (!DBAccess::Update('TB_Profesiones', $arr, $where))
			return false;
		
		return $oProfesion;
	}
	

	public function Delete($IdProfesion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdProfesion = " . DB::Number($IdProfesion);

		if (!DBAccess::Delete('TB_Profesiones', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>