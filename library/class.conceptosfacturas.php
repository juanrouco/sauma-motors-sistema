<?php 

require_once('class.dbaccess.php');
require_once('class.conceptofactura.php');
require_once('class.filter.php');
require_once('class.page.php');


class ConceptosFacturas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ConceptosFacturas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oConceptoFactura = new ConceptoFactura();
			$oConceptoFactura->ParseFromArray($oRow);
			
			array_push($arr, $oConceptoFactura);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetById($IdConceptoFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ConceptosFacturas";
		$sql.= " WHERE IdConceptoFactura = " . DB::Number($IdConceptoFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oConceptoFactura = new ConceptoFactura();
		$oConceptoFactura->ParseFromArray($oRow);
		
		return $oConceptoFactura;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ConceptosFacturas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(ConceptoFactura $oConceptoFactura)
	{
		$arr = array
		(
			'Nombre' 			=> DB::String($oConceptoFactura->Nombre),
			'IvaGravado' 		=> DB::Number($oConceptoFactura->IvaGravado)
		);
		
		if (!$this->Insert('TB_ConceptosFacturas', $arr))
			return false;

		/* asignamos el id generado */
		$oConceptoFactura->IdConceptoFactura = DBAccess::GetLastInsertId();
			
		return $oConceptoFactura;
	}
	
	
	public function Update(ConceptoFactura $oConceptoFactura)
	{
		$where = " IdConceptoFactura = " . DB::Number($oConceptoFactura->IdConceptoFactura);
		
		$arr = array
		(
			'Nombre' 			=> DB::String($oConceptoFactura->Nombre),
			'IvaGravado' 		=> DB::Number($oConceptoFactura->IvaGravado)
		);
		
		if (!DBAccess::Update('TB_ConceptosFacturas', $arr, $where))
			return false;
		
		return $oConceptoFactura;
	}
}

?>