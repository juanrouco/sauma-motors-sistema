<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.comprobanteafip.php');
require_once('class.comprobantes.php');
require_once('class.filter.php');
require_once('class.page.php');

class ComprobantesAfip extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdComprobante'])) && ($filter['IdComprobante'] != ''))
			$sql.= " AND IdComprobante = " . DB::Number($filter['IdComprobante']);

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_ComprobantesAfip";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdComprobanteAfip";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobanteAfip = new ComprobanteAfip();
			$oComprobanteAfip->ParseFromArray($oRow);
			
			array_push($arr, $oComprobanteAfip);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdComprobanteAfip)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_ComprobantesAfip";
		$sql.= " WHERE IdComprobanteAfip = " . DB::Number($IdComprobanteAfip);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oComprobanteAfip = new ComprobanteAfip();
		$oComprobanteAfip->ParseFromArray($oRow);

		return $oComprobanteAfip;		
	}


	public function GetByIdComprobante($IdComprobante)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_ComprobantesAfip";
		$sql.= " WHERE IdComprobante = " . DB::Number($IdComprobante);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oComprobanteAfip = new ComprobanteAfip();
		$oComprobanteAfip->ParseFromArray($oRow);

		return $oComprobanteAfip;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_ComprobantesAfip";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function CreateArrayDB(ComprobanteAfip $oComprobanteAfip)
	{
		$arr = array
		(
			'IdComprobante'					=> DB::Number($oComprobanteAfip->IdComprobante),
			'IdTipoComprobanteAfip'			=> DB::Number($oComprobanteAfip->IdTipoComprobanteAfip),
			'PuntoVenta'					=> DB::Number($oComprobanteAfip->PuntoVenta),
			'Numero'						=> DB::Number($oComprobanteAfip->Numero),
			'Cae'							=> DB::String($oComprobanteAfip->Cae),
			'Fecha'							=> DB::String($oComprobanteAfip->Fecha),
			'IdConcepto'					=> DB::Number($oComprobanteAfip->IdConcepto),
			'TipoDocumento'					=> DB::String($oComprobanteAfip->TipoDocumento),
			'NumeroDocumento'				=> DB::String($oComprobanteAfip->NumeroDocumento),
			'Total'							=> DB::Number($oComprobanteAfip->Total),
			'TotalNoGravado'				=> DB::Number($oComprobanteAfip->TotalNoGravado),
			'TotalGravado'					=> DB::Number($oComprobanteAfip->TotalGravado),
			'ImporteIva21'					=> DB::Number($oComprobanteAfip->ImporteIva21),
			'ImporteIva10'					=> DB::Number($oComprobanteAfip->ImporteIva10),
			'ImporteIva'					=> DB::Number($oComprobanteAfip->ImporteIva),
			'ImportePercepcionIIBB'			=> DB::Number($oComprobanteAfip->ImportePercepcionIIBB),
			'ImporteImpuestoInterno'		=> DB::Number($oComprobanteAfip->ImporteImpuestoInterno),
			'ImporteImpuestos'				=> DB::Number($oComprobanteAfip->ImporteImpuestos),
			'ImporteExento'					=> DB::Number($oComprobanteAfip->ImporteExento),
			'FechaVencimiento'				=> DB::String($oComprobanteAfip->FechaVencimiento),
			'IdComprobanteAsociado'			=> DB::Number($oComprobanteAfip->IdComprobanteAsociado),
			'IdEstado'						=> DB::Number($oComprobanteAfip->IdEstado),
			'VencimientoCae'				=> DB::String($oComprobanteAfip->VencimientoCae),
			'CodigoTipoIva'					=> DB::Number($oComprobanteAfip->CodigoTipoIva)
		);
		
		return $arr;
	}
	
	
	public function Create(ComprobanteAfip $oComprobanteAfip)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->CreateArrayDB($oComprobanteAfip);

		if (!DBAccess::Insert('TB_ComprobantesAfip', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oComprobanteAfip->IdComprobanteAfip = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oComprobanteAfip;
	}
	
	
	public function Update(ComprobanteAfip $oComprobanteAfip)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->CreateArrayDB($oComprobanteAfip);

		$where = " IdComprobanteAfip = " . (int)$oComprobanteAfip->IdComprobanteAfip;
		
		if (!DBAccess::Update('TB_ComprobantesAfip', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oComprobanteAfip;
	}
	
	
	public function Delete($IdComprobanteAfip)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdComprobanteAfip = " . DB::Number($IdComprobanteAfip);

		if (!DBAccess::Delete('TB_ComprobantesAfip', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>