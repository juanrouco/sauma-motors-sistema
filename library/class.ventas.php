<?php 

require_once('class.dbaccess.php');
require_once('class.venta.php');
require_once('class.filter.php');
require_once('class.page.php');

class Ventas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oVenta = new Venta();
			$oVenta->ParseFromArray($oRow);
			
			array_push($arr, $oVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oVenta = new Venta();
			$oVenta->ParseFromArray($oRow);
			
			array_push($arr, $oVenta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdVenta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= " WHERE IdVenta = " . DB::Number($IdVenta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oVenta = new Venta();
		$oVenta->ParseFromArray($oRow);
		
		return $oVenta;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oVenta = new Venta();
		$oVenta->ParseFromArray($oRow);
		
		return $oVenta;		
	}


	public function GetByUnidad(Unidad $oUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= " WHERE IdUnidad = " . DB::Number($oUnidad->IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oVenta = new Venta();
		$oVenta->ParseFromArray($oRow);
		
		return $oVenta;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ventas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Venta $oVenta)
	{
		$arr = array
		(
			'IdUnidad' 				=> DB::Number($oVenta->IdUnidad),
			'IdUsuario' 			=> DB::Number($oVenta->IdUsuario),
			'IdCliente' 			=> DB::Number($oVenta->IdCliente),
			'FechaVenta' 			=> DB::Date($oVenta->FechaVenta),
			'FechaFactura' 			=> DB::Date($oVenta->FechaFactura),
			'NumeroFactura' 		=> DB::String($oVenta->NumeroFactura),
			'EntregaUsado' 			=> DB::Bool($oVenta->EntregaUsado),
			'PrecioVenta' 			=> DB::Number($oVenta->PrecioVenta),
			'Circular' 				=> DB::Number($oVenta->Circular),
			'Anticipo' 				=> DB::Number($oVenta->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oVenta->FinanciacionCapital)
		);
		
		if (!$this->Insert('TB_Ventas', $arr))
			return false;
			
		return $oVenta;
	}
	
	
	public function Update(Venta $oVenta)
	{
		$where = " IdVenta = " . DB::Number($oVenta->IdVenta);
		
		$arr = array
		(
			'IdUnidad' 				=> DB::Number($oVenta->IdUnidad),
			'IdUsuario' 			=> DB::Number($oVenta->IdUsuario),
			'IdCliente' 			=> DB::Number($oVenta->IdCliente),
			'FechaVenta' 			=> DB::Date($oVenta->FechaVenta),
			'FechaFactura' 			=> DB::Date($oVenta->FechaFactura),
			'NumeroFactura' 		=> DB::String($oVenta->NumeroFactura),
			'EntregaUsado' 			=> DB::Bool($oVenta->EntregaUsado),
			'PrecioVenta' 			=> DB::Number($oVenta->PrecioVenta),
			'Circular' 				=> DB::Number($oVenta->Circular),
			'Anticipo' 				=> DB::Number($oVenta->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oVenta->FinanciacionCapital)
		);
		
		if (!DBAccess::Update('TB_Ventas', $arr, $where))
			return false;
		
		return $oVenta;
	}
	

	public function Delete($IdVenta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdVenta = " . DB::Number($IdVenta);

		if (!DBAccess::Delete('TB_Ventas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>