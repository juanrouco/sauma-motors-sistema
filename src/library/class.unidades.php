<?php 

require_once('class.dbaccess.php');
require_once('class.cliente.php');
require_once('class.clientes.php');
require_once('class.facturacompra.php');
require_once('class.facturascompras.php');
require_once('class.comprobantetipos.php');
require_once('class.unidad.php');
require_once('class.modelos.php');
require_once('class.colores.php');
require_once('class.ubicaciones.php');
require_once('class.estadosunidad.php');
require_once('class.planillasrecepcion.php');
require_once('class.planillascompra.php');
require_once('class.recepcionestados.php');
require_once('class.pedidosaccesorios.php');
require_once('class.minutasfinanciacion.php');
require_once('class.cuentasgestoria.php');
require_once('class.pagos.php');
require_once('class.usuarios.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.usados.php');
require_once('class.tipopago.php');
require_once('class.ubicaciones.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.periodos.php');
require_once('class.localidades.php');
require_once('excel_export/class.xlsexport.php');
require_once('excel_reader/class.xlsreader.php');


class Unidades extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['CodigoComercial'])) && ($filter['CodigoComercial'] != ''))
			$sql.= " AND CodigoComercial LIKE '%" . DB::StringUnquoted($filter['CodigoComercial']) . "%'";

		if ((isset($filter['NumeroVinPrefijo'])) && ($filter['NumeroVinPrefijo'] != ''))
			$sql.= " AND m.NumeroVinPrefijo LIKE '%" . DB::StringUnquoted($filter['NumeroVinPrefijo']) . "%'";

		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";

		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND Patente LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";

		if ((isset($filter['NumeroChasis'])) && ($filter['NumeroChasis'] != ''))
			$sql.= " AND NumeroChasis LIKE '%" . DB::StringUnquoted($filter['NumeroChasis']) . "%'";

		if ((isset($filter['NumeroMotor'])) && ($filter['NumeroMotor'] != ''))
			$sql.= " AND NumeroMotor LIKE '%" . DB::StringUnquoted($filter['NumeroMotor']) . "%'";

		if ((isset($filter['NumeroPedido'])) && ($filter['NumeroPedido'] != ''))
			$sql.= " AND NumeroPedido LIKE '%" . DB::StringUnquoted($filter['NumeroPedido']) . "%'";

		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND IdUnidad = " . DB::Number($filter['IdUnidad']);
		
		if ((isset($filter['IdOrigenCliente'])) && ($filter['IdOrigenCliente'] != ''))
			$sql.= " AND IdOrigenCliente = " . DB::Number($filter['IdOrigenCliente']);
			
		if ((isset($filter['IdMarca'])) && ($filter['IdMarca'] != ''))
			$sql.= " AND m.IdMarcaVehiculo =  " . DB::Number($filter['IdMarca']);
			
		if ((isset($filter['IdUnidadDesde'])) && ($filter['IdUnidadDesde'] != ''))
			$sql.= " AND IdUnidad >= " . DB::Number($filter['IdUnidadDesde']);
		
		if ((isset($filter['IdUnidadHasta'])) && ($filter['IdUnidadHasta'] != ''))
			$sql.= " AND IdUnidad <= " . DB::Number($filter['IdUnidadHasta']);

		if ((isset($filter['IdModelo'])) && ($filter['IdModelo'] != ''))
			$sql.= " AND m.DenominacionComercial IN (SELECT DenominacionComercial FROM TB_Modelos where IdModelo = " . DB::Number($filter['IdModelo']) . ")";

		if ((isset($filter['ClienteReventa'])) && ($filter['ClienteReventa'] != ''))
			$sql.= " AND IdUnidad IN (SELECT IdMinuta FROM TB_Minutas m INNER JOIN TB_Clientes c ON m.IdClienteReventa = c.IdCliente WHERE c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['ClienteReventa']) . "%')";

		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
			$sql.= " AND u.IdModelo IN (SELECT IdModelo FROM TB_Modelos WHERE DenominacionComercial LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%')";

		if ((isset($filter['IdUbicacion'])) && ($filter['IdUbicacion'] != ''))
			$sql.= " AND IdUbicacion = " . DB::Number($filter['IdUbicacion']);

		if ((isset($filter['IdPlanillaRecepcion'])) && ($filter['IdPlanillaRecepcion'] != ''))
			$sql.= " AND IdPlanillaRecepcion = " . DB::Number($filter['IdPlanillaRecepcion']);

		if ((isset($filter['IdPlanillaCompra'])) && ($filter['IdPlanillaCompra'] != ''))
			$sql.= " AND IdPlanillaCompra = " . DB::Number($filter['IdPlanillaCompra']);

		if ((isset($filter['IdReporteFacturacion'])) && ($filter['IdReporteFacturacion'] != ''))
			$sql.= " AND IdReporteFacturacion = " . DB::Number($filter['IdReporteFacturacion']);

		if ((isset($filter['IdTipoModelo'])) && ($filter['IdTipoModelo'] != ''))
			$sql.= " AND IdTipoModelo = " . DB::Number($filter['IdTipoModelo']);

		if ((isset($filter['NotIdTipoModelo'])) && ($filter['NotIdTipoModelo'] != ''))
			$sql.= " AND IdTipoModelo <> " . DB::Number($filter['NotIdTipoModelo']);
				
		if ((isset($filter['FechaRetiroDesde'])) && ($filter['FechaRetiroDesde'] != ''))
			$sql.= " AND FechaRetiro >= " . DB::Date($filter['FechaRetiroDesde']);
		
		if ((isset($filter['FechaRetiroHasta'])) && ($filter['FechaRetiroHasta'] != ''))
			$sql.= " AND FechaRetiro <= " . DB::Date($filter['FechaRetiroHasta']);
				
		if ((isset($filter['FechaArriboEstimadaDesde'])) && ($filter['FechaArriboEstimadaDesde'] != ''))
			$sql.= " AND FechaArriboEstimada >= " . DB::Date($filter['FechaArriboEstimadaDesde']);
		
		if ((isset($filter['FechaArriboEstimadaHasta'])) && ($filter['FechaArriboEstimadaHasta'] != ''))
			$sql.= " AND FechaArriboEstimada <= " . DB::Date($filter['FechaArriboEstimadaHasta']);
			
		if ((isset($filter['FechaFacturaDesde'])) && ($filter['FechaFacturaDesde'] != ''))
			$sql.= " AND FechaFacturaCompra >= " . DB::Date($filter['FechaFacturaDesde']);
		
		if ((isset($filter['FechaFacturaHasta'])) && ($filter['FechaFacturaHasta'] != ''))
			$sql.= " AND FechaFacturaCompra <= " . DB::Date($filter['FechaFacturaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND FechaMinuta >= " . DB::Date($filter['FechaDesde']);
		
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND FechaMinuta <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaMarchaVencimientoDesde'])) && ($filter['FechaMarchaVencimientoDesde'] != ''))
			$sql.= " AND FechaMarchaVencimiento >= " . DB::Date($filter['FechaMarchaVencimientoDesde']);
		
		if ((isset($filter['FechaMarchaVencimientoHasta'])) && ($filter['FechaMarchaVencimientoHasta'] != ''))
			$sql.= " AND FechaMarchaVencimiento <= " . DB::Date($filter['FechaMarchaVencimientoHasta']);
		
		if ((isset($filter['Marcha'])) && ($filter['Marcha'] != ''))
			$sql.= " AND Marcha = " . DB::Bool($filter['Marcha']);
		
		if ((isset($filter['Conforme'])) && ($filter['Conforme'] != ''))
			$sql.= " AND Conforme = " . DB::Bool($filter['Conforme']);
		
		if ((isset($filter['Lavado'])) && ($filter['Lavado'] != ''))
			$sql.= " AND Lavado = " . DB::Bool($filter['Lavado']);
			
		if ((isset($filter['Pisado'])) && ($filter['Pisado'] != ''))
			$sql.= " AND Pisado = " . DB::Bool($filter['Pisado']);
			
		if ((isset($filter['Certificado'])) && ($filter['Certificado'] != ''))
			$sql.= " AND Certificado = " . DB::Bool($filter['Certificado']);
		
		if ((isset($filter['Cancelado'])) && ($filter['Cancelado'] != ''))
			$sql.= " AND Cancelada = " . DB::Bool($filter['Cancelado']);
		
		if ((isset($filter['Consignacion'])) && ($filter['Consignacion'] != ''))
			$sql.= " AND Consignacion = " . DB::Bool($filter['Consignacion']);

		if ((isset($filter['NotIdClientePlan'])) && ($filter['NotIdClientePlan'] != ''))
			$sql.= " AND IdClientePlan IS NULL";

		if ((isset($filter['IdProveedor'])) && ($filter['IdProveedor'] != ''))
			$sql.= " AND IdProveedor = " . DB::Number($filter['IdProveedor']);
			
		if ((isset($filter['FechaRetiroNull'])) && ($filter['FechaRetiroNull'] != ''))
			$sql.= " AND ((FechaRetiro IS NULL AND IdEstado = " . DB::Number($filter['FechaRetiroNull']) . ") OR  IdEstado = " . $filter['IdEstado'] . ")";
		elseif (isset($filter['IdEstado']))
		{
			if (is_array($filter['IdEstado']))
			{
				$sql.= " AND (IdEstado IN(";
				for ($i=0; $i<count($filter['IdEstado']); $i++) $sql.= DB::Number($filter['IdEstado'][$i]) . ',';
				$sql.= "0)";
				if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
					$sql.= " OR IdUnidad IN (SELECT IdUnidad FROM TB_Minutas WHERE IdUsuario = " . DB::Number($filter['IdUsuario']) . ")";
				$sql.= ")";
			}
			elseif ($filter['IdEstado'] != '')
			{
				$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
			}
		}
		if ($filter['Reportado'] == '0' || $filter['Reportado'] == '1')
			$sql.= " AND mt.Reportado = " . DB::Bool($filter['Reportado']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdUnidad DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrderByEstado(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IF (u.IdEstado = " . DB::Number(EstadoUnidad::Reservado) . ", 0, IF (u.Pisado = 1, 1, u.IdUnidad)) ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrder(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY m.DenominacionComercial ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllReporteStock(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE u.IdEstado <> " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::VentasEspeciales);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Vendido);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Rodriguez);
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Transito);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdUnidad DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetTotalReporteStock(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUnidad) AS CantidadTotal,";
		$sql.= " SUM(IF(u.Consignacion, 0, m.PrecioCompra)) AS CostoTotal,";
		$sql.= " SUM(IF(u.Consignacion, 0, m.Precio1 + " . Config::FleteFormulario . ")) AS VentaTotal,";
		$sql.= " SUM(IF(u.Consignacion, 0, m.Precio2 + " . Config::FleteFormulario . ")) AS VentaCreditoTotal";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " LEFT JOIN TB_Minutas v ON u.IdUnidad = v.IdUnidad";
		$sql.= " WHERE u.IdEstado <> " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::VentasEspeciales);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Vendido);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Rodriguez);
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Transito);
		
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
		$oReporteTotal->VentaTotal = $oRow['VentaTotal'];
		$oReporteTotal->VentaCreditoTotal = $oRow['VentaCreditoTotal'];

		return $oReporteTotal;		
	}
	
	public function GetTotalReporteDeuda(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUnidad) AS CantidadTotal, SUM(u.ImporteNotaCredito - IF (Aux.TotalPago IS NULL, 0, Aux.TotalPago)) AS CostoTotal";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " LEFT JOIN (";
		$sql.= " SELECT SUM(mpi.Importe) AS TotalPago, mpi.IdUnidad";
		$sql.= " FROM TB_MinutasPagoItems mpi";
		$sql.= " GROUP BY mpi.IdUnidad";
		$sql.= ") AS Aux ON Aux.IdUnidad = u.IdUnidad";
		$sql.= " WHERE u.Cancelada = 0";
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
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE u.IdEstado <> " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::VentasEspeciales);
		$sql.= " AND u.IdEstado <> " . DB::Number(EstadoUnidad::Vendido);
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Rodriguez);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetAllReporteVendidos(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " INNER JOIN TB_Minutas mt ON u.IdUnidad = mt.IdUnidad";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::VentasEspeciales) . ")";
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Rodriguez);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY mt.FechaMinuta DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetTotalReporteVendidos(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUnidad) AS CantidadTotal,";
		$sql.= " SUM(IF(u.ImporteCompraNeto IS NULL OR u.ImporteCompraNeto = 0, m.PrecioCompra, u.ImporteCompraNeto)) AS CostoTotal,";
		$sql.= " SUM(mt.PrecioVenta + mt.GastosOtorgamiento + mt.GastosPatentamiento) AS VentaTotal";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " INNER JOIN TB_Minutas mt ON mt.IdUnidad = u.IdUnidad";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::VentasEspeciales) . ")";
		$sql.= " AND u.IdUbicacion <> " . DB::Number(Ubicacion::Rodriguez);
		$sql.= " AND mt.IdUsuario <> 26";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];
		$oReporteTotal->VentaTotal = $oRow['VentaTotal'];

		return $oReporteTotal;		
	}
	
	public function GetTotalReporteRecibido(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->CantidadTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = "SELECT Count(u.IdUnidad) AS CantidadTotal,";
		$sql.= " SUM(IF(u.ImporteCompraNeto IS NULL OR u.ImporteCompraNeto = 0, m.PrecioCompra, u.ImporteCompraNeto)) AS CostoTotal";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->CantidadTotal = $oRow['CantidadTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];
		$oReporteTotal->VentaTotal = $oRow['VentaTotal'];

		return $oReporteTotal;		
	}
	
	public function GetCountRowsReporteVendidos(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " INNER JOIN TB_Minutas mt ON u.IdUnidad = mt.IdUnidad";
		$sql.= " WHERE (u.IdEstado = " . DB::Number(EstadoUnidad::Facturado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Entregado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::Reservado);
		$sql.= " OR u.IdEstado = " . DB::Number(EstadoUnidad::VentasEspeciales) . ")";
		$sql.= " AND u.IdUbicacion <> ". DB::Number(Ubicacion::Rodriguez);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " INNER JOIN TB_Marcas ma ON ma.IdMarca = m.IdMarcaVehiculo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY ma.Nombre, m.DenominacionComercial, u.IdUnidad";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

public function GetAllOrderedById(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY u.IdUnidad, m.DenominacionComercial";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}	

	public function GetAllByModelo(Modelo $oModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdModelo = " . DB::Number($oModelo->IdModelo);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByUbicacion(Ubicacion $oUbicacion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdUbicacion = " . DB::Number($oUbicacion->IdUbicacion);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByColor(Color $oColor)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdColor = " . DB::Number($oColor->IdColor);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByEstado(EstadoUnidad $oEstadoUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdEstado = " . DB::Number($oEstadoUnidad->IdEstado);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByPlanillaRecepcion(PlanillaRecepcion $oPlanillaRecepcion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdPlanillaRecepcion = " . DB::Number($oPlanillaRecepcion->IdPlanillaRecepcion);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByPlanillaCompra(PlanillaCompra $oPlanillaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdPlanillaCompra = " . DB::Number($oPlanillaCompra->IdPlanillaCompra);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByReporteFacturacion(ReporteFacturacion $oReporteFacturacion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdReporteFacturacion = " . DB::Number($oReporteFacturacion->IdReporteFacturacion);
		$sql.= " ORDER BY IdUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidad = new Unidad();
			$oUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

				
	public function GetById($IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUnidad = new Unidad();
		$oUnidad->ParseFromArray($oRow);
		
		return $oUnidad;		
	}


	public function GetByNumeroVin($NumeroVin, $IdModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE NumeroVin = " . DB::String($NumeroVin);	
		$sql.= " AND IdModelo = " . DB::Number($IdModelo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUnidad = new Unidad();
		$oUnidad->ParseFromArray($oRow);
		
		return $oUnidad;		
	}


	public function GetByNumeroChasis($NumeroChasis)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE NumeroChasis = " . DB::String($NumeroChasis);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUnidad = new Unidad();
		$oUnidad->ParseFromArray($oRow);
		
		return $oUnidad;		
	}
	

	public function GetByNumeroMotor($NumeroMotor)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " WHERE NumeroMotor = " . DB::String($NumeroMotor);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUnidad = new Unidad();
		$oUnidad->ParseFromArray($oRow);
		
		return $oUnidad;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Unidad $oUnidad)
	{
		$arr = array
		(
			'IdUnidad' 				=> DB::Number($oUnidad->IdUnidad),
			'IdModelo' 				=> DB::Number($oUnidad->IdModelo),
			'IdUbicacion' 			=> DB::Number($oUnidad->IdUbicacion),
			'IdColor' 				=> DB::Number($oUnidad->IdColor),
			'IdPlanillaRecepcion' 	=> DB::Number($oUnidad->IdPlanillaRecepcion),
			'IdMinutaPago' 			=> DB::Number($oUnidad->IdMinutaPago),
			'CodigoComercial' 		=> DB::String($oUnidad->CodigoComercial),
			'NumeroVinPrefijo' 		=> DB::String($oUnidad->NumeroVinPrefijo),
			'NumeroVin' 			=> DB::String($oUnidad->NumeroVin),
			'NumeroMotor' 			=> DB::String($oUnidad->NumeroMotor),
			'NumeroChasis' 			=> DB::String($oUnidad->NumeroChasis),
			'Anio' 					=> DB::Number($oUnidad->Anio),
			'Patente' 				=> DB::String($oUnidad->Patente),
			'FechaFacturaCompra' 	=> DB::Date($oUnidad->FechaFacturaCompra),
			'FechaArriboEstimada' 	=> DB::Date($oUnidad->FechaArriboEstimada),
			'NumeroFacturaCompra' 	=> DB::String($oUnidad->NumeroFacturaCompra),
			'ImporteCompraNeto' 	=> DB::Number($oUnidad->ImporteCompraNeto),
			'Iva10' 				=> DB::Number($oUnidad->Iva10),
			'Iva21' 				=> DB::Number($oUnidad->Iva21),
			'PercepcionIVA' 		=> DB::Number($oUnidad->PercepcionIVA),
			'PercepcionIB' 			=> DB::Number($oUnidad->PercepcionIB),
			'PercepcionGanancias' 	=> DB::Number($oUnidad->PercepcionGanancias),
			'NoGrabado' 			=> DB::Number($oUnidad->NoGrabado),
			'ImporteCompraBruto' 	=> DB::Number($oUnidad->ImporteCompraBruto),
			'ImporteNotaCredito' 	=> DB::Number($oUnidad->ImporteNotaCredito),
			'ImpuestoInterno'	 	=> DB::Number($oUnidad->ImpuestoInterno),
			'ImpuestoInternoD'	 	=> DB::Number($oUnidad->ImpuestoInternoD),
			'CodigoLlaves' 			=> DB::String($oUnidad->CodigoLlaves),
			'CodigoRadio' 			=> DB::String($oUnidad->CodigoRadio),
			'NumeroPedido' 			=> DB::String($oUnidad->NumeroPedido),
			'Cancelada' 			=> DB::Bool($oUnidad->Cancelada),
			'Verificado' 			=> DB::Bool($oUnidad->Verificado),
			'Certificado' 			=> DB::Bool($oUnidad->Certificado),
			'Reparacion' 			=> DB::Bool($oUnidad->Reparacion),
			'Lavado' 				=> DB::Bool(false),
			'IdEstado' 				=> DB::Number($oUnidad->IdEstado),
			'IdClientePlan'			=> DB::Number($oUnidad->IdClientePlan),
			'Pisado'				=> DB::Bool($oUnidad->Pisado),
			'Comentarios'			=> DB::String($oUnidad->Comentarios),
			'Plan'					=> DB::Bool($oUnidad->Plan),
			'VentaEspecial'			=> DB::Bool($oUnidad->VentaEspecial),
			'IdProveedor'			=> DB::Number($oUnidad->IdProveedor),
			'FechaPedidoFactura'	=> DB::Date($oUnidad->FechaPedidoFactura),
			'FechaRecepcionFactura'	=> DB::Date($oUnidad->FechaRecepcionFactura),
			'FechaPatentamiento'	=> DB::Date($oUnidad->FechaPatentamiento),
			'Consignacion'			=> DB::Bool($oUnidad->Consignacion),
			'Observaciones'			=> DB::String($oUnidad->Observaciones),
			'DNRPA'					=> DB::String($oUnidad->DNRPA),
			'LugarPatentamiento'	=> DB::String($oUnidad->LugarPatentamiento),
			'NumeroCertificado'		=> DB::String($oUnidad->NumeroCertificado),
			'Marcha'				=> DB::Bool($oUnidad->Marcha),
			'FechaMarchaVencimiento'=> DB::Date($oUnidad->FechaMarchaVencimiento),
			'Conforme'				=> DB::Bool($oUnidad->Conforme),
			'PrecioUnidad'			=> DB::Number($oUnidad->PrecioUnidad)
		);
		
		if (!$this->Insert('TB_Unidades', $arr))
			return false;

		/* asignamos el id generado */
		$oUnidad->IdUnidad = DBAccess::GetLastInsertId();
			
		return $oUnidad;
	}
	
	
	public function Update(Unidad $oUnidad)
	{
		$where = " IdUnidad = " . DB::Number($oUnidad->IdUnidad);
		
		$arr = array
		(
			'IdModelo' 				=> DB::Number($oUnidad->IdModelo),
			'IdUbicacion' 			=> DB::Number($oUnidad->IdUbicacion),
			'IdColor' 				=> DB::Number($oUnidad->IdColor),
			'IdMinutaPago' 			=> DB::Number($oUnidad->IdMinutaPago),
			'IdPlanillaRecepcion' 	=> DB::Number($oUnidad->IdPlanillaRecepcion),
			'IdPlanillaCompra' 		=> DB::Number($oUnidad->IdPlanillaCompra),
			'IdReporteFacturacion' 	=> DB::Number($oUnidad->IdReporteFacturacion),
			'CodigoComercial' 		=> DB::String($oUnidad->CodigoComercial),
			'NumeroVinPrefijo' 		=> DB::String($oUnidad->NumeroVinPrefijo),
			'NumeroVin' 			=> DB::String($oUnidad->NumeroVin),
			'NumeroMotor' 			=> DB::String($oUnidad->NumeroMotor),
			'NumeroChasis' 			=> DB::String($oUnidad->NumeroChasis),
			'Anio' 					=> DB::Number($oUnidad->Anio),
			'Patente' 				=> DB::String($oUnidad->Patente),
			'ImporteCompraNeto' 	=> DB::Number($oUnidad->ImporteCompraNeto),
			'Iva10' 				=> DB::Number($oUnidad->Iva10),
			'Iva21' 				=> DB::Number($oUnidad->Iva21),
			'PercepcionIVA' 		=> DB::Number($oUnidad->PercepcionIVA),
			'PercepcionIB' 			=> DB::Number($oUnidad->PercepcionIB),
			'PercepcionGanancias' 	=> DB::Number($oUnidad->PercepcionGanancias),
			'NoGrabado' 			=> DB::Number($oUnidad->NoGrabado),
			'ImporteCompraBruto' 	=> DB::Number($oUnidad->ImporteCompraBruto),
			'ImporteNotaCredito' 	=> DB::Number($oUnidad->ImporteNotaCredito),
			'ImpuestoInterno'	 	=> DB::Number($oUnidad->ImpuestoInterno),
			'ImpuestoInternoD'	 	=> DB::Number($oUnidad->ImpuestoInternoD),
			'FechaFacturaCompra' 	=> DB::Date($oUnidad->FechaFacturaCompra),
			'FechaArriboEstimada' 	=> DB::Date($oUnidad->FechaArriboEstimada),
			'NumeroFacturaCompra' 	=> DB::String($oUnidad->NumeroFacturaCompra),
			'CodigoLlaves' 			=> DB::String($oUnidad->CodigoLlaves),
			'CodigoRadio' 			=> DB::String($oUnidad->CodigoRadio),
			'NumeroPedido' 			=> DB::String($oUnidad->NumeroPedido),
			'Cancelada' 			=> DB::Bool($oUnidad->Cancelada),
			'Verificado' 			=> DB::Bool($oUnidad->Verificado),
			'Certificado' 			=> DB::Bool($oUnidad->Certificado),
			'Reparacion' 			=> DB::Bool($oUnidad->Reparacion),
			'Lavado' 				=> DB::Bool($oUnidad->Lavado),
			'FechaRetiro' 			=> DB::Date($oUnidad->FechaRetiro),
			'IdEstado' 				=> DB::Number($oUnidad->IdEstado),
			'IdClientePlan'			=> DB::Number($oUnidad->IdClientePlan),
			'Pisado'				=> DB::Bool($oUnidad->Pisado),
			'Comentarios'			=> DB::String($oUnidad->Comentarios),
			'Plan'					=> DB::Bool($oUnidad->Plan),
			'VentaEspecial'			=> DB::Bool($oUnidad->VentaEspecial),
			'IdProveedor'			=> DB::Number($oUnidad->IdProveedor),
			'FechaPedidoFactura'	=> DB::Date($oUnidad->FechaPedidoFactura),
			'FechaRecepcionFactura'	=> DB::Date($oUnidad->FechaRecepcionFactura),
			'FechaPatentamiento'	=> DB::Date($oUnidad->FechaPatentamiento),
			'Consignacion'			=> DB::Bool($oUnidad->Consignacion),
			'Observaciones'			=> DB::String($oUnidad->Observaciones),
			'DNRPA'					=> DB::String($oUnidad->DNRPA),
			'LugarPatentamiento'	=> DB::String($oUnidad->LugarPatentamiento),
			'NumeroCertificado'		=> DB::String($oUnidad->NumeroCertificado),
			'Marcha'				=> DB::Bool($oUnidad->Marcha),
			'FechaMarchaVencimiento'=> DB::Date($oUnidad->FechaMarchaVencimiento),
			'Conforme'				=> DB::Bool($oUnidad->Conforme),
			'PrecioUnidad'			=> DB::Number($oUnidad->PrecioUnidad)
		);
		
		if (!DBAccess::Update('TB_Unidades', $arr, $where))
			return false;
		
		return $oUnidad;
	}
	

	public function UpdateChecks(Unidad $oUnidad)
	{
		$where = " IdUnidad = " . DB::Number($oUnidad->IdUnidad);
		
		$arr = array
		(
			'Cancelada' 	=> DB::Bool($oUnidad->Cancelada),
			'Verificado' 	=> DB::Bool($oUnidad->Verificado),
			'Certificado' 	=> DB::Bool($oUnidad->Certificado),
			'Lavado' 		=> DB::Bool($oUnidad->Lavado)
		);
		
		if (!DBAccess::Update('TB_Unidades', $arr, $where))
			return false;
		
		return $oUnidad;
	}


	public function Delete($IdUnidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUnidad = " . DB::Number($IdUnidad);

		if (!DBAccess::Delete('TB_Unidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	

	public function ExportXls(array $filter = NULL, $fullpermiso = false)
	{
		/* declaramos variables necesarias */
		$oModelos 				= new Modelos();
		$oUbicaciones 			= new Ubicaciones();
		$oColores 				= new Colores();
		$oEstadosUnidad 		= new EstadosUnidad();
		$oPlanillasRecepcion 	= new PlanillasRecepcion();

		/* obtenemos el listado de datos a exportar */			
		$arrUnidades = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		if ($fullpermiso)
		{
			$arrData[] = array
			(
				"NRO. INTERNO",
				"NRO. VIN",
				"MODELO",
				"CODIGO LISTA",
				"NUMERO CHASIS",
				"NUMERO MOTOR",
				"ANIO",
				"PATENTE",
				"COLOR",
				"UBICACION",
				"ESTADO",
				"CERTIFICADO",
				"CODIGO LLAVES",
				"CODIGO RADIO",
				"NRO. RECEPCION",
				"FECHA RECEPCION",
				"GALPON",
				"CONTADO",
				"CREDITO"
			);
		}
		else
		{
			$arrData[] = array
			(
				"NRO. INTERNO",
				"NRO. VIN",
				"MODELO",
				"CODIGO LISTA",
				"NUMERO CHASIS",
				"NUMERO MOTOR",
				"ANIO",
				"PATENTE",
				"COLOR",
				"UBICACION",
				"ESTADO",
				"CERTIFICADO",
				"CODIGO RADIO",
				"GALPON",
				"CONTADO",
				"CREDITO"
			);
		}
				
		foreach ($arrUnidades as $oUnidad)
		{	
			$oModelo 			= $oModelos->GetById($oUnidad->IdModelo);
			$oColor 			= $oColores->GetById($oUnidad->IdColor);
			$oUbicacion 		= $oUbicaciones->GetById($oUnidad->IdUbicacion);
			$oEstadoUnidad 		= $oEstadosUnidad->GetById($oUnidad->IdEstado);
			$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion);

			/* obtenemos codigo de llaves si la planilla de recepcion esta aprobada */
			$CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : '';
				
			/* almacenamos el registro */
			if ($fullpermiso)
			{
				$arrData[] = array
				(
					trim($oUnidad->IdUnidad),
					trim($oUnidad->NumeroVin),
					trim($oModelo->DenominacionModelo),
					trim($oUnidad->CodigoComercial),
					trim($oUnidad->NumeroChasis),
					trim($oUnidad->NumeroMotor),
					trim($oUnidad->Anio),
					trim($oUnidad->Patente),
					trim($oColor->Nombre),
					trim($oUbicacion->Nombre),
					trim($oEstadoUnidad->Nombre),
					trim($oUnidad->NumeroCertificado),
					trim($CodigoLlaves),
					trim($oUnidad->CodigoRadio),
					trim($oUnidad->IdPlanillaRecepcion),
					trim(CambiarFecha($oPlanillaRecepcion->FechaRecepcion)),
					trim($oModelo->PrecioCompra),
					trim($oModelo->Precio1),
					trim($oModelo->Precio2)
				);
			} else {
				$arrData[] = array
				(
					trim($oUnidad->IdUnidad),
					trim($oUnidad->NumeroVin),
					trim($oModelo->DenominacionModelo),
					trim($oUnidad->CodigoComercial),
					trim($oUnidad->NumeroChasis),
					trim($oUnidad->NumeroMotor),
					trim($oUnidad->Anio),
					trim($oUnidad->Patente),
					trim($oColor->Nombre),
					trim($oUbicacion->Nombre),
					trim($oEstadoUnidad->Nombre),
					trim($oUnidad->NumeroCertificado),
					trim($oUnidad->CodigoRadio),
					trim($oModelo->PrecioCompra),
					trim($oModelo->Precio1),
					trim($oModelo->Precio2)
				);

			}
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'unidades';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function Import($FileName)
	{
		/* declaramos variables necesarias */
		$oModelos			= new Modelos();
		$oColores			= new Colores();
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
			$Modelo = $arrData->sheets[0]['cells'][$i];
			
			$err						= 0;			
			$Cliente			 		= trim($Modelo[1]);
			$Cliente2	 				= trim($Modelo[2]);
			$Numero						= trim($Modelo[5]);
			$FechaEnvio					= trim($Modelo[6]);
			$NumeroMotor				= trim($Modelo[7]);
			$NumeroSeria	 			= trim($Modelo[8]);
			$PrefijoVin	 				= substr($NumeroSeria, 0, 10);
			$Vin						= substr($NumeroSeria, 10, 7);
			$Referencia		 			= trim($Modelo[9]);
			$Factura		 			= trim($Modelo[10]);
			$Certificado	 			= trim($Modelo[11]);
			$Linea			 			= trim($Modelo[12]);
			$Anio			 			= trim($Modelo[13]);
			$AnioFabricacion 			= trim($Modelo[14]);
			$ModeloTexto	 			= trim($Modelo[15]);  
			$Descripcion	 			= trim($Modelo[16]);
			$DNRPA			 			= trim($Modelo[17]);
			$FechaFabricacion 			= trim($Modelo[18]);
			$Cilindrada		 			= trim($Modelo[19]);
			$Ubicacion		 			= trim($Modelo[20]);
			
			$arrDescripcion				= explode(' ', $Descripcion);
			$Color 						= $arrDescripcion[count($arrDescripcion) - 2];
			
			if ($Color == 2015 || $Color == 2016)
				$Color 	= $arrDescripcion[count($arrDescripcion) - 3];
			
			
			if (!($PrefijoVin == '' && $Vin == '' && $NumeroMotor == '' && $Anio == ''))
			{
				if ($PrefijoVin == '' || !($oModelo = $oModelos->GetByPrefijoVin($PrefijoVin)))
				{
					$oModelo = new Modelo();
					$oModelo->IdMarcaVehiculo = 17;
					$oModelo->DenominacionComercial = $ModeloTexto;
					$oModelo->DenominacionModelo = $ModeloTexto;
					$oModelo->IdMarcaMotor = 17;
					$oModelo->IdMarcaChasis = 17;
					$oModelo->Iva = 21;
					$oModelo->IdTipoModelo = 13;
					$oModelo->IdCategoriaModelo = 52;
					$oModelo->IdTipoCombustible = 1;
					$oModelo->Anio = $Anio;
					$oModelo->Cilindrada = $Cilindrada;
					$oModelo->NumeroVinPrefijo = $PrefijoVin;
					$oModelo = $oModelos->Create($oModelo);
				}
				else
				{
					$oModelo = $oModelo[0];
				}
				if ($Vin == '')
					$err|= 2;
				elseif ($oUnidadAux = $this->GetByNumeroChasis($PrefijoVin . $Vin))
					$err|= 64;
				if ($NumeroMotor == '')
					$err|= 4;				
				if ($Color == '' || !$oColor = $oColores->GetByNombre($Color))
				{
					$err|= 8;				
				}				
				if ($Ubicacion == '')
					$Ubicacion = 'PANAMERICANA';
				if ($Ubicacion == '' || !$oUbicacion = $oUbicaciones->GetByNombre($Ubicacion))
				{
					$err|= 32;				
				}
				if ($Anio == '')
					$err|= 16;
							
				if ($err == 0)
				{
					$oUnidad = new Unidad();
					$oUnidad->IdModelo 				= $oModelo->IdModelo;
					$oUnidad->IdUbicacion			= $oUbicacion->IdUbicacion; //Ubicacion de Stock
					$oUnidad->IdColor 				= $oColor->IdColor;
					//$oUnidad->CodigoComercial	 	= $oModelo->CodigoComercial;
					$oUnidad->NumeroVinPrefijo	 	= $PrefijoVin;
					$oUnidad->NumeroVin			 	= $Vin;
					$oUnidad->NumeroMotor		 	= $NumeroMotor;
					$oUnidad->NumeroChasis		 	= $PrefijoVin . $Vin;
					$oUnidad->Anio				 	= $Anio;
					$oUnidad->DNRPA				 	= $DNRPA;
					$oUnidad->FechaPedidoFactura	= $FechaEnvio;
					$oUnidad->IdEstado				= 1;//Estado STOCK
					$oUnidad->NumeroFacturaCompra	= str_replace('A ', '', $Factura);
					$oUnidad->NumeroCertificado		= $Certificado;
					$oUnidad->Certificado			= 1;
					$oUnidad->Conforme				= 1;
					$oUnidad->IdProveedor			= 33;
					$oUnidad->FechaArriboEstimada	= date('d-m-Y');
							
					if ($this->Create($oUnidad))
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
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el color $Color es inv&aacute;lido. <br>";
					if ($err & 16)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el a&ntilde;o es incorrecto. <br>";
					if ($err & 32)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la ubicaci&oacute;n es incorrecta. <br>";	
					if ($err == 64)
					{
						
						if ($oUnidadAux = $this->GetByNumeroChasis($PrefijoVin . $Vin))
						{
							$oUnidadAux->NumeroCertificado		= $Certificado;
							$oUnidadAux->Certificado			= 1;
							$oUnidadAux->DNRPA				 	= $DNRPA;
							$oUnidadAux->NumeroFacturaCompra	= str_replace('A ', '', $Factura);
							$oUnidadAux->IdColor 				= $oColor->IdColor;
							if (!$oUnidadAux->FechaArriboEstimada)
								$oUnidadAux->FechaArriboEstimada	= date('d-m-Y');
							//print_r($oUnidadAux);exit;
							$this->Update($oUnidadAux);
							$Edit++;
						}
					}
				}					
				
				//exit;
				$Row++;
			}
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
	
	public function ImportTxt($FileName)
	{
		/* declaramos variables necesarias */
		$oModelos			= new Modelos();
		$oColores			= new Colores();
		$oClientes			= new Clientes();
		$oFacturasCompras	= new FacturasCompras();
		$oPeriodos			= new Periodos();
		
		$FechaImportacion = '';
		
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
			
			$Modelo = str_getcsv($line, '	');

			
			if ($count != 0)
			{
				//$Modelo = $arrData->sheets[0]['cells'][$i];
				
				$err						= 0;			
				$Fecha				 		= trim($Modelo[0]);
				$Comentarios		 		= trim($Modelo[1]);
				$Chasis		 				= trim($Modelo[2]);
				$PrefijoVin	 				= substr($Chasis, 0, 10);
				$Vin						= substr($Chasis, 10, 7);
				$Color						= trim($Modelo[3]);
				$NumeroMotor				= trim($Modelo[4]);
				$NroFactura					= trim($Modelo[16]);
				$ImporteBruto				= trim($Modelo[8]);
				$ImporteBruto 				= str_replace(',', '.', str_replace('.', '', $ImporteBruto));
				$Impuestos					= trim($Modelo[9]);
				$Impuestos					= str_replace(',', '.', str_replace('.', '', $Impuestos));
				$Plan						= trim($Modelo[23]);				
				$Suscriptor					= trim($Modelo[13]);
				$SuscriptorVD				= trim($Modelo[40]);
				$CodigoPrefijo	 			= trim($Modelo[27]);
				$CodigoVariacion 			= trim($Modelo[28]);
				
				$CodigoComercial			= $CodigoPrefijo . '-' . $CodigoVariacion;
				$Anio			 			= trim($Modelo[29]);
			
				if (!($NroFactura == '' &&$Fecha == '' && $PrefijoVin == '' && $Vin == '' && $NumeroMotor == '' && $Color == '' && $Anio == '' && $ImporteBruto == ''))
				{
					if ($PrefijoVin == '' || !($oModelo = $oModelos->GetByCodigoComercialAndPrefijoVin($CodigoComercial, $PrefijoVin)))
						$err+= 1;				
					
					if ($Vin == '' || ($oUnidadAux = $this->GetByNumeroVin($Vin, $oModelo->IdModelo) && $Comentarios == ''))
						$err+= 2;
					if ($NumeroMotor == '')
						$err+= 4;				
					if ($Color == '' || !$oColor = $oColores->GetByNombre($Color))
					{
						$err+= 8;				
					}
					if ($Anio == '')
						$err+= 16;
								
					if ($err == 0)
					{
						if ($Comentarios == '')
						{
							
							
							$oUnidad = new Unidad();
							$oUnidad->IdModelo 				= $oModelo->IdModelo;
							$oUnidad->IdUbicacion			= 198;
							$oUnidad->IdColor 				= $oColor->IdColor;
							$oUnidad->CodigoComercial	 	= $oModelo->CodigoComercial;
							$oUnidad->NumeroVinPrefijo	 	= $PrefijoVin;
							$oUnidad->NumeroVin			 	= $Vin;
							$oUnidad->NumeroMotor		 	= $NumeroMotor;
							$oUnidad->NumeroChasis		 	= $PrefijoVin . $Vin;
							$oUnidad->Anio				 	= $Anio;
							if ($Plan == 'PLAN')
							{
								$oUnidad->IdEstado				= EstadoUnidad::Plan;
								$oUnidad->Plan					= 1;
								
								$oCliente = new Cliente();
								$oCliente->IdTipoPersona = PersonaTipos::PersonaFisica;
								$oCliente->RazonSocial = $Suscriptor;
								$oCliente->IdTipoIva = TipoIva::CF;
								
								$oCliente = $oClientes->Create($oCliente);
								
								$oUnidad->Cancelada = 1;
								$oUnidad->IdClientePlan = $oCliente->IdCliente;
							}
							elseif ($Plan == 'VEMP')
							{
								$oUnidad->IdEstado				= EstadoUnidad::VentaEmpleados;
								$oUnidad->VentaEspecial			= 1;
								
								$oCliente = new Cliente();
								$oCliente->IdTipoPersona = PersonaTipos::PersonaFisica;
								$oCliente->RazonSocial = $Suscriptor;
								$oCliente->IdTipoIva = TipoIva::CF;
								
								$oCliente = $oClientes->Create($oCliente);
								
								$oUnidad->Cancelada = 1;
								$oUnidad->IdClientePlan = $oCliente->IdCliente;
							}
							elseif ($Plan == 'VESP')
							{
								$oUnidad->IdEstado				= EstadoUnidad::VentasEspeciales;
								$oUnidad->VentaEspecial			= 1;
								
								$oCliente = new Cliente();
								$oCliente->IdTipoPersona = PersonaTipos::PersonaFisica;
								$oCliente->RazonSocial = $SuscriptorVD;
								$oCliente->IdTipoIva = TipoIva::CF;
								
								$oCliente = $oClientes->Create($oCliente);
								
								$oUnidad->IdClientePlan = $oCliente->IdCliente;
							}
							else
								$oUnidad->IdEstado				= EstadoUnidad::Stock;
								
							$ImporteCompraNeto = $ImporteBruto - $Impuestos;
							$Iva10 = 0;
							$Iva21 = 0;
							if ($oModelo->Iva == 21)
								$Iva21 = $ImporteCompraNeto * 0.21;
							elseif ($oModelo->Iva == 10.5)
								$Iva10 = $ImporteCompraNeto * 0.105;
								
							$PercepcionIVA = $ImporteCompraNeto * 0.03;
							$PercepcionIB = $ImporteCompraNeto * 0.025;
							
							$oUnidad->ImporteCompraBruto	= $ImporteBruto;
							$oUnidad->FechaFacturaCompra	= $Fecha;
							$oUnidad->NumeroFacturaCompra	= '0051-' . str_pad($NroFactura, 8, '0', STR_PAD_LEFT);
							$oUnidad->ImporteCompraNeto		= $ImporteCompraNeto;
							$oUnidad->Iva10					= $Iva10;
							$oUnidad->Iva21					= $Iva21;
							$oUnidad->PercepcionIVA			= $PercepcionIVA;
							$oUnidad->PercepcionIB			= $PercepcionIB;
							$oUnidad->ImpuestoInterno		= $ImpuestoInterno;
							$oUnidad->ImpuestoInternoD		= $ImpuestoInternoD;
							$oUnidad->Certificado			= 1;
							$oUnidad->Conforme				= 1;
							
							if ($oUnidad = $this->Create($oUnidad))
							{
								$oFacturaCompra = new FacturaCompra();
								$oFacturaCompra->IdComprobanteTipo		= ComprobanteTipos::FacturaA;
								$oFacturaCompra->Numero					= '0051-' . str_pad($NroFactura, 8, '0', STR_PAD_LEFT);
								$oFacturaCompra->Fecha					= $Fecha;
								$oFacturaCompra->IdProveedor			= 1;
								$oFacturaCompra->Cuit					= '30-66207168-0';
								$oFacturaCompra->ImporteNeto			= $ImporteCompraNeto;
								$oFacturaCompra->Iva10					= $Iva10;
								$oFacturaCompra->Iva21					= $Iva21;
								$oFacturaCompra->Iva27					= 0;
								$oFacturaCompra->PercepcionIva			= $PercepcionIva;
								$oFacturaCompra->PercepcionIB			= $PercepcionIB;
								$oFacturaCompra->IdConcepto				= 18;
								$oFacturaCompra->Total					= $ImporteBruto;
								$oFacturaCompra->IdUnidad				= $oUnidad->IdUnidad;
								
								$oPeriodo = $oPeriodos->GetPeriodoCorrespondienteAbierto($Fecha);
								if ($oPeriodo)
									$oFacturaCompra->IdPeriodo = $oPeriodo->IdPeriodo;
								
								$oFacturasCompras->Create($oFacturaCompra);
		
								$CountCreate++;
								$FechaImportacion = $Fecha;
							}
						}
						elseif ($Comentarios == 'Factura Flete')
						{
							if ($oUnidad = $this->GetByNumeroVin($Vin, $oModelo->IdModelo))
							{
								$oUnidad->ImporteCompraBruto	= $ImporteBruto;
								$oUnidad->FechaFacturaCompra	= $Fecha;
								$oUnidad->NumeroFacturaCompra	= $NroFactura;
								$this->Update($oUnidad);
							}
							
						}
					}
					else
					{
						if ($err & 1)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el n&uacute;mero de prefijo de Vin " . $PrefijoVin . " y c&oacute;digo comercial ". $CodigoComercial . "No se han hayado. <br>";
						if ($err & 2)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de Vin es incorrecto. <br>";
						if ($err & 4)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de motor es incorrecto. <br>";
						if ($err & 8)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el color es inv&aacute;lido " . $Color . ". <br>";
						if ($err & 16)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el a&ntilde;o es incorrecto. <br>";				
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
	
	public function ImportTxtPagos($FileName)
	{
		/* declaramos variables necesarias */
		$oModelos			= new Modelos();
		$oColores			= new Colores();
		$oClientes			= new Clientes();
		$oFacturasCompras	= new FacturasCompras();
		
		$FechaImportacion = '';
		
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
			
			$Modelo = str_getcsv($line, '|');

			
			if ($count != 0)
			{
				//$Modelo = $arrData->sheets[0]['cells'][$i];
				
				$err						= 0;			
				$ModeloD			 		= trim($Modelo[0]);
				$IdUnidad			 		= trim($Modelo[1]);
				$Pago		 				= trim($Modelo[2]);
				
				if ($IdUnidad)
				{
					if (!($oUnidad = $this->GetById($IdUnidad)))
						$err+= 2;
					
					if ($err == 0)
					{
						if (true)
						{
							
							
							if ($Pago == 'P')
								$oUnidad->Cancelada = '1';
							
							if ($oUnidad = $this->Update($oUnidad))
							{
								$CountCreate++;
								$FechaImportacion = $Fecha;
							}
						}
						elseif ($Comentarios == 'Factura Flete')
						{
							if ($oUnidad = $this->GetByNumeroVin($Vin,  $oModelo->IdModelo))
							{
								$oUnidad->ImporteCompraBruto	= $ImporteBruto;
								$oUnidad->FechaFacturaCompra	= $Fecha;
								$oUnidad->NumeroFacturaCompra	= $NroFactura;
								$this->Update($oUnidad);
							}
							
						}
					}
					else
					{
						if ($err & 1)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el n&uacute;mero de prefijo de Vin " . $PrefijoVin . " y c&oacute;digo comercial ". $CodigoComercial . "No se han hayado. <br>";
						if ($err & 2)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de Vin es incorrecto. <br>";
						if ($err & 4)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de motor es incorrecto. <br>";
						if ($err & 8)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el color es inv&aacute;lido " . $Color . ". <br>";
						if ($err & 16)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el a&ntilde;o es incorrecto. <br>";				
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
	
	public function ExportReporteVendidasCsv(array $filter = NULL)
	{
		$oModelos 				= new Modelos();
		$oColores 				= new Colores();
		$oUbicaciones			= new Ubicaciones();
		$oMinutas				= new Minutas();
		$oClientes				= new Clientes();
		$oCuentasGestoria		= new CuentasGestoria();
		$oPagos					= new Pagos();
		$oMinutasFinanciacion	= new MinutasFinanciacion();
		$oPedidosAccesorios		= new PedidosAccesorios();
		$oUsados				= new Usados();
		$oLocalidades			= new Localidades();
		$oUsuarios				= new Usuarios();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "unidades_reporte_vendidas.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrUnidades = $this->GetAllReporteVendidos($filter);
		$oReporteTotal 	= $this->GetTotalReporteVendidos($filter);
				
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
		$csv.= "Vendedor";
		$csv.= $Separador;
		$csv.= "Cliente";
		$csv.= $Separador;
		$csv.= "Codigo Postal";
		$csv.= $Separador;
		$csv.= "Localidad";
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
		$csv.= "Credicuotas";		
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
		$csv.= "PP";		
		$csv.= $Separador;
		$csv.= "Ganancia";		
		$csv.= $SaltoLinea;
	
		foreach ($arrUnidades as $oUnidad)
		{				
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			$oMinuta	= $oMinutas->GetById($oUnidad->IdUnidad);
			$oCliente	= $oClientes->GetById($oMinuta->IdCliente);
			$oLocalidad	= $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
			$oUsuario	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oCuentaGestoria	= $oCuentasGestoria->GetByIdMinuta($oMinuta->IdMinuta);
			$PrecioUsado = 0;
							$Efectivo = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Efectivo);
							$Transferencia = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Transferencia);
							$Pagare = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Pagare);
							$Tarshop = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credicuotas);
							$Confina = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Confina);
							$Credilogros = $oMinutasFinanciacion->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
							$Credilogros = $oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Credilogros);
							$AM = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Credito);
							$Visa = 0; //$oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::Visa);
							$MC = 0; //$oPagos->GetByIdMinutaIdAcreedor($oMinuta->IdMinuta, Acreedor::MC);
							$DepositoEfectivo = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoEfectivo);
							$DepositoCheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::DepositoCheque);
							$Debito = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Debito);
							$Cheque = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::Cheque);
							$MP = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::MercadoPago);
							$TP = $oPagos->GetTotalIdMinutaIdTipoPago($oMinuta->IdMinuta, TipoPago::TodoPago);
							
							$PrecioVentaTotal = $oMinuta->PrecioVenta + $oMinuta->GastosOtorgamiento + $oMinuta->GastosPatentamiento;
							
							$Tarjeta = $AM + $Visa + $MC;
							$Deposito = $DepositoEfectivo + $DepositoCheque;
							
							$oPedidoAccesorio = $oPedidosAccesorios->GetByIdMinuta($oMinuta->IdMinuta);
							$CostoAccesorios = 0;
							if ($oPedidoAccesorio)
								$CostoAccesorios = $oPedidoAccesorio->GetCosto();
							
							if ($oMinuta->EntregaUsado) 
							{
								$arrUsados = $oUsados->GetAllByIdMinuta($oMinuta->IdMinuta);
								
								$oUsado = $arrUsados[0];
								if (count($arrUsados) > 1)
								{
									$oUsado2 = $arrUsados[1];
									$PrecioUsado+= $oUsado2->Valuacion;
								}
								
								$PrecioUsado+= $oUsado->Valuacion;
							}
			$PrecioCompra = $oUnidad->ImporteCompraNeto && $oUnidad->ImporteCompraNeto != 0 ? $oUnidad->ImporteCompraNeto : $oModelo->PrecioCompra;
			
			$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oMinuta->FechaMinuta)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUnidad->IdUnidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oModelo->DenominacionComercial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUsuario->Nombre . ' ' . $oUsuario->Apellido));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->RazonSocial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCliente->DomicilioCodigoPostal));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oLocalidad->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioVentaTotal, 2, ',', '.')));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioCompra, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($oCuentaGestoria->TotalFinal, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($CostoAccesorios, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim('0,00'));	
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
			$csv.= str_replace('(\t|\n)','', trim(number_format($TP, 2, ',', '.')));	
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($PrecioVentaTotal - $PrecioCompra - $oCuentaGestoria->TotalFinal - $CostoAccesorios, 2, ',', '.')));	
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function ExportReporteStockCsv(array $filter = NULL)
	{
		$oModelos 		= new Modelos();
		$oColores 		= new Colores();
		$oUbicaciones	= new Ubicaciones();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "unidades_reporte_stock.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrUnidades = $this->GetAllReporteStock($filter);
		$oReporteTotal 	= $this->GetTotalReporteStock($filter);
				
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
	
		foreach ($arrUnidades as $oUnidad)
		{				
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			$oColor	= $oColores->GetById($oUnidad->IdColor);
			$oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
			
			
			$csv.= str_replace('(\t|\n)','', trim($oUnidad->IdUnidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oModelo->DenominacionComercial));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUbicacion->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oUnidad->Anio));	
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function ExportReporteFacturasCompraCsv(array $filter = NULL)
	{
		$oModelos 		= new Modelos();
		$oColores 		= new Colores();
		$oUbicaciones	= new Ubicaciones();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "reporte_facturas.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrUnidades = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Nro. Interno";
		$csv.= $Separador;
		$csv.= "Nro. Vin";
		$csv.= $Separador;
		$csv.= "Denominacion";
		$csv.= $Separador;
		$csv.= "Color";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Año";
		$csv.= $Separador;
		$csv.= "Fecha Factura";
		$csv.= $Separador;
		$csv.= "Nro. Factura";
		$csv.= $Separador;
		$csv.= "Importe Neto";
		$csv.= $Separador;
		$csv.= "Importe Bruto";
		$csv.= $SaltoLinea;
	
		foreach ($arrUnidades as $oUnidad)
		{				
			if ($oUnidad->NumeroFacturaCompra)
			{
				$oModelo = $oModelos->GetById($oUnidad->IdModelo);
				$oColor	= $oColores->GetById($oUnidad->IdColor);
				$oUbicacion = $oUbicaciones->GetById($oUnidad->IdUbicacion);
				
				
				$csv.= str_replace('(\t|\n)','', trim($oUnidad->IdUnidad));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oUnidad->NumeroVin));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oModelo->DenominacionComercial));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oColor->Nombre));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oUbicacion->Nombre));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oUnidad->Anio));	
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim(CambiarFecha($oUnidad->FechaFacturaCompra)));	
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim($oUnidad->NumeroFacturaCompra));	
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim(number_format($oUnidad->ImporteCompraNeto, 2)));
				$csv.= $Separador;
				$csv.= str_replace('(\t|\n)','', trim(number_format($oUnidad->ImporteCompraBruto, 2)));	
				$csv.= $SaltoLinea;			
			}		
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function ExportReporteFacturasCompra(array $filter = NULL)
	{
		$oModelos = new Modelos();
	
		/* obtenemos el listado de datos a exportar */			
		$arrUnidades = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"INTERNO", 
			"RAZON SOCIAL", 
			"CUIT", 
			"NETO", 
			"IVA 10,5%", 
			"IVA 21%", 
			"PERCEPCION IVA", 
			"PERCEPCION IB", 
			"PERCEPCION GANANCIAS", 
			"NO GRABADO", 
			"TOTAL");
				
		foreach ($arrUnidades as $oUnidad)
		{
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			
			$arrData[] = array(
				trim(CambiarFecha($oUnidad->FechaFacturaCompra)),
				trim("FACTURA A"),
				trim($oUnidad->NumeroFacturaCompra),
				trim($oUnidad->IdUnidad),
				trim("GENERAL MOTORS DE ARGENTINA SRL"), 
				trim("30-66207168-0"),
				trim(number_format($oUnidad->ImporteCompraNeto, 2, ',', '.')),
				trim(number_format($oUnidad->Iva10, 2, ',', '.')),
				trim(number_format($oUnidad->Iva21, 2, ',', '.')),
				trim(number_format($oUnidad->PercepcionIVA, 2, ',', '.')),
				trim(number_format($oUnidad->PercepcionIB, 2, ',', '.')),
				trim(number_format($oUnidad->PercepcionGanancias, 2, ',', '.')),
				trim(number_format($oUnidad->NoGrabado, 2, ',', '.')),
				trim(number_format($oUnidad->ImporteCompraBruto, 2, ',', '.'))
				);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'facturas_compra_unidades';
		
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
		
		$oMarcas = new Marcas();
		$oModelosMigracion = new ModelosMigracion();
		$oSeriesMigracion = new SeriesMigracion();
		$oTiposModelo = new TiposModelo();
		$oCategoriasModelo = new CategoriasModelo();
		$oUbicaciones = new Ubicaciones();
		$oColores = new Colores();
		$oEstadosUnidad = new EstadosUnidad();
		$oModelos = new Modelos();
		
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
				$CodigoInterno		 		= trim($Cliente[0]);
				$NroSerie			 		= trim($Cliente[1]);
				$NroVin				 		= trim($Cliente[2]);
				$CodigoMarca		 		= trim($Cliente[3]);
				$CodigoModelo		 		= trim($Cliente[4]);
				$CodigoEstado		 		= trim($Cliente[5]);
				$CodigoCliente		 		= trim($Cliente[6]);
				$CodigoColor		 		= trim($Cliente[7]);
				$Tipo				 		= trim($Cliente[8]);
				$Anio				 		= trim($Cliente[9]);
				$Denominacion		 		= trim($Cliente[10]);
				$NroMotor			 		= trim($Cliente[11]);
				$MarcaMotor			 		= trim($Cliente[12]);
				$NroChasis			 		= trim($Cliente[13]);
				$MarcaChasis			 	= trim($Cliente[14]);
				$Patente				 	= trim($Cliente[15]);
				$CodUbicacion			 	= trim($Cliente[16]);
				print_r($Denominacion);
				if ($CodUbicacion == '')
					$CodUbicacion = 'PIL';
				
				$oMarca = $oMarcas->GetByCodigo($CodigoMarca);
				$oMarcaMotor = $oMarcas->GetByNombre($MarcaMotor);
				$oMarcaChasis = $oMarcas->GetByNombre($MarcaChasis);
				$oModeloMigracion = $oModelosMigracion->GetByCodigo($CodigoModelo);
				
				$oSerieMigracion = $oSeriesMigracion->GetByCodigo($oModeloMigracion->IdModeloMigracion, substr($NroSerie,0, 5));
				$oTipoModelo = $oTiposModelo->GetByNombre($Tipo);
				$oCategoriaModelo = $oCategoriasModelo->GetByNombre($Tipo);
				
				$oUbicacion = $oUbicaciones->GetByCodigo($CodUbicacion);
				$oColor = $oColores->GetByCodigo($CodigoColor);
				$oEstadoUnidad = $oEstadosUnidad->GetByCodigo($CodigoEstado);
				/*if (!$oModeloMigracion->Denominacion)
				{
					print_r($oModeloMigracion->Denominacion);Exit;
				}*/
				
				if (!(!$oCategoriaModelo && !$oTipoModelo && !$oMarca && !$oMarcaMotor && !$oMarcaMotor && !$oModeloMigracion && $Codigo == '' && $Denominacion == '' && $Iva == ''))
				{
					
					$encontrado = false;
					$oModelo = null;
					
					if ($arrModelos = $oModelos->GetByPrefijoVin($NroSerie))
					{
						foreach ($arrModelos as $oModeloAux)
						{
							if ($oModeloAux->NumeroVinPrefijo == $NroSerie && $oModeloAux->DenominacionComercial == $Denominacion)
							{
								$encontrado  = true;
								$oModelo = $oModeloAux;
							}
						}
					}
					
					
					if ($err == 0 && $encontrado)
					{
						$oUnidad = new Unidad();
						$oUnidad->IdUnidad = $CodigoInterno;
						$oUnidad->IdModelo = $oModelo->IdModelo;
						$oUnidad->IdUbicacion = $oUbicacion->IdUbicacion;
						$oUnidad->IdColor = $oColor->IdColor;
						$oUnidad->CodigoComercial	= $oModelo->CodigoComercial;
						$oUnidad->NumeroVinPrefijo	= $oModelo->NumeroVinPrefijo;
						$oUnidad->NumeroVin 	= $NroVin;
						$oUnidad->NumeroMotor 	= $NroMotor;
						$oUnidad->NumeroChasis	= $NroChasis;
						$oUnidad->Anio	= $Anio;
						$oUnidad->Patente = $Patente;
						$oUnidad->IdEstado = $oEstadoUnidad->IdEstado;
							
						if ($oUnidad = $this->Create($oUnidad))
						{
							$CountCreate++;
							$FechaImportacion = $Fecha;
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