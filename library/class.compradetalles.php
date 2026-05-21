<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.compra.php');
require_once('class.tipomovimiento.php');
require_once('class.compradetalle.php');

class CompraDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CompraDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCompra DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompraDetalle = new CompraDetalle();
			$oCompraDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oCompraDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByCompra(Compra $oCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CompraDetalles";
		$sql.= " WHERE IdCompra = " . DB::Number($oCompra->IdCompra);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCompraDetalle = new CompraDetalle();
			$oCompraDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oCompraDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdCompra, $IdArticulo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CompraDetalles";
		$sql.= " WHERE IdCompra = " . DB::Number($IdCompra);	
		$sql.= " AND IdArticulo = " . DB::Number($IdArticulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCompraDetalle = new CompraDetalle();
		$oCompraDetalle->ParseFromArray($oRow);
		
		return $oCompraDetalle;		
	}


	public function GetByIdIncrement($IdCompraDetalle)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CompraDetalles";
		$sql.= " WHERE IdCompraDetalle = " . DB::Number($IdCompraDetalle);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCompraDetalle = new CompraDetalle();
		$oCompraDetalle->ParseFromArray($oRow);
		
		return $oCompraDetalle;		
	}
	
	public function GetCountByTareaAndArticulo($IdOrdenTrabajoTarea, $IdArticulo)
	{
		$sql = "SELECT SUM(cd.Cantidad * IF (c.IdTipoMovimiento = " . DB::Number(TipoMovimiento::Devolucion) . ", -1, 1)) AS Cantidad";
		$sql.= " FROM TB_CompraDetalles cd";
		$sql.= " INNER JOIN TB_Compras c ON cd.IdCompra = c.IdCompra";
		$sql.= " WHERE c.IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);	
		$sql.= " AND cd.IdArticulo = " . DB::Number($IdArticulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		return $oRow['Cantidad'];		
	}
	
	public function GetAllByTareaAndArticulo($IdOrdenTrabajoTarea, $IdArticulo)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM TB_CompraDetalles cd";
		$sql.= " INNER JOIN TB_Compras c ON cd.IdCompra = c.IdCompra";
		$sql.= " WHERE c.IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);	
		$sql.= " AND cd.IdArticulo = " . DB::Number($IdArticulo);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCompraDetalle = new CompraDetalle();
		$oCompraDetalle->ParseFromArray($oRow);
		
		return $oCompraDetalle;			
	}
	
	public function Create(CompraDetalle $oCompraDetalle)
	{
		$arr = array
		(
			'IdCompra' 			=> DB::Number($oCompraDetalle->IdCompra),
			'IdArticulo' 		=> DB::Number($oCompraDetalle->IdArticulo),
			'ImporteUnidad' 	=> DB::Number($oCompraDetalle->ImporteUnidad),
			'Cantidad' 			=> DB::Number($oCompraDetalle->Cantidad),
			'ImporteCompraNeto' => DB::Number($oCompraDetalle->ImporteCompraNeto),
			'PrecioCompra'		=> DB::Number($oCompraDetalle->PrecioCompra)
		);
	
		if (!$this->Insert('TB_CompraDetalles', $arr))
			return false;
			
		return $oCompraDetalle;
	}
	

	public function Update(CompraDetalle $oCompraDetalle)
	{
		$where = " IdCompraDetalle = " . (int)$oCompraDetalle->IdCompraDetalle;		

		$arr = array
		(
			'IdCompra' 			=> DB::Number($oCompraDetalle->IdCompra),
			'IdArticulo' 		=> DB::Number($oCompraDetalle->IdArticulo),
			'ImporteUnidad' 	=> DB::Number($oCompraDetalle->ImporteUnidad),
			'Cantidad' 			=> DB::Number($oCompraDetalle->Cantidad),
			'ImporteCompraNeto' => DB::Number($oCompraDetalle->ImporteCompraNeto),
			'PrecioCompra'		=> DB::Number($oCompraDetalle->PrecioCompra)
		);

		if (!DBAccess::Update('TB_CompraDetalles', $arr, $where))
			return false;
			
		return $oCompraDetalle;
	}

	
	public function Delete($IdCompraDetalle)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCompraDetalle = " . (int)$IdCompraDetalle;		
		
		if ( !DBAccess::Delete('TB_CompraDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>