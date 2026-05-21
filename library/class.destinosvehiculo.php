<?php 

require_once('class.dbaccess.php');
require_once('class.destinovehiculo.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class DestinosVehiculo extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_DestinosVehiculo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oDestinoVehiculo = new DestinoVehiculo();
			$oDestinoVehiculo->ParseFromArray($oRow);
			
			array_push($arr, $oDestinoVehiculo);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdDestinoVehiculo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DestinosVehiculo";
		$sql.= " WHERE IdDestinoVehiculo = " . DB::Number($IdDestinoVehiculo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oDestinoVehiculo = new DestinoVehiculo();
		$oDestinoVehiculo->ParseFromArray($oRow);
		
		return $oDestinoVehiculo;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DestinosVehiculo";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oDestinoVehiculo = new DestinoVehiculo();
		$oDestinoVehiculo->ParseFromArray($oRow);
		
		return $oDestinoVehiculo;		
	}


	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DestinosVehiculo";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oDestinoVehiculo = new DestinoVehiculo();
		$oDestinoVehiculo->ParseFromArray($oRow);
		
		return $oDestinoVehiculo;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DestinosVehiculo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(DestinoVehiculo $oDestinoVehiculo)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oDestinoVehiculo->Codigo),
			'Nombre' 	=> DB::String($oDestinoVehiculo->Nombre)
		);
		
		if (!$this->Insert('TB_DestinosVehiculo', $arr))
			return false;

		/* asignamos el id generado */
		$oDestinoVehiculo->IdDestinoVehiculo = DBAccess::GetLastInsertId();
			
		return $oDestinoVehiculo;
	}
	
	
	public function Update(DestinoVehiculo $oDestinoVehiculo)
	{
		$where = " IdDestinoVehiculo = " . DB::Number($oDestinoVehiculo->IdDestinoVehiculo);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oDestinoVehiculo->Codigo),
			'Nombre' 	=> DB::String($oDestinoVehiculo->Nombre)
		);
		
		if (!DBAccess::Update('TB_DestinosVehiculo', $arr, $where))
			return false;
		
		return $oDestinoVehiculo;
	}
	

	public function Delete($IdDestinoVehiculo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdDestinoVehiculo = " . DB::Number($IdDestinoVehiculo);
		if (!DBAccess::Delete('TB_DestinosVehiculo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>