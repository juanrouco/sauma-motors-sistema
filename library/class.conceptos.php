<?php 

require_once('class.dbaccess.php');
require_once('class.concepto.php');
require_once('class.filter.php');
require_once('class.page.php');

class Conceptos extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_Conceptos";
		
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
		$sql.= " FROM TB_Conceptos";

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
			$oConcepto = new Concepto();
			$oConcepto->ParseFromArray($oRow);
			
			array_push($arr, $oConcepto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdConcepto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Conceptos";
		$sql.= " WHERE IdConcepto = " . DB::Number($IdConcepto);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oConcepto = new Concepto();
		$oConcepto->ParseFromArray($oRow);
		
		return $oConcepto;		
	}
	


	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Conceptos";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oConcepto = new Concepto();
			$oConcepto->ParseFromArray($oRow);
			
			array_push($arr, $oConcepto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Conceptos";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Concepto $oConcepto)
	{
		$arr = array
		(
			'Nombre' 	=> DB::String($oConcepto->Nombre)
		);
		
		if (!$this->Insert('TB_Conceptos', $arr))
			return false;
		$oConcepto->IdConcepto = DBAccess::GetLastInsertId();
			
		return $oConcepto;
	}
	
	
	public function Update(Concepto $oConcepto)
	{
		$where = " IdConcepto = " . DB::Number($oConcepto->IdConcepto);
		
		$arr = array
		(
			'Nombre' 	=> DB::String($oConcepto->Nombre)
		);
		
		if (!DBAccess::Update('TB_Conceptos', $arr, $where))
			return false;
		
		return $oConcepto;
	}
	

	public function Delete($IdConcepto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdConcepto = " . DB::Number($IdConcepto);
		if (!DBAccess::Delete('TB_Conceptos', $where))
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
		
		$FileName = "Conceptos.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrConceptos = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Concepto";
		$csv.= $Separador;
		$csv.= "Disponible";
		$csv.= $SaltoLinea;
	
		foreach ($arrConceptos as $oConcepto)
		{
			$Activo = ($oConcepto->Activo == '1') ? "Si" : "No";
			
			$csv.= str_replace('(\t|\n)','', trim($oConcepto->Nombre));
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