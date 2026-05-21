<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulo.php');
require_once('class.articulostocks.php');
require_once('class.articulostock.php');
require_once('class.stockmovimiento.php');
require_once('class.stockmovimientos.php');
require_once('class.proveedores.php');
require_once('class.ivas.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_reader/class.xlsreader.php');

class Articulos extends DBAccess implements IFilterable
{
	const PathImport = '../_recursos/archivos/';

	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		if ($filter['Codigo'] != null && $filter['Codigo'] != "")
		{	
			$sql.= " AND a.Codigo RLIKE '" . DB::StringUnquoted($filter['Codigo']) . "'";			
		}
				
		if ($filter['Descripcion'] != null && $filter['Descripcion'] != "")
		{	
			$arrDescripcion = explode(' ', $filter['Descripcion']);
			$sql.= " AND (a.Descripcion RLIKE '" . DB::StringUnquoted($filter['Descripcion']) . "' OR (1";
			foreach ($arrDescripcion as $desc)
				$sql.= " AND a.Descripcion RLIKE '" . DB::StringUnquoted($desc) . "'";
			$sql.= ") OR a.Descripcion IS NULL)";
		}
		
		if ($filter['IdProveedor'] != null && $filter['IdProveedor'] != "")
		{	
			$sql.= " AND a.IdProveedor = " . DB::Number($filter['IdProveedor']);
		}
		
		if ($filter['NotIdProveedor'] != null && $filter['NotIdProveedor'] != "")
		{	
			$sql.= " AND a.IdProveedor <> " . DB::Number($filter['NotIdProveedor']);
		}
		
		if ($filter['ClasePieza'] != null && $filter['ClasePieza'] != "")
		{	
			$sql.= " AND a.ClasePieza = '" . DB::StringUnquoted($filter['ClasePieza']) . "'";
		}
		
		if ($filter['ConStock'] != null && $filter['ConStock'] != "")
		{	
			$sql.= " AND ass.StockActual > 0";
		}
		
		if ($filter['IdUbicacion'] != null && $filter['IdUbicacion'] != "")
		{	
			if ($filter['AlertaStockMinimo'] != null && $filter['AlertaStockMinimo'] != "")
				$sql.= " AND (ass.IdUbicacion IS NULL OR ass.IdUbicacion = " . DB::Number($filter['IdUbicacion']) . ")";
			else
				$sql.= " AND (ass.IdUbicacion = " . DB::Number($filter['IdUbicacion']) . ")";
		}
		
		if ($filter['AlertaStockMinimo'] != null && $filter['AlertaStockMinimo'] != "")
		{	
			$sql.= " AND (a.StockMaximo > 0 OR a.StockMinimo > 0) AND (a.StockMinimo >= ass.StockActual OR ass.StockActual IS NULL) ";
		}
		
