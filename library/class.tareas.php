<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.tarea.php');

class Tareas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ($filter['Nombre'] != "")
		{	
			$sql.= " AND (u.Nombre RLIKE '" . DB::StringUnquoted($filter['Nombre']) . "'";
			$sql.= " OR u.Nombre IS NULL)";
		}
		
		if ($filter['Descripcion'] != "")
		{	
			$sql.= " AND (u.Descripcion RLIKE '" . DB::StringUnquoted($filter['Descripcion']) . "'";
			$sql.= " OR u.Descripcion IS NULL)";
		}
		
		if ($filter['IdUsuarioFrom'] != "")
		{
			$sql.= " AND u.IdUsuarioFrom = " . DB::Number($filter['IdUsuarioFrom']);
		}

		if ($filter['IdUsuarioTo'] != "")
		{
			$sql.= " AND u.IdUsuarioTo = " . DB::Number($filter['IdUsuarioTo']);
		}

		if ($filter['IdTipo'] != "")
		{
			$sql.= " AND u.IdTipo = " . DB::Number($filter['IdTipo']);
		}

		/*if ($filter['IdEstado'] != "")
		{
			$sql.= " AND u.IdEstado = " . DB::Number($filter['IdEstado']);
		}*/
		
		if ($filter['IdEstado'] == TareaEstados::Pendiente)
		{
			$sql.= " AND (u.IdEstado = " . DB::Number($filter['IdEstado']);
			$sql.= " OR u.IdEstado = " . DB::Number(TareaEstados::EnProceso) . ")";
		}
		else if ($filter['IdEstado'] != '')
			$sql.= " AND u.IdEstado = " . DB::Number($filter['IdEstado']);
		
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

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		$sql.= " GROUP BY u.IdTarea";

		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
	public function GetAllByUsuario(Usuario $oUsuario)
	{
		$arr = array();
	
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE u.IdUsuarioTo = " . DB::Number($oUsuario->IdUsuario);
		$sql.= " OR u.IdUsuarioFrom = " . DB::Number($oUsuario->IdUsuario);
		$sql.= " GROUP BY u.IdTarea";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oTarea = new Tarea();
			$oTarea->ParseFromArray($oRow);
			
			array_push($arr, $oTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
		
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.FechaFin DESC";
		//print_r($sql);	
		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oTarea = new Tarea();
			$oTarea->ParseFromArray($oRow);
			
			
			array_push($arr, $oTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	
	public function GetTareasDerivadas(Usuario $oUsuario)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE u.IdUsuarioFrom = " . $oUsuario->IdUsuario;
		$sql.= " AND u.IdUsuarioTo <> " . $oUsuario->IdUsuario;
		$sql.= " ORDER BY u.FechaFin DESC";
		$sql.= " LIMIT 10";
		
		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oTarea = new Tarea();
			$oTarea->ParseFromArray($oRow);
			
			
			array_push($arr, $oTarea);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}

	
	public function GetById($IdTarea)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE u.IdTarea = " . DB::Number($IdTarea);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oTarea = new Tarea();
		$oTarea->ParseFromArray($oRow);

		
		return $oTarea;		
	}

	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Tareas u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Nombre";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Tarea $oTarea)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdTipo'			=> DB::Number($oTarea->IdTipo),						
			'FechaInicio'		=> DB::Date($oTarea->FechaInicio),
			'FechaFin'			=> DB::Date($oTarea->FechaFin),			
			'Nombre'			=> DB::String($oTarea->Nombre),
			'IdUsuarioFrom'		=> DB::Number($oTarea->IdUsuarioFrom),
			'IdUsuarioTo'		=> DB::Number($oTarea->IdUsuarioTo),
			'IdEstado'			=> DB::Number($oTarea->IdEstado),						
			'Descripcion'		=> DB::String($oTarea->Descripcion),			
			'Hora'				=> DB::String($oTarea->Hora),	
			'IdCliente'			=> DB::Number($oTarea->IdCliente),		
			'IdPresupuesto'		=> DB::Number($oTarea->IdPresupuesto)		
		);

		if (!DBAccess::Insert('TB_Tareas', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oCliente;
	}
	
	
	public function Update(Tarea $oTarea)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdTipo'			=> DB::Number($oTarea->IdTipo),						
			'FechaInicio'		=> DB::Date($oTarea->FechaInicio),
			'FechaFin'			=> DB::Date($oTarea->FechaFin),			
			'Nombre'			=> DB::String($oTarea->Nombre),
			'IdUsuarioFrom'		=> DB::Number($oTarea->IdUsuarioFrom),
			'IdUsuarioTo'		=> DB::Number($oTarea->IdUsuarioTo),
			'IdEstado'			=> DB::Number($oTarea->IdEstado),						
			'Descripcion'		=> DB::String($oTarea->Descripcion),			
			'Hora'				=> DB::String($oTarea->Hora),		
			'IdCliente'			=> DB::Number($oTarea->IdCliente),		
			'IdPresupuesto'		=> DB::Number($oTarea->IdPresupuesto)
		);

		$where = " IdTarea = " . (int)$oTarea->IdTarea;
		
		if (!DBAccess::Update('TB_Tareas', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oCliente;
	}
	
	public function Delete($IdTarea)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTarea = " . DB::Number($IdTarea);
		if (!DBAccess::Delete('TB_Tareas', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>