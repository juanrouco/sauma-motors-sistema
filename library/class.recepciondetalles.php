<?php

require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.recepcion.php');
require_once('class.recepciondetalle.php');

class RecepcionDetalles extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}


	public function GetAll(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionDetalles";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdRecepcion DESC";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRecepcionDetalle = new RecepcionDetalle();
			$oRecepcionDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oRecepcionDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllByRecepcion(Recepcion $oRecepcion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionDetalles";
		$sql.= " WHERE IdRecepcion = " . DB::Number($oRecepcion->IdRecepcion);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRecepcionDetalle = new RecepcionDetalle();
			$oRecepcionDetalle->ParseFromArray($oRow);
			
			array_push($arr, $oRecepcionDetalle);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdRecepcion, $IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionDetalles";
		$sql.= " WHERE IdRecepcion = " . DB::Number($IdRecepcion);	
		$sql.= " AND IdUnidad = " . DB::Number($IdUnidad);
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oRecepcionDetalle = new RecepcionDetalle();
		$oRecepcionDetalle->ParseFromArray($oRow);
		
		return $oRecepcionDetalle;		
	}
	

	public function Create(RecepcionDetalle $oRecepcionDetalle)
	{
		$arr = array
		(
			'IdRecepcion' 	=> DB::Number($oRecepcionDetalle->IdRecepcion),
			'IdUnidad' 		=> DB::Number($oRecepcionDetalle->IdUnidad),
			'CodigoLlaves' 	=> DB::String($oRecepcionDetalle->CodigoLlaves)
		);
	
		if (!$this->Insert('TB_RecepcionDetalles', $arr))
			return false;
			
		return $oRecepcionDetalle;
	}
	

	public function Update(RecepcionDetalle $oRecepcionDetalle)
	{
		$where = " IdRecepcion = " . (int)$oRecepcionDetalle->IdRecepcion;
		$where.= " AND IdUnidad = " . (int)$oRecepcionDetalle->IdUnidad;

		$arr = array('CodigoLlaves' => DB::String($oRecepcionDetalle->CodigoLlaves));

		if (!DBAccess::Update('TB_RecepcionDetalles', $arr, $where))
			return false;
			
		return $oRecepcionDetalle;
	}

	
	public function Delete($IdRecepcion, $IdUnidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdRecepcion = " . (int)$IdRecepcion;
		$where.= " AND IdUnidad = " . (int)$IdUnidad;
		
		if ( !DBAccess::Delete('TB_RecepcionDetalles', $where) )
		{
			DBAccess::$db->Rollback();
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>