<?php 

require_once('class.dbaccess.php');
require_once('class.estadocivil.php');
require_once('class.filter.php');
require_once('class.page.php');

class EstadosCiviles extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_EstadosCiviles";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oEstadoCivil = new EstadoCivil();
			$oEstadoCivil->ParseFromArray($oRow);
			
			array_push($arr, $oEstadoCivil);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdEstadoCivil)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosCiviles";
		$sql.= " WHERE IdEstadoCivil = " . DB::Number($IdEstadoCivil);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoCivil = new EstadoCivil();
		$oEstadoCivil->ParseFromArray($oRow);
		
		return $oEstadoCivil;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosCiviles";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoCivil = new EstadoCivil();
		$oEstadoCivil->ParseFromArray($oRow);
		
		return $oEstadoCivil;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosCiviles";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(EstadoCivil $oEstadoCivil)
	{
		$arr = array
		(
			'Nombre' => DB::String($oEstadoCivil->Nombre),
			'Codigo' => DB::String($oEstadoCivil->Codigo)
		);
		
		if (!$this->Insert('TB_EstadosCiviles', $arr))
			return false;

		/* asignamos el id generado */
		$oEstadoCivil->IdEstadoCivil = DBAccess::GetLastInsertId();
			
		return $oEstadoCivil;
	}
	
	
	public function Update(EstadoCivil $oEstadoCivil)
	{
		$where = " IdEstadoCivil = " . DB::Number($oEstadoCivil->IdEstadoCivil);
		
		$arr = array
		(
			'Nombre' => DB::String($oEstadoCivil->Nombre),
			'Codigo' => DB::String($oEstadoCivil->Codigo)
		);
		
		if (!DBAccess::Update('TB_EstadosCiviles', $arr, $where))
			return false;
		
		return $oEstadoCivil;
	}
	

	public function Delete($IdEstadoCivil)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdEstadoCivil = " . DB::Number($IdEstadoCivil);

		if (!DBAccess::Delete('TB_EstadosCiviles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>