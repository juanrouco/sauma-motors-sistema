<?php 

require_once('class.dbaccess.php');
require_once('class.formapago.php');
require_once('class.filter.php');
require_once('class.page.php');

class FormasPago extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE Disponible = 1';
		
		if (isset($filter['Nombre']) && $filter['Nombre'] != '')
		$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FormasPago";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFormaPago";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormaPago = new FormaPago();
			$oFormaPago->ParseFromArray($oRow);
			
			array_push($arr, $oFormaPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllTarjetas(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FormasPago";
		$sql.= " WHERE IdFormaPago = 2 OR IdFormaPago = 3";
		$sql.= " ORDER BY IdFormaPago";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormaPago = new FormaPago();
			$oFormaPago->ParseFromArray($oRow);
			
			array_push($arr, $oFormaPago);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdFormaPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FormasPago";
		$sql.= " WHERE IdFormaPago = " . DB::Number($IdFormaPago);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormaPago = new FormaPago();
		$oFormaPago->ParseFromArray($oRow);
		
		return $oFormaPago;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FormasPago";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormaPago = new FormaPago();
		$oFormaPago->ParseFromArray($oRow);
		
		return $oFormaPago;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FormasPago";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFormaPago";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(FormaPago $oFormaPago)
	{
		$arr = array
		(
			'Nombre' => DB::String($oFormaPago->Nombre),
			'Disponible' 	=> DB::Bool($oFormaPago->Disponible)
		);
		
		if (!$this->Insert('TB_FormasPago', $arr))
			return false;

		/* asignamos el id generado */
		$oFormaPago->IdFormaPago = DBAccess::GetLastInsertId();
			
		return $oFormaPago;
	}
	
	
	public function Update(FormaPago $oFormaPago)
	{
		$where = " IdFormaPago = " . DB::Number($oFormaPago->IdFormaPago);
		
		$arr = array
		(
			'Nombre' 		=> DB::String($oFormaPago->Nombre),
			'Disponible' 	=> DB::Bool($oFormaPago->Disponible)
		);
		
		if (!DBAccess::Update('TB_FormasPago', $arr, $where))
			return false;
		
		return $oFormaPago;
	}
	

	public function Delete($IdFormaPago)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFormaPago = " . DB::Number($IdFormaPago);

		if (!DBAccess::Delete('TB_FormasPago', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>