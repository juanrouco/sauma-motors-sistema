<?php 

require_once('class.dbaccess.php');
require_once('class.estadounidad.php');
require_once('class.filter.php');
require_once('class.page.php');

class EstadosUnidad extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if (isset($filter['Nombre']) && $filter['Nombre'] != '')
		{
			$sql.= " AND (Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
			$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%')";
		}
		
		if (isset($filter['Predeterminado']) && $filter['Predeterminado'] != '')
		{
			$sql.= " AND Predeterminado = " . DB::Number($filter['Predeterminado']);
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosUnidad";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oEstadoUnidad = new EstadoUnidad();
			$oEstadoUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oEstadoUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdEstado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosUnidad";
		$sql.= " WHERE IdEstado = " . DB::Number($IdEstado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoUnidad = new EstadoUnidad();
		$oEstadoUnidad->ParseFromArray($oRow);
		
		return $oEstadoUnidad;		
	}
	
	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosUnidad";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoUnidad = new EstadoUnidad();
		$oEstadoUnidad->ParseFromArray($oRow);
		
		return $oEstadoUnidad;		
	}

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosUnidad";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oEstadoUnidad = new EstadoUnidad();
		$oEstadoUnidad->ParseFromArray($oRow);
		
		return $oEstadoUnidad;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_EstadosUnidad";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(EstadoUnidad $oEstadoUnidad)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oEstadoUnidad->Codigo),
			'Nombre' 	=> DB::String($oEstadoUnidad->Nombre),
			'Color' 	=> DB::String($oEstadoUnidad->Color)
		);
		
		if (!$this->Insert('TB_EstadosUnidad', $arr))
			return false;

		/* asignamos el id generado */
		$oEstadoUnidad->IdEstadoUnidad = DBAccess::GetLastInsertId();
			
		return $oEstadoUnidad;
	}
	
	
	public function Update(EstadoUnidad $oEstadoUnidad)
	{
		$where = " IdEstado = " . DB::Number($oEstadoUnidad->IdEstado);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oEstadoUnidad->Codigo),
			'Nombre' 	=> DB::String($oEstadoUnidad->Nombre),
			'Color' 	=> DB::String($oEstadoUnidad->Color)
		);
		
		if (!DBAccess::Update('TB_EstadosUnidad', $arr, $where))
			return false;
		
		return $oEstadoUnidad;
	}
	

	public function Delete($IdEstado)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdEstado = " . DB::Number($IdEstado);

		if (!DBAccess::Delete('TB_EstadosUnidad', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>