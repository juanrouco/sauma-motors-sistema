<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulo.php');
require_once('class.ubicacion.php');
require_once('class.articulostock.php');
require_once('class.filter.php');
require_once('class.page.php');

class ArticuloStocks extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		if ($filter['IdArticulo'] != null && $filter['IdArticulo'] != "")
		{	
			$sql.= " AND ass.IdArticulo = " . DB::Number($filter['IdArticulo']);			
		}
		
		if ($filter['IdUbicacion'] != null && $filter['IdUbicacion'] != "")
		{	
			$sql.= " AND ass.IdUbicacion = " . DB::Number($filter['IdUbicacion']);
		}

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " INNER JOIN TB_Articulos a ON ass.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ass.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
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
		$sql = " SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " INNER JOIN TB_Articulos a ON ass.IdArticulo = a.IdArticulo";
		$sql.= " INNER JOIN TB_Ubicaciones u ON ass.IdUbicacion = u.IdUbicacion";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Nombre";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oArticuloStock = new ArticuloStock();
			$oArticuloStock->ParseFromArray($oRow);
			
			
			array_push($arr, $oArticuloStock);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByUbicacion(Ubicacion $oUbicacion)
	{
		$arr = array();
	
		$sql = " SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " WHERE ass.IdUbicacion = " . DB::Number($oUbicacion->IdUbicacion);		
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oArticuloStock = new ArticuloStock();
			$oArticuloStock->ParseFromArray($oRow);
			
			array_push($arr, $oArticuloStock);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllByArticulo(Articulo $oArticulo)
	{
		$arr = array();
	
		$sql = "SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " WHERE ass.IdArticulo = " . DB::Number($oArticulo->IdArticulo);		
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oArticuloStock = new ArticuloStock();
			$oArticuloStock->ParseFromArray($oRow);
			
			array_push($arr, $oArticuloStock);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdArticuloStock)
	{
		$sql = " SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " WHERE ass.IdArticuloStock = " . DB::Number($IdArticuloStock);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oArticuloStock = new ArticuloStock();
		$oArticuloStock->ParseFromArray($oRow);

		
		return $oArticuloStock;		
	}
	
	public function GetByArticuloAndUbicacion($IdArticulo, $IdUbicacion)
	{
		$sql = " SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " WHERE ass.IdArticulo = " . DB::Number($IdArticulo);	
		$sql.= " AND ass.IdUbicacion = " . DB::Number($IdUbicacion);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oArticuloStock = new ArticuloStock();
		$oArticuloStock->ParseFromArray($oRow);

		
		return $oArticuloStock;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT ass.*";
		$sql.= " FROM TB_ArticuloStocks ass";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(ArticuloStock $oArticuloStock)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdArticulo'				=> DB::Number($oArticuloStock->IdArticulo),
			'IdUbicacion'				=> DB::Number($oArticuloStock->IdUbicacion),
			'Ubicacion'					=> DB::String($oArticuloStock->Ubicacion),
			'StockInicial'				=> DB::Number($oArticuloStock->StockInicial),
			'StockActual'				=> DB::Number($oArticuloStock->StockActual)	
		);

		if (!DBAccess::Insert('TB_ArticuloStocks', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oArticulo;
	}
	
	
	public function Update(ArticuloStock $oArticuloStock)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdArticulo'				=> DB::Number($oArticuloStock->IdArticulo),
			'IdUbicacion'				=> DB::Number($oArticuloStock->IdUbicacion),
			'Ubicacion'					=> DB::String($oArticuloStock->Ubicacion),
			'StockInicial'				=> DB::Number($oArticuloStock->StockInicial),
			'StockActual'				=> DB::Number($oArticuloStock->StockActual)	
		);

		$where = " IdArticuloStock = " . (int)$oArticuloStock->IdArticuloStock;
		
		if (!DBAccess::Update('TB_ArticuloStocks', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oArticuloStock;
	}
	/*
	public function ChangePassword(Proveedor $oProveedor)
	{
		$where = " IdProveedor = " . (int)$oProveedor->IdProveedor;
		
		$arr = array('Contrasenia' => DB::String(md5($oProveedor->Contrasenia)));
		
		if (!DBAccess::Update('TB_Proveedores', $arr, $where))
			return false;
		
		return $oProveedor;
	}
	*/
	
	public function Delete($IdArticuloStock)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdArticuloStock = " . DB::Number($IdArticuloStock);
		if (!DBAccess::Delete('TB_ArticuloStocks', $where))
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
		$oListaTipos = new ListaTipos();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "Proveedores.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$Proveedores = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "Apellido";
		$csv.= $Separador;
		$csv.= "Nombre";
		$csv.= $Separador;
		$csv.= "Cod. Area";
		$csv.= $Separador;
		$csv.= "Telefono";
		$csv.= $Separador;
		$csv.= "Cod. Area";
		$csv.= $Separador;
		$csv.= "Fax";
		$csv.= $Separador;
		$csv.= "Email";
		$csv.= $Separador;
		$csv.= "Calle";
		$csv.= $Separador;
		$csv.= "Numero";
		$csv.= $Separador;
		$csv.= "Piso";
		$csv.= $Separador;
		$csv.= "Dpto";
		$csv.= $Separador;
		$csv.= "IdPais";
		$csv.= $Separador;
		$csv.= "IdProvincia";
		$csv.= $Separador;
		$csv.= "IdPartido";
		$csv.= $Separador;
		$csv.= "IdLocalidad";
		$csv.= $Separador;
		$csv.= "Codigo postal";
		$csv.= $Separador;
		$csv.= "Empresa";
		$csv.= $Separador;
		$csv.= "CUIT/CUIL";
		$csv.= $Separador;
		$csv.= "Tipo de lista";
		$csv.= $Separador;
		$csv.= "Newsletter";
		$csv.= $Separador;
		$csv.= "Grupos";
		$csv.= $SaltoLinea;
	
		foreach ($Proveedores as $oProveedor)
		{				
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Apellido));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Nombre));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->TelefonoCodigoArea));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Telefono));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->FaxCodigoArea));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Fax));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Email));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->DomicilioCalle));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->DomicilioNumero));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->DomicilioPiso));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->DomicilioDpto));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->IdPais));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->IdProvincia));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->IdPartido));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->IdLocalidad));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->CodigoPostal));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Empresa));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->CuitCuil));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', $oListaTipos->GetById($oProveedor->IdTipoLista));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Newsletter));
			$csv.= $Separador;
			
			if ($oProveedor->Newsletter == '1')
			{
				$Grupos = $oProveedor->GetAllGrupos();
				
				$IdGrupos = '';
				
				foreach ($Grupos as $oGrupo)
				{
					$IdGrupos.= $oGrupo->IdGrupo . ",";
				}
				
				if (isset($IdGrupos) && ($IdGrupos != ""))
				{
					$IdGrupos = substr($IdGrupos, 0, -1);
										
					$csv.= str_replace('(\t|\n)','', $IdGrupos);					
				}
			}
			
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>