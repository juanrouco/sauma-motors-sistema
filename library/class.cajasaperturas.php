<?php
require_once('class.dbaccess.php');
require_once('class.cajaapertura.php');
require_once('class.filter.php');

class CajasAperturas extends DBAccess
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != "")
		{
			$sql.= " AND cd.Fecha >= " . DB::Date($filter['FechaDesde']);
		}

		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != "")
		{
			$sql.= " AND cd.Fecha <= " . DB::Date($filter['FechaHasta']);
		}

		if (isset($filter['IdAdministrador']) && $filter['IdAdministrador'] != "")
		{
			$sql.= " AND cd.IdUsuario <= " . DB::Number($filter['IdAdministrador']);
		}

		return $sql;
	}
	
	public function GetById($IdCajaApertura)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasAperturas cd";
		$sql.= " WHERE cd.IdCajaApertura = ".DB::Number($IdCajaApertura);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCajaApertura = new CajaApertura();
		$oCajaApertura->ParseFromArray($oRow);
		
		return $oCajaApertura;			
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasAperturas cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetUltimoTipoApertura()
	{
		$sql = "SELECT IdTipoApertura FROM tb_CajasAperturas ORDER BY Fecha DESC LIMIT 1";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		return $oRow['IdTipoApertura'];
	}
	
	public function GetAll(array $filter = NULL)
	{   
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasAperturas cd";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC";
			
		if ( !($oRes = 	$this->GetQuery($sql)) )
			return false;
						
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaApertura = new CajaApertura();
						
			$oCajaApertura->ParseFromArray($oRow);
			array_push($arr, $oCajaApertura);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}
	
	
	public function GetAllByIdCaja($IdCaja)
	{
		$sql = "SELECT cd.*";
		$sql.= " FROM tb_CajasAperturas cd";
		$sql.= " WHERE cd.IdCaja = " . DB::Number($IdCaja);
		$sql.= " ORDER BY cd.Fecha ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
	
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaApertura = new CajaApertura();
						
			$oCajaApertura->ParseFromArray($oRow);
			array_push($arr, $oCajaApertura);
			
			$oRes->MoveNext();
		}	
		
		return $arr;			
	}

	private function GetArrayDB(CajaApertura $oCajaApertura)
	{
		$arr = array
		(
			'IdUsuario'				=> DB::Number($oCajaApertura->IdUsuario),
			'IdTipoApertura'		=> DB::Number($oCajaApertura->IdTipoApertura),
			'IdTurno'				=> DB::Number($oCajaApertura->IdTurno),
			'IdCajaDetalle'			=> DB::Number($oCajaApertura->IdCajaDetalle),
			'TotalRendir' 			=> DB::Number($oCajaApertura->TotalRendir),
			'TotalReal' 			=> DB::Number($oCajaApertura->TotalReal),
			'Diferencia' 			=> DB::Number($oCajaApertura->Diferencia),
			'Fecha' 				=> DB::Date($oCajaApertura->Fecha)
		);
		
		return $arr;
	}

	public function Create(CajaApertura $oCajaApertura)
	{	
		$arr = $this->GetArrayDB($oCajaApertura);
		
		if ( !$this->Insert('tb_CajasAperturas', $arr) )
			return false;
		
		return $oCajaApertura;
	}
	
	public function Update(CajaApertura $oCajaApertura)
	{
		$where = " IdCajaApertura = " . (int)$oCajaApertura->IdCajaApertura;
		

		$arr = $this->GetArrayDB($oCajaApertura);
				
		if ( !DBAccess::Update('tb_CajasAperturas', $arr, $where) )
			return false;
			
		return $oCajaApertura;
	}

	public function Delete($IdCajaApertura)
	{	
		$where = " IdCajaApertura = " . (int)$IdCajaApertura;

		if ( !DBAccess::Delete('tb_CajasAperturas', $where) )
			return false;
		
		return true;
	}
}

?>