		if ($filter['IdOrdenTrabajoTarea'] != null && $filter['IdOrdenTrabajoTarea'] != "")
		{	
			$sql.= " AND a.IdArticulo IN (SELECT cd.IdArticulo FROM TB_Compras c INNER JOIN TB_CompraDetalles cd ON c.IdCompra = cd.IdCompra WHERE c.IdOrdenTrabajoTarea = " . DB::Number($filter['IdOrdenTrabajoTarea']) . ")";
		}

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Articulos a";
		$sql.= " LEFT JOIN TB_Proveedores u ON a.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		$sql.= " GROUP BY a.IdArticulo";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
			
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " LEFT JOIN TB_Proveedores u ON a.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		$sql.= " GROUP BY a.IdArticulo";
		$sql.= " ORDER BY a.Codigo";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oArticulo = new Articulo();
			$oArticulo->ParseFromArray($oRow);
			
			
			array_push($arr, $oArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetAllReporte(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " INNER JOIN TB_Proveedores u ON a.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		$sql.= " GROUP BY a.IdArticulo";
		$sql.= " ORDER BY a.Codigo";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oArticulo = new Articulo();
			$oArticulo->ParseFromArray($oRow);			
			
			array_push($arr, $oArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetTotalReporte(array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->StockTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = " SELECT SUM(ass.StockActual) AS StockTotal, SUM(ass.StockActual * a.PrecioLista) AS CostoTotal";
		$sql.= " FROM TB_Articulos a";
		$sql.= " INNER JOIN TB_Proveedores u ON a.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		$sql.= " ORDER BY a.Codigo";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->StockTotal = $oRow['StockTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];

		return $oReporteTotal;
	}
	
	public function GetTotalReporteRotacion($TipoMovimiento, array $filter = NULL)
	{
		$oReporteTotal = new stdClass();
		$oReporteTotal->StockTotal = 0;
		$oReporteTotal->CostoTotal = 0;
		
		$sql = " SELECT SUM(ass.StockActual) AS StockTotal, SUM(ass.StockActual * a.PrecioLista) AS CostoTotal";
		$sql.= " FROM TB_Articulos a";
		$sql.= " INNER JOIN TB_Proveedores u ON a.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";		
		$sql.= " WHERE 1";
		
		if ($TipoMovimiento == 1)
		{
			$sql.= " AND a.IdArticulo IN (SELECT cd.IdArticulo FROM tb_Compras c INNER JOIN tb_CompraDetalles cd ON c.IdCompra = cd.IdCompra WHERE c.FechaCarga >= DATE_SUB(NOW(), INTERVAL 6 MONTH))";
		}
		elseif ($TipoMovimiento == 2)
		{
			$sql.= " AND a.IdArticulo NOT IN (SELECT cd.IdArticulo FROM tb_Compras c INNER JOIN tb_CompraDetalles cd ON c.IdCompra = cd.IdCompra WHERE c.FechaCarga >= DATE_SUB(NOW(), INTERVAL 6 MONTH))";
			//$sql.= " AND a.IdArticulo IN (SELECT cd.IdArticulo FROM tb_Compras c INNER JOIN tb_CompraDetalles cd ON c.IdCompra = cd.IdCompra WHERE c.FechaCarga >= DATE_SUB(NOW(), INTERVAL 12 MONTH))";
			
		}

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		$sql.= " ORDER BY a.Codigo";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oReporteTotal->StockTotal = $oRow['StockTotal'];
		$oReporteTotal->CostoTotal = $oRow['CostoTotal'];

		return $oReporteTotal;
	}
	
	public function GetAllByProveedor(Proveedor $oProveedor)
	{
		$arr = array();
	
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " WHERE a.IdProveedor = " . DB::Number($oProveedor->IdProveedor);
		$sql.= " ORDER BY a.Descripcion";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oArticulo = new Articulo();
			$oArticulo->ParseFromArray($oRow);
			
			array_push($arr, $oArticulo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdArticulo)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " WHERE a.IdArticulo = " . DB::Number($IdArticulo);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oArticulo = new Articulo();
		$oArticulo->ParseFromArray($oRow);

		
		return $oArticulo;		
	}
	
	public function GetByCodigo($CodigoArticulo)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " WHERE a.Codigo = " . DB::String($CodigoArticulo);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oArticulo = new Articulo();
		$oArticulo->ParseFromArray($oRow);

		
		return $oArticulo;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_Articulos a";
		$sql.= " LEFT JOIN TB_ArticuloStocks ass ON a.IdArticulo = ass.IdArticulo";
		$sql.= " WHERE 1";		
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY a.IdArticulo";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Articulo $oArticulo)
	{
		$arr = array
		(
			'Codigo'					=> DB::String($oArticulo->Codigo),
			'Descripcion'				=> DB::String($oArticulo->Descripcion),
			'Reemplazo'					=> DB::String($oArticulo->Reemplazo),
			'PrecioCompra'				=> DB::Number($oArticulo->PrecioCompra),
			'PrecioLista'				=> DB::Number($oArticulo->PrecioLista),
			'PrecioOferta'				=> DB::Number($oArticulo->PrecioOferta),
			'PrecioTerceros'			=> DB::Number($oArticulo->PrecioTerceros),
			'IdProveedor'				=> DB::Number($oArticulo->IdProveedor),
			'UnidadVenta'				=> DB::Number($oArticulo->UnidadVenta),			
			'ClasePieza'				=> DB::String($oArticulo->ClasePieza),
			'StockMaximo'				=> DB::Number($oArticulo->StockMaximo),
			'StockMinimo'				=> DB::Number($oArticulo->StockMinimo),
			'IdIva'						=> DB::Number($oArticulo->IdIva),
			'Utilidad'					=> DB::String($oArticulo->Utilidad),
			'DescCod'					=> DB::String($oArticulo->DescCod),
			'CodDes'					=> DB::String($oArticulo->CodDes)
		);
		return $arr;
	}
	
	public function Create(Articulo $oArticulo)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oArticulo);

		if (!DBAccess::Insert('TB_Articulos', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oArticulo;
	}
	
	
	public function Update(Articulo $oArticulo)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetArrayDB($oArticulo);

		$where = " IdArticulo = " . (int)$oArticulo->IdArticulo;
		
		if (!DBAccess::Update('TB_Articulos', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oArticulo;
	}
	
	public function Delete($IdArticulo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdArticulo = " . DB::Number($IdArticulo);
		if (!DBAccess::Delete('TB_Articulos', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
		
	public function ExportToPDF($outputPdf = '', array $filter = NULL)
	{
		$pdf = new FPDF('P', 'cm', 'A4');
		$arr = array();
		
		$arr = $this->GetAll($filter);
		$paginaActual = 0;
		$cont = 50;
		$x = 0;
		$y = 3;
		
		foreach ($arr as $oProveedor)
		{	  
			if ($cont == 50)
			{
				$pdf->AddPage();
				$paginaActual++;
				$cont = 0;
				$y = 3;

				$pdf->SetFont('Arial','B', 15);
				$pdf->Text($x + 8, $y - 2, "Reporte de Proveedores");

				$pdf->SetFont('Arial','B', 6.5);				
				$pdf->Text($x + 1, $y - 1, "Apellido y Nombre");
				$pdf->Text($x + 4.8, $y - 1, "Proveedor");
				$pdf->Text($x + 7.4, $y - 1, "Cuit / Cuil");
				$pdf->Text($x + 12.5, $y - 1, "Email");
				$pdf->Text($x + 16.6, $y - 1, "Telefono");
				
				$pdf->SetFont('Arial','B', 9);
				$pdf->Text($x + 18.5, $y + 25.6, "Pagina ".$paginaActual);				
			}

			$pdf->SetFont('Arial', 'B', 6.1);
			$pdf->Text($x + 1, $y, $oProveedor->Apellido . ", " . $oProveedor->Nombre);
			$pdf->Text($x + 4.8, $y, $oProveedor->Proveedor);
			$pdf->Text($x + 7.4, $y, $oProveedor->CuitCuil);
			$pdf->Text($x + 12.5, $y, $oProveedor->Email);
			$pdf->Text($x + 16.6, $y, $oProveedor->Telefono);
			
			$cont++;
			$y+=0.5;
		}
		
		$pdf->Output($outputPdf);	
	}
	
	
	public function ExportCsv(array $filter = NULL)
	{
		$Proveedores = new Proveedores();
		$ArticuloStocks = new ArticuloStocks();
		$Ivas			= new Ivas();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "Repuestos.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrArticulos = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Codigo";
		$csv.= $Separador;
		$csv.= "Descripcion";
		$csv.= $Separador;
		$csv.= "Proveedor";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Precio Dealer";
		$csv.= $Separador;
		$csv.= "Sugerido (s/IVA)";
		$csv.= $Separador;
		$csv.= "Sugerido (c/IVA)";
		$csv.= $Separador;
		$csv.= "Stock";		
		$csv.= $SaltoLinea;
	
		foreach ($arrArticulos as $oArticulo)
		{				
			$oProveedor = $Proveedores->GetById($oArticulo->IdProveedor);
			$oArticuloStock = $ArticuloStocks->GetAllByArticulo($oArticulo);
			$oIva			= $Ivas->GetById($oArticulo->IdIva);
			$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
			
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Empresa));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticuloStock[0]->Ubicacion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->PrecioTerceros));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->PrecioLista));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($precioConIva, 2)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->StockTotal()));			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$Proveedores = new Proveedores();
		$ArticuloStocks = new ArticuloStocks();
		$Ivas			= new Ivas();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "Repuestos_reporte_stock.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrArticulos = $this->GetAllReporte($filter);
		$oReporteTotal 	= $this->GetTotalReporte($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
		
		$csv.= "Datos Totales";
		$csv.= $SaltoLinea;
		$csv.= "Stock Total de Repuestos";
		$csv.= $Separador;
		$csv.= $oReporteTotal->StockTotal;
		$csv.= $SaltoLinea;
		$csv.= "Valorización de Stock";
		$csv.= $Separador;
		$csv.= number_format($oReporteTotal->CostoTotal, 2);
		$csv.= $SaltoLinea;
				
		$csv.= "Codigo";
		$csv.= $Separador;
		$csv.= "Descripcion";
		$csv.= $Separador;
		$csv.= "Proveedor";
		$csv.= $Separador;
		$csv.= "Ubicacion";
		$csv.= $Separador;
		$csv.= "Precio Compra";
		$csv.= $Separador;
		$csv.= "Precio Dealer";
		$csv.= $Separador;
		$csv.= "Sugerido (s/IVA)";
		$csv.= $Separador;
		$csv.= "Sugerido (c/IVA)";
		$csv.= $Separador;
		$csv.= "Stock";		
		$csv.= $SaltoLinea;
	
		foreach ($arrArticulos as $oArticulo)
		{				
			$oProveedor = $Proveedores->GetById($oArticulo->IdProveedor);
			$oArticuloStock = $ArticuloStocks->GetAllByArticulo($oArticulo);
			$oIva			= $Ivas->GetById($oArticulo->IdIva);
			$precioConIva 	= $oArticulo->PrecioLista * ($oIva->Alicuota + 1);
			$stock = $oArticulo->StockTotal();
			if ($filter['IdUbicacion'])
			{
				foreach ($oArticulo->Stocks as $oStock)
				{
					if ($oStock->IdUbicacion == $filter['IdUbicacion'])
						$stock = $oStock->StockActual;
				}
			}
			
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Codigo));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Empresa));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticuloStock[0]->Ubicacion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->PrecioCompra));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->PrecioTerceros));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oArticulo->PrecioLista));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim(number_format($precioConIva, 2)));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($stock));			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
	
	public function Import($file)
	{
		if (!DBAccess::$db->Begin())		
			return false;
			
		$handle = fopen(Articulos::PathImport . $file, "r");
		$mensaje = 'Los siguientes codigos no fueron hayados: ';
		$header = false;
		while(!feof($handle)) { 
            $linea = fgets($handle);
			
			$Articulos = str_getcsv($linea, ';');
			//print_r($Articulos);exit;
			if (!$header)
			{
			$header = true;
			}
			else
			{
            $Codigo = trim($Articulos[0]);
			$Descripcion = trim($Articulos[1]);
			$PrecioLista = str_replace(',', '.', str_replace('.', '', trim($Articulos[3])));
			$PrecioLista = floatval($PrecioLista) / 1.21;
			$PrecioTerceros = str_replace(',', '.', str_replace('.', '', trim($Articulos[2])));
			$PrecioTerceros = floatval($PrecioTerceros);
			/*if ($Articulos[2] != '0'){
			print_r($PrecioTerceros);exit;}*/
			
			$oArticuloRepetido = $this->GetByCodigo($Codigo);
			if ($oArticuloRepetido)
			{
				//$oArticuloRepetido->PrecioOferta = $PrecioLista - $Descuento;
				//$oArticuloRepetido->PrecioLista = floatval($PrecioLista) / 1.21;
				//$oArticuloRepetido->PrecioCompra = $PrecioLista;
				$oArticuloRepetido->PrecioTerceros = $PrecioTerceros;
				$oArticuloRepetido->PrecioLista = $PrecioLista;
				$oArticuloRepetido->PrecioCompra = $PrecioLista * 1.21;
				$this->Update($oArticuloRepetido);
			}
			else
			{
				$oArticuloNuevo = new Articulo();
				$oArticuloNuevo->Codigo = $Codigo;
				
				$oArticuloNuevo->Descripcion = trim($Descripcion);
				$oArticuloNuevo->PrecioLista = $PrecioLista;
				$oArticuloNuevo->PrecioCompra = $PrecioLista * 1.21;
				$oArticuloNuevo->PrecioTerceros = $PrecioTerceros;
				//$oArticuloNuevo->PrecioOferta = $PrecioLista - $Descuento;
				$oArticuloNuevo->IdProveedor = 108;
				$oArticuloNuevo->UnidadVenta = 1;
				$oArticuloNuevo->IdIva = 1;
				$oArticuloNuevo->Reemplazo = $Reemplazo;
				
				$this->Create($oArticuloNuevo);
				
			}
			}
        }
		DBAccess::$db->Commit();
        fclose($handle);
		return $mensaje;
	}
	
	public function Ajustar($FileName)
	{
		/* declaramos variables necesarias */
		
		/* processamos el archivo */		 
		//return false;
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;
		$Cantidad = 0;

		/* procesamos el archivo */
		$Row = 1;
		try
		{
		$strError = '';
		
		$StockMovimientos	= new StockMovimientos();
		$ArticuloStocks		= new ArticuloStocks();
		
			
		$handle = fopen(Articulos::PathImport . $FileName, "r");
		$mensaje = 'Los siguientes codigos no fueron hayados: ';
		$header = false;
		while(!feof($handle)) { 
            $linea = fgets($handle);
			$Articulo = str_getcsv($linea, ';');
			// print_r($Articulo);exit;
			if (!$header)
			{
			$header = true;
			}
			else
			{
			// print_r($Articulo);exit;
			$err						= 0;			
			$Codigo			 			= trim($Articulo[0]);
			$Diferencia	 				= intval(trim($Articulo[7]));
			$Ubicacion	 				= trim($Articulo[7]);
			
			if (($Codigo != ''))
			{			
				/*print_r($Codigo);
				print_r(' - '. $Diferencia);exit;*/
				if ($err == 0)
				{
					if ($oArticulo = $this->GetByCodigo($Codigo))
					{
						$oStockMovimiento	= new StockMovimiento();
						
						$Fecha = date("Y-m-d");
						$Fecha = CambiarFecha($Fecha);
						
						
						$oArticuloStocks = $ArticuloStocks->GetByArticuloAndUbicacion($oArticulo->IdArticulo, 3);
						if ($oArticuloStocks)
							$Diferencia = $Diferencia - $oArticuloStocks->StockActual;
						
						$oStockMovimiento->IdArticulo 		= $oArticulo->IdArticulo;
						$oStockMovimiento->IdUbicacion 		= 3;
						$oStockMovimiento->Remito			= '';
						$oStockMovimiento->Fecha			= $Fecha;
						$oStockMovimiento->Cantidad			= $Diferencia;
						$oStockMovimiento->Observaciones	= 'Ajuste de stock al ' . date('d/m/Y');
						
						if (!$oArticuloStocks)
						{
							$oArticuloStocks = new ArticuloStock();
							$oArticuloStocks->IdArticulo = $oArticulo->IdArticulo;
							$oArticuloStocks->IdUbicacion = 3;
							$oArticuloStocks->StockInicial = 0;
							$oArticuloStocks->StockActual = 0;
						}
						
						$oArticuloStocks->AumentarStock($Diferencia);
						// $oArticuloStocks->Ubicacion = $Ubicacion;
						
						if ($oArticuloStocks->IdArticuloStock)
							$oArticuloStocks = $ArticuloStocks->Update($oArticuloStocks);
						else
							$oArticuloStocks = $ArticuloStocks->Create($oArticuloStocks);
						
						/* crea el proveedor */
						$oStockMovimiento = $StockMovimientos->Create($oStockMovimiento);
						$CountCreate++;
						$Cantidad += intval($Diferencia);
					}
					else
					{
						print_R('No encontrado: '.$Codigo.'<br>');
						}
						
				}
				else
				{
								
				}					
				
				$Row++;
			}
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
		
			$strError.= "<br> Se ajustaron " . $CountCreate . " articulos, siendo la cantidad " . $Cantidad . ".";		
			
		
		return $strError;
	}
}

?>