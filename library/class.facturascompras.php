<?php 

require_once('class.dbaccess.php');
require_once('class.comprobante.php');
require_once('class.comprobanteestados.php');
require_once('class.comprobantetipos.php');
require_once('class.operaciontipos.php');
require_once('class.facturacompra.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.proveedores.php');
require_once('excel_export/class.xlsexport.php');


class FacturasCompras extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = " WHERE Numero IS NOT NULL AND Numero <> ''";

		if ((isset($filter['IdTipoComprobante'])) && ($filter['IdTipoComprobante'] != ''))
			$sql.= " AND nc.IdComprobanteTipo = " . DB::Number($filter['IdTipoComprobante']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND nc.Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";

		if ((isset($filter['Proveedor'])) && ($filter['Proveedor'] != ''))
			$sql.= " AND p.Empresa LIKE '%" . DB::StringUnquoted($filter['Proveedor']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
				$sql.= " AND nc.Fecha <=" . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['Reportado'])) && ($filter['Reportado'] != ''))
		{
			$sql.= " AND (nc.Reportado =" . DB::Number($filter['Reportado']);
			
			if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
				$sql.= " OR nc.Fecha >=" . DB::Date($filter['FechaDesde']);
			
			$sql.= ")";
			
		}
		else
		{
			if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
				$sql.= " AND nc.Fecha >=" . DB::Date($filter['FechaDesde']);
		}
			
		if ((isset($filter['IdConcepto'])) && ($filter['IdConcepto'] != ''))
			$sql.= " AND nc.IdConcepto =" . DB::Number($filter['IdConcepto']);
			
		if ((isset($filter['IdPeriodo'])) && ($filter['IdPeriodo'] != ''))
			$sql.= " AND nc.IdPeriodo =" . DB::Number($filter['IdPeriodo']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc INNER JOIN TB_Proveedores p ON p.IdProveedor = nc.IdProveedor";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY nc.Fecha";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompra = new FacturaCompra();
			$oFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompra);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetLibroCompras(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY nc.Fecha, nc.Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompra = new FacturaCompra();
			$oFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompra);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetLibroComprasCountRows(array $filter = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY  nc.Fecha, nc.Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	
	
	public function GetLibroComprasTotales(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ",ImporteNeto * -1, ImporteNeto)) AS NetoGravado, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", Iva27 * -1, Iva27)) AS Iva27, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", Iva21 * -1, Iva21)) AS Iva21, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", Iva10 * -1, Iva10)) AS Iva10, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", PercepcionIva * -1, PercepcionIva)) AS PercepcionIva, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", PercepcionIB * -1, PercepcionIB)) AS PercepcionIB, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", PercepcionGanancias * -1, PercepcionGanancias)) AS PercepcionGanancias, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", NoGrabados * -1, NoGrabados)) AS NoGrabados, 
			SUM(IF (IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoB) . " OR IdComprobanteTipo = " . DB::Number(ComprobanteTipos::NotaCreditoC) . ", Total * -1, Total)) AS Total";
		$sql.= " FROM TB_FacturasCompras nc";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";	
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oTotales = new stdClass();
		$oTotales->NetoGravado 			= $oRow['NetoGravado'];
		$oTotales->Iva27 				= $oRow['Iva27'];
		$oTotales->Iva21 				= $oRow['Iva21'];
		$oTotales->Iva10 				= $oRow['Iva10'];
		$oTotales->PercepcionIva 		= $oRow['PercepcionIva'];
		$oTotales->PercepcionIB 		= $oRow['PercepcionIB'];
		$oTotales->PercepcionGanancias 	= $oRow['PercepcionGanancias'];
		$oTotales->NoGrabados 			= $oRow['NoGrabados'];
		$oTotales->Total				= $oRow['Total'];
		
		return $oTotales;
	}
	
	public function GetAllByConcepto(Concepto $oConcepto)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc INNER JOIN TB_Proveedores p ON p.IdProveedor = nc.IdProveedor";
		$sql.= " WHERE nc.IdConcepto = " . DB::Number($oConcepto->IdConcepto);
		$sql.= " ORDER BY nc.Fecha";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaCompra = new FacturaCompra();
			$oFacturaCompra->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaCompra);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdFacturaCompra)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturasCompras";
		$sql.= " WHERE IdFacturaCompra = " . DB::Number($IdFacturaCompra);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompra = new FacturaCompra();
		$oFacturaCompra->ParseFromArray($oRow);
		
		return $oFacturaCompra;		
	}
	
	public function GetByNumero($Numero)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc INNER JOIN TB_Proveedores p ON p.IdProveedor = nc.IdProveedor";
		$sql.= " WHERE nc.Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompra = new FacturaCompra();
		$oFacturaCompra->ParseFromArray($oRow);
		
		return $oFacturaCompra;		
	}
	
	public function GetByIdUnidad($IdUnidad)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc";
		$sql.= " WHERE nc.IdUnidad = " . DB::String($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompra = new FacturaCompra();
		$oFacturaCompra->ParseFromArray($oRow);
		
		return $oFacturaCompra;		
	}
	
	public function GetByNumeroAndProveedorAndTipo($Numero, $IdProveedor, $IdComprobanteTipo)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc INNER JOIN TB_Proveedores p ON p.IdProveedor = nc.IdProveedor";
		$sql.= " WHERE nc.Numero = " . DB::String($Numero);	
		$sql.= " AND nc.IdProveedor = " . DB::Number($IdProveedor);	
		$sql.= " AND nc.IdComprobanteTipo = " . DB::Number($IdComprobanteTipo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaCompra = new FacturaCompra();
		$oFacturaCompra->ParseFromArray($oRow);
		
		return $oFacturaCompra;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT nc.*";
		$sql.= " FROM TB_FacturasCompras nc INNER JOIN TB_Proveedores p ON p.IdProveedor = nc.IdProveedor";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	
	
	public function ActualizarFacturasUnidades()
	{
		$sql = "UPDATE TB_FacturasCompras fc";
		$sql.= " INNER JOIN TB_Unidades u ON u.IdUnidad = fc.IdUnidad";
		$sql.= " SET fc.PercepcionIVA = u.PercepcionIVA";
		$sql.= " WHERE fc.PercepcionIva IS NULL AND u.PercepcionIVA > 0";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(FacturaCompra $oFacturaCompra)
	{
		$arr = array
		(
			'IdComprobanteTipo' 	=> DB::Number($oFacturaCompra->IdComprobanteTipo),
			'IdProveedor' 			=> DB::String($oFacturaCompra->IdProveedor),
			'Cuit' 					=> DB::String($oFacturaCompra->Cuit),
			'Numero' 				=> DB::String($oFacturaCompra->Numero),
			'Fecha'					=> DB::Date($oFacturaCompra->Fecha),
			'ImporteNeto'			=> DB::Number($oFacturaCompra->ImporteNeto),
			'Iva10'					=> DB::Number($oFacturaCompra->Iva10),
			'Iva21'					=> DB::Number($oFacturaCompra->Iva21),
			'Iva27'					=> DB::Number($oFacturaCompra->Iva27),
			'IdTipoCargo'			=> DB::Number($oFacturaCompra->IdTipoCargo),
			'PercepcionIva'			=> DB::Number($oFacturaCompra->PercepcionIva),
			'PercepcionIB'			=> DB::Number($oFacturaCompra->PercepcionIB),
			'PercepcionGanancias'	=> DB::Number($oFacturaCompra->PercepcionGanancias),
			'NoGrabados'			=> DB::Number($oFacturaCompra->NoGrabados),
			'ImpuestoInterno'		=> DB::Number($oFacturaCompra->ImpuestoInterno),
			'ImpuestoInternoD'		=> DB::Number($oFacturaCompra->ImpuestoInternoD),
			'Total'					=> DB::Number($oFacturaCompra->Total),
			'IdConcepto'			=> DB::Number($oFacturaCompra->IdConcepto),
			'Reportado'				=> DB::Bool($oFacturaCompra->Reportado),
			'IdUnidad'				=> DB::Number($oFacturaCompra->IdUnidad),
			'IdPeriodo'				=> DB::Number($oFacturaCompra->IdPeriodo)
		);
		
		if (!$this->Insert('TB_FacturasCompras', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaCompra->IdFacturaCompra = DBAccess::GetLastInsertId();
			
		return $oFacturaCompra;
	}
	
	
	public function Update(FacturaCompra $oFacturaCompra)
	{
		$where = " IdFacturaCompra = " . DB::Number($oFacturaCompra->IdFacturaCompra);
		
		$arr = array
		(
			'IdComprobanteTipo' 	=> DB::Number($oFacturaCompra->IdComprobanteTipo),
			'IdProveedor' 			=> DB::String($oFacturaCompra->IdProveedor),
			'Cuit' 					=> DB::String($oFacturaCompra->Cuit),
			'Numero' 				=> DB::String($oFacturaCompra->Numero),
			'Fecha'					=> DB::Date($oFacturaCompra->Fecha),
			'ImporteNeto'			=> DB::Number($oFacturaCompra->ImporteNeto),
			'Iva10'					=> DB::Number($oFacturaCompra->Iva10),
			'Iva21'					=> DB::Number($oFacturaCompra->Iva21),
			'Iva27'					=> DB::Number($oFacturaCompra->Iva27),
			'IdTipoCargo'			=> DB::Number($oFacturaCompra->IdTipoCargo),
			'PercepcionIva'			=> DB::Number($oFacturaCompra->PercepcionIva),
			'PercepcionIB'			=> DB::Number($oFacturaCompra->PercepcionIB),
			'PercepcionGanancias'	=> DB::Number($oFacturaCompra->PercepcionGanancias),
			'NoGrabados'			=> DB::Number($oFacturaCompra->NoGrabados),
			'ImpuestoInterno'		=> DB::Number($oFacturaCompra->ImpuestoInterno),
			'ImpuestoInternoD'		=> DB::Number($oFacturaCompra->ImpuestoInternoD),
			'Total'					=> DB::Number($oFacturaCompra->Total),
			'IdConcepto'			=> DB::Number($oFacturaCompra->IdConcepto),
			'Reportado'				=> DB::Bool($oFacturaCompra->Reportado),
			'IdUnidad'				=> DB::Number($oFacturaCompra->IdUnidad),
			'IdPeriodo'				=> DB::Number($oFacturaCompra->IdPeriodo)
		);
		
		if (!DBAccess::Update('TB_FacturasCompras', $arr, $where))
			return false;
		
		return $oFacturaCompra;
	}
	
	public function Delete($IdFacturaCompra)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFacturaCompra = " . DB::Number($IdFacturaCompra);
		if (!DBAccess::Delete('TB_FacturasCompras', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}


		DBAccess::$db->Commit();
		
		return true;	
	}	
	

	public function ExportXls(array $filter = NULL)
	{
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("Comprobante");
				
		foreach ($arrComprobantes as $oFacturaCompra)
		{	
			/* almacenamos el registro */
			$arrData[] = array(trim($oFacturaCompra->Nombre));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'comprobantes';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$filter['Reportado'] = '0';
		/* obtenemos el listado de datos a exportar */		
		$this->ActualizarFacturasUnidades();		
		$arrFacturasCompras = $this->GetAll($filter);
				
		$oProveedores = new Proveedores();
		$oUnidades = new Unidades();
		$oModelos = new Modelos();
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA",
			"TIPO", 
			"NUMERO",
			"NRO INTERNO",
			"VIN",
			"MODELO",
			"PROVEEDOR",
			"CUIT",
			"IMPORTE NETO",
			"IVA 10,5%",
			"IVA 21%",
			"PERCEPCION IVA",
			"PERCEPCION IIBB",
			"PERCEPCION GANANCIAS",
			"NO GRABADOS",  
			"IMPUESTO INTERNO",  
			"IMPUESTO INTERNO DIESEL",  
			"TOTAL",
			"TIPO");
				
		foreach ($arrFacturasCompras as $oFacturaCompra)
		{	
			$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor);
			$modelo = '';
			$vin = '';
			if ($oFacturaCompra->IdUnidad)
			{
				$oUnidad = $oUnidades->GetById($oFacturaCompra->IdUnidad);
				$vin = $oUnidad->NumeroVin;
				$oModelo = $oModelos->GetById($oUnidad->IdModelo);
				$modelo = $oModelo->DenominacionComercial;
			}
			/* almacenamos el registro */
			$arrData[] = array(
				trim(CambiarFecha($oFacturaCompra->Fecha)),
				trim(ComprobanteTipos::GetDescripcionById($oFacturaCompra->IdComprobanteTipo)),
				trim($oFacturaCompra->Numero), 
				trim($oFacturaCompra->IdUnidad),
				trim($vin),
				trim($modelo),
				trim($oProveedor->Empresa),
				trim($oFacturaCompra->Cuit),
				trim(number_format($oFacturaCompra->ImporteNeto, 2, ',', '.')),
				trim(number_format($oFacturaCompra->Iva10, 2, ',', '.')),
				trim(number_format($oFacturaCompra->Iva21, 2, ',', '.')),
				trim(number_format($oFacturaCompra->PercepcionIva ? $oFacturaCompra->PercepcionIva : $oUnidad->PercepcionIva, 2, ',', '.')),
				trim(number_format($oFacturaCompra->PercepcionIB, 2, ',', '.')),
				trim(number_format($oFacturaCompra->PercepcionGanancias, 2, ',', '.')),
				trim(number_format($oFacturaCompra->NoGrabados, 2, ',', '.')),
				trim(number_format($oFacturaCompra->ImpuestoInterno, 2, ',', '.')),
				trim(number_format($oFacturaCompra->ImpuestoInternoD, 2, ',', '.')),
				trim(number_format($oFacturaCompra->Total, 2, ',', '.')),
				$oUnidad->Plan ? 'PLAN' : ($oUnidad->VentaEspecial ? 'VE' : 'COMUN'));
				
			$oFacturaCompra->Reportado = 1;
			$this->Update($oFacturaCompra);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'facturas_compras';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function ExportLibroIvaCsv(array $filter = NULL)
	{
		$oProveedores = new Proveedores();
		//$oTiposIva = new TiposIva();
	
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetLibroCompras($filter);
				
		$arrData = array();
		
		$arrData[] = array(
			"ACTION MOTORSPORTS S.R.L.");
		
		$arrData[] = array(
			"Av. Del Libertador 2275, Olivos");
		$arrData[] = array(
			"Venta de autos, camionetas y utilitarios, nuevos");
		$arrData[] = array(
			"30-71194065-7");
			
		$arrData[] = array(
			"DESDE " . $filter['FechaDesde'] . " HASTA " . $filter['FechaHasta']);
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"Fecha", 
			"Tipo", 
			"Numero", 
			"Razon Social", 
			"Condicion", 
			"Cuit", 
			"Neto Grav.", 			
			"IVA 21%",
			"IVA 27%",
			"IVA 10,5%", 
			"Perc. IB", 
			"Percep.", 
			"No Grav.",  
			"Imp. Int..",  
			"T. Comprobante");
				
		foreach ($arrComprobantes as $oFacturaCompra)
		{	
			$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor);
			//$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
			
			/* almacenamos el registro */
			$arrData[] = array(
				trim(str_replace('-', '/', CambiarFecha($oFacturaCompra->Fecha))),
				trim(ComprobanteTipos::GetTipoById($oFacturaCompra->IdComprobanteTipo) . "C"),
				trim(ComprobanteTipos::GetLetraById($oFacturaCompra->IdComprobanteTipo) . $oFacturaCompra->Numero),
				trim($oProveedor->Empresa), 
				trim('RI'),
				trim(substr_replace(substr_replace(str_replace('-', '', $oProveedor->Cuit), '-', 10, 0), '-', 2, 0)),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->ImporteNeto, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->Iva21, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->Iva27, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->Iva10, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->PercepcionIB, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->PercepcionIva, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->NoGrabados, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format(0, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oFacturaCompra->IdComprobanteTipo) . number_format($oFacturaCompra->Total, 2, ',', '.'))
				);
		}
		
		$oTotales = $this->GetLibroComprasTotales($filter, $oPage);
		
		$arrData[] = array(
			"Neto Gravado: " . number_format($oTotales->NetoGravado, 2, ',', '.'));
		/*$arrData[] = array(
			"Neto Gravado 10.50: " . number_format($oTotales->Iva10 * 100 / 10.5, 2, ',', '.'));
		$arrData[] = array(
			"Neto Gravado 21.00: " . number_format($oTotales->Iva21 * 100 / 21, 2, ',', '.'));
		$arrData[] = array(
			"Neto Gravado 27.00: " . number_format($oTotales->Iva27 * 100 /27, 2, ',', '.'));*/
		$arrData[] = array(
			"IVA 10.50: " . number_format($oTotales->Iva10, 2, ',', '.'));
		$arrData[] = array(
			"IVA 21.00: " . number_format($oTotales->Iva21, 2, ',', '.'));
		$arrData[] = array(
			"IVA 27.00: " . number_format($oTotales->Iva27, 2, ',', '.'));		
		$arrData[] = array(
			"Exento: " . number_format(0, 2, ',', '.'));
		$arrData[] = array(
			"No Gravado: " . number_format($oTotales->NoGrabados, 2, ',', '.'));
		$arrData[] = array(
			"Imp. Interno: " . number_format(0, 2, ',', '.'));
		$arrData[] = array(
			"Percep. IVA: " . number_format($oTotales->PercepcionIva, 2, ',', '.'));
		$arrData[] = array(
			"Percep. IB: " . number_format($oTotales->PercepcionIB, 2, ',', '.'));
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'libro iva compras';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function GenerarArchivoCiti($filter)
	{
		$oProveedores = new Proveedores();
		
		$arrData = $this->GetLibroCompras($filter);
		
		$SaltoLinea = "\r\n";
		
		$txt = '';
		
		$FileName = "LIBRO COMPRAS.txt";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
		
		foreach ($arrData as $oFacturaCompra)
		{
			$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor);
			$Cuit = str_replace('-', '', $oProveedor->Cuit);
				
			if ($txt != '')
				$txt.= $SaltoLinea;
			
			$txt.= str_replace('-', '', $oFacturaCompra->Fecha);
			$txt.= ComprobanteTipos::GetTipoCitiById($oFacturaCompra->IdComprobanteTipo);
			
			$arrNumero = explode('-', $oFacturaCompra->Numero);
			$txt.= str_pad($arrNumero[0], 5, '0', STR_PAD_LEFT);
			$txt.= str_pad($arrNumero[1], 20, '0', STR_PAD_LEFT);
			$txt.= str_pad(' ', 16, ' ', STR_PAD_RIGHT);
			$txt.= "80";
			$txt.= str_pad($Cuit, 20, '0', STR_PAD_LEFT);
			$txt.= str_pad(substr($oProveedor->Empresa, 0, 30), 30, ' ', STR_PAD_RIGHT);
			$txt.= str_pad(number_format($oFacturaCompra->Total, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oFacturaCompra->NoGrabados, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format(0, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oFacturaCompra->PercepcionIva, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oFacturaCompra->PercepcionGanancias, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oFacturaCompra->PercepcionIB + $oFacturaCompra->PercepcionIBCABA, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format(0, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format($oFacturaCompra->ImpuestoInterno + $oFacturaCompra->ImpuestoInternoD, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= 'PES';
			$txt.= str_pad(number_format(1, 6, '', ''), 10, '0', STR_PAD_LEFT);
			
			$CantidadAlicuota = 0;
			if ($oFacturaCompra->Iva10 && $oFacturaCompra->Iva10 != 0)
				$CantidadAlicuota++;
			if ($oFacturaCompra->Iva21 && $oFacturaCompra->Iva21 != 0)
				$CantidadAlicuota++;
			if ($oFacturaCompra->Iva25 && $oFacturaCompra->Iva25 != 0)
				$CantidadAlicuota++;
			if ($oFacturaCompra->Iva27 && $oFacturaCompra->Iva27 != 0)
				$CantidadAlicuota++;
			$txt.= $CantidadAlicuota;
			
			if ($oFacturaCompra->IdComprobanteTipo == ComprobanteTipos::FacturaC || $oFacturaCompra->IdComprobanteTipo == ComprobanteTipos::NotaCreditoC || $oFacturaCompra->IdComprobanteTipo == ComprobanteTipos::NotaDebitoC || $oFacturaCompra->IdComprobanteTipo == ComprobanteTipos::ReciboC)
				$txt.= 'N';
			elseif (!$oFacturaCompra->ImporteNeto || $oFacturaCompra->ImporteNeto == 0)
				$txt.= 'N';
			else
				$txt.= '0';
			
			$txt.= str_pad(number_format(0, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad(number_format(0, 2, '', ''), 15, '0', STR_PAD_LEFT);
			$txt.= str_pad('0', 11, '0', STR_PAD_LEFT);
			$txt.= str_pad(' ', 30, ' ', STR_PAD_LEFT);
			$txt.= str_pad(number_format(0, 2, '', ''), 15, '0', STR_PAD_LEFT);
			
		}
		
		print_r($txt);
	}
	
	public function GenerarArchivoCitiAlicuotas($filter)
	{
		$oProveedores = new Proveedores();
		
		$arrData = $this->GetLibroCompras($filter);
		
		$SaltoLinea = "\r\n";
		
		$txt = '';
		
		$FileName = "LIBRO COMPRAS ALICUOTAS.txt";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
		
		foreach ($arrData as $oFacturaCompra)
		{
			$oProveedor = $oProveedores->GetById($oFacturaCompra->IdProveedor);
			$Cuit = str_replace('-', '', $oProveedor->Cuit);
			
			$arrNumero = explode('-', $oFacturaCompra->Numero);
			if ($oFacturaCompra->Iva10 && $oFacturaCompra->Iva10 != 0)
			{	
				if ($txt != '')
					$txt.= $SaltoLinea;
				
				$txt.= ComprobanteTipos::GetTipoCitiById($oFacturaCompra->IdComprobanteTipo);
				
				$txt.= str_pad($arrNumero[0], 5, '0', STR_PAD_LEFT);
				$txt.= str_pad($arrNumero[1], 20, '0', STR_PAD_LEFT);
				$txt.= "80";
				$txt.= str_pad($Cuit, 20, '0', STR_PAD_LEFT);
				$txt.= str_pad(number_format($oFacturaCompra->Iva10 / 0.105, 2, '', ''), 15, '0', STR_PAD_LEFT);
				$txt.= '0004';
				$txt.= str_pad(number_format($oFacturaCompra->Iva10, 2, '', ''), 15, '0', STR_PAD_LEFT);
			}
			
			if ($oFacturaCompra->Iva21 && $oFacturaCompra->Iva21 != 0)
			{	
				if ($txt != '')
					$txt.= $SaltoLinea;
				
				$txt.= ComprobanteTipos::GetTipoCitiById($oFacturaCompra->IdComprobanteTipo);
				
				$txt.= str_pad($arrNumero[0], 5, '0', STR_PAD_LEFT);
				$txt.= str_pad($arrNumero[1], 20, '0', STR_PAD_LEFT);
				$txt.= "80";
				$txt.= str_pad($Cuit, 20, '0', STR_PAD_LEFT);
				$txt.= str_pad(number_format($oFacturaCompra->Iva21 / 0.21, 2, '', ''), 15, '0', STR_PAD_LEFT);
				$txt.= '0005';
				$txt.= str_pad(number_format($oFacturaCompra->Iva21, 2, '', ''), 15, '0', STR_PAD_LEFT);
			}
			if ($oFacturaCompra->Iva25 && $oFacturaCompra->Iva25 != 0)
			{	
				if ($txt != '')
					$txt.= $SaltoLinea;
				
				$txt.= ComprobanteTipos::GetTipoCitiById($oFacturaCompra->IdComprobanteTipo);
				
				$txt.= str_pad($arrNumero[0], 5, '0', STR_PAD_LEFT);
				$txt.= str_pad($arrNumero[1], 20, '0', STR_PAD_LEFT);
				$txt.= "80";
				$txt.= str_pad($Cuit, 20, '0', STR_PAD_LEFT);
				$txt.= str_pad(number_format($oFacturaCompra->Iva25 / 0.25, 2, '', ''), 15, '0', STR_PAD_LEFT);
				$txt.= '0005';
				$txt.= str_pad(number_format($oFacturaCompra->Iva25, 2, '', ''), 15, '0', STR_PAD_LEFT);
			}
			if ($oFacturaCompra->Iva27 && $oFacturaCompra->Iva27 != 0)
			{	
				if ($txt != '')
					$txt.= $SaltoLinea;
				
				$txt.= ComprobanteTipos::GetTipoCitiById($oFacturaCompra->IdComprobanteTipo);
				
				$txt.= str_pad($arrNumero[0], 5, '0', STR_PAD_LEFT);
				$txt.= str_pad($arrNumero[1], 20, '0', STR_PAD_LEFT);
				$txt.= "80";
				$txt.= str_pad($Cuit, 20, '0', STR_PAD_LEFT);
				$txt.= str_pad(number_format($oFacturaCompra->Iva27 / 0.27, 2, '', ''), 15, '0', STR_PAD_LEFT);
				$txt.= '0006';
				$txt.= str_pad(number_format($oFacturaCompra->Iva27, 2, '', ''), 15, '0', STR_PAD_LEFT);
			}
		}
		
		print_r($txt);
	}
}

?>