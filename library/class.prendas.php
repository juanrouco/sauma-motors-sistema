<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.prenda.php');
require_once('class.filter.php');
require_once('class.page.php');

class Prendas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Prendas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oPrenda = new Prenda();
			$oPrenda->ParseFromArray($oRow);
			
			array_push($arr, $oPrenda);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByAcreedor(Acreedor $oAcreedor)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_Prendas";
		$sql.= " WHERE IdAcreedor = " . DB::Number($oAcreedor->IdAcreedor);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oPrenda = new Prenda();
			$oPrenda->ParseFromArray($oRow);
			
			array_push($arr, $oPrenda);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdPrenda)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Prendas";
		$sql.= " WHERE IdPrenda = " . DB::Number($IdPrenda);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPrenda = new Prenda();
		$oPrenda->ParseFromArray($oRow);

		return $oPrenda;		
	}


	public function GetByIdGestoria($IdGestoria)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Prendas";
		$sql.= " WHERE IdGestoria = " . DB::Number($IdGestoria);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPrenda = new Prenda();
		$oPrenda->ParseFromArray($oRow);

		return $oPrenda;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Prendas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Prenda $oPrenda)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdGestoria'					=> DB::Number($oPrenda->IdGestoria),
			'IdAcreedor'					=> DB::Number($oPrenda->IdAcreedor),
			'FinanciacionCapital'			=> DB::Number($oPrenda->FinanciacionCapital),
			'CantidadCuotas'				=> DB::Number($oPrenda->CantidadCuotas),
			'ImporteCuota'					=> DB::Number($oPrenda->ImporteCuota),
			'FechaVencimientoPrimerCuota'	=> DB::Date($oPrenda->FechaVencimientoPrimerCuota),
			'TasaNominal'					=> DB::Number($oPrenda->TasaNominal),
			'TasaEfectiva'					=> DB::Number($oPrenda->TasaEfectiva),
			'CostoFinancieroTotal'			=> DB::Number($oPrenda->CostoFinancieroTotal),
			'Observaciones'					=> DB::String($oPrenda->Observaciones)
		);

		if (!DBAccess::Insert('TB_Prendas', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oPrenda->IdPrenda = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oPrenda;
	}
	
	
	public function Update(Prenda $oPrenda)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdGestoria'					=> DB::Number($oPrenda->IdGestoria),
			'IdAcreedor'					=> DB::Number($oPrenda->IdAcreedor),
			'FinanciacionCapital'			=> DB::Number($oPrenda->FinanciacionCapital),
			'CantidadCuotas'				=> DB::Number($oPrenda->CantidadCuotas),
			'ImporteCuota'					=> DB::Number($oPrenda->ImporteCuota),
			'FechaVencimientoPrimerCuota'	=> DB::Date($oPrenda->FechaVencimientoPrimerCuota),
			'TasaNominal'					=> DB::Number($oPrenda->TasaNominal),
			'TasaEfectiva'					=> DB::Number($oPrenda->TasaEfectiva),
			'CostoFinancieroTotal'			=> DB::Number($oPrenda->CostoFinancieroTotal),
			'Observaciones'					=> DB::String($oPrenda->Observaciones)
		);

		$where = " IdPrenda = " . (int)$oPrenda->IdPrenda;
		
		if (!DBAccess::Update('TB_Prendas', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oPrenda;
	}
	
	
	public function Delete($IdPrenda)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPrenda = " . DB::Number($IdPrenda);

		if (!DBAccess::Delete('TB_PrendaFiadores', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		
		if (!DBAccess::Delete('TB_PrendaConyuges', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		
		if (!DBAccess::Delete('TB_Prendas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>