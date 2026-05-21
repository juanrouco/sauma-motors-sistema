<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.unidadfacturacompra.php');
require_once('class.filter.php');
require_once('class.page.php');

class UnidadesFacturasCompras extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}	


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_UnidadesFacturasCompras tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT tta.*";
		$sql.= " FROM TB_UnidadesFacturasCompras tta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadFacturaCompra = new UnidadFacturaCompra();
			$oUnidadFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadFacturaCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByUnidad(Unidad $oUnidad)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_UnidadesFacturasCompras tta";
		$sql.= " WHERE tta.IdUnidad = " . DB::Number($oUnidad->IdUnidad);
		$sql.= " GROUP BY tta.IdFacturaCompra";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadFacturaCompra = new UnidadFacturaCompra();
			$oUnidadFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadFacturaCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByFacturaCompra(FacturaCompra $oFacturaCompra)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_UnidadesFacturasCompras tta";
		$sql.= " WHERE tta.IdFacturaCompra = " . DB::Number($oFacturaCompra->IdFacturaCompra);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadFacturaCompra = new UnidadFacturaCompra();
			$oUnidadFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadFacturaCompra);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdUnidad, $IdFacturaCompra)
	{
		$sql = "SELECT tta.*";
		$sql.= " FROM TB_UnidadesFacturasCompras tta";
		$sql.= " WHERE tta.IdUnidad = " . DB::Number($IdUnidad);	
		$sql.= " AND tta.IdFacturaCompra = " . DB::Number($IdFacturaCompra);	
				
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUnidadFacturaCompra = new UnidadFacturaCompra();
		$oUnidadFacturaCompra->ParseFromArray($oRow);

		return $oUnidadFacturaCompra;		
	}


	public function Create(UnidadFacturaCompra $oUnidadFacturaCompra)
	{
		$arr = array
		(
			'IdUnidad'			=> DB::Number($oUnidadFacturaCompra->IdUnidad),
			'IdFacturaCompra'	=> DB::Number($oUnidadFacturaCompra->IdFacturaCompra)
		);

		if (!$this->Insert('TB_UnidadesFacturasCompras', $arr))
			return false;
			
		return $oUnidadFacturaCompra;
	}

	public function Delete($IdUnidadFacturaCompra)
	{
		$where = " IdUnidadFacturaCompra = " . DB::Number($IdUnidadFacturaCompra);
		
		if (!DBAccess::Delete('TB_UnidadesFacturasCompras', $where))
			return false;
		
		return true;	
	}
	
	public function DeleteByUnidad(Unidad $oUnidad)
	{
		$where = " IdUnidad = ".DB::Number($oUnidadFacturaCompra->IdUnidad);
		
		if (!DBAccess::Delete('TB_UnidadesFacturasCompras', $where))
			return false;
		
		return true;	
	}
	
	public function DeleteByFacturaCompra(FacturaCompra $oFacturaCompra)
	{
		$where = " IdFacturaCompra = ".DB::Number($oFacturaCompra->IdFacturaCompra);
		
		if (!DBAccess::Delete('TB_UnidadesFacturasCompras', $where))
			return false;
		
		return true;	
	}
}

?>