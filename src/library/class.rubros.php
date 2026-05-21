<?php 

require_once('class.dbaccess.php');
require_once('class.rubro.php');
require_once('class.filter.php');
require_once('class.page.php');

class Rubros extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_Rubros";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['Count'];
		
		return ceil($Count);
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Rubros";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY Nombre";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRubro = new Rubro();
			$oRubro->ParseFromArray($oRow);
			
			array_push($arr, $oRubro);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdRubro)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Rubros";
		$sql.= " WHERE IdRubro = " . DB::Number($IdRubro);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRubro = new Rubro();
		$oRubro->ParseFromArray($oRow);
		
		return $oRubro;		
	}
	


	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Rubros";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRubro = new Rubro();
			$oRubro->ParseFromArray($oRow);
			
			array_push($arr, $oRubro);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Rubros";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Rubro $oRubro)
	{
		$arr = array
		(
			'Nombre' 	=> DB::String($oRubro->Nombre)
		);
		
		if (!$this->Insert('TB_Rubros', $arr))
			return false;
		$oRubro->IdRubro = DBAccess::GetLastInsertId();
			
		return $oRubro;
	}
	
	
	public function Update(Rubro $oRubro)
	{
		$where = " IdRubro = " . DB::Number($oRubro->IdRubro);
		
		$arr = array
		(
			'Nombre' 	=> DB::String($oRubro->Nombre)
		);
		
		if (!DBAccess::Update('TB_Rubros', $arr, $where))
			return false;
		
		return $oRubro;
	}
	

	public function Delete($IdRubro)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdRubro = " . DB::Number($IdRubro);
		if (!DBAccess::Delete('TB_Rubros', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	
	public function ExportCsv(array $filter = NULL)
	{
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "Rubros.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrRubros = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Rubro";
		$csv.= $Separador;
		$csv.= "Disponible";
		$csv.= $SaltoLinea;
	
		foreach ($arrRubros as $oRubro)
		{
			$Activo = ($oRubro->Activo == '1') ? "Si" : "No";
			
			$csv.= str_replace('(\t|\n)','', trim($oRubro->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($Activo));
			$csv.= $Separador;
			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>