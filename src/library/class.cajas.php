<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.caja.php');
require_once('class.filter.php');
require_once('class.page.php');

class Cajas extends DBAccess implements IFilterable
{
	
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM tb_Cajas c";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM tb_Cajas c";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY c.IdCaja DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCaja = new Caja();
			$oCaja->ParseFromArray($oRow);
			
			array_push($arr, $oCaja);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetById($IdCaja)
	{
		$sql = "SELECT c.*";
		$sql.= " FROM tb_Cajas c";
		$sql.= " WHERE IdCaja = ".DB::Number($IdCaja);	
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCaja = new Caja();
		$oCaja->ParseFromArray($oRow);
		
		return $oCaja;		
	}


	private function GetArrayDB(Caja $oCaja)
	{
		$arr = array
		(
			'TotalRendir' 			=> DB::Number($oCaja->TotalRendir),
			'TotalDeudas' 			=> DB::Number($oCaja->TotalDeudas),
			'TotalDetalles'			=> DB::Number($oCaja->TotalDetalles),
			'FechaUltimoMovimiento' => DB::Date($oCaja->FechaUltimoMovimiento)
		);
		
		return $arr;
	}

	public function Create(Caja $oCaja)
	{
		$oCaja->FechaUltimoMovimiento = date('Y-m-d H:i:s');

		$arr = $this->GetArrayDB($oCaja);
				
		if ( !$this->Insert('tb_Cajas', $arr) )
			return false;
				
		$oCaja->IdCaja = DBAccess::GetLastInsertId();
		
		return $oCaja;
	}
	
	
	public function Update(Caja $oCaja)
	{
		$where = " IdCaja = " . (int)$oCaja->IdCaja;
		
		$oCaja->FechaUltimoMovimiento = date('Y-m-d H:i:s');

		$arr = $this->GetArrayDB($oCaja);
				
		if ( !DBAccess::Update('tb_Cajas', $arr, $where) )
			return false;
			
		return $oCaja;
	}
	
	
	public function Delete($IdCaja)
	{
		/* inicia una CuentaCorriente */
		if (!DBAccess::$db->Begin())
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$where = " IdCaja = " . DB::Number($IdCaja);
		if ( !DBAccess::Delete('tb_CajasDetalles', $where) )
		{
			DBAccess::$db->Rollback();	
			return false;
		}	
		if ( !DBAccess::Delete('tb_Cajas', $where) )
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la CuentaCorriente */
		DBAccess::$db->Commit();
					
		return true;
	}
}

?>