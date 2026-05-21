<?php 

require_once('class.dbaccess.php');
require_once('class.cheque.php');
require_once('class.proveedores.php');
require_once('class.facturascompras.php');
require_once('class.session.php');
require_once('class.filter.php');
require_once('class.page.php');

class Cheques extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdFacturaCompra'])) && ($filter['IdFacturaCompra'] != ''))
			$sql.= " AND IdFacturaCompra = " . DB::Number($filter['IdFacturaCompra']);

		if ((isset($filter['NumeroCheque'])) && ($filter['NumeroCheque'] != ''))
			$sql.= " AND NumeroCheque LIKE '%" . DB::StringUnquoted($filter['NumeroCheque']) . "%'";

		if ((isset($filter['Banco'])) && ($filter['Banco'] != ''))
			$sql.= " AND Banco LIKE '%" . DB::StringUnquoted($filter['Banco']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaDepositoHasta'])) && ($filter['FechaDepositoHasta'] != ''))
			$sql.= " AND FechaDeposito <= " . DB::Date($filter['FechaDepositoHasta']);
			
		if ((isset($filter['FechaDepositoDesde'])) && ($filter['FechaDepositoDesde'] != ''))
			$sql.= " AND FechaDeposito >= " . DB::Date($filter['FechaDepositoDesde']);
			
		if ((isset($filter['Pago'])) && ($filter['Pago'] != ''))
			if ($filter['Pago'] != '0')
			$sql.= " AND Pago = " . DB::Bool($filter['Pago']);
		else
			$sql.= " AND (Pago = " . DB::Bool($filter['Pago']) . " OR Pago IS NULL)";
			
		if ((isset($filter['NumeroFactura'])) && ($filter['NumeroFactura'] != ''))
			$sql.= " AND NumeroFactura LIKE '%" . DB::String($filter['NumeroFactura']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCheque DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCheque = new Cheque();
			$oCheque->ParseFromArray($oRow);
			
			array_push($arr, $oCheque);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetTotales(array $filter = NULL)
	{
		$sql = "SELECT COUNT(IdCheque) AS Cantidad, SUM(Importe) AS Valuacion";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!($oRow = $oRes->GetRow()))	
			return false;
		
		$oResultado = new stdClass();
		$oResultado->Cantidad = $oRow['Cantidad'];
		$oResultado->Valuacion = $oRow['Valuacion'];
		
		return $oResultado;
	}

	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY FechaDeposito ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCheque = new Cheque();
			$oCheque->ParseFromArray($oRow);
			
			array_push($arr, $oCheque);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdCheque)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE IdCheque = " . DB::Number($IdCheque);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCheque = new Cheque();
		$oCheque->ParseFromArray($oRow);
		
		return $oCheque;		
	}
	

	public function GetByNumeroFactura($NumeroFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE NumeroFactura = " . DB::String($NumeroFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCheque = new Cheque();
		$oCheque->ParseFromArray($oRow);
		
		return $oCheque;		
	}
	

	public function GetByIdFacturaPostVenta($IdFacturaPostVenta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_Cheques fu";
		$sql.= " WHERE fu.IdFacturaPostVenta = " . DB::Number($IdFacturaPostVenta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCheque = new Cheque();
			$oCheque->ParseFromArray($oRow);
			
			array_push($arr, $oCheque);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Cheques";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Cheque $oCheque)
	{
		$arr = array
		(
			'Fecha' 				=> DB::Date($oCheque->Fecha),
			'NumeroCheque' 			=> DB::String($oCheque->NumeroCheque),
			'Banco' 				=> DB::String($oCheque->Banco),
			'FechaEmision' 			=> DB::Date($oCheque->FechaEmision),
			'FechaDeposito'			=> DB::Date($oCheque->FechaDeposito),
			'Importe' 				=> DB::Number($oCheque->Importe),
			'Observaciones' 		=> DB::String($oCheque->Observaciones),
			'IdProveedor'			=> DB::Number($oCheque->IdProveedor),
			'IdFacturaCompra'		=> DB::Number($oCheque->IdFacturaCompra),
			'Pago'					=> DB::Number($oCheque->Pago),
			'NumeroFactura'			=> DB::String($oCheque->NumeroFactura)
		);
		
		return $arr;
	}
	
	public function Create(Cheque $oCheque)
	{
		$arr = $this->GetArrayDB($oCheque);
		
		if (!$this->Insert('TB_Cheques', $arr))
			return false;

		/* asignamos el id generado */
		$oPago->IdPago = DBAccess::GetLastInsertId();
			
		return $oPago;
	}
	
	public function Update(Cheque $oCheque)
	{
		$where = " IdCheque = " . DB::Number($oCheque->IdCheque);
		
		$arr = $this->GetArrayDB($oCheque);
		
		if (!DBAccess::Update('TB_Cheques', $arr, $where))
			return false;
		
		return $oCheque;
	}
	
	
	public function Delete($IdCheque)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCheque = " . DB::Number($IdCheque);
		
		if (!DBAccess::Delete('TB_Cheques', $where))
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
		
		$FileName = "Cheques.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrCheques = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Nro. Cheque";
		$csv.= $Separador;
		$csv.= "Emision";
		$csv.= $Separador;
		$csv.= "Deposito";
		$csv.= $Separador;
		$csv.= "Banco";
		$csv.= $Separador;
		$csv.= "Proveedor";
		$csv.= $Separador;
		$csv.= "Nro. Factura";
		$csv.= $Separador;
		$csv.= "Importe";	
		$csv.= $Separador;
		$csv.= "Observaciones";		
		$csv.= $SaltoLinea;
	
		foreach ($arrCheques as $oCheque)
		{				
			$oProveedor	= $oCheque->GetProveedor();
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oCheque->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCheque->NumeroCheque));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oCheque->FechaEmision)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oCheque->FechaDeposito)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCheque->Banco));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Empresa));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCheque->NumeroFactura));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oCheque->Importe, 2, ',', '.')));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCheque->Observaciones));
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>