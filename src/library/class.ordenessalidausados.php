<?php 

require_once('class.dbaccess.php');
require_once('class.ordensalidausado.php');
require_once('class.filter.php');
require_once('class.page.php');

class OrdenesSalidaUsados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdUsado'])) && ($filter['IdUsado'] != ''))
			$sql.= " AND os.IdUsado = " . DB::Number($filter['IdUsado']);

		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND os.Fecha >= " . DB::Date($filter['FechaDesde']);

		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND os.Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT os.*";
		$sql.= " FROM TB_OrdenesSalidaUsados os";
		$sql.= " LEFT JOIN TB_Minutas m ON os.IdUsado = m.IdUsado";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY os.IdOrden DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenSalida = new OrdenSalidaUsado();
			$oOrdenSalida->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenSalida);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdOrden)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesSalidaUsados";
		$sql.= " WHERE IdOrden = " . DB::Number($IdOrden);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenSalida = new OrdenSalidaUsado();
		$oOrdenSalida->ParseFromArray($oRow);
		
		return $oOrdenSalida;		
	}


	public function GetByIdUsado($IdUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesSalidaUsados";
		$sql.= " WHERE IdUsado = " . DB::Number($IdUsado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenSalida = new OrdenSalidaUsado();
		$oOrdenSalida->ParseFromArray($oRow);
		
		return $oOrdenSalida;		
	}
	

	public function GetByMinuta(MinutaUsado $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesSalidaUsados";
		$sql.= " WHERE IdUsado = " . DB::Number($oMinuta->IdUsado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenSalida = new OrdenSalidaUsado();
		$oOrdenSalida->ParseFromArray($oRow);
		
		return $oOrdenSalida;		
	}
	

	public function GetByIdMinutaUsado($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesSalidaUsados";
		$sql.= " WHERE IdUsado = " . DB::Number($IdMinuta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenSalida = new OrdenSalidaUsado();
		$oOrdenSalida->ParseFromArray($oRow);
		
		return $oOrdenSalida;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT os.*";
		$sql.= " FROM TB_OrdenesSalidaUsados os";
		$sql.= " LEFT JOIN TB_Minutas m ON os.IdUsado = m.IdUsado";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(OrdenSalidaUsado $oOrdenSalida)
	{
		$arr = array
		(
			'IdUsado' 						=> DB::Number($oOrdenSalida->IdUsado),
			'IdCliente' 					=> DB::Number($oOrdenSalida->IdCliente),
			'IdTipoDestinatario' 			=> DB::Number($oOrdenSalida->IdTipoDestinatario),
			'Transporte' 					=> DB::String($oOrdenSalida->Transporte),
			'TransporteClaveFiscalTipo'		=> DB::Number($oOrdenSalida->TransporteClaveFiscalTipo),
			'TransporteClaveFiscalNumero'	=> DB::String($oOrdenSalida->TransporteClaveFiscalNumero),
			'AdquirienteRazonSocial' 		=> DB::String($oOrdenSalida->AdquirienteRazonSocial),
			'AdquirienteDocumentoTipo' 		=> DB::String($oOrdenSalida->AdquirienteDocumentoTipo) == 0? 'null' : DB::String($oOrdenSalida->AdquirienteDocumentoTipo),
			'AdquirienteDocumentoNumero' 	=> DB::String($oOrdenSalida->AdquirienteDocumentoNumero),
			'Fecha' 						=> DB::Date($oOrdenSalida->Fecha),
			'EntregaManuales'				=> DB::Number($oOrdenSalida->EntregaManuales),
			'EntregaLlaves'					=> DB::Number($oOrdenSalida->EntregaLlaves),
			'EntregaTarjetaCode'			=> DB::Number($oOrdenSalida->EntregaTarjetaCode),
			'IdUbicacion' 					=> DB::Number($oOrdenSalida->IdUbicacion),
			'EntregaDocumentacion'			=> DB::Number($oOrdenSalida->EntregaDocumentacion)
		);
		
		if (!$this->Insert('TB_OrdenesSalidaUsados', $arr))
			return false;

		/* asignamos el id generado */
		$oOrdenSalida->IdOrden = DBAccess::GetLastInsertId();
			
		return $oOrdenSalida;
	}
	
	
	public function Delete($IdOrden)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrden = " . DB::Number($IdOrden);

		if (!DBAccess::Delete('TB_OrdenesSalidaUsados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>