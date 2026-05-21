<?php 

require_once('class.dbaccess.php');
require_once('class.minutausado.php');
require_once('class.usados.php');
require_once('class.modelos.php');
require_once('class.clientes.php');
require_once('class.usuarios.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.ordenessalida.php');
require_once('excel_export/class.xlsexport.php');
require_once('excel_export/class.xlsexport.php');
require_once('class.pedidosaccesorios.php');


class MinutasUsados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND v.IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		if ((isset($filter['IdUsado'])) && ($filter['IdUsado'] != ''))
			$sql.= " AND v.IdUsado = " . DB::Number($filter['IdUsado']);

		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND v.IdCliente = " . DB::Number($filter['IdCliente']);

		if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
			$sql.= " AND v.IdUsuario = " . DB::Number($filter['IdUsuario']);

		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND u.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
			
		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND v.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND u.Dominio LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND v.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);

		if ((isset($filter['FechaRetiroDesde'])) && ($filter['FechaRetiroDesde'] != ''))
			$sql.= " AND v.FechaRetiro >= " . DB::Date($filter['FechaRetiroDesde']);

		if ((isset($filter['FechaRetiroHasta'])) && ($filter['FechaRetiroHasta'] != ''))
			$sql.= " AND v.FechaRetiro <= " . DB::Date($filter['FechaRetiroHasta']);

		if ((isset($filter['FechaVencimientoDesde'])) && ($filter['FechaVencimientoDesde'] != ''))
			$sql.= " AND v.FechaVencimiento >= " . DB::Date($filter['FechaVencimientoDesde']);

		if ((isset($filter['FechaVencimientoHasta'])) && ($filter['FechaVencimientoHasta'] != ''))
			$sql.= " AND v.FechaVencimiento <= " . DB::Date($filter['FechaVencimientoHasta']);

		if ((isset($filter['Entregado'])) && ($filter['Entregado'] != ''))
			$sql.= " AND u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);

		if ((isset($filter['FechaRetiroHasta'])) && ($filter['FechaRetiroHasta'] != ''))
			$sql.= " AND u.FechaRetiro <= " . DB::Date($filter['FechaRetiroHasta']);

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
		{
			$sql.= " AND (";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= " OR";
			$sql.= " c2.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= ")";
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
		
		if ((isset($filter['Reportado'])) && ($filter['Reportado'] != ''))
			$sql.= " AND v.Reportado = 0";

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasUsados v";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
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
			$oMinutaUsado = new MinutaUsado();
			$oMinutaUsado->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	
	
	public function GetAllConSaldo($filter = NULL)
	{
		$sql = "SELECT mi.*, aauu.Saldo";
		$sql.= " FROM TB_MinutasUsados mi";
		$sql.= " INNER JOIN (";
		$sql.= " SELECT IdMinuta, PrecioAuto - ValorUsado - FinanciacionCapital + ImporteAccesorios - ImportePagos AS Saldo";
		$sql.= " FROM (";
		$sql.= " SELECT IF (m.PrecioVenta IS NULL, 0, m.PrecioVenta) + m.Gastos + m.GastosPrenda + m.GastosOtorgamiento + m.Anticipo - m.DepositoGarantia AS PrecioAuto,";
		$sql.= " IF (u.Valuacion IS NULL, 0, u.Valuacion) AS ValorUsado,";
		$sql.= " IF (cp.MontoOtorgado IS NULL, 0, cp.MontoOtorgado) AS FinanciacionCapital,";
		$sql.= " IF (pg.Importe IS NULL, 0, pg.Importe) AS ImportePagos,";
		$sql.= " m.IdMinuta";
		$sql.= " FROM TB_MinutasUsados m";
		$sql.= " LEFT JOIN TB_Usados u ON m.IdUsado = u.IdUsado AND u.IdUbicacion <> " . DB::Number(Ubicacion::Transito);
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinutaUsado, SUM(Importe) AS Importe";
		$sql.= " 	FROM TB_Pagos";
		$sql.= " 	GROUP BY IdMinutaUsado";
		$sql.= " ) pg ON m.IdMinuta = pg.IdMinutaUsado";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinuta, MontoOtorgado AS MontoOtorgado";
		$sql.= " 	FROM TB_ContratosPrendasUsados";
		$sql.= " 	GROUP BY IdMinuta";
		$sql.= " ) cp ON m.IdMinuta = cp.IdMinuta";
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
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new MinutaUsado();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	
	
	public function GetSaldoTotal($filter = NULL)
	{
		$sql = "SELECT COUNT(mi.IdUsado) AS Cantidad, SUM(aauu.Saldo) AS Saldo, SUM(ValorUsado) AS ValorUsado, SUM(ImportePagos) AS ImportePagos";
		$sql.= " FROM TB_MinutasUsados mi";
		$sql.= " INNER JOIN (";
		$sql.= " SELECT IdMinuta, PrecioAuto - ValorUsado - FinanciacionCapital - ImportePagos AS Saldo, ValorUsado, ImportePagos";
		$sql.= " FROM (";
		$sql.= " SELECT IF (m.PrecioVenta IS NULL, 0, m.PrecioVenta) + m.Gastos + m.GastosPrenda + m.GastosOtorgamiento + m.Anticipo - m.DepositoGarantia AS PrecioAuto,";
		$sql.= " IF (u.Valuacion IS NULL, 0, u.Valuacion) AS ValorUsado,";
		$sql.= " IF (cp.MontoOtorgado IS NULL, 0, cp.MontoOtorgado) AS FinanciacionCapital,";
		$sql.= " IF (pg.Importe IS NULL, 0, pg.Importe) AS ImportePagos,";
		$sql.= " m.IdMinuta";
		$sql.= " FROM TB_MinutasUsados m";
		$sql.= " LEFT JOIN (SELECT SUM(Valuacion) AS Valuacion, IdMinutaUsado FROM TB_Usados WHERE IdMinutaUsado IS NOT NULL AND IdUbicacion <> " . DB::Number(Ubicacion::Transito) . " GROUP BY IdMinutaUsado) AS u ON m.IdMinuta = u.IdMinutaUsado";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinutaUsado, SUM(Importe) AS Importe";
		$sql.= " 	FROM TB_Pagos";
		$sql.= " 	WHERE IdMinutaUsado IS NOT NULL";
		$sql.= " 	GROUP BY IdMinutaUsado";
		$sql.= " ) pg ON m.IdMinuta = pg.IdMinutaUsado";
		$sql.= " LEFT JOIN (";
		$sql.= " 	SELECT IdMinuta, MontoOtorgado AS MontoOtorgado";
		$sql.= " 	FROM TB_ContratosPrendasUsados";
		$sql.= " 	GROUP BY IdMinuta";
		$sql.= " ) cp ON m.IdMinuta = cp.IdMinuta";
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
		$sql.= " FROM TB_MinutasUsados mi";
		$sql.= " INNER JOIN TB_Usados u ON u.IdMinutaUsado = mi.IdMinuta";
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


	public function GetAllByUsuario(Usuario $oUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsados";
		$sql.= " WHERE IdUsuario = " . DB::Number($oUsuario->IdUsuario);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaUsado = new MinutaUsado();
			$oMinutaUsado->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllForComisiones(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasUsados v";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
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
			$oMinuta = new MinutaUsado();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsados";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaUsado = new MinutaUsado();
			$oMinutaUsado->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsados";
		$sql.= " WHERE IdMinuta = " . DB::Number($IdMinuta);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaUsado = new MinutaUsado();
		$oMinutaUsado->ParseFromArray($oRow);
		
		return $oMinutaUsado;		
	}
	
	public function GetByIdUsadoTomado($IdUsadoTomado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsados";
		$sql.= " WHERE IdUsadoTomado = " . DB::Number($IdUsadoTomado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaUsado = new MinutaUsado();
		$oMinutaUsado->ParseFromArray($oRow);
		
		return $oMinutaUsado;		
	}
	
	
	
	public function GetAllPatentables(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasUsados v";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado AND u.IdEstado <> " . DB::Number(EstadoUnidad::Stock);
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		//$sql.= " LEFT JOIN TB_FacturaUnidades fu ON fu.IdMinuta = v.IdMinuta";
		$sql.= " WHERE 1=1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " AND v.IdMinuta NOT IN (SELECT IdMinuta FROM TB_CuentasGestoriaUsados)";
		$sql.= " GROUP BY v.IdMinuta";
		$sql.= " ORDER BY v.FechaMinuta DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinuta = new MinutaUsado();
			$oMinuta->ParseFromArray($oRow);
			
			array_push($arr, $oMinuta);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetCountRowsPatentables(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasUsados v";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		
		$sql.= " LEFT JOIN TB_FacturaUnidades fu ON fu.IdMinuta = v.IdMinuta";
		$sql.= " WHERE 1=1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " AND v.IdMinuta NOT IN (SELECT IdMinuta FROM TB_CuentasGestoria)";
		$sql.= " GROUP BY v.IdMinuta";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	public function GetByUsado(Usado $oUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasUsados";
		$sql.= " WHERE IdUsado = " . DB::Number($oUsado->IdUsado);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaUsado = new MinutaUsado();
		$oMinutaUsado->ParseFromArray($oRow);
		
		return $oMinutaUsado;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasUsados v";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " LEFT JOIN TB_Clientes c2 ON v.IdClienteCondominio = c2.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinuta";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(MinutaUsado $oMinutaUsado)
	{
	
		$arr = array
		(
			'IdMinuta' 				=> DB::Number($oMinutaUsado->IdUsado),
			'IdUsado' 				=> DB::Number($oMinutaUsado->IdUsado),
			'IdUsuario' 			=> DB::Number($oMinutaUsado->IdUsuario),
			'IdCliente' 			=> DB::Number($oMinutaUsado->IdCliente),
			'IdClienteCondominio'	=> DB::Number($oMinutaUsado->IdClienteCondominio) == 0 ? 'NULL' : DB::Number($oMinutaUsado->IdClienteCondominio),
			'FechaMinuta' 			=> DB::Date($oMinutaUsado->FechaMinuta),
			'PrecioVenta' 			=> DB::Number($oMinutaUsado->PrecioVenta),
			'GastosOtorgamiento' 	=> DB::Number($oMinutaUsado->GastosOtorgamiento),
			'GastosPrenda' 			=> DB::Number($oMinutaUsado->GastosPrenda),
			'Gastos' 				=> DB::Number($oMinutaUsado->Gastos),
			'Anticipo' 				=> DB::Number($oMinutaUsado->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oMinutaUsado->FinanciacionCapital),
			'Condominio' 			=> DB::Bool($oMinutaUsado->Condominio),
			'DepositoGarantia'		=> DB::Number($oMinutaUsado->DepositoGarantia),
			'PlazoPrenda'			=> DB::Number($oMinutaUsado->PlazoPrenda),
			'Reportado'				=> DB::Bool($oMinutaUsado->Reportado),
			'IdClienteReventa'		=> DB::Number($oMinutaUsado->IdClienteReventa),
			'IdUsadoTomado'			=> DB::Number($oMinutaUsado->IdUsadoTomado),
			'EntregaUsado'			=> DB::Bool($oMinutaUsado->EntregaUsado),
			'IdAcreedor'			=> DB::Number($oMinutaUsado->IdAcreedor),
			'Observaciones'			=> DB::String($oMinutaUsado->Observaciones),
			'FechaVencimiento'		=> DB::Date($oMinutaUsado->FechaVencimiento),
			'FechaRetiro'			=> DB::Date($oMinutaUsado->FechaRetiro),
			'CedulaAzul'			=> DB::Bool($oMinutaUsado->CedulaAzul)
		);
		
		if (!$this->Insert('TB_MinutasUsados', $arr))
			return false;

		/* asignamos el id generado */
		$oMinutaUsado->IdMinuta = DBAccess::GetLastInsertId();
			
		return $oMinutaUsado;
	}
	
	
	public function Update(MinutaUsado $oMinutaUsado)
	{
		$where = " IdMinuta = " . DB::Number($oMinutaUsado->IdMinuta);
		
		$arr = array
		(
			'IdUsuario' 			=> DB::Number($oMinutaUsado->IdUsuario),
			'IdCliente' 			=> DB::Number($oMinutaUsado->IdCliente),
			'IdClienteCondominio'	=> DB::Number($oMinutaUsado->IdClienteCondominio) == 0 ? 'NULL' : DB::Number($oMinutaUsado->IdClienteCondominio),
			'FechaMinuta' 			=> DB::Date($oMinutaUsado->FechaMinuta),
			'PrecioVenta' 			=> DB::Number($oMinutaUsado->PrecioVenta),
			'GastosOtorgamiento' 	=> DB::Number($oMinutaUsado->GastosOtorgamiento),
			'GastosPrenda' 			=> DB::Number($oMinutaUsado->GastosPrenda),
			'Gastos' 				=> DB::Number($oMinutaUsado->Gastos),
			'Anticipo' 				=> DB::Number($oMinutaUsado->Anticipo),
			'FinanciacionCapital' 	=> DB::Number($oMinutaUsado->FinanciacionCapital),
			'Condominio' 			=> DB::Bool($oMinutaUsado->Condominio),
			'DepositoGarantia'		=> DB::Number($oMinutaUsado->DepositoGarantia),
			'PlazoPrenda'			=> DB::Number($oMinutaUsado->PlazoPrenda),
			'Reportado'				=> DB::Bool($oMinutaUsado->Reportado),
			'IdClienteReventa'		=> DB::Number($oMinutaUsado->IdClienteReventa),
			'IdUsadoTomado'			=> DB::Number($oMinutaUsado->IdUsadoTomado),
			'EntregaUsado'			=> DB::Bool($oMinutaUsado->EntregaUsado),
			'IdAcreedor'			=> DB::Number($oMinutaUsado->IdAcreedor),
			'Observaciones'			=> DB::String($oMinutaUsado->Observaciones),
			'FechaVencimiento'		=> DB::Date($oMinutaUsado->FechaVencimiento),
			'FechaRetiro'			=> DB::Date($oMinutaUsado->FechaRetiro),
			'CedulaAzul'			=> DB::Bool($oMinutaUsado->CedulaAzul)
		);
		
		if (!DBAccess::Update('TB_MinutasUsados', $arr, $where))
			return false;
		
		return $oMinutaUsado;
	}
	

	public function Delete($IdMinuta)
	{
		if (!DBAccess::$db->Begin())
			return false;
				
		$where = " IdMinuta = " . DB::Number($IdMinuta);

		if (!DBAccess::Delete('TB_MinutasUsados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	
	
	public function ExportXlsConSaldo(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUsados 	= new Usados();
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
			"DOMINIO",
			"MODELO",
			"CLIENTE",
			"VENDEDOR",
			"PRECIO DE VENTA",
			"FLETE",
			"CIRCULAR",
			"ACCESORIOS",
			"COSTO",
			"SALDO"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUsado 	= $oUsados->GetById($oMinuta->IdUsado);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			
			$Condominio = ($oMinuta->Condominio) ? "SI" : "NO";
			$Rentabilidad = $oMinuta->PrecioVenta + $oMinuta->GastosFlete + $oMinuta->Circular - $oUnidad->ImporteCompraBruto;

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinuta),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oUsado->IdUnidad),
				trim($oUsado->NumeroVin),
				trim($oModelo->DenominacionComercial),
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinuta->PrecioVenta),
				trim($oMinuta->GastosFlete),
				trim($oMinuta->Circular),
				trim($oMinuta->GetTotalAccesorios()),
				trim($oUsado->Valuacion),
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
	
	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUsados 	= new Usados();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. CARPETA",
			"FECHA",
			"NRO. INTERNO",
			"MODELO",
			"DOMINIO",
			"CLIENTE",
			"VENDEDOR",
			"PRECIO DE VENTA",
			"COSTO",
			"UTILIDAD BRUTA"
		);
				
		foreach ($arrMinutas as $oMinutaUsado)
		{	
			$oUsado 	= $oUsados->GetById($oMinutaUsado->IdUsado);
			$oCliente 	= $oClientes->GetById($oMinutaUsado->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinutaUsado->IdUsuario);
			
			$Rentabilidad = $oMinutaUsado->PrecioVenta - $oUsado->Valuacion;
			
			$Condominio = ($oMinutaUsado->Condominio) ? "SI" : "NO";

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinutaUsado->IdMinuta),
				trim(CambiarFecha($oMinutaUsado->FechaMinuta)),
				trim($oUsado->IdUsado),
				
				trim($oUsado->Modelo),
				trim($oUsado->Dominio),
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinutaUsado->PrecioVenta),
				trim($oUsado->Valuacion),
				trim($Rentabilidad)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'ventas usados';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	
	
	public function ExportComisionesXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUsados 	= new Usados();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();

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
			"COMISION"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oUsado 	= $oUsados->GetById($oMinuta->IdUsado);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$Comision 	= $oMinuta->PrecioVenta / 1.21 * Config::ComisionUsados / 100;
				
			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinuta),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oUsado->Modelo),
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinuta->PrecioVenta),
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
	
	
}

?>