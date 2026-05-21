<?php 

require_once('class.dbaccess.php');
require_once('class.estadoorden.php');
require_once('class.filter.php');
require_once('class.page.php');

class EstadosOrden extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosOrden";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oEstadoOrden = new EstadoOrden();
			$oEstadoOrden->ParseFromArray($oRow);
			
			array_push($arr, $oEstadoOrden);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdEstado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosOrden";
		$sql.= " WHERE IdEstado = " . DB::Number($IdEstado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoOrden = new EstadoOrden();
		$oEstadoOrden->ParseFromArray($oRow);
		
		return $oEstadoOrden;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosOrden";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoOrden = new EstadoOrden();
		$oEstadoOrden->ParseFromArray($oRow);
		
		return $oEstadoOrden;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosOrden";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(EstadoOrden $oEstadoOrden)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oEstadoOrden->Codigo),
			'Nombre' 	=> DB::String($oEstadoOrden->Nombre),
			'Color' 	=> DB::String($oEstadoOrden->Color)
		);
		
		if (!$this->Insert('TB_EstadosOrden', $arr))
			return false;

		/* asignamos el id generado */
		$oEstadoOrden->IdEstado = DBAccess::GetLastInsertId();
			
		return $oEstadoOrden;
	}
	
	
	public function Update(EstadoOrden $oEstadoOrden)
	{
		$where = " IdEstado = " . DB::Number($oEstadoOrden->IdEstado);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oEstadoOrden->Codigo),
			'Nombre' 	=> DB::String($oEstadoOrden->Nombre),
			'Color' 	=> DB::String($oEstadoOrden->Color)
		);
		
		if (!DBAccess::Update('TB_EstadosOrden', $arr, $where))
			return false;
		
		return $oEstadoOrden;
	}
	

	public function Delete($IdEstado)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdEstado = " . DB::Number($IdEstado);

		if (!DBAccess::Delete('TB_EstadosOrden', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>