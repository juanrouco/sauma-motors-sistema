<?php 

require_once('class.dbaccess.php');
require_once('class.tipovehiculo.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class TiposVehiculo extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposVehiculo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoVehiculo = new TipoVehiculo();
			$oTipoVehiculo->ParseFromArray($oRow);
			
			array_push($arr, $oTipoVehiculo);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdTipoVehiculo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposVehiculo";
		$sql.= " WHERE IdTipoVehiculo = " . DB::Number($IdTipoVehiculo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoVehiculo = new TipoVehiculo();
		$oTipoVehiculo->ParseFromArray($oRow);
		
		return $oTipoVehiculo;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposVehiculo";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoVehiculo = new TipoVehiculo();
		$oTipoVehiculo->ParseFromArray($oRow);
		
		return $oTipoVehiculo;		
	}


	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposVehiculo";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoVehiculo = new TipoVehiculo();
		$oTipoVehiculo->ParseFromArray($oRow);
		
		return $oTipoVehiculo;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposVehiculo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoVehiculo $oTipoVehiculo)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoVehiculo->Codigo),
			'Nombre' 	=> DB::String($oTipoVehiculo->Nombre)
		);
		
		if (!$this->Insert('TB_TiposVehiculo', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoVehiculo->IdTipoVehiculo = DBAccess::GetLastInsertId();
			
		return $oTipoVehiculo;
	}
	
	
	public function Update(TipoVehiculo $oTipoVehiculo)
	{
		$where = " IdTipoVehiculo = " . DB::Number($oTipoVehiculo->IdTipoVehiculo);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oTipoVehiculo->Codigo),
			'Nombre' 	=> DB::String($oTipoVehiculo->Nombre)
		);
		
		if (!DBAccess::Update('TB_TiposVehiculo', $arr, $where))
			return false;
		
		return $oTipoVehiculo;
	}


	public function Delete($IdTipoVehiculo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoVehiculo = " . DB::Number($IdTipoVehiculo);
		if (!DBAccess::Delete('TB_TiposVehiculo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>