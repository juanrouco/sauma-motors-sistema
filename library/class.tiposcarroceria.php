<?php 

require_once('class.dbaccess.php');
require_once('class.tipocarroceria.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class TiposCarroceria extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposCarroceria";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoCarroceria = new TipoCarroceria();
			$oTipoCarroceria->ParseFromArray($oRow);
			
			array_push($arr, $oTipoCarroceria);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdTipoCarroceria)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCarroceria";
		$sql.= " WHERE IdTipoCarroceria = " . DB::Number($IdTipoCarroceria);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoCarroceria = new TipoCarroceria();
		$oTipoCarroceria->ParseFromArray($oRow);
		
		return $oTipoCarroceria;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCarroceria";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoCarroceria = new TipoCarroceria();
		$oTipoCarroceria->ParseFromArray($oRow);
		
		return $oTipoCarroceria;		
	}


	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCarroceria";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoCarroceria = new TipoCarroceria();
		$oTipoCarroceria->ParseFromArray($oRow);
		
		return $oTipoCarroceria;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposCarroceria";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoCarroceria $oTipoCarroceria)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoCarroceria->Codigo),
			'Nombre' 	=> DB::String($oTipoCarroceria->Nombre)
		);
		
		if (!$this->Insert('TB_TiposCarroceria', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoCarroceria->IdTipoCarroceria = DBAccess::GetLastInsertId();
			
		return $oTipoCarroceria;
	}
	
	
	public function Update(TipoCarroceria $oTipoCarroceria)
	{
		$where = " IdTipoCarroceria = " . DB::Number($oTipoCarroceria->IdTipoCarroceria);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoCarroceria->Codigo),
			'Nombre' 	=> DB::String($oTipoCarroceria->Nombre)
		);
		
		if (!DBAccess::Update('TB_TiposCarroceria', $arr, $where))
			return false;
		
		return $oTipoCarroceria;
	}
	
	
	public function Delete($IdTipoCarroceria)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoCarroceria = " . DB::Number($IdTipoCarroceria);
		if (!DBAccess::Delete('TB_TiposCarroceria', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>