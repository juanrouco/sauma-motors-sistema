<?php 

require_once('class.dbaccess.php');
require_once('class.usado.php');
require_once('class.colores.php');
require_once('class.marcas.php');
require_once('class.estadosunidad.php');
require_once('class.ubicaciones.php');
require_once('class.tipopago.php');
require_once('class.pedidosaccesorios.php');
require_once('class.minutasusadosfinanciacion.php');
require_once('class.cuentasgestoriausados.php');
require_once('class.pagos.php');
require_once('class.minutasusados.php');
require_once('class.clientes.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');
require_once('excel_reader/class.xlsreader.php');


class Usados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdUsado'])) && ($filter['IdUsado'] != ''))
			$sql.= " AND IdUsado = " . DB::Number($filter['IdUsado']);

		if ((isset($filter['IdMarca'])) && ($filter['IdMarca'] != ''))
			$sql.= " AND u.IdMarca = " . DB::Number($filter['IdMarca']);
		
		if ((isset($filter['IdUbicacion'])) && ($filter['IdUbicacion'] != ''))
			$sql.= " AND IdUbicacion = " . DB::Number($filter['IdUbicacion']);
			
		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
			
		if ((isset($filter['AnioDesde'])) && ($filter['AnioDesde'] != ''))
			$sql.= " AND ModeloAnio >= " . DB::Number($filter['AnioDesde']);
			
		if ((isset($filter['AnioHasta'])) && ($filter['AnioHasta'] != ''))
			$sql.= " AND ModeloAnio <= " . DB::Number($filter['AnioHasta']);
			
		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND FechaMinuta >= " . DB::Date($filter['FechaDesde']);
		
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND FechaMinuta <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['IdMinutaUsado'])) && ($filter['IdMinutaUsado'] != ''))
			$sql.= " AND IdMinutaUsado = " . DB::Number($filter['IdMinutaUsado']);

		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
			$sql.= " AND Modelo LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%'";
			
		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND Dominio LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";
			
		if ((isset($filter['IdUnidadDesde'])) && ($filter['IdUnidadDesde'] != ''))
			$sql.= " AND IdUsado >= " . DB::Number($filter['IdUnidadDesde']);
		
		if ((isset($filter['IdUnidadHasta'])) && ($filter['IdUnidadHasta'] != ''))
			$sql.= " AND IdUsado <= " . DB::Number($filter['IdUnidadHasta']);
			
		if ((isset($filter['Pisado'])) && ($filter['Pisado'] != ''))
			$sql.= " AND Pisado = " . DB::Bool($filter['Pisado']);
			
		if ((isset($filter['Consignacion'])) && ($filter['Consignacion'] != ''))
			$sql.= " AND Consignacion = " . DB::Bool($filter['Consignacion']);

		if ($filter['Reportado'] == '0' || $filter['Reportado'] == '1')
			$sql.= " AND mt.Reportado = " . DB::Bool($filter['Reportado']);
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_Marcas m ON u.IdMarca = m.IdMarca";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY m.Nombre, u.Modelo, u.ModeloAnio ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_Marcas m ON u.IdMarca = m.IdMarca";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY m.Nombre, Modelo DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByColor(Color $oColor)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdColor = " . DB::Number($oColor->IdColor);
		$sql.= " ORDER BY IdUsado DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByMarca(Marca $oMarca)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdMarca = " . DB::Number($oMarca->IdMarca);
		$sql.= " ORDER BY IdUsado DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllReporteStock(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Usados u";
		$sql.= " WHERE 1";//u.IdEstado <> " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdUsado DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetTotalReporteStock(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUsado) AS CantidadTotal, SUM(IF (u.Consignacion, 0, u.Valuacion)) AS CostoTotal";
		$sql.= " FROM TB_Usados u";
		$sql.= " WHERE 1";// u.IdEstado <> " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		
		if (isset($filter['FechaDesde']) && $filter['FechaDesde'] != '')
			$sql.= " AND v.FechaMinuta >= " . DB::Date($filter['FechaDesde']);
		if (isset($filter['FechaHasta']) && $filter['FechaHasta'] != '')
			$sql.= " AND v.FechaMinuta <= " . DB::Date($filter['FechaHasta']);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];

		return $oReporteTotal;		
	}
	
	public function GetCountRowsReporteStock(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados u";
		$sql.= " WHERE 1";//u.IdEstado <> " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	
	public function GetAllReporteVendidos(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_MinutasUsados mt ON mt.IdUsado = u.IdUsado";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ")";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY u.IdUsado DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetTotalReporteVendidos(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUsado) AS CantidadTotal, SUM(mt.PrecioVenta) AS CostoTotal";
		
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_MinutasUsados mt ON mt.IdUsado = u.IdUsado";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ")";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];

		return $oReporteTotal;		
	}
	
	public function GetCountRowsReporteVendidos(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_MinutasUsados mt ON mt.IdUsado = u.IdUsado";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ")";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	public function GetAllByIdMinuta($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " ORDER BY IdUsado DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetAllByIdMinutaUsado($IdMinutaUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdMinutaUsado = " . DB::Number($IdMinutaUsado);
		$sql.= " ORDER BY IdUsado DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetAllByIdMinutaEspera($IdMinutaEspera)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdMinutaEspera = " . DB::Number($IdMinutaEspera);
		$sql.= " ORDER BY IdUsado DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsado = new Usado();
			$oUsado->ParseFromArray($oRow);
			
			array_push($arr, $oUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE IdUsado = " . DB::Number($IdUsado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUsado = new Usado();
		$oUsado->ParseFromArray($oRow);
		
		return $oUsado;		
	}


	public function GetByDominio($Dominio)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usados";
		$sql.= " WHERE Dominio = " . DB::String($Dominio);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUsado = new Usado();
		$oUsado->ParseFromArray($oRow);
		
		return $oUsado;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Usados u";
		$sql.= " INNER JOIN TB_Marcas m ON u.IdMarca = m.IdMarca";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Usado $oUsado)
	{
		$arr = array
		(
			'IdMarca' 			=> DB::Number($oUsado->IdMarca),
			'IdColor' 			=> (DB::Number($oUsado->IdColor) == 0)? 'NULL' : DB::Number($oUsado->IdColor),
			'Modelo' 			=> DB::String($oUsado->Modelo),
			'Dominio' 			=> DB::String($oUsado->Dominio),
			'ModeloAnio' 		=> DB::Number($oUsado->ModeloAnio),
			'Kilometraje' 		=> DB::Number($oUsado->Kilometraje),
			'Valuacion' 		=> DB::Number($oUsado->Valuacion),
			'IdEstado'	 		=> DB::Number($oUsado->IdEstado),
			'IdUbicacion' 		=> DB::Number($oUsado->IdUbicacion),
			'NumeroVinPrefijo' 	=> DB::String($oUsado->NumeroVinPrefijo),
			'NumeroVin' 		=> DB::String($oUsado->NumeroVin),
			'NumeroMotor' 		=> DB::String($oUsado->NumeroMotor),
			'NumeroChasis' 		=> DB::String($oUsado->NumeroChasis),
			'Pisado'			=> DB::Bool($oUsado->Pisado),
			'Comentarios'		=> DB::String($oUsado->Comentarios),
			'IdMarcaMotor'		=> DB::Number($oUsado->IdMarcaMotor),
			'IdMarcaChasis'		=> DB::Number($oUsado->IdMarcaChasis),
			'PrecioVenta'		=> DB::Number($oUsado->PrecioVenta),
			'PrecioVenta2'		=> DB::Number($oUsado->PrecioVenta2),
			'IdTipoModelo'		=> DB::Number($oUsado->IdTipoModelo),
			'IdProveedor'		=> DB::Number($oUsado->IdProveedor),
			'IdCliente'			=> DB::Number($oUsado->IdCliente),
			'IdMinuta'			=> DB::Number($oUsado->IdMinuta),
			'IdMinutaUsado'		=> DB::Number($oUsado->IdMinutaUsado),
			'Arreglos'			=> DB::Number($oUsado->Arreglos),
			'Observaciones'		=> DB::String($oUsado->Observaciones),
			'Info'				=> DB::Number($oUsado->Info),
			'IdMinutaEspera'	=> DB::Number($oUsado->IdMinutaEspera),
			'EntregaTitulo' 			=> DB::Bool($oUsado->EntregaTitulo),
			'EntregaCedula' 			=> DB::Bool($oUsado->EntregaCedula),
			'Entrega08' 				=> DB::Bool($oUsado->Entrega08),
			'EntregaInformeDominio' 	=> DB::Bool($oUsado->EntregaInformeDominio),
			'Entrega13I' 				=> DB::Bool($oUsado->Entrega13I),
			'EntregaVerificacionBomberos' 			=> DB::Bool($oUsado->EntregaVerificacionBomberos),
			'EntregaPatentes' 			=> DB::Bool($oUsado->EntregaPatentes),
			'EntregaManualLlaves' 		=> DB::Bool($oUsado->EntregaManualLlaves),
			'EntregaManual'		 		=> DB::Bool($oUsado->EntregaManual),
			'EntregaClaveFiscal' 		=> DB::Bool($oUsado->EntregaClaveFiscal),
			'FechaRetiro' 				=> DB::Date($oUsado->FechaRetiro),
			'Consignacion'				=> DB::Bool($oUsado->Consignacion)
		);
		return $arr;
	}
	
	public function Create(Usado $oUsado)
	{
		$arr = $this->GetArrayDB($oUsado);
		
		if (!$this->Insert('TB_Usados', $arr))
			return false;

		/* asignamos el id generado */
		$oUsado->IdUsado = DBAccess::GetLastInsertId();
			
		return $oUsado;
	}
	
	
	public function Update(Usado $oUsado)
	{
		$where = " IdUsado = " . DB::Number($oUsado->IdUsado);
		
		$arr = $this->GetArrayDB($oUsado);
		
		if (!DBAccess::Update('TB_Usados', $arr, $where))
			return false;
		
		return $oUsado;
	}
	

	public function Delete($IdUsado)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsado = " . DB::Number($IdUsado);

		if (!DBAccess::Delete('TB_Usados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	
	
	public function ExportReporteStockCsv(array $filter = NULL)
	{
		$oColores 		= new Colores();
		$oUbicaciones	= new Ubicaciones();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "usados_reporte_stock.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrUsados = $this->GetAllReporteStock($filter);
		//$oReporteTotal 	= $this->GetTotalReporteStock($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		/*$csv.= "Datos Totales";
		$csv.= $SaltoLinea;
		$csv.= "Cantidad Unidades en Stock";
		$csv.= $Separador;
		$csv.= $oReporteTotal->CantidadTotal;
		$csv.= $SaltoLinea;
		$csv.= "Valuación Stock";
		$csv.= $Separador;
		$csv.= number_format($oReporteTotal->CostoTotal, 2);
		$csv.= $SaltoLinea;*/
				
		$csv.= "Nro. Interno";
		$csv.= $Separador;
		$csv.= "Denominacion";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Año";		
		$csv.= $SaltoLinea;
	
		foreach ($arrUsados as $oUsado)
		{				
			$oColor	= $oColores->GetById($oUsado->IdColor);
			$oUbicacion = $oUbicaciones->GetById($oUsado->IdUbicacion);
			
			
			$csv.= str_replace('(\t|\n)','', trim($oUsado->IdUsado));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsado->Modelo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUbicacion->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsado->ModeloAnio));	
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}	
	
	public function ExportReporteVendidasCsv(array $filter = NULL)
	{
		$oColores 				= new Colores();
		$oUbicaciones			= new Ubicaciones();
		$oMinutas				= new MinutasUsados();
		$oClientes				= new Clientes();
		$oCuentasGestoria		= new CuentasGestoriaUsados();
		$oPagos					= new Pagos();
		$oMinutasFinanciacion	= new MinutasUsadosFinanciacion();
		$oPedidosAccesorios		= new PedidosAccesorios();
		$oUsados				= new Usados();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "usados_reporte_vendidas.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrUsados = $this->GetAllReporteVendidos($filter);
		//$oReporteTotal 	= $this->GetTotalReporteVendidos($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		/*$csv.= "Datos Totales";
		$csv.= $SaltoLinea;
		$csv.= "Cantidad Unidades en Stock";
		$csv.= $Separador;
		$csv.= $oReporteTotal->CantidadTotal;
		$csv.= $SaltoLinea;
		$csv.= "Valuación Stock";
		$csv.= $Separador;
		$csv.= number_format($oReporteTotal->CostoTotal, 2);
		$csv.= $SaltoLinea;*/
				
		$csv.= "Fecha";
		$csv.= $Separador;
		$csv.= "Nro. Interno";
		$csv.= $Separador;
		$csv.= "Denominacion";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Precio Venta";
		$csv.= $Separador;
		$csv.= "Costo";	
		$csv.= $Separador;
		$csv.= "Patentamiento";	
		$csv.= $Separador;
		$csv.= "Accesorios";	
		$csv.= $Separador;
		$csv.= "Gastos";		
		$csv.= $Separador;
		$csv.= "Tarshop";		
		$csv.= $Separador;
		$csv.= "Credilogros";		
		$csv.= $Separador;
		$csv.= "Confina";		
		$csv.= $Separador;
		$csv.= "Tarjeta";		
		$csv.= $Separador;
		$csv.= "Debido";		
		$csv.= $Separador;
		$csv.= "Usada";		
		$csv.= $Separador;
		$csv.= "Transferencia";		
		$csv.= $Separador;
		$csv.= "Deposito";			
		$csv.= $Separador;
		$csv.= "Pagare";			
		$csv.= $Separador;
		$csv.= "Cheque";			
		$csv.= $Separador;
		$csv.= "Efectivo";		
		$csv.= $Separador;
		$csv.= "MP";		
		$csv.= $Separador;
		$csv.= "Ganancia";		
		$csv.= $SaltoLinea;
	
		foreach ($arrUsados as $oUsado)
		{				
			$oMinuta	= $oMinutas->GetById($oUsado->IdUsado);
			$oCliente	= $oClientes->GetById($oMinuta->IdCliente);
			$oCuentaGestoria	= $oCuentasGestoria->GetByIdMinuta($oMinuta->IdMinuta);
			$PrecioUsado = 0;
			$Efectivo = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Efectivo);
			$Transferencia = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Transferencia);
			$Pagare = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Pagare);
			$Tarshop = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Tarshop);
			$Confina = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Confina);
			$Credilogros = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
			$Credilogros = $oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
			$AM = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Credito);
			$Visa = 0; //$oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::Visa);
			$MC = 0; //$oPagos->GetByIdMinutaUsadoIdAcreedor($oMinuta->IdMinuta, Acreedor::MC);
			$DepositoEfectivo = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoEfectivo);
			$DepositoCheque = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoCheque);
			$Debito = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Debito);
			$Cheque = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::Cheque);
			$MP = $oPagos->GetTotalIdMinutaUsadoIdTipoPago($oMinuta->IdMinuta, TipoPago::MercadoPago);
							
			$PrecioVentaTotal = $oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento;
							
			$Tarjeta = $AM + $Visa + $MC;
			$Deposito = $DepositoEfectivo + $DepositoCheque;
							
			$oPedidoAccesorio = $oPedidosAccesorios->GetByIdMinutaUsado($oMinuta->IdMinuta);
			$CostoAccesorios = 0;
			if ($oPedidoAccesorio)
				$CostoAccesorios = $oPedidoAccesorio->GetCosto();
							
			if ($oMinuta->EntregaUsado) 
			{
				$arrUsados = $oUsados->GetAllByIdMinuta($oMinuta->IdMinuta);
								
				$oUsadoTomado = $arrUsados[0];
				if (count($arrUsados) > 1)
				{
					$oUsado2 = $arrUsados[1];
					$PrecioUsado+= $oUsado2->Valuacion;
				}
								
				$PrecioUsado+= $oUsadoTomado->Valuacion;
			}
			
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oMinuta->FechaMinuta)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsado->IdUsado));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsado->Modelo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioVentaTotal, 2, ',', '.')));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oUsado->Valuacion, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oCuentaGestoria->PatentamientoFinal, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($CostoAccesorios, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim('0.00'));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Tarshop, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Credilogros, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Confina, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Tarjeta, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Debito, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioUsado, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Transferencia, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Deposito, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Pagare, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Cheque, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($Efectivo, 2, ',', '.')));		
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($MP, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioVentaTotal - $oUsado->Valuacion - $oCuentaGestoria->PatentamientoFinal - $CostoAccesorios, 2, ',', '.')));	
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	

	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oMarcas 		= new Marcas();
		$oColores 		= new Colores();
		$oEstadosUnidad = new EstadosUnidad();
		$oUbicaciones 	= new Ubicaciones();
		
		/* obtenemos el listado de datos a exportar */			
		$arrUsados = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. INTERNO",
			"NRO. VIN",
			"MARCA",
			"MODELO",
			"NUMERO CHASIS",
			"NUMERO MOTOR",
			"ANIO",
			"DOMINIO",
			"COLOR",
			"KILOMETRAJE",
			"VALUACION",
			"ESTADO",
			"UBICACION"
		);
				
		foreach ($arrUsados as $oUsado)
		{
			$oMarca = $oMarcas->GetById($oUsado->IdMarca);
			$oColor = $oColores->GetById($oUsado->IdColor);
			$oEstado = $oEstadosUnidad->GetById($oUsado->IdEstado);
			$oUbicacion = $oUbicaciones->GetById($oUsado->IdUbicacion);
			
			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oUsado->IdUsado),
				trim($oUsado->NumeroVin),
				trim($oMarca->Nombre),
				trim($oUsado->Modelo),
				trim($oUsado->NumeroChasis),
				trim($oUsado->NumeroMotor),
				trim($oUsado->ModeloAnio),
				trim($oUsado->Dominio),
				trim($oColor->Nombre),
				trim($oUsado->Kilometraje . ' Km.'),
				trim('$ ' . $oUsado->Valuacion),
				trim($oEstado->Nombre),
				trim($oUbicacion->Nombre)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'usados';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	private function EstadoImportador($Estado)
	{
		switch($Estado)
		{
			case 's': return EstadoUnidad::Stock;
			default: return EstadoUnidad::Stock;
		}
	}
	
	public function Import($FileName)
	{
		/* declaramos variables necesarias */
		$oClientes			= new Clientes();
		$oColores			= new Colores();
		$oMarcas			= new Marcas();
		$oUbicaciones		= new Ubicaciones();
		
		/* processamos el archivo */		 
		$arrData = new Spreadsheet_Excel_Reader(Unidad::PathCsvImportBack . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		try
		{
		$strError = '';
		for ($i=2; $i<=$arrData->sheets[0]['numRows']; $i++) 
		{		
			$Usado = $arrData->sheets[0]['cells'][$i];
			
			$err						= 0;			
			$IdEstado					= $this->EstadoImportador($Estado);
			$Marca		 				= trim($Usado[1]);
			$Modelo		 				= trim($Usado[2]);
			$Anio						= trim($Usado[3]);
			$Kilometraje				= trim($Usado[4]);
			$Dominio					= trim($Usado[5]);
			$Color			 			= trim($Usado[6]);
			$Precio			 			= trim($Usado[7]);
			$FechaIngreso	 			= trim($Usado[8]);
			$Info			 			= trim($Usado[9]);
			$Valuacion		 			= trim($Usado[10]);
			$Ubicacion		 			= trim($Usado[11]);
			$Cliente		 			= trim($Usado[12]);
			$IdColor 					= '';
			$IdUbicacion 				= '';
			$IdCliente	 				= '';
			$IdMarca	 				= '';
			$oUsado = $this->GetByDominio(str_replace(' ', '',$Dominio));
			if ($oUsado){
			$oUsado->PrecioVenta = $Precio;
			$this->Update($oUsado);}
			/*
			if (!($Modelo == ''))
			{
				if ($Color != '')
				{
					$oColor = $oColores->GetByNombre(strtoupper($Color));
					if ($oColor)
						$IdColor = $oColor->IdColor;
				}	
				
				if ($Ubicacion != '')
				{
					$oUbicacion = $oUbicaciones->GetById($Ubicacion);
					if ($oUbicacion)
						$IdUbicacion = $oUbicacion->IdUbicacion;
					else
						$IdUbicacion = 7;
				}	
				
				if ($Cliente != '')
				{
					$oCliente = new Cliente();
					$oCliente->IdTipoPersona = PersonaTipos::PersonaFisica;
					$oCliente->RazonSocial = $Cliente;
					$oCliente->IdTipoIva = TipoIva::CF;
					
					$oCliente = $oClientes->Create($oCliente);
				}
				
				if ($Marca != '')
				{
					$oMarca = $oMarcas->GetById($Marca);
					if ($oMarca)
						$IdMarca = $oMarca->IdMarca;
					else
						$IdMarca = 40;
				}	
							
				if ($err == 0)
				{
					$oUsado = new Usado();
					$oUsado->IdMarca 				= $IdMarca;
					$oUsado->IdColor 				= $IdColor;
					$oUsado->Modelo	 				= $Modelo;
					$oUsado->ModeloAnio	 			= $Anio;
					$oUsado->Kilometraje	 		= $Kilometraje;
					$oUsado->Valuacion		 		= $Valuacion;
					$oUsado->Dominio		 		= str_replace(' ', '', $Dominio);
					$oUsado->IdEstado		 		= $IdEstado;
					$oUsado->IdUbicacion		 	= $IdUbicacion;
					$oUsado->IdCliente			 	= $oCliente->IdCliente;
					$oUsado->PrecioVenta		 	= $Precio;
					$oUsado->Info				 	= $Info;
								
					if ($this->Create($oUsado))
						$Edit++;
				}
				else
				{
					if ($err & 1)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el n&uacute;mero de prefijo de Vin es inv&aacute;lido. <br>";
					if ($err & 2)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de Vin es incorrecto. <br>";
					if ($err & 4)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de motor es incorrecto. <br>";
					if ($err & 8)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el color es inv&aacute;lido. <br>";
					if ($err & 16)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el a&ntilde;o es incorrecto. <br>";				
				}					
				
				$Row++;
			}*/
		}
		}
		catch(Exception $e)
		{
		}
		if ($strError != '')
		{
			DBAccess::$db->Rollback();
		}
		else
		{
			DBAccess::$db->Commit();
		}
		
		if ($Creados)
		{
			$strError.= "<br> Se crearon " . $Edit . " unidades.";		
		}		
		
		return $strError;
	}
}

?>