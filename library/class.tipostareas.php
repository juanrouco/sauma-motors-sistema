<?php 

require_once('class.dbaccess.php');
require_once('class.tipotarea.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposTareas extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposTareas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoTarea = new TipoTarea();
			$oTipoTarea->ParseFromArray($oRow);
			
			array_push($arr, $oTipoTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	
	public function GetById($IdTipoTarea)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposTareas";
		$sql.= " WHERE IdTipoTarea = " . DB::Number($IdTipoTarea);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoTarea = new TipoTarea();
		$oTipoTarea->ParseFromArray($oRow);
		
		return $oTipoTarea;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposTareas";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoTarea = new TipoTarea();
		$oTipoTarea->ParseFromArray($oRow);
		
		return $oTipoTarea;		
	}
	
	public function GetByNacionalidad($Nacionalidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposTareas";
		$sql.= " WHERE Nacionalidad RLIKE " . DB::String($Nacionalidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoTarea = new TipoTarea();
		$oTipoTarea->ParseFromArray($oRow);
		
		return $oTipoTarea;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposTareas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
		
	public function Create(TipoTarea $oTipoTarea)
	{
		$arr = array
		(
			'Nombre' 	=> DB::String($oTipoTarea->Nombre)
		);
		
		if (!$this->Insert('TB_TiposTareas', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoTarea->IdTipoTarea = DBAccess::GetLastInsertId();
			
		return $oTipoTarea;
	}
	
	
	public function Update(TipoTarea $oTipoTarea)
	{
		$where = " IdTipoTarea = " . DB::Number($oTipoTarea->IdTipoTarea);
		
		$arr = array
		(
			'Nombre' 	=> DB::String($oTipoTarea->Nombre)
		);
		
		if (!DBAccess::Update('TB_TiposTareas', $arr, $where))
			return false;
		
		return $oTipoTarea;
	}
	

	public function Delete($IdTipoTarea)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoTarea = " . DB::Number($IdTipoTarea);
		if (!DBAccess::Delete('TB_TiposTareas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>