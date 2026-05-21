<?php 

require_once('class.dbaccess.php');
require_once('class.tipouso.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class TiposUso extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
		{
			$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
			$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposUso";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoUso = new TipoUso();
			$oTipoUso->ParseFromArray($oRow);
			
			array_push($arr, $oTipoUso);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdTipoUso)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposUso";
		$sql.= " WHERE IdTipoUso = " . DB::Number($IdTipoUso);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoUso = new TipoUso();
		$oTipoUso->ParseFromArray($oRow);
		
		return $oTipoUso;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposUso";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoUso = new TipoUso();
		$oTipoUso->ParseFromArray($oRow);
		
		return $oTipoUso;		
	}


	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposUso";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoUso = new TipoUso();
		$oTipoUso->ParseFromArray($oRow);
		
		return $oTipoUso;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposUso";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoUso $oTipoUso)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoUso->Codigo),
			'Nombre' 	=> DB::String($oTipoUso->Nombre)
		);
		
		if (!$this->Insert('TB_TiposUso', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoUso->IdTipoUso = DBAccess::GetLastInsertId();
			
		return $oTipoUso;
	}
	
	
	public function Update(TipoUso $oTipoUso)
	{
		$where = " IdTipoUso = " . DB::Number($oTipoUso->IdTipoUso);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoUso->Codigo),
			'Nombre' 	=> DB::String($oTipoUso->Nombre)
		);
		
		if (!DBAccess::Update('TB_TiposUso', $arr, $where))
			return false;
		
		return $oTipoUso;
	}
	

	public function Delete($IdTipoUso)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoUso = " . DB::Number($IdTipoUso);
		if (!DBAccess::Delete('TB_TiposUso', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>