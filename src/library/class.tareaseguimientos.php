<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.tareaseguimiento.php');

class TareaSeguimientos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ($filter['IdTarea'] != "")
		{
			$sql.= " AND u.IdTarea = " . DB::Number($filter['IdTarea']);
		}
		
		if ($filter['FechaInicio'] != "")
		{
			$sql.= " AND u.FechaInicio >= " . DB::Date($filter['FechaInicio']);
		}

		if ($filter['FechaFin'] != "")
		{
			$sql.= " AND u.FechaFin <= " . DB::Date($filter['FechaFin']);
		}
		if ($filter['IdUsuarioTo'] != "")
		{
			$sql.= " AND u.IdUsuario = " . DB::Number($filter['IdUsuarioTo']);
		}
		if ($filter['SeguimientoRealizado'] != "")
		{
			$sql.= " AND u.SeguimientoRealizado = " . DB::Bool($filter['SeguimientoRealizado']);
		}

		return $sql;
	}	
	
	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_TareaSeguimientos u";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		$sql.= " GROUP BY u.IdSeguimiento";

		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	public function GetAllByTarea($IdTarea)
	{
		$arr = array();
	
		$sql = " SELECT u.*";
		$sql.= " FROM TB_TareaSeguimientos u";
		$sql.= " WHERE u.IdTarea = " . DB::Number($IdTarea);
		$sql.= " GROUP BY u.IdTarea";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaSeguimiento = new TareaSeguimiento();
			$oTareaSeguimiento->ParseFromArray($oRow);
			
			array_push($arr, $oTareaSeguimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_TareaSeguimientos u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Fecha DESC";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaSeguimiento = new TareaSeguimiento();
			$oTareaSeguimiento->ParseFromArray($oRow);
			
			
			array_push($arr, $oTareaSeguimiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetById($IdSeguimiento)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_TareaSeguimientos u";
		$sql.= " WHERE u.IdSeguimiento = " . DB::Number($IdSeguimiento);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oTareaSeguimiento = new TareaSeguimiento();
		$oTareaSeguimiento->ParseFromArray($oRow);

		
		return $oTareaSeguimiento;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_TareaSeguimientos u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY u.IdTarea";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TareaSeguimiento $oTareaSeguimiento)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdTarea'				=> DB::Number($oTareaSeguimiento->IdTarea),	
			'IdUsuario'				=> DB::Number($oTareaSeguimiento->IdUsuario),	
			'IdAccion'				=> DB::Number($oTareaSeguimiento->IdAccion),		
			'Detalle'				=> DB::String($oTareaSeguimiento->Detalle),
			'Fecha'					=> DB::Date($oTareaSeguimiento->Fecha),
			'FechaAccion'			=> DB::Date($oTareaSeguimiento->FechaAccion),
			'SeguimientoRealizado'	=> DB::Bool($oTareaSeguimiento->SeguimientoRealizado),
			'Resultado'				=> DB::String($oTareaSeguimiento->Resultado)
		);

		if (!DBAccess::Insert('TB_TareaSeguimientos', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			
		
		$oPlan->IdSeguimiento = DBAccess::GetLastInsertId();
				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oTareaSeguimiento;
	}
	
	
	public function Update(TareaSeguimiento $oTareaSeguimiento)
	{

		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdTarea'				=> DB::Number($oTareaSeguimiento->IdTarea),	
			'IdUsuario'				=> DB::Number($oTareaSeguimiento->IdUsuario),	
			'IdAccion'				=> DB::Number($oTareaSeguimiento->IdAccion),		
			'Detalle'				=> DB::String($oTareaSeguimiento->Detalle),
			'Fecha'					=> DB::Date($oTareaSeguimiento->Fecha),
			'FechaAccion'			=> DB::Date($oTareaSeguimiento->FechaAccion),
			'SeguimientoRealizado'	=> DB::Bool($oTareaSeguimiento->SeguimientoRealizado),
			'Resultado'				=> DB::String($oTareaSeguimiento->Resultado)
		);

		$where = " IdSeguimiento = " . (int)$oTareaSeguimiento->IdSeguimiento;
		
		if (!DBAccess::Update('TB_TareaSeguimientos', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oTareaSeguimiento;
	}
	
	public function Delete($IdSeguimiento)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdSeguimiento = " . DB::Number($IdSeguimiento);
		if (!DBAccess::Delete('TB_TareaSeguimientos', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	

}

?>