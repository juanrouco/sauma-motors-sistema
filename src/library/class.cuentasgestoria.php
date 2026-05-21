<?php 

require_once('class.dbaccess.php');
require_once('class.cuentagestoria.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.gestores.php');
require_once('class.cajasgestoria.php');
require_once('class.filter.php');
require_once('class.session.php');
require_once('class.tipopago.php');
require_once('class.cajasdetallesdefault.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');

class CuentasGestoria extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['IdGestor'])) && ($filter['IdGestor'] != ''))
			$sql.= " AND IdGestor = " . DB::Number($filter['IdGestor']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['SinRendir'])) && ($filter['SinRendir'] != '' && $filter['SinRendir'] == '1'))
			$sql.= " AND FechaRendicion IS NULL";
			
		if ((isset($filter['Rendido'])) && ($filter['Rendido'] != '' && $filter['Rendido'] == '1'))
			$sql.= " AND FechaRendicion IS NOT NULL";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCuentaGestoria = new CuentaGestoria();
			$oCuentaGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oCuentaGestoria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllHeaders(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		$Fecha = '';
		$array_headers = array();
		$index = 0;
		while ($oRow = $oRes->GetRow())	
		{	
			$oCuentaGestoria = new CuentaGestoria();
			$oCuentaGestoria->ParseFromArray($oRow);
			
			if ($Fecha != $oCuentaGestoria->Fecha)
			{
				if ($Fecha != '')
				{
					$array_headers[] = array('Fecha' => $Fecha, 'CuentasCorriente' => $arr);
					$arr = array(); 
				}
				
				$Fecha = $oCuentaGestoria->Fecha;
			}
			
			array_push($arr, $oCuentaGestoria);
			
			$oRes->MoveNext();
		}	
		if (count($arr) > 0)
			$array_headers[] = array('Fecha' => $Fecha, 'CuentasCorriente' => $arr);
					
		
		return $array_headers;
	}
	
	public function GetAllByFecha($Fecha)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE Fecha = " . DB::Date($Fecha);
		$sql.= " ORDER BY IdMinuta DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCuentaGestoria = new CuentaGestoria();
			$oCuentaGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oCuentaGestoria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByFechaOrdered($Fecha)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE Fecha = " . DB::Date($Fecha);
		$sql.= " ORDER BY IdGestor DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCuentaGestoria = new CuentaGestoria();
			$oCuentaGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oCuentaGestoria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetById($IdCuentaGestoria)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE IdCuentaGestoria = " . DB::Number($IdCuentaGestoria);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuentaGestoria = new CuentaGestoria();
		$oCuentaGestoria->ParseFromArray($oRow);
		
		return $oCuentaGestoria;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_CuentasGestoria fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuentaGestoria = new CuentaGestoria();
		$oCuentaGestoria->ParseFromArray($oRow);
		
		return $oCuentaGestoria;		
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuentaGestoria = new CuentaGestoria();
		$oCuentaGestoria->ParseFromArray($oRow);
		
		return $oCuentaGestoria;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetCountRowsHeaders(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuentasGestoria";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY Fecha";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(CuentaGestoria $oCuentaGestoria)
	{
		$arr = array
		(
			'IdMinuta' 					=> DB::Number($oCuentaGestoria->IdMinuta),
			'IdGestor'					=> DB::Number($oCuentaGestoria->IdGestor),
			'PatentamientoCalculado' 	=> DB::Number($oCuentaGestoria->PatentamientoCalculado),
			'PatentamientoFinal'	 	=> DB::Number($oCuentaGestoria->PatentamientoFinal),
			'PrendaCalculado'			=> DB::Number($oCuentaGestoria->PrendaCalculado),
			'PrendaFinal'				=> DB::Number($oCuentaGestoria->PrendaFinal),
			'AltaCalculado' 			=> DB::Number($oCuentaGestoria->AltaCalculado),
			'AltaFinal' 				=> DB::Number($oCuentaGestoria->AltaFinal),
			'SelladoCalculado'			=> DB::Number($oCuentaGestoria->SelladoCalculado),
			'SelladoFinal'				=> DB::Number($oCuentaGestoria->SelladoFinal),
			'TotalCalculado'			=> DB::Number($oCuentaGestoria->TotalCalculado),
			'TotalFinal'				=> DB::Number($oCuentaGestoria->TotalFinal),
			'ComisionGestor'			=> DB::Number($oCuentaGestoria->ComisionGestor),
			'Fecha' 					=> DB::Date($oCuentaGestoria->Fecha),
			'FechaRendicion'			=> DB::Date($oCuentaGestoria->FechaRendicion),
			'TotalRendicion'			=> DB::Number($oCuentaGestoria->TotalRendicion),
			'Comentarios'				=> DB::String($oCuentaGestoria->Comentarios)
		);
		
		return $arr;
	}
	
	public function Create(CuentaGestoria $oCuentaGestoria)
	{
		$arr = $this->GetArrayDB($oCuentaGestoria);
		
		if (!$this->Insert('TB_CuentasGestoria', $arr))
			return false;

		/* asignamos el id generado */
		$oCuentaGestoria->IdCuentaGestoria = DBAccess::GetLastInsertId();
		
		$this->CrearMovimientoCuentaGestoria($oCuentaGestoria);
			
		return $oCuentaGestoria;
	}
	
	

	private function CrearMovimientoCuentaGestoria($oCuentaGestoria)
	{
		$oCajasMovimientos		= new CajasMovimientos();
		$oCajaMovimiento		= new CajaMovimiento();
		$oCajasDetallesDefault	= new CajasDetallesDefault();
		
		$oUsuario		= Session::GetCurrentUser();
		$oCajaDefault	= $oCajasDetallesDefault->GetById(TipoPago::Efectivo, $oUsuario->IdUbicacion);
		
		$Crear = true;
		$IdCajaDetalle = $oCajaDefault->IdCajaAdministracion;
		
		$oCajaMovimiento->IdCajaDetalle = $IdCajaDetalle;
		//$oCajaMovimiento->IdCajaDetalle = 8;
		
		//print_R($oCuentaGestoria);exit;
		if ($Crear && $oCuentaGestoria->PrendaCalculado && $oCuentaGestoria->PrendaCalculado > 0)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::CuentaCorriente;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oCuentaGestoria->IdCuentaGestoria;
			$oCajaMovimiento->Total = $oCuentaGestoria->PrendaCalculado * -1;
			$oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}
		
		$oCajaMovimiento->IdCajaDetalle = $IdCajaDetalle;
		//$oCajaMovimiento->IdCajaDetalle = 8;
		
		
		if ($Crear)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::CuentaCorriente;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oCuentaGestoria->IdCuentaGestoria;
			$oCajaMovimiento->Total = ($oCuentaGestoria->TotalCalculado - $oCuentaGestoria->PrendaCalculado) * -1;
			$oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}
	}	
	
	

	private function CrearMovimientoCuentaGestoriaRendicion($oCuentaGestoria)
	{
		$oCajasMovimientos		= new CajasMovimientos();
		$oCajaMovimiento		= new CajaMovimiento();
		$oCajasDetallesDefault	= new CajasDetallesDefault();
		
		$oUsuario		= Session::GetCurrentUser();
		$oCajaDefault	= $oCajasDetallesDefault->GetById(TipoPago::Efectivo, $oUsuario->IdUbicacion);
		
		$Crear = true;
		$IdCajaDetalle = $oCajaDefault->IdCajaAdministracion;
		
		$oCajaMovimiento->IdCajaDetalle = $IdCajaDetalle;
		//$oCajaMovimiento->IdCajaDetalle = 8;
		
		
		if ($Crear)
		{
			$oCajaMovimiento->IdTipoMovimiento = TiposMovimientosCaja::Rendicion;
			$oCajaMovimiento->Fecha = date('Y-m-d H:i:s');
			$oCajaMovimiento->IdEntidad = $oCuentaGestoria->IdCuentaGestoria;
			$oCajaMovimiento->Total = $oCuentaGestoria->TotalRendicion;
			$oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}
	}	
	
	private function EliminarMovimientoCuentaGestoria($IdCuentaGestoria)
	{
		$oCajasMovimientos		= new CajasMovimientos();
		$oCajaMovimiento		= new CajaMovimiento();
		$oCajasDetallesDefault	= new CajasDetallesDefault();
		
		$oUsuario		= Session::GetCurrentUser();
		$oCajaDefault	= $oCajasDetallesDefault->GetById(TipoPago::Efectivo, $oUsuario->IdUbicacion);
		
		$Crear = true;
		$IdCajaDetalle = $oCajaDefault->IdCajaAdministracion;
		
		if ($oCajaMovimiento = $oCajasMovimientos->GetByIdCajaDetalleIdEntidad($IdCajaDetalle, TiposMovimientosCaja::CuentaCorriente, $IdCuentaGestoria))
		{
			$oCajasMovimientos->Delete($oCajaMovimiento->IdCajaMovimiento);
			
			$Importe = $oCajaMovimiento->Total;
		
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total -= $Importe;
			$oCajasDetalles->Update($oCajaDetalle);
				
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir -= $Importe;
			$oCaja->TotalDetalles -= $Importe;
			$oCajas->Update($oCaja);
		}
		if ($oCajaMovimiento = $oCajasMovimientos->GetByIdCajaDetalleIdEntidad($IdCajaDetalle, TiposMovimientosCaja::CuentaCorriente, $IdCuentaGestoria))
		{
			$oCajasMovimientos->Delete($oCajaMovimiento->IdCajaMovimiento);
			
			$Importe = $oCajaMovimiento->Total;
		
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total -= $Importe;
			$oCajasDetalles->Update($oCajaDetalle);
				
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir -= $Importe;
			$oCaja->TotalDetalles -= $Importe;
			$oCajas->Update($oCaja);
		}
	}
	
	private function EliminarMovimientoCuentaRendicionGestoria($IdCuentaGestoria)
	{
		$oCajasMovimientos = new CajasMovimientos();
		if ($oCajaMovimiento = $oCajasMovimientos->GetByIdEntidad(TiposMovimientosCaja::Rendicion, $IdCuentaGestoria))
		{
			$oCajasMovimientos->Delete($oCajaMovimiento->IdCajaMovimiento);
			
			$Importe = $oCajaMovimiento->Total;
		
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total -= $Importe;
			$oCajasDetalles->Update($oCajaDetalle);
				
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir -= $Importe;
			$oCaja->TotalDetalles -= $Importe;
			$oCajas->Update($oCaja);
		}
	}
	
	public function Update(CuentaGestoria $oCuentaGestoria)
	{
		$where = " IdCuentaGestoria = " . DB::Number($oCuentaGestoria->IdCuentaGestoria);
		
		$arr = $this->GetArrayDB($oCuentaGestoria);
		
		if (!DBAccess::Update('TB_CuentasGestoria', $arr, $where))
			return false;
			
		$oCajasGestoria = new CajasGestoria();
		
		$this->EliminarMovimientoCuentaGestoria($oCuentaGestoria->IdCuentaGestoria);
		$this->CrearMovimientoCuentaGestoria($oCuentaGestoria);
		
		return $oCuentaGestoria;
	}
	
	public function UpdateRendicion(CuentaGestoria $oCuentaGestoria)
	{
		$where = " IdCuentaGestoria = " . DB::Number($oCuentaGestoria->IdCuentaGestoria);
		
		$arr = $this->GetArrayDB($oCuentaGestoria);
		
		if (!DBAccess::Update('TB_CuentasGestoria', $arr, $where))
			return false;
			
		$this->EliminarMovimientoCuentaRendicionGestoria($oCuentaGestoria->IdCuentaGestoria);
		$this->CrearMovimientoCuentaGestoriaRendicion($oCuentaGestoria);
		
		return $oCuentaGestoria;
	}
	
	public function Delete($IdCuentaGestoria)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCuentaGestoria = " . DB::Number($IdCuentaGestoria);

		if (!DBAccess::Delete('TB_CuentasGestoria', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		$oCajasGestoria = new CajasGestoria();
		
		$this->EliminarMovimientoCuentaGestoria($IdCuentaGestoria);
		$this->EliminarMovimientoCuentaRendicionGestoria($IdCuentaGestoria);
		
		return true;	
	}
	
	public function ExportXls(array $filter = NULL, $fullpermiso = false)
	{
		/* declaramos variables necesarias */
		$oMinutas 	= new Minutas();
		$oGestores 	= new Gestores();

		/* obtenemos el listado de datos a exportar */			
		$arrCuentasGestorias = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"FECHA",
			"NRO. INTERNO",
			"GESTOR",
			"GESTORIA COBRADA",
			"GESTORIA REAL",
			"GANANCIA"
		);
		
				
		foreach ($arrCuentasGestorias as $oCuentaGestoria)
		{	
			$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
			$oGestor = $oGestores->GetById($oCuentaGestoria->IdGestor);

			$arrData[] = array
			(
				trim(CambiarFecha($oCuentaGestoria->Fecha)),
				trim($oMinuta->IdMinuta),
				trim($oGestor->RazonSocial),
				trim($oMinuta->GastosPatentamiento),
				trim($oCuentaGestoria->TotalFinal),
				trim($oMinuta->GastosPatentamiento - $oCuentaGestoria->TotalFinal)
			);
			
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'gestorias';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>