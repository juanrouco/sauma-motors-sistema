<?php 

require_once('class.dbaccess.php');
require_once('class.cajadetalleusuario.php');
require_once('class.filter.php');
require_once('class.page.php');

class CajasDetallesUsuarios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['IdUsuario']) && $filter['IdUsuario'] != '')
			$sql.= ' AND cdu.IdUsuario = ' . DB::Number($filter['IdUsuario']);
		
		if (isset($filter['IdCajaDetalle']) && $filter['IdCajaDetalle'] != '')
			$sql.= ' AND cdu.IdCajaDetalle = ' . DB::Number($filter['IdCajaDetalle']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT cdu.*";
		$sql.= " FROM TB_CajasDetallesUsuarios cdu";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY cdu.IdUsuario DESC, cdu.IdCajaDetalle DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCajaDetalleUsuario = new CajaDetalleUsuario();
			$oCajaDetalleUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oCajaDetalleUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT cdu.*";
		$sql.= " FROM TB_CajasDetallesUsuarios cdu";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(CajaDetalleUsuario $oCajaDetalleUsuario)
	{
		$arr = array
		(
			'IdCajaDetalle' => DB::Number($oCajaDetalleUsuario->IdCajaDetalle),
			'IdUsuario'		=> DB::Number($oCajaDetalleUsuario->IdUsuario)
		);
		
		if (!$this->Insert('TB_CajasDetallesUsuarios', $arr))
			return false;
		
		return $oCajaDetalleUsuario;
	}
	

	public function Delete($IdUsuario)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsuario = " . DB::Number($IdUsuario);

		if (!DBAccess::Delete('TB_CajasDetallesUsuarios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>