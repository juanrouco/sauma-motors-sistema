<?php 

require_once('class.dbaccess.php');
require_once('class.turno.php');
require_once('class.modelos.php');
require_once('class.tipoventa.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class Turnos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND ot.Fecha >= " . DB::Date($filter['FechaDesde']);
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND ot.Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaInicio'])) && ($filter['FechaInicio'] != ''))
			$sql.= " AND ot.FechaInicio = " . DB::Date($filter['FechaInicio']);
			
		if ((isset($filter['FechaInicioDesde'])) && ($filter['FechaInicioDesde'] != ''))
			$sql.= " AND ot.FechaInicio >= " . DB::Date($filter['FechaInicioDesde']);
		
		if ((isset($filter['FechaInicioHasta'])) && ($filter['FechaInicioHasta'] != ''))
			$sql.= " AND ot.FechaInicio < " . DB::Date($filter['FechaInicioHasta'] . ' 23:00');
		
		if ((isset($filter['FechaFin'])) && ($filter['FechaFin'] != ''))
			$sql.= " AND ot.FechaFin = " . DB::Date($filter['FechaFin']);
			
		if ((isset($filter['IdUsuarioAsignado'])) && ($filter['IdUsuarioAsignado'] != ''))
			$sql.= " AND ot.IdUsuarioAsignado = " . DB::Number($filter['IdUsuarioAsignado']);

		if ((isset($filter['IdEstadoOrden'])) && ($filter['IdEstadoOrden'] != ''))
			$sql.= " AND ot.IdEstadoOrden = " . DB::Number($filter['IdEstadoOrden']);

		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND tu.Dominio LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";
			
		if ((isset($filter['IdTipoVenta'])) && $filter['IdTipoVenta'] != '')
		{
			if (intval($filter['IdTipoVenta']) == 0)
				$sql.= " AND (ot.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number(TipoVenta::OrdenReparacion) . ")";
			else
				$sql.= " AND (ot.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . " OR ott.IdTipoVenta = " . DB::Number($filter['IdTipoVenta']) . ")";
		}
		
		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			
		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND tu.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
		
		if ((isset($filter['IdTurno'])) && ($filter['IdTurno'] != ''))		
			$sql.= " AND ot.IdTurno = " . DB::Number($filter['IdTurno']);
			
		if ((isset($filter['Bahia'])) && ($filter['Bahia'] != ''))
			$sql.= " AND ot.`Bahia` = " . DB::Bool($filter['Bahia']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_Turnos ot";
		$sql.= " LEFT JOIN TB_TurnosTareas ott ON ot.IdTurno = ott.IdTurno";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY ot.IdTurno";
		$sql.= " ORDER BY ot.FechaInicio DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurno = new Turno();
			$oTurno->ParseFromArray($oRow);
			
			array_push($arr, $oTurno);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByEstado($IdEstadoOrden, $Cantidad = 10)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_Turnos ot";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1=1";
		$sql.= " AND ot.IdEstadoOrden = " . DB::Number($IdEstadoOrden);
		$sql.= " ORDER BY ot.FechaIngreso, ot.IdTurno";
		$sql.= " LIMIT " . DB::Number($Cantidad);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTurno = new Turno();
			$oTurno->ParseFromArray($oRow);
			
			array_push($arr, $oTurno);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}	
	
	public function GetById($IdTurno)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Turnos";
		$sql.= " WHERE IdTurno = " . DB::Number($IdTurno);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurno = new Turno();
		$oTurno->ParseFromArray($oRow);
		
		return $oTurno;		
	}
	
	public function GetByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Turnos";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurno = new Turno();
		$oTurno->ParseFromArray($oRow);
		
		return $oTurno;		
	}
		
	public function GetLastByIdTallerUnidad($IdTallerUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Turnos";
		$sql.= " WHERE IdTallerUnidad = " . DB::Number($IdTallerUnidad);	
		$sql.= " AND IdEstadoOrden = " . DB::Number(EstadoOrden::Aceptada);	
		$sql.= " ORDER BY IdTurno";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTurno = new Turno();
		$oTurno->ParseFromArray($oRow);
		
		return $oTurno;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT ot.*";
		$sql.= " FROM TB_Turnos ot";
		$sql.= " LEFT JOIN TB_TurnosTareas ott ON ot.IdTurno = ott.IdTurno";
		$sql.= " INNER JOIN TB_TallerUnidades tu ON tu.IdTallerUnidad = ot.IdTallerUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY ot.IdTurno";
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Turno $oTurno)
	{
		$arr = array
		(
			'IdEstadoOrden' 	=> DB::Number($oTurno->IdEstadoOrden),
			'IdTallerUnidad' 	=> DB::Number($oTurno->IdTallerUnidad),
			'Fecha' 			=> DB::Date($oTurno->Fecha),
			'FechaInicio' 		=> DB::Date($oTurno->FechaInicio),
			'FechaFin' 			=> DB::Date($oTurno->FechaFin),
			'IdUsuarioCreacion' => DB::Number($oTurno->IdUsuarioCreacion),
			'IdUsuarioAsignado' => DB::Number($oTurno->IdUsuarioAsignado),
			'Kilometros'		=> DB::Number($oTurno->Kilometros),
			'Comentarios'		=> DB::String($oTurno->Comentarios),
			'IdTipoVenta'		=> DB::Number($oTurno->IdTipoVenta),
			'IdOrdenTrabajo'	=> DB::Number($oTurno->IdOrdenTrabajo),
			'Bahia'				=> DB::Bool($oTurno->Bahia),
			'Remis'				=> DB::Bool($oTurno->Remis),
			'Reconfirmado'		=> DB::Bool($oTurno->Reconfirmado)
		);
		
		return $arr;
	}
	
	public function Create(Turno $oTurno)
	{
		$arr = $this->GetArrayDB($oTurno);
		
		if (!$this->Insert('TB_Turnos', $arr))
			return false;

		/* asignamos el id generado */
		$oTurno->IdTurno = DBAccess::GetLastInsertId();
			
		return $oTurno;
	}
	
	
	public function Update(Turno $oTurno)
	{
		$where = " IdTurno = " . DB::Number($oTurno->IdTurno);
		
		$arr = $this->GetArrayDB($oTurno);
		
		if (!DBAccess::Update('TB_Turnos', $arr, $where))
			return false;
		
		return $oTurno;
	}

	public function Delete($IdTurno)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTurno = " . DB::Number($IdTurno);

		if (!DBAccess::Delete('TB_Turnos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function ExportCsv(array $filter = NULL)
	{
		$oTallerUnidades = new TallerUnidades();
		$oClientes = new Clientes();
		$oEstadosOrden = new EstadosOrden();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "ordenestrabajo.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrData 	= $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Nro. OT";
		$csv.= $Separador;
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Dominio";
		$csv.= $Separador;
		$csv.= "Modelo";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Estado";
		$csv.= $Separador;
		$csv.= "Ingreso";
		$csv.= $Separador;
		$csv.= "Salida";
		$csv.= $Separador;
		$csv.= "Total";
		$csv.= $SaltoLinea;
	
		foreach ($arrData as $oTurno)
		{				
			$oTallerUnidad = $oTallerUnidades->GetById($oTurno->IdTallerUnidad);
			$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
			$oEstadoOrden = $oEstadosOrden->GetById($oTurno->IdEstadoOrden);
			
			$csv.= str_replace('(\t|\n)','', trim($oTurno->IdTurno));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oTurno->Fecha)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Dominio));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTallerUnidad->Modelo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oEstadoOrden->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oTurno->FechaInicio)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oTurno->FechaFin)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oTurno->ImporteTotal()));			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>