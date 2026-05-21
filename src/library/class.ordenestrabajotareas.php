<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajotarea.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class OrdenesTrabajoTareas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['PalabraClave'])) && ($filter['PalabraClave'] != ''))
		{
			$sql.= " AND (tt.Titulo LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%'";
			$sql.= " OR tt.Descripcion LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%')";
		}
		
		if ((isset($filter['IdOrdenTrabajo'])) && ($filter['IdOrdenTrabajo'] != ''))
		{
			$sql.= " AND tt.IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
		}
		
		if ((isset($filter['NotIdEstado'])) && ($filter['NotIdEstado'] != ''))
		{
			$sql.= " AND tt.IdEstado <> " . DB::Number($filter['NotIdEstado']);
		}

		return $sql;
	}

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT tt.*";
		$sql.= " FROM TB_OrdenesTrabajoTareas tt";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY tt.IdOrdenTrabajoTarea ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoTarea = new OrdenTrabajoTarea();
			$oOrdenTrabajoTarea->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByOrdenTrabajo(OrdenTrabajo $oOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoTareas";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
		$sql.= " ORDER BY IdOrdenTrabajoTarea ASC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoTarea = new OrdenTrabajoTarea();
			$oOrdenTrabajoTarea->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdOrdenTrabajo, $IdTareaTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoTareas";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
		$sql.= " AND IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoTarea = new OrdenTrabajoTarea();
		$oOrdenTrabajoTarea->ParseFromArray($oRow);
		
		return $oOrdenTrabajoTarea;		
	}
	
	public function GetByIdIncrement($IdOrdenTrabajoTarea)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoTareas";
		$sql.= " WHERE IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoTarea = new OrdenTrabajoTarea();
		$oOrdenTrabajoTarea->ParseFromArray($oRow);
		
		return $oOrdenTrabajoTarea;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoTareas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(OrdenTrabajoTarea $oOrdenTrabajoTarea)
	{
		$arr = array
		(
			'IdOrdenTrabajo'		=> DB::Number($oOrdenTrabajoTarea->IdOrdenTrabajo),
			'Importe' 				=> DB::Number($oOrdenTrabajoTarea->Importe),
			'Titulo' 				=> DB::String($oOrdenTrabajoTarea->Titulo),
			'Descripcion' 			=> DB::String($oOrdenTrabajoTarea->Descripcion),
			'HorasEstimadas' 		=> DB::Number($oOrdenTrabajoTarea->HorasEstimadas),
			'IdTareaTrabajo'		=> DB::Number($oOrdenTrabajoTarea->IdTareaTrabajo),
			'IdTipoVenta'			=> DB::Number($oOrdenTrabajoTarea->IdTipoVenta),
			'IdEstado'				=> DB::Number($oOrdenTrabajoTarea->IdEstado),
			'IdCodigoTrabajo'		=> DB::Number($oOrdenTrabajoTarea->IdCodigoTrabajo),
			'IdFacturaCompra'		=> DB::Number($oOrdenTrabajoTarea->IdFacturaCompra),
			'Tarea'					=> DB::String($oOrdenTrabajoTarea->Tarea),
			'IdCategoria'			=> DB::Number($oOrdenTrabajoTarea->IdCategoria),
			'Agrupar'				=> DB::Bool($oOrdenTrabajoTarea->Agrupar),
			'TotalMO'				=> DB::Number($oOrdenTrabajoTarea->TotalMO),
			'TotalRepuestos'		=> DB::Number($oOrdenTrabajoTarea->TotalRepuestos),
			'IdVendedor'			=> DB::Number($oOrdenTrabajoTarea->IdVendedor),
			'IdProveedor'			=> DB::Number($oOrdenTrabajoTarea->IdProveedor),
			'CostoTotal'			=> DB::Number($oOrdenTrabajoTarea->CostoTotal),
			'Terceros'				=> DB::Bool($oOrdenTrabajoTarea->Terceros)
		);
		return $arr;
	}
	
	public function Create(OrdenTrabajoTarea $oOrdenTrabajoTarea)
	{
		$oOrdenTrabajoTarea->IdEstado = OrdenTrabajoTarea::IdEstadoActivo;
		$arr = $this->GetArrayDB($oOrdenTrabajoTarea);
		
		if (!$this->Insert('TB_OrdenesTrabajoTareas', $arr))
			return false;

		/* asignamos el id generado */
		$oOrdenTrabajoTarea->IdOrdenTrabajoTarea = DBAccess::GetLastInsertId();
			
		return $oOrdenTrabajoTarea;
	}
	
	
	public function Update(OrdenTrabajoTarea $oOrdenTrabajoTarea)
	{
		$where = " IdOrdenTrabajoTarea = " . DB::Number($oOrdenTrabajoTarea->IdOrdenTrabajoTarea);
		
		$arr = $this->GetArrayDB($oOrdenTrabajoTarea);
		
		if (!DBAccess::Update('TB_OrdenesTrabajoTareas', $arr, $where))
			return false;
		
		return $oOrdenTrabajoTarea;
	}

	public function Delete($IdOrdenTrabajo, $IdTareaTrabajo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$where .= " AND IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);

		if (!DBAccess::Delete('TB_OrdenesTrabajoTareas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function DeleteIncrement($IdOrdenTrabajoTarea)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajoTarea = " . DB::Number($IdOrdenTrabajoTarea);

		if (!DBAccess::Delete('TB_OrdenesTrabajoTareas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>