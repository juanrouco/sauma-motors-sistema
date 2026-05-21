<?php 

require_once('class.dbaccess.php');
require_once('class.minuta.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.clientes.php');
require_once('class.ubicacion.php');
require_once('class.usuarios.php');
require_once('class.comisiones.php');
require_once('class.localidades.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.ordenessalida.php');
require_once('excel_export/class.xlsexport.php');
require_once('excel_export/class.xlsexport.php');
require_once('class.pedidosaccesorios.php');
require_once('class.unidadesarreglos.php');
require_once('class.facturaunidades.php');


class Minutas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND v.IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND v.IdUnidad = " . DB::Number($filter['IdUnidad']);

		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND v.IdCliente = " . DB::Number($filter['IdCliente']);

		if ((isset($filter['IdOrigenCliente'])) && ($filter['IdOrigenCliente'] != ''))
			$sql.= " AND v.IdOrigenCliente = " . DB::Number($filter['IdOrigenCliente']);

		if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
			$sql.= " AND v.IdUsuario = " . DB::Number($filter['IdUsuario']);

		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND u.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
			
		if ((isset($filter['NumeroPedido'])) && ($filter['NumeroPedido'] != ''))
			$sql.= " AND u.NumeroPedido = " . DB::String($filter['NumeroPedido']);

		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND v.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND v.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);

		if ((isset($filter['FechaFacturaDesde'])) && ($filter['FechaFacturaDesde'] != ''))
			$sql.= " AND f.Fecha >= " . DB::Date($filter['FechaFacturaDesde']);

		if ((isset($filter['FechaFacturaHasta'])) && ($filter['FechaFacturaHasta'] != ''))
			$sql.= " AND f.Fecha <= " . DB::Date($filter['FechaFacturaHasta']);

		if ((isset($filter['FechaRetiroDesde'])) && ($filter['FechaRetiroDesde'] != ''))
			$sql.= " AND v.FechaRetiro >= " . DB::Date($filter['FechaRetiroDesde']);

		if ((isset($filter['FechaRetiroHasta'])) && ($filter['FechaRetiroHasta'] != ''))
			$sql.= " AND v.FechaRetiro <= " . DB::Date($filter['FechaRetiroHasta']);

		if ((isset($filter['FechaVencimientoDesde'])) && ($filter['FechaVencimientoDesde'] != ''))
			$sql.= " AND v.FechaVencimiento >= " . DB::Date($filter['FechaVencimientoDesde']);

		if ((isset($filter['FechaVencimientoHasta'])) && ($filter['FechaVencimientoHasta'] != ''))
			$sql.= " AND v.FechaVencimiento <= " . DB::Date($filter['FechaVencimientoHasta']);

		if ((isset($filter['Entregado'])) && ($filter['Entregado'] != ''))
			$sql.= " AND u.IdUbicacion = " . DB::Number(Ubicacion::Entregado);
		
		if ((isset($filter['Facturado'])) && ($filter['Facturado'] != ''))
			$sql.= " AND u.IdUnidad IN (SELECT IdMinuta FROM TB_FacturaUnidades)";

		if ((isset($filter['FechaRetiroHasta'])) && ($filter['FechaRetiroHasta'] != ''))
			$sql.= " AND u.FechaRetiro <= " . DB::Date($filter['FechaRetiroHasta']);
			
		if ((isset($filter['NotPedidoMayorista'])) && ($filter['NotPedidoMayorista'] != ''))
			$sql.= " AND v.IdMinuta NOT IN (SELECT IdMinuta FROM TB_PedidosMayoristaDetalles)";

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
		{
			$sql.= " AND (";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= " OR";
			$sql.= " c2.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= ")";
		}
		
		if ((isset($filter['Reventa'])) && ($filter['Reventa'] != ''))
		{
			$sql.= " AND IdClienteReventa IN(SELECT IdCliente FROM TB_Clientes WHERE RazonSocial LIKE '%" . DB::StringUnquoted($filter['Reventa']) . "%')";
			
		}
		
		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
		{
			$sql.= " AND u.IdModelo IN(SELECT IdModelo FROM TB_Modelos WHERE DenominacionComercial LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%')";
			
		}

		if ((isset($filter['Usuario'])) && ($filter['Usuario'] != ''))
		{
			$sql.= " AND ";
			$sql.= " ( ";
			$sql.= " 	us.Nombre LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " 	OR ";
			$sql.= " 	us.Apellido LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " 	OR ";
			$sql.= " 	CONCAT(us.Nombre, ' ', us.Apellido) LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " ) ";
		}
		
		if ((isset($filter['ReportadoSeguros'])) && ($filter['ReportadoSeguros'] != ''))
			$sql.= " AND v.ReportadoSeguros = 0 AND (v.IdClienteReventa IS NULL OR v.IdClienteReventa = 0) AND (v.FinanciacionCapital = 0 OR v.FinanciacionCapital IS NULL)";
		elseif ((isset($filter['Reportado'])) && ($filter['Reportado'] != ''))
			$sql.= " AND v.Reportado = 0 AND (v.IdClienteReventa IS NULL OR v.IdClienteReventa = 0)";
		
		if ((isset($filter['Facturado'])) && ($filter['Facturado'] != ''))
			$sql.= " AND v.IdMinuta IN (SELECT IdMinuta FROM TB_FacturaUnidades)";

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinuta";
		$sql.= " ORDER BY v.FechaMinuta DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllPatentables(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		//$sql.= " LEFT JOIN TB_FacturaUnidades fu ON fu.IdMinuta = v.IdMinuta";
		$sql.= " WHERE 1=1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		//$sql.= " AND v.GastosPatentamiento > 0";
		//$sql.= " AND (fu.IdFactura IS NOT NULL OR u.VentaEspecial OR u.Plan)";
		$sql.= " AND v.IdMinuta NOT IN (SELECT IdMinuta FROM TB_CuentasGestoria)";
		$sql.= " GROUP BY v.IdMinuta";
		$sql.= " ORDER BY v.FechaMinuta DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllConSaldo($filter = NULL)
	{
		$sql = "SELECT mi.*, aauu.Saldo";
		$sql.= " FROM TB_Minutas mi";
		$sql.= " INNER JOIN (";
		$sql.= " SELECT IdMinuta, PrecioAuto - ValorUsado - FinanciacionCapital - ImportePagos AS Saldo";
		$sql.= " FROM (";
		$sql.= " SELECT IF (m.PrecioVenta IS NULL, 0, m.PrecioVenta) + m.GastosOtorgamiento + m.GastosPatentamiento + m.Interes AS PrecioAuto,";
		$sql.= " IF (u.Valuacion IS NULL, 0, u.Valuacion) AS ValorUsado,";
		$sql.= " IF (cp.MontoOtorgado IS NULL, 0, cp.MontoOtorgado) AS FinanciacionCapital,";
		$sql.= " IF (pg.Importe IS NULL, 0, pg.Importe) AS ImportePagos,";
		$sql.= " m.IdMinuta";
		$sql.= " FROM TB_Minutas m";
		$sql.= " INNER JOIN TB_Unidades un ON m.IdUnidad = un.IdUnidad and un.IdEstado <> 1";
		
		//if ($filter['UsadoNoRecibido'])
			$sql.= " LEFT JOIN TB_Usados u ON m.IdMinuta = u.IdMinuta";
		/*else
			$sql.= " LEFT JOIN TB_Usados u ON m.IdUsado = u.IdUsado AND u.IdUbicacion <> " . DB::Number(Ubicacion::Transito);*/
		
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT pa.IdMinuta, SUM(pai.Importe) AS Importe";
		$sql.= " 	FROM TB_PedidosAccesorios pa";
		$sql.= " 	INNER JOIN TB_PedidosAccesoriosItems pai ON pa.IdPedido= pai.IdPedidoAccesorio";
		$sql.= " 	GROUP BY pa.IdPedido LIMIT 1";
		$sql.= " ) paa ON m.IdMinuta = paa.IdMinuta";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinuta, SUM(Importe) AS Importe";
		$sql.= " 	FROM TB_Pagos";
		$sql.= " 	GROUP BY IdMinuta";
		$sql.= " ) pg ON m.IdMinuta = pg.IdMinuta";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinuta, MontoOtorgado AS MontoOtorgado";
		$sql.= " 	FROM TB_ContratosPrendas";
		$sql.= " 	GROUP BY IdMinuta";
		$sql.= " ) cp ON m.IdMinuta = cp.IdMinuta";
		$sql.= " WHERE m.FechaMinuta > '2010-10-21'";
		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND m.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND m.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);

		if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
			$sql.= " AND m.IdUsuario = " . DB::Number($filter['IdUsuario']);

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND m.IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		$sql.= " ) TB_Aux";
		$sql.= " ) aauu ON aauu.IdMinuta = mi.IdMinuta";
		$sql.= " WHERE aauu.Saldo > 0";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetSaldoTotal($filter = NULL)
	{
		$sql = "SELECT COUNT(mi.IdMinuta) AS Cantidad, SUM(aauu.Saldo) AS Saldo, SUM(ValorUsado) AS ValorUsado, SUM(ImportePagos) AS ImportePagos";
		$sql.= " FROM TB_Minutas mi";
		$sql.= " INNER JOIN (";
		$sql.= " SELECT IdMinuta, PrecioAuto - ValorUsado - ImportePagos AS Saldo, ValorUsado, ImportePagos";
		$sql.= " FROM (";
		$sql.= " SELECT IF (m.PrecioVenta IS NULL, 0, m.PrecioVenta) + m.GastosPatentamiento + m.GastosPrenda + m.GastosOtorgamiento + m.Interes + m.GastosFlete + IF (m.RentasFinal = 0, m.Rentas, m.RentasFinal) + m.Alta - m.DepositoGarantia AS PrecioAuto,";
		$sql.= " IF (u.Valuacion IS NULL, 0, u.Valuacion) AS ValorUsado,";
		$sql.= " IF (pg.Importe IS NULL, 0, pg.Importe) AS ImportePagos,";
		$sql.= " m.IdMinuta";
		$sql.= " FROM TB_Minutas m";
		$sql.= " LEFT JOIN (SELECT SUM(Valuacion) AS Valuacion, IdMinuta FROM TB_Usados WHERE IdMinuta IS NOT NULL AND IdUbicacion <> " . DB::Number(Ubicacion::Transito) . " GROUP BY IdMinuta) AS u ON m.IdMinuta = u.IdMinuta";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinuta, SUM(Importe) AS Importe";
		$sql.= " 	FROM TB_Pagos";
		$sql.= " 	WHERE IdMinuta IS NOT NULL";
		$sql.= " 	GROUP BY IdMinuta";
		$sql.= " ) pg ON m.IdMinuta = pg.IdMinuta";
		$sql.= " WHERE m.FechaMinuta > '2010-10-21'";
		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND m.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND m.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);
		$sql.= " ) TB_Aux";
		$sql.= " ) aauu ON aauu.IdMinuta = mi.IdMinuta";
		$sql.= " WHERE aauu.Saldo > 0";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!$oRow = $oRes->GetRow())	
			return false;
		
		$oReporte = new stdClass();
		$oReporte->Cantidad = $oRow['Cantidad'];
		$oReporte->Saldo = $oRow['Saldo'];
		$oReporte->ValorUsado = $oRow['ValorUsado'];
		$oReporte->ImportePagos = $oRow['ImportePagos'];
		return $oReporte;
	}
	
	
	
	public function GetUsadosAIngresar($filter = NULL)
	{
		$sql = "SELECT COUNT(u.IdUsado) AS Cantidad, SUM(Valuacion) as Valuacion";
		$sql.= " FROM TB_Minutas mi";
		$sql.= " INNER JOIN TB_Usados u ON u.IdMinuta = mi.IdMinuta";
		$sql.= " WHERE mi.FechaMinuta > '2010-10-21'";
		$sql.= " AND u.IdUbicacion = " . DB::Number(Ubicacion::Transito);
		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND mi.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND mi.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		if (!$oRow = $oRes->GetRow())	
			return false;
		
		$oReporte = new stdClass();
		$oReporte->Cantidad = $oRow['Cantidad'];
		$oReporte->Valuacion = $oRow['Valuacion'];
		return $oReporte;
	}

	public function GetAllForComisiones(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		//$sql.= " INNER JOIN TB_FacturaUnidades f ON f.IdMinuta = v.IdMinuta";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario and us.IdUsuario <> 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinuta";
		$sql.= " ORDER BY us.Nombre, us.Apellido, v.FechaMinuta ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetCountRowsForComisiones(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		//$sql.= " INNER JOIN TB_FacturaUnidades f ON f.IdMinuta = v.IdMinuta";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario and us.IdUsuario <> 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinuta";
		$sql.= " ORDER BY us.Nombre, us.Apellido, v.FechaMinuta ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	public function GetAllByUsuario(Usuario $oUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Minutas";
		$sql.= " WHERE IdUsuario = " . DB::Number($oUsuario->IdUsuario);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Minutas";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new Minuta();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Minutas";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinuta = new Minuta();
		$oMinuta->ParseFromArray($oRow);
		
		return $oMinuta;		
	}
	
	public function GetByIdUsado($IdUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Minutas";
		$sql.= " WHERE IdUsado = " . DB::Number($IdUsado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinuta = new Minuta();
		$oMinuta->ParseFromArray($oRow);
		
		return $oMinuta;		
	}
	

	public function GetByUnidad(Unidad $oUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Minutas";
		$sql.= " WHERE IdUnidad = " . DB::Number($oUnidad->IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinuta = new Minuta();
		$oMinuta->ParseFromArray($oRow);
		
		return $oMinuta;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " LEFT JOIN TB_FacturaUnidades f ON v.IdMinuta = f.IdMinuta";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinuta";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetCountRowsPatentables(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Minutas v";
		$sql.= " INNER JOIN TB_Unidades u ON v.IdUnidad = u.IdUnidad";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		
		$sql.= " LEFT JOIN TB_FacturaUnidades fu ON fu.IdMinuta = v.IdMinuta";
		$sql.= " WHERE 1=1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		//$sql.= " AND v.GastosPatentamiento > 0";
		$sql.= " AND v.IdMinuta NOT IN (SELECT IdMinuta FROM TB_CuentasGestoria)";
		$sql.= " GROUP BY v.IdMinuta";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(Minuta $oMinuta)
	{
	
		$arr = array
		(
			'IdMinuta' 				=> DB::Number($oMinuta->IdUnidad),
			'IdUnidad' 				=> DB::Number($oMinuta->IdUnidad),
			'IdUsuario' 			=> DB::Number($oMinuta->IdUsuario),
			'IdCliente' 			=> DB::Number($oMinuta->IdCliente),
			'IdClienteCondominio'	=> DB::Number($oMinuta->IdClienteCondominio) == 0 ? 'NULL' : DB::Number($oMinuta->IdClienteCondominio),
			'FechaMinuta' 			=> DB::Date($oMinuta->FechaMinuta),
			'PrecioVenta' 			=> DB::Number($oMinuta->PrecioVenta),
			'GastosFlete' 			=> DB::Number($oMinuta->GastosFlete),
			'GastosPatentamiento' 	=> DB::Number($oMinuta->GastosPatentamiento),
			'GastosOtorgamiento' 	=> DB::Number($oMinuta->GastosOtorgamiento),
			'GastosPrenda' 			=> DB::Number($oMinuta->GastosPrenda),
			'Circular' 				=> DB::Number($oMinuta->Circular),
			'Anticipo' 				=> DB::Number($oMinuta->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oMinuta->FinanciacionCapital),
			'Condominio' 			=> DB::Bool($oMinuta->Condominio),
			'EntregaUsado' 			=> DB::Bool($oMinuta->EntregaUsado),
			'IdUsado' 				=> DB::Number($oMinuta->IdUsado),
			'DepositoGarantia'		=> DB::Number($oMinuta->DepositoGarantia),
			'PlazoPrenda'			=> DB::Number($oMinuta->PlazoPrenda),
			'Rentas'				=> DB::Number($oMinuta->Rentas),
			'Reportado'				=> DB::Bool($oMinuta->Reportado),
			'ReportadoSeguros'		=> DB::Bool($oMinuta->ReportadoSeguros),
			'IdClienteReventa'		=> DB::Number($oMinuta->IdClienteReventa),
			'Alta'					=> DB::Number($oMinuta->Alta ? $oMinuta->Alta : '0'),
			'RentasFinal'			=> DB::Number($oMinuta->RentasFinal ? $oMinuta->RentasFinal : '0'),
			'IdAcreedor'			=> DB::Number($oMinuta->IdAcreedor),
			'Observaciones'			=> DB::String($oMinuta->Observaciones),
			'FechaVencimiento'		=> DB::Date($oMinuta->FechaVencimiento),
			'FechaRetiro'			=> DB::Date($oMinuta->FechaRetiro),
			'SeguroCompania'		=> DB::String($oMinuta->SeguroCompania),
			'SeguroCobertura'		=> DB::String($oMinuta->SeguroCobertura),
			'SeguroValor'			=> DB::Number($oMinuta->SeguroValor),
			'SeguroIdTipoPago'		=> DB::Number($oMinuta->SeguroIdTipoPago),
			'CedulaAzul'			=> DB::Bool($oMinuta->CedulaAzul),
			'IdOrigenCliente'		=> DB::Number($oMinuta->IdOrigenCliente),
			'NumeroGarantia'		=> DB::String($oMinuta->NumeroGarantia),
			'Interes'				=> DB::Number($oMinuta->Interes)
		);
		
		if (!$this->Insert('TB_Minutas', $arr))
			return false;

		/* asignamos el id generado */
		$oMinuta->IdMinuta = DBAccess::GetLastInsertId();
			
		return $oMinuta;
	}
	
	
	public function Update(Minuta $oMinuta)
	{
		$where = " IdMinuta = " . DB::Number($oMinuta->IdMinuta);
		
		$arr = array
		(
			'IdUsuario' 			=> DB::Number($oMinuta->IdUsuario),
			'IdCliente' 			=> DB::Number($oMinuta->IdCliente),
			'IdClienteCondominio'	=> DB::Number($oMinuta->IdClienteCondominio) == 0 ? 'NULL' : DB::Number($oMinuta->IdClienteCondominio),
			'FechaMinuta' 			=> DB::Date($oMinuta->FechaMinuta),
			'PrecioVenta' 			=> DB::Number($oMinuta->PrecioVenta),
			'GastosFlete' 			=> DB::Number($oMinuta->GastosFlete),
			'GastosPatentamiento' 	=> DB::Number($oMinuta->GastosPatentamiento),
			'GastosOtorgamiento' 	=> DB::Number($oMinuta->GastosOtorgamiento),
			'GastosPrenda' 			=> DB::Number($oMinuta->GastosPrenda),
			'Circular' 				=> DB::Number($oMinuta->Circular),
			'Anticipo' 				=> DB::Number($oMinuta->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oMinuta->FinanciacionCapital),
			'Condominio' 			=> DB::Bool($oMinuta->Condominio),
			'EntregaUsado' 			=> DB::Bool($oMinuta->EntregaUsado),
			'IdUsado' 				=> DB::Number($oMinuta->IdUsado),
			'DepositoGarantia'		=> DB::Number($oMinuta->DepositoGarantia),
			'PlazoPrenda'			=> DB::Number($oMinuta->PlazoPrenda),
			'Rentas'				=> DB::Number($oMinuta->Rentas),
			'Reportado'				=> DB::Bool($oMinuta->Reportado),
			'ReportadoSeguros'		=> DB::Bool($oMinuta->ReportadoSeguros),
			'IdClienteReventa'		=> DB::Number($oMinuta->IdClienteReventa),
			'Alta'					=> DB::Number($oMinuta->Alta ? $oMinuta->Alta : '0'),
			'RentasFinal'			=> DB::Number($oMinuta->RentasFinal ? $oMinuta->RentasFinal : '0'),
			'IdAcreedor'			=> DB::Number($oMinuta->IdAcreedor),
			'Observaciones'			=> DB::String($oMinuta->Observaciones),
			'FechaVencimiento'		=> DB::Date($oMinuta->FechaVencimiento),
			'FechaRetiro'			=> DB::Date($oMinuta->FechaRetiro),
			'SeguroCompania'		=> DB::String($oMinuta->SeguroCompania),
			'SeguroCobertura'		=> DB::String($oMinuta->SeguroCobertura),
			'SeguroValor'			=> DB::Number($oMinuta->SeguroValor),
			'SeguroIdTipoPago'		=> DB::Number($oMinuta->SeguroIdTipoPago),
			'CedulaAzul'			=> DB::Bool($oMinuta->CedulaAzul),
			'IdOrigenCliente'		=> DB::Number($oMinuta->IdOrigenCliente),
			'NumeroGarantia'		=> DB::String($oMinuta->NumeroGarantia),
			'Interes'				=> DB::Number($oMinuta->Interes)
		);
		
		if (!DBAccess::Update('TB_Minutas', $arr, $where))
			return false;
		
		return $oMinuta;
	}
	

	public function Delete($IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$oPedidosAccesorios = new PedidosAccesorios();
		$oPedidoAccesorio = $oPedidosAccesorios->GetByIdMinuta($IdMinuta);
		$oPedidosAccesorios->Delete($oPedidoAccesorio->IdPedido);
			
		$where = " IdMinuta = " . DB::Number($IdMinuta);

		if (!DBAccess::Delete('TB_Minutas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	
	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUnidades 	= new Unidades();
		$oModelos 	= new Modelos();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();
		$oLocalidades 	= new Localidades();
		$oUnidadesArreglos= new UnidadesArreglos();

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. CARPETA",
			"FECHA",
			"NRO. INTERNO",
			"NRO. VIN",
			"NRO. MOTOR",
			"MODELO",
			"CLIENTE",
			"DNI",
			"FECHA NACIMIENTO",
			"DIRECCION",
			"LOCALIDAD",
			"COD. POSTAL",
			"TELEFONO",
			"EMAIL",
			"VENDEDOR",
			"PRECIO DE VENTA",
			"INTERES",
			"COSTO",
			"UTILIDAD BRUTA"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo 	= $oModelos->GetById($oUnidad->IdModelo);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
			$arrUnidadesArreglos = $oUnidadesArreglos->GetAllByUnidad($oUnidad);
			
			$TotalArreglos = 0;
			foreach ($arrUnidadesArreglos as $oUnidadArreglo)
			{
				$TotalArreglos+= $oUnidadArreglo->Importe;
			}
			
			$Condominio = ($oMinuta->Condominio) ? "SI" : "NO";
			$Rentabilidad = $oMinuta->PrecioVenta + $oMinuta->GastosFlete + $oMinuta->Circular - $oUnidad->ImporteCompraBruto  - $TotalArreglos;

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinuta),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oUnidad->IdUnidad),
				trim($oUnidad->NumeroVin),
				trim($oUnidad->NumeroMotor),
				trim($oModelo->DenominacionComercial),
				trim($oCliente->RazonSocial),
				trim($oCliente->ObtenerNumeroDocumentoAfip()),
				trim(CambiarFecha($oCliente->FechaNacimiento)),
				trim($oCliente->GetDomicilio()),
				trim($oLocalidad->Nombre),
				trim($oCliente->DomicilioCodigoPostal),
				trim($oCliente->TelefonoCodigoArea . ' ' . $oCliente->Telefono),
				trim($oCliente->Email),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento + $oMinuta->Interes),
				trim($oMinuta->Interes),
				trim($oUnidad->ImporteCompraBruto + $TotalArreglos),
				trim($Rentabilidad)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'ventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ExportXlsConSaldo(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUnidades 	= new Unidades();
		$oModelos 	= new Modelos();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAllConSaldo($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. CARPETA",
			"FECHA",
			"NRO. INTERNO",
			"NRO. VIN",
			"MODELO",
			"CLIENTE",
			"VENDEDOR",
			"PRECIO DE VENTA",
			"SALDO"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo 	= $oModelos->GetById($oUnidad->IdModelo);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			
			$Condominio = ($oMinuta->Condominio) ? "SI" : "NO";
			$Rentabilidad = $oMinuta->PrecioVenta + $oMinuta->GastosFlete + $oMinuta->Circular + $oMinuta->Interes - $oUnidad->ImporteCompraBruto;

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinuta),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oUnidad->IdUnidad),
				trim($oUnidad->NumeroVin),
				trim($oModelo->DenominacionComercial),
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinuta->PrecioVenta),
				trim($oMinuta->Saldo)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'ventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ExportComisionesXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUnidades 	= new Unidades();
		$oModelos 	= new Modelos();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();
		$oComisiones = new Comisiones();
		$oFacturaUnidades = new FacturaUnidades();

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAllForComisiones($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. INTERNO",
			"FECHA",
			"MODELO",
			"CLIENTE",
			"VENDEDOR",
			"PRECIO DE VENTA",
			"INDICE COMISION",
			"COMISION"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo 	= $oModelos->GetById($oUnidad->IdModelo);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oComision 	= $oComisiones->GetByIdMinuta($oMinuta->IdMinuta);
			$oFactura 	= $oFacturaUnidades->GetByIdMinuta($oMinuta->IdMinuta);
			$PrecioVentaTotal = $oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento;
			
			$IndiceComision = 0.5;
			if ($oComision)
			{
				$Comision = $PrecioVentaTotal * $oComision->IndiceComision / 100;
				$IndiceComision = $oComision->IndiceComision ;
			}
			else
				$Comision = $PrecioVentaTotal * 0.5 / 100;
			
				
			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinuta),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oModelo->DenominacionComercial),
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($PrecioVentaTotal),
				trim($IndiceComision),
				trim($Comision)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'comisiones';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ExportXlsReporte(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUnidades 	= new Unidades();
		$oModelos 	= new Modelos();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();
		$oOrdenesSalida = new OrdenesSalida();
		
		$filter['Reportado'] = '0';

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"FECHA",
			"MODELO",
			"CLIENTE",
			"TELEFONO",
			"TELEFONO 2",
			"EMAIL",
			"NRO. VIN",
			"FECHA ENTREGA"
		);
		
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUnidad 	= $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo 	= $oModelos->GetById($oUnidad->IdModelo);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oOrdenSalida 	= $oOrdenesSalida->GetByIdUnidad($oMinuta->IdUnidad);
			$oPedidoAccesorios = $oMinuta->GetPedidoAccesorios();
			
			if (!$oPedidoAccesorios || $oCliente->DomicilioIdLocalidad == 85 || $oCliente->DomicilioIdLocalidad == 385 || $oCliente->DomicilioIdLocalidad == 981 || $oCliente->DomicilioIdLocalidad == 117 || $oCliente->DomicilioIdLocalidad == 316 || $oCliente->DomicilioIdLocalidad == 5445 || $oCliente->DomicilioIdLocalidad == 5461 || $oCliente->DomicilioIdLocalidad == 104)
			{

				/* almacenamos el registro */
				$arrData[] = array
				(
					trim(CambiarFecha($oMinuta->FechaMinuta)),
					trim($oModelo->DenominacionComercial),
					trim($oCliente->RazonSocial),
					trim($oCliente->TelefonoCodigoArea . ' ' . $oCliente->Telefono),
					trim($oCliente->FaxCodigoArea . ' ' . $oCliente->Fax),
					trim($oCliente->Email),
					trim($oUnidad->NumeroVin),
					trim(CambiarFecha($oOrdenSalida->Fecha))
				);
				if ((isset($filter['ReportadoSeguros'])) && ($filter['ReportadoSeguros'] != ''))
					$oMinuta->ReportadoSeguros = 1;
				else
					$oMinuta->Reportado = 1;
				$this->Update($oMinuta);
			}
		}	
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'minutas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ImportTxtMigracion($FileName)
	{
		/* declaramos variables necesarias */
		$FechaImportacion = '';
		
		$oUnidades = new Unidades();
		$oClientes = new Clientes();
		
		/* processamos el archivo */		 
		$fp = fopen(Unidad::PathFile . $FileName, 'r');
		//$arrData = new Spreadsheet_Excel_Reader(Unidad::PathCsvImportBack . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		$count == 0;
		try
		{
		$strError = '';
		while ( !feof($fp) )
		{
			
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, '|');
print_r($Cliente);
print_r('<br />');
			
			if ($count != 0)
			{
			
				$err						= 0;			
				$Numero				 		= trim($Cliente[0]);
				$FechaMinuta		 		= trim($Cliente[2]);
				$PrecioVenta		 		= trim($Cliente[3]);
				$PrecioVenta = str_replace(',', '', $PrecioVenta);
				
				
				$ImpBonif			 		= trim($Cliente[4]);
				$ImpBonif = str_replace(',', '', $ImpBonif);
				
				$IdUnidad			 		= trim($Cliente[5]);
				$IdClienteMigracion	 		= trim($Cliente[6]);
				$CodigoVendedor		 		= 1;//trim($Cliente[7]);
				$DepositoGarantia	 		= trim($Cliente[8]);
				$DepositoGarantia = str_replace(',', '', $DepositoGarantia);
				
				$EntregaUsado		 		= trim($Cliente[9]);
				$Circular			 		= trim($Cliente[10]);
				$Circular = str_replace(',', '', $Circular);
				
				$Anticipo			 		= trim($Cliente[11]);
				$Anticipo = str_replace(',', '', $Anticipo);
				
				$FinanciacionCapital 		= trim($Cliente[12]);
				$FinanciacionCapital = str_replace(',', '', $FinanciacionCapital);
				
				$PlazoPrenda			 	= trim($Cliente[13]);
				$Cuota				 		= trim($Cliente[14]);
				$GastosPatentamiento	 	= trim($Cliente[15]);
				$GastosPatentamiento = str_replace(',', '', $GastosPatentamiento);
				
				$GastosPrenda			 	= trim($Cliente[16]);
				$GastosPrenda = str_replace(',', '', $GastosPrenda);
				
				$GastosOtorgamiento		 	= trim($Cliente[17]);
				$GastosOtorgamiento = str_replace(',', '', $GastosOtorgamiento);
				
				$GastosFlete			 	= trim($Cliente[18]);
				$GastosFlete = str_replace(',', '', $GastosFlete);
				
				$oCliente = $oClientes->GetByIdMigracion($IdClienteMigracion);
				$oUnidad = $oUnidades->GetById($IdUnidad);
				
				if ($oCliente && $oUnidad)
				{
					
					if ($err == 0)
					{
						$nuevo = false;
						if (!$oMinuta = $this->GetById($oUnidad->IdUnidad))
						{
							$oMinuta = new Minuta();
							$nuevo = true;
						}
						$oMinuta->IdUnidad = $oUnidad->IdUnidad;
						$oMinuta->IdCliente = $oCliente->IdCliente;
						$oMinuta->FechaMinuta = $FechaMinuta;
						$oMinuta->PrecioVenta = $PrecioVenta;
						$oMinuta->IdUsuario	= $CodigoVendedor;
						$oMinuta->DepositoGarantia	= $DepositoGarantia;
						$oMinuta->EntregaUsado 	= $EntregaUsado;
						$oMinuta->Circular 	= $Circular;
						$oMinuta->Anticipo	= $Anticipo;
						$oMinuta->FinanciacionCapital	= $FinanciacionCapital;
						$oMinuta->PlazoPrenda = $PlazoPrenda;
						$oMinuta->GastosPatentamiento = $GastosPatentamiento;
						$oMinuta->GastosPrenda = $GastosPrenda;
						$oMinuta->GastosOtorgamiento = $GastosOtorgamiento;
						$oMinuta->GastosFlete = $GastosFlete;
							
						if ($nuevo)
						{
							if ($oMinuta = $this->Create($oMinuta))
							{
								$CountCreate++;
								$FechaImportacion = $Fecha;
							}
						}
						else
						{
							if ($oMinuta = $this->Update($oMinuta))
							{
								$CountCreate++;
								$FechaImportacion = $Fecha;
							}
						}
						
					}
					else
					{
						print_r('NO ENCONTRADO<br /><br />');		exit;	
					}					
					
					$Row++;
				}
			}
			$count++;
			}
			$strError.= "Se importaron " . $CountCreate . " unidades.";
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
		
		$res = new stdClass();
		$res->Mensaje = $strError;
		$res->Fecha = $FechaImportacion;
		return $res;
	}
}

?>