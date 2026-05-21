<?php 

require_once('class.dbaccess.php');
require_once('class.remito.php');
require_once('class.filter.php');
require_once('class.page.php');

class Remitos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['NumeroComprobante'])) && ($filter['NumeroComprobante'] != ''))
			$sql.= " AND NumeroComprobante LIKE '%" . DB::StringUnquoted($filter['NumeroComprobante']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Remitos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdRemito DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRemito = new Remito();
			$oRemito->ParseFromArray($oRow);
			
			array_push($arr, $oRemito);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Remitos";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRemito = new Remito();
		$oRemito->ParseFromArray($oRow);
		
		return $oRemito;		
	}


	public function GetById($IdRemito)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Remitos";
		$sql.= " WHERE IdRemito = " . DB::Number($IdRemito);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRemito = new Remito();
		$oRemito->ParseFromArray($oRow);
		
		return $oRemito;		
	}
	
	public function GetByNumero($Numero)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Remitos";
		$sql.= " WHERE NumeroComprobante = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRemito = new Remito();
		$oRemito->ParseFromArray($oRow);
		
		return $oRemito;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Remitos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Remito $oRemito)
	{
		$arr = array
		(
			'IdMinuta' 						=> DB::Number($oRemito->IdMinuta),
			'IdComprobante' 				=> DB::Number($oRemito->IdComprobante),
			'NumeroComprobante' 			=> DB::String($oRemito->NumeroComprobante),
			'Fecha' 						=> DB::Date($oRemito->Fecha),
			'Transporte' 					=> DB::String($oRemito->Transporte),
			'TransporteClaveFiscalTipo' 	=> DB::Number($oRemito->TransporteClaveFiscalTipo),
			'TransporteClaveFiscalNumero' 	=> DB::String($oRemito->TransporteClaveFiscalNumero)
		);
		
		if (!$this->Insert('TB_Remitos', $arr))
			return false;

		/* asignamos el id generado */
		$oRemito->IdRemito = DBAccess::GetLastInsertId();
			
		return $oRemito;
	}
	
	
	public function Delete($IdRemito)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdRemito = " . DB::Number($IdRemito);

		if (!DBAccess::Delete('TB_Remitos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>