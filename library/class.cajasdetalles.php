<?php

require_once('class.dbaccess.php');
require_once('class.cajadetalle.php');
require_once('class.filter.php');
require_once('class.filter.php');
require_once('class.page.php');

class CajasDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdCajaDetalle'])) && ($filter['IdCajaDetalle'] != ''))
			$sql.= " AND cd.IdCajaDetalle = " . DB::Number($filter['IdCajaDetalle']);

		if ((isset($filter['RecibeDeposito'])) && ($filter['RecibeDeposito'] != ''))
			$sql.= " AND cd.RecibeDeposito = " . DB::Bool($filter['RecibeDeposito']);

		if ((isset($filter['PermisoUsuario'])) && ($filter['PermisoUsuario'] != ''))
			$sql.= " AND cdu.IdUsuario = " . DB::Number($filter['PermisoUsuario']);
		
		return $sql;
	}
	
	public function GetById($IdCajaDetalle)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasDetalles cd";
		$sql.= " WHERE cd.IdCajaDetalle = " . DB::Number($IdCajaDetalle);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCajaDetalle = new CajaDetalle();
		$oCajaDetalle->ParseFromArray($oRow);
		
		return $oCajaDetalle;			
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasDetalles cd";
		$sql.= " LEFT JOIN tb_cajasdetallesusuarios cdu ON cdu.IdCajaDetalle = cd.IdCajaDetalle";
		$sql.= " WHERE (cd.IdCajaDetalle not in (2, 16, 17, 18, 19))";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY cd.IdCajaDetalle";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetAll(array $filter = NULL)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasDetalles cd";
		$sql.= " LEFT JOIN tb_cajasdetallesusuarios cdu ON cdu.IdCajaDetalle = cd.IdCajaDetalle";
		$sql.= " WHERE (cd.IdCajaDetalle not in (2, 16, 17, 18, 19))";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY cd.IdCajaDetalle";
		$sql.= " ORDER BY IdCajaDetalle ASC";
		
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaDetalle = new CajaDetalle();
						
			$oCajaDetalle->ParseFromArray($oRow);
			array_push($arr, $oCajaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	
	public function GetAllByIdCaja($IdCaja)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasDetalles cd";
		$sql.= " WHERE cd.IdCaja = " . DB::Number($IdCaja);
		$sql.= " ORDER BY cd.Fecha ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaDetalle = new CajaDetalle();
						
			$oCajaDetalle->ParseFromArray($oRow);
			array_push($arr, $oCajaDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	public function TienePermiso($oUsuario, $IdCajaDetalle)
	{
		$filter['PermisoUsuario'] 	= $oUsuario->IdUsuario;
		$filter['IdCajaDetalle'] 	= $IdCajaDetalle;
		return $this->GetCountRows($filter) > 0;
		//return false;
		
	}

	private function GetArrayDB(CajaDetalle $oCajaDetalle)
	{
		$arr = array
		(
			'IdCaja'				=> DB::Number($oCajaDetalle->IdCaja),
			'Nombre' 				=> DB::String($oCajaDetalle->Nombre),
			'FechaUltimoMovimiento' => DB::Date($oCajaDetalle->FechaUltimoMovimiento),
			'Total' 				=> DB::Number($oCajaDetalle->Total)
		);
		
		return $arr;
	}

	public function Create(CajaDetalle $oCajaDetalle)
	{	
		$arr = $this->GetArrayDB($oCajaDetalle);
		
		if ( !$this->Insert('tb_CajasDetalles', $arr) )
			return false;
		
		return $oCajaDetalle;
	}
	
	public function Update(CajaDetalle $oCajaDetalle)
	{
		$where = " IdCajaDetalle = " . (int)$oCajaDetalle->IdCajaDetalle;
		

		$arr = $this->GetArrayDB($oCajaDetalle);
				
		if ( !DBAccess::Update('tb_CajasDetalles', $arr, $where) )
			return false;
			
		return $oCajaDetalle;
	}

	public function Delete($IdCajaDetalle)
	{	
		$where = " IdCajaDetalle = " . (int)$IdCajaDetalle;

		if ( !DBAccess::Delete('tb_CajasDetalles', $where) )
			return false;
		
		return true;
	}	
}
?>