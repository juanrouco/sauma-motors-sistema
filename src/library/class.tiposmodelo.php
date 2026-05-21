<?php 

require_once('class.dbaccess.php');
require_once('class.tipomodelo.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposModelo extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposModelo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoModelo = new TipoModelo();
			$oTipoModelo->ParseFromArray($oRow);
			
			array_push($arr, $oTipoModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdTipoModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposModelo";
		$sql.= " WHERE IdTipoModelo = " . DB::Number($IdTipoModelo);	
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoModelo = new TipoModelo();
		$oTipoModelo->ParseFromArray($oRow);
		
		return $oTipoModelo;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposModelo";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoModelo = new TipoModelo();
		$oTipoModelo->ParseFromArray($oRow);
		
		return $oTipoModelo;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposModelo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoModelo $oTipoModelo)
	{
		$arr = array
		(
			'Nombre' => DB::String($oTipoModelo->Nombre),
			'Codigo' => DB::String($oTipoModelo->Codigo)
		);
		
		if (!$this->Insert('TB_TiposModelo', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoModelo->IdTipoModelo = DBAccess::GetLastInsertId();
			
		return $oTipoModelo;
	}
	
	
	public function Update(TipoModelo $oTipoModelo)
	{
		$where = " IdTipoModelo = " . DB::Number($oTipoModelo->IdTipoModelo);
		
		$arr = array
		(
			'Nombre' => DB::String($oTipoModelo->Nombre),
			'Codigo' => DB::String($oTipoModelo->Codigo)
		);
		
		if (!DBAccess::Update('TB_TiposModelo', $arr, $where))
			return false;
		
		return $oTipoModelo;
	}
	

	public function Delete($IdTipoModelo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoModelo = " . DB::Number($IdTipoModelo);

		if (!DBAccess::Delete('TB_TiposModelo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>