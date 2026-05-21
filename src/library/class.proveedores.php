<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.pais.php');
require_once('class.proveedor.php');
require_once('class.filter.php');
require_once('class.page.php');

class Proveedores extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		if ($filter['Empresa'] != "")
		{	
			$sql.= " AND (u.Empresa RLIKE '" . DB::StringUnquoted($filter['Empresa']) . "'";
			$sql.= " OR u.Empresa IS NULL)";
		}
		
		if ($filter['Email'] != "")
		{	
			$sql.= " AND (u.Email RLIKE '" . DB::StringUnquoted($filter['Email']) . "'";
			$sql.= " OR u.Email IS NULL)";
		}
		
		if ($filter['IdPais'] != "")
		{	
			$sql.= " AND u.IdPais = " . DB::Number($filter['IdPais']);
		}
		
		if ($filter['IdProvincia'] != "")
		{	
			$sql.= " AND u.IdProvincia = " . DB::Number($filter['IdProvincia']);
		}

		if ($filter['IdPartido'] != "")
		{	
			$sql.= " AND u.IdPartido = " . DB::Number($filter['IdPartido']);
		}
		
		if ($filter['IdLocalidad'] != "")
		{	
			$sql.= " AND u.IdLocalidad = " . DB::Number($filter['IdLocalidad']);
		}

		if ($filter['IdRubro'] != "")
		{
			$sql.= " AND u.IdRubro = " . DB::Number($filter['IdRubro']);
		}
		
		if ($filter['Cuit'] != "")
		{
			$sql.= " AND u.Cuit LIKE '%" . DB::StringUnquoted($filter['Cuit']) . "%'";
		}

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		$sql.= " GROUP BY u.IdProveedor";

		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
	
		/*
	public function GetForSendNewsletter(array $filter)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM tblProveedorGrupos ug";
		$sql.= " LEFT JOIN tb_Proveedores u ON ug.IdProveedor = u.IdProveedor";
		$sql.= " LEFT JOIN tblProvincias pr ON u.IdProvincia = pr.IdProvincia";
		$sql.= " LEFT JOIN tblPaises pa ON u.IdPais = pa.IdPais";
		$sql.= " WHERE 1";



		/* filtro de paises *//*
		if ($filter['Paises'])
		{
			$sql.= " AND u.IdPais IN (";
			foreach ($filter['Paises'] as $IdPais)
			{
				$sql.= DB::Number($IdPais) . ", ";
			}
			$sql.= "0";
			$sql.= ")";
		}

		/* filtro de provincias */
	/*	if ($filter['Provincias'])
		{
			$sql.= " AND u.IdProvincia IN (";
			foreach ($filter['Provincias'] as $IdProvincia)
			{
				$sql.= DB::Number($IdProvincia) . ", ";
			}
			$sql.= "0";
			$sql.= ")";
		}
*/
		/* filtro de newsletter *//*
		if (isset($filter['Newsletter']))
		{
			$sql.= " AND u.Newsletter = " . DB::Bool($filter['Newsletter']);
		}

		$sql.= " GROUP BY u.IdProveedor";

		if (!($oRes = $this->GetQuery($sql)))
			return false;

		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedor = new Proveedor();
			$oProveedor->ParseFromArray($oRow);
			
			array_push($arr, $oProveedor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
		*/
		
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY u.IdProveedor";
		$sql.= " ORDER BY u.Empresa";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedor = new Proveedor();
			$oProveedor->ParseFromArray($oRow);
			
			
			array_push($arr, $oProveedor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByPais(Pais $oPais)
	{
		$arr = array();
	
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE u.IdPais = " . DB::Number($oPais->IdPais);
		$sql.= " GROUP BY u.IdProveedor";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedor = new Proveedor();
			$oProveedor->ParseFromArray($oRow);
			
			array_push($arr, $oProveedor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByProvincia(Provincia $oProvincia)
	{
		$arr = array();
	
		$sql = "SELECT *";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE u.IdProvincia = " . DB::Number($oProvincia->IdProvincia);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedor = new Proveedor();
			$oProveedor->ParseFromArray($oRow);
			
			array_push($arr, $oProveedor);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}
	
	public function GetById($IdProveedor)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE u.IdProveedor = " . DB::Number($IdProveedor);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedor = new Proveedor();
		$oProveedor->ParseFromArray($oRow);

		
		return $oProveedor;		
	}

	public function GetByCUIT($CUIT)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE REPLACE(u.Cuit, '-', '') = " . DB::String(str_replace(array('-', '.', ',', '/'), '', $CUIT));	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedor = new Proveedor();
		$oProveedor->ParseFromArray($oRow);

		
		return $oProveedor;		
	}


	public function GetByLogin($Proveedor)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE Email = " . DB::String($Proveedor);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedor = new Proveedor();
		$oProveedor->ParseFromArray($oRow);
		
		return $oProveedor;		
	}

	
	public function GetByEmail($Email)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE Email = " . DB::String($Email);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedor = new Proveedor();
		$oProveedor->ParseFromArray($oRow);
		
		return $oProveedor;		
	}
	
		public function GetByEmpresa($Empresa)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE u.Empresa  = " . DB::String($Empresa);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedor = new Proveedor();
		$oProveedor->ParseFromArray($oRow);
		
		return $oProveedor;		
	}
	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY u.IdProveedor";
		$sql.= " ORDER BY u.Empresa";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Proveedor $oProveedor)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'Empresa'					=> DB::String($oProveedor->Empresa),
			'IdRubro'					=> DB::Number($oProveedor->IdRubro),
			'TelefonoCodigoArea'		=> DB::String($oProveedor->TelefonoCodigoArea),
			'Telefono'					=> DB::String($oProveedor->Telefono),
			'TelefonoCodigoArea2'		=> DB::String($oProveedor->TelefonoCodigoArea2),
			'Telefono2'					=> DB::String($oProveedor->Telefono2),
			'FaxCodigoArea'				=> DB::String($oProveedor->FaxCodigoArea),
			'Fax'						=> DB::String($oProveedor->Fax),
			'Email'						=> DB::String($oProveedor->Email),
			'Web'						=> DB::String($oProveedor->Web),			
			'DomicilioCalle'			=> DB::String($oProveedor->DomicilioCalle),
			'DomicilioNumero'			=> DB::Number($oProveedor->DomicilioNumero),
			'DomicilioPiso'				=> DB::String($oProveedor->DomicilioPiso),
			'DomicilioDpto'				=> DB::String($oProveedor->DomicilioDpto),
			'IdPais'					=> DB::Number($oProveedor->IdPais),
			'IdProvincia'				=> DB::Number($oProveedor->IdProvincia),
			'IdPartido'					=> DB::Number($oProveedor->IdPartido),
			'IdLocalidad'				=> DB::Number($oProveedor->IdLocalidad),
			'CodigoPostal'				=> DB::String($oProveedor->CodigoPostal),
			'Cuit'						=> DB::String($oProveedor->Cuit),					
			'Observaciones'				=> DB::String($oProveedor->Observaciones)
		);

		if (!DBAccess::Insert('TB_Proveedores', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oProveedor;
	}
	
	
	public function Update(Proveedor $oProveedor)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'Empresa'				=> DB::String($oProveedor->Empresa),
			'IdRubro'				=> DB::Number($oProveedor->IdRubro),
			'TelefonoCodigoArea'	=> DB::String($oProveedor->TelefonoCodigoArea),
			'Telefono'				=> DB::String($oProveedor->Telefono),
			'TelefonoCodigoArea2'	=> DB::String($oProveedor->TelefonoCodigoArea2),
			'Telefono2'				=> DB::String($oProveedor->Telefono2),
			'FaxCodigoArea'			=> DB::String($oProveedor->FaxCodigoArea),
			'Fax'					=> DB::String($oProveedor->Fax),
			'Email'					=> DB::String($oProveedor->Email),
			'Web'					=> DB::String($oProveedor->Web),			
			'DomicilioCalle'		=> DB::String($oProveedor->DomicilioCalle),
			'DomicilioNumero'		=> DB::Number($oProveedor->DomicilioNumero),
			'DomicilioPiso'			=> DB::String($oProveedor->DomicilioPiso),
			'DomicilioDpto'			=> DB::String($oProveedor->DomicilioDpto),
			'IdPais'				=> DB::Number($oProveedor->IdPais),
			'IdProvincia'			=> DB::Number($oProveedor->IdProvincia),
			'IdPartido'				=> DB::Number($oProveedor->IdPartido),
			'IdLocalidad'			=> DB::Number($oProveedor->IdLocalidad),
			'CodigoPostal'			=> DB::String($oProveedor->CodigoPostal),
			'Cuit'					=> DB::String($oProveedor->Cuit),			
			'Observaciones'			=> DB::String($oProveedor->Observaciones)
		);

		$where = " IdProveedor = " . (int)$oProveedor->IdProveedor;
		
		if (!DBAccess::Update('TB_Proveedores', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oProveedor;
	}
	/*
	public function ChangePassword(Proveedor $oProveedor)
	{
		$where = " IdProveedor = " . (int)$oProveedor->IdProveedor;
		
		$arr = array('Contrasenia' => DB::String(md5($oProveedor->Contrasenia)));
		
		if (!DBAccess::Update('tb_Proveedores', $arr, $where))
			return false;
		
		return $oProveedor;
	}
	*/
	
	public function Delete($IdProveedor)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdProveedor = " . DB::Number($IdProveedor);
		if (!DBAccess::Delete('TB_Proveedores', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
	
	public function GetAllByRubro(Rubro $oRubro)
	{
		$arr = array();
	
		$sql = "SELECT *";
		$sql.= " FROM TB_Proveedores u";
		$sql.= " WHERE u.IdRubro = " . DB::Number($oRubro->IdRubro);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedor = new Proveedor();
			$oProveedor->ParseFromArray($oRow);
			
			array_push($arr, $oProveedor);
			
			$oRes->MoveNext();
		}
		
		return $arr;
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