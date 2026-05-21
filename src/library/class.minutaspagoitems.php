<?php 

require_once('class.dbaccess.php');
require_once('class.minutapagoitem.php');
require_once('class.minutaspago.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.proveedores.php');
require_once('class.page.php');

class MinutasPagoItems extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '' && $filter['FechaDesde'])
			$sql.= ' AND mpi.Fecha >= ' . DB::Date($filter['FechaDesde']);
		
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '' && $filter['FechaHasta'])
			$sql.= ' AND mpi.Fecha <= ' . DB::Date($filter['FechaHasta']);
			
		if (isset($filter['Saldo']) && $filter['Saldo'] != '')
			$sql.= ' AND mpi.Saldo = ' . DB::Number($filter['Saldo']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_MinutasPagoItems mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPagoItem = new MinutaPagoItem();
			$oMinutaPagoItem->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPagoItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_MinutasPagoItems mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.NumeroRetencion, mpi.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPagoItem = new MinutaPagoItem();
			$oMinutaPagoItem->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPagoItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdMinutaPago($IdMinutaPago)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdMinutaPago = " . DB::Number($IdMinutaPago);
		$sql.= " ORDER BY IdUnidad";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPagoItem = new MinutaPagoItem();
			$oMinutaPagoItem->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPagoItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdMinutaPagoOrdered($IdMinutaPago)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_MinutasPagoItems mpi";
		$sql.= " INNER JOIN TB_Unidades u on mpi.IdUnidad = u.IdUnidad";
		$sql.= " WHERE mpi.IdMinutaPago = " . DB::Number($IdMinutaPago);
		$sql.= " ORDER BY mpi.Saldo ASC, IF (u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ", 0, IF (u.Pisado = 1, 1, u.IdUnidad)) ASC, IdUnidad";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPagoItem = new MinutaPagoItem();
			$oMinutaPagoItem->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPagoItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByIdUnidad($IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);
		$sql.= " ORDER BY IdMinutaPago";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaPagoItem = new MinutaPagoItem();
			$oMinutaPagoItem->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaPagoItem);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetByIdFacturaCompra($IdFacturaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdFacturaCompra = " . DB::Number($IdFacturaCompra);
		$sql.= " ORDER BY IdMinutaPago";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))	
			return false;
		
		$oMinutaPagoItem = new MinutaPagoItem();
		$oMinutaPagoItem->ParseFromArray($oRow);
		
		return $oMinutaPagoItem;
	}
	
	public function GetPagadoByIdUnidad($IdUnidad)
	{
		$sql = "SELECT SUM(Importe) AS Total";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);
		$sql.= " GROUP BY IdUnidad";
						
		if (!($oRes = $this->GetQuery($sql)))
			return 0;
			
			
		if (!$oRow = $oRes->GetRow())	
		{	
			return 0;
		}
		
		$Total  = $oRow['Total'];
		
		return $Total;
	}

	public function GetById($IdMinutaPagoItem)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdMinutaPagoItem = " . DB::Number($IdMinutaPagoItem);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaPagoItem = new MinutaPagoItem();
		$oMinutaPagoItem->ParseFromArray($oRow);
		
		return $oMinutaPagoItem;		
	}
	
	public function GetProximoNumeroRetencion()
	{
		$sql = "SELECT MAX(NumeroRetencion) AS NumeroRetencion";
		$sql.= " FROM TB_MinutasPagoItems";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		if ($oRow['NumeroRetencion'])
			return $oRow['NumeroRetencion'] + 1;
		
		return 6987;		
	}
	
	
	public function GetByIdMinutaPagoAndIdUnidad($IdMinutaPago, $IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasPagoItems";
		$sql.= " WHERE IdMinutaPago = " . DB::Number($IdMinutaPago);	
		$sql.= " AND IdUnidad = " . DB::Number($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaPagoItem = new MinutaPagoItem();
		$oMinutaPagoItem->ParseFromArray($oRow);
		
		return $oMinutaPagoItem;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT mpi.*";
		$sql.= " FROM TB_MinutasPagoItems mpi";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mpi.Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(MinutaPagoItem $oMinutaPagoItem)
	{
		$arr = array
		(
			'IdMinutaPago'		=> DB::Number($oMinutaPagoItem->IdMinutaPago),
			'IdUnidad' 			=> DB::Number($oMinutaPagoItem->IdUnidad),
			'Neto'				=> DB::Number($oMinutaPagoItem->Neto),
			'Retencion'			=> DB::Number($oMinutaPagoItem->Retencion),
			'Importe'			=> DB::Number($oMinutaPagoItem->Importe),
			'Saldo'				=> DB::Number($oMinutaPagoItem->Saldo),
			'PagoParcial'		=> DB::Bool($oMinutaPagoItem->PagoParcial),
			'NumeroRetencion'	=> DB::Number($oMinutaPagoItem->NumeroRetencion),
			'IdFacturaCompra'	=> DB::Number($oMinutaPagoItem->IdFacturaCompra),
			'Fecha'				=> DB::Date($oMinutaPagoItem->Fecha),
			'Cuit'				=> DB::String($oMinutaPagoItem->Cuit),
			'IdProveedor'		=> DB::Number($oMinutaPagoItem->IdProveedor)
		);
		
		if (!$this->Insert('TB_MinutasPagoItems', $arr))
			return false;

		/* asignamos el id generado */
		$oMinutaPagoItem->IdMinutaPago = DBAccess::GetLastInsertId();
			
		return $oMinutaPagoItem;
	}
	
	
	public function Update(MinutaPagoItem $oMinutaPagoItem)
	{
		$where = " IdMinutaPagoItem = " . DB::Number($oMinutaPagoItem->IdMinutaPagoItem);
		
		$arr = array
		(
			'IdMinutaPago'		=> DB::Number($oMinutaPagoItem->IdMinutaPago),
			'IdUnidad' 			=> DB::Number($oMinutaPagoItem->IdUnidad),
			'Neto'				=> DB::Number($oMinutaPagoItem->Neto),
			'Retencion'			=> DB::Number($oMinutaPagoItem->Retencion),
			'Importe'			=> DB::Number($oMinutaPagoItem->Importe),
			'Saldo'				=> DB::Number($oMinutaPagoItem->Saldo),
			'PagoParcial'		=> DB::Bool($oMinutaPagoItem->PagoParcial),
			'NumeroRetencion'	=> DB::Number($oMinutaPagoItem->NumeroRetencion),
			'IdFacturaCompra'	=> DB::Number($oMinutaPagoItem->IdFacturaCompra),
			'Fecha'				=> DB::Date($oMinutaPagoItem->Fecha),
			'Cuit'				=> DB::String($oMinutaPagoItem->Cuit),
			'IdProveedor'		=> DB::Number($oMinutaPagoItem->IdProveedor)
		);
		
		if (!DBAccess::Update('TB_MinutasPagoItems', $arr, $where))
			return false;
		
		return $oMinutaPagoItem;
	}
	

	public function Delete($IdMinutaPagoItem)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMinutaPagoItem = " . DB::Number($IdMinutaPagoItem);

		if (!DBAccess::Delete('TB_MinutasPagoItems', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function GenerarArchivo($filter)
	{
		$oMinutasPago = new MinutasPago();
		$oProveedores = new Proveedores();
		
		$arrData = $this->GetAllOrdered($filter);
		
		$SaltoLinea = "\r\n";
		
		$txt = '';
		
		$FileName = "RETENC.txt";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
		
		foreach ($arrData as $oMinutaPagoItem)
		{
			$oMinutaPago = $oMinutasPago->GetById($oMinutaPagoItem->IdMinutaPago);
			$oProveedor = $oProveedores->GetById($oMinutaPagoItem->IdProveedor);
			
			if (!$oMinutaPagoItem->NumeroRetencion)
			{
				$oMinutaPagoItem->NumeroRetencion = $this->GetProximoNumeroRetencion();
				$this->Update($oMinutaPagoItem);
			}
			if ($txt != '')
				$txt.= $SaltoLinea;
			$txt.= $oProveedor->Cuit;
			$txt.= CambiarFecha($oMinutaPagoItem->Fecha);
			$txt.= str_pad($oMinutaPagoItem->NumeroRetencion, 12, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oMinutaPagoItem->Retencion, 2, ',', ''), 11, '0', STR_PAD_LEFT);
			$txt.= 'A';
			
		}
		
		print_r($txt);
	}
}

?>