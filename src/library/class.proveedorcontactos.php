<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.proveedorcontacto.php');

class ProveedorContactos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		if ($filter['Apellido'] != "")
		{	
			$sql.= " AND (u.Apellido RLIKE '" . DB::StringUnquoted($filter['Apellido']) . "'";
		$sql.= " OR u.Apellido IS NULL)";
		}
		
		if ($filter['Nombre'] != "")
		{	
			$sql.= " AND (u.Nombre RLIKE '" . DB::StringUnquoted($filter['Nombre']) . "'";
		$sql.= " OR u.Nombre IS NULL)";
		}
		if ($filter['Email'] != "")
		{	
			$sql.= " AND (u.Email RLIKE '" . DB::StringUnquoted($filter['Email']) . "'";
			$sql.= " OR u.Email IS NULL)";
		}
		
		if ($filter['IdDepartamento'] != "")
		{
			$sql.= " AND u.IdDepartamento = " . DB::Number($filter['IdDepartamento']);
		}
		
		if ($filter['IdProveedor'] != "")
		{
			$sql.= " AND u.IdProveedor = " . DB::Number($filter['IdProveedor']);
		}

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM tblProveedorContactos u";
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
	
		
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM tblProveedorContactos u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " ORDER BY u.Apellido, u.Nombre";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oProveedorContacto = new ProveedorContacto();
			$oProveedorContacto->ParseFromArray($oRow);
			
			
			array_push($arr, $oProveedorContacto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetById($IdContacto)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM tblProveedorContactos u";
		$sql.= " WHERE u.IdContacto = " . DB::Number($IdContacto);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedorContacto = new ProveedorContacto();
		$oProveedorContacto->ParseFromArray($oRow);

		
		return $oProveedorContacto;		
	}



	public function GetByLogin($ProveedorContacto)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM tblProveedorContactos u";
		$sql.= " WHERE Email = " . DB::String($oProveedorContacto);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedorContacto = new ProveedorContacto();
		$oProveedorContacto->ParseFromArray($oRow);
		
		return $oProveedorContacto;		
	}

	
	public function GetByEmail($Email)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM tblProveedorContactos u";
		$sql.= " WHERE Email = " . DB::String($Email);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oProveedorContacto = new oProveedorContacto();
		$oProveedorContacto->ParseFromArray($oRow);
		
		return $oProveedorContacto;		
	}
	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT u.*";
		$sql.= " FROM tblProveedorContactos u";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		$sql.= " GROUP BY u.IdContacto";
		$sql.= " ORDER BY u.Apellido";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(ProveedorContacto $oProveedorContacto)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdProveedor'				=> DB::Number($oProveedorContacto->IdProveedor),			
			'Apellido'					=> DB::String($oProveedorContacto->Apellido),
			'Nombre'					=> DB::String($oProveedorContacto->Nombre),
			'IdDepartamento'			=> DB::Number($oProveedorContacto->IdDepartamento),
			'IdCargo'					=> DB::Number($oProveedorContacto->IdCargo),			
			'TelefonoCodigoArea'		=> DB::String($oProveedorContacto->TelefonoCodigoArea),
			'Telefono'					=> DB::String($oProveedorContacto->Telefono),
			'TelefonoCodigoArea2'		=> DB::String($oProveedorContacto->TelefonoCodigoArea2),
			'Telefono2'					=> DB::String($oProveedorContacto->Telefono2),
			'PINBlackberry'				=> DB::String($oProveedorContacto->PINBlackberry),
			'Email'						=> DB::String($oProveedorContacto->Email),
			'Email2'					=> DB::String($oProveedorContacto->Email2),
			'IdSkype'					=> DB::String($oProveedorContacto->IdSkype),			
			'Msn'						=> DB::String($oProveedorContacto->Msn),
			'FechaNacimiento'			=> DB::Date($oProveedorContacto->FechaNacimiento),
			'IdEstadoCivil'				=> DB::String($oProveedorContacto->IdEstadoCivil),	
			'Observaciones'				=> DB::String($oProveedorContacto->Observaciones)
		);

		if (!DBAccess::Insert('tblProveedorContactos', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oProveedorContacto->IdContacto = DBAccess::GetLastInsertId();		
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oProveedorContacto;
	}
	
	
	public function Update(ProveedorContacto $oProveedorContacto)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdProveedor'				=> DB::Number($oProveedorContacto->IdProveedor),			
			'Apellido'					=> DB::String($oProveedorContacto->Apellido),
			'Nombre'					=> DB::String($oProveedorContacto->Nombre),
			'IdDepartamento'			=> DB::Number($oProveedorContacto->IdDepartamento),
			'IdCargo'					=> DB::Number($oProveedorContacto->IdCargo),			
			'TelefonoCodigoArea'		=> DB::String($oProveedorContacto->TelefonoCodigoArea),
			'Telefono'					=> DB::String($oProveedorContacto->Telefono),
			'TelefonoCodigoArea2'		=> DB::String($oProveedorContacto->TelefonoCodigoArea2),
			'Telefono2'					=> DB::String($oProveedorContacto->Telefono2),
			'PINBlackberry'				=> DB::String($oProveedorContacto->PINBlackberry),
			'Email'						=> DB::String($oProveedorContacto->Email),
			'Email2'					=> DB::String($oProveedorContacto->Email2),
			'IdSkype'					=> DB::String($oProveedorContacto->IdSkype),			
			'Msn'						=> DB::String($oProveedorContacto->Msn),
			'FechaNacimiento'			=> DB::Date($oProveedorContacto->FechaNacimiento),
			'IdEstadoCivil'				=> DB::String($oProveedorContacto->IdEstadoCivil),	
			'Observaciones'				=> DB::String($oProveedorContacto->Observaciones)
		);

		$where = " IdContacto = " . (int)$oProveedorContacto->IdContacto;
		
		if (!DBAccess::Update('tblProveedorContactos', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oProveedor;
	}
	
	public function Delete($IdContacto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdContacto = " . DB::Number($IdContacto);
		if (!DBAccess::Delete('tblProveedorContactos', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
	
	
	
	public function Validar($Email)
	{	
		$ValidEmail = new SMTP_validateEmail();
		
		$Sender		= Config::CorreoAdministrador;
		$arrEmail 	= array($Email);

		$Valid = $ValidEmail->validate($arrEmail, $Sender);

		if ($Valid)
			return true;
		
		return false;
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
		$csv.= "Localidad";
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
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->Localidad));
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