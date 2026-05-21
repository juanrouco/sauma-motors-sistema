<?php 

require_once('class.dbaccess.php');
require_once('class.tipoiva.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposIva extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposIva";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoIva = new TipoIva();
			$oTipoIva->ParseFromArray($oRow);
			
			array_push($arr, $oTipoIva);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdTipoIva)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposIva";
		$sql.= " WHERE IdTipoIva = " . DB::Number($IdTipoIva);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoIva = new TipoIva();
		$oTipoIva->ParseFromArray($oRow);
		
		return $oTipoIva;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposIva";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoIva = new TipoIva();
		$oTipoIva->ParseFromArray($oRow);
		
		return $oTipoIva;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposIva";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoIva $oTipoIva)
	{
		$arr = array
		(
			'Nombre' 		=> DB::String($oTipoIva->Nombre),
			'Codigo' 		=> DB::String($oTipoIva->Codigo),
			'CodigoAfip' 	=> DB::String($oTipoIva->CodigoAfip)
		);
		
		if (!$this->Insert('TB_TiposIva', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoIva->IdTipoIva = DBAccess::GetLastInsertId();
			
		return $oTipoIva;
	}
	
	
	public function Update(TipoIva $oTipoIva)
	{
		$where = " IdTipoIva = " . DB::Number($oTipoIva->IdTipoIva);
		
		$arr = array
		(
			'Nombre' 		=> DB::String($oTipoIva->Nombre),
			'Codigo' 		=> DB::String($oTipoIva->Codigo),
			'CodigoAfip' 	=> DB::String($oTipoIva->CodigoAfip)
		);
		
		if (!DBAccess::Update('TB_TiposIva', $arr, $where))
			return false;
		
		return $oTipoIva;
	}
	

	public function Delete($IdTipoIva)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoIva = " . DB::Number($IdTipoIva);

		if (!DBAccess::Delete('TB_TiposIva', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>