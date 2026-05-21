<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.cliente.php');
require_once('class.personatipos.php');
require_once('class.tiposdocumento.php');
require_once('class.tiposiva.php');
require_once('class.profesiones.php');
require_once('class.estadosciviles.php');
require_once('class.usuarios.php');
require_once('class.localidades.php');
require_once('class.paises.php');
require_once('class.provincias.php');
require_once('class.tiposdocumento.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.unidad.php');
require_once('class.estadosciviles.php');
require_once('class.clientemigracion.php');
require_once('class.clientesmigracion.php');
require_once('class.padronesarba.php');
require_once('excel_export/class.xlsexport.php');


class Clientes extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['RazonSocial'])) && ($filter['RazonSocial'] != ''))
			$sql.= " AND RazonSocial LIKE '%" . DB::StringUnquoted($filter['RazonSocial']) . "%'";

		if ((isset($filter['Email'])) && ($filter['Email'] != ''))
			$sql.= " AND Email LIKE '%" . DB::StringUnquoted($filter['Email']) . "%'";

		if ((isset($filter['ClaveFiscalNumero'])) && ($filter['ClaveFiscalNumero'] != ''))
			$sql.= " AND ClaveFiscalNumero LIKE '%" . DB::StringUnquoted($filter['ClaveFiscalNumero']) . "%'";

		if ((isset($filter['IdTipoPersona'])) && ($filter['IdTipoPersona'] != ''))
			$sql.= " AND IdTipoPersona = " . DB::Number($filter['IdTipoPersona']);
		
		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdCliente IN (SELECT IdCliente FROM TB_Minutas WHERE IdMinuta = " . DB::Number($filter['IdMinuta']) . ")";

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY RazonSocial";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByPais(Pais $oPais)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdNacionalidad = " . DB::Number($oPais->IdPais);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE ";
		$sql.= " (";
		$sql.= " 	DomicilioIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);
		$sql.= " 	OR ";		
		$sql.= " 	NacimientoIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);
		$sql.= " )";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByTipoDocumento(TipoDocumento $oTipoDocumento)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE ";
		$sql.= " (";
		$sql.= " 	DocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);
		$sql.= " 	OR ";		
		$sql.= " 	ConyugeDocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);
		$sql.= " )";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}

	public function GetAllByUsuario(Usuario $oUsuario)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdVendedor = " . DB::Number($oUsuario->IdUsuario);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByTipoIva(TipoIva $oTipoIva)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdTipoIva = " . DB::Number($oTipoIva->IdTipoIva);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByProfesion(Profesion $oProfesion)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdProfesion = " . DB::Number($oProfesion->IdProfesion);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByEstadoCivil(EstadoCivil $oEstadoCivil)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdEstadoCivil = " . DB::Number($oEstadoCivil->IdEstadoCivil);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oCliente = new Cliente();
			$oCliente->ParseFromArray($oRow);
			
			array_push($arr, $oCliente);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}

	
	public function GetById($IdCliente)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdCliente = " . DB::Number($IdCliente);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCliente = new Cliente();
		$oCliente->ParseFromArray($oRow);
		
		//$this->ActualizarPercepciones($oCliente);

		return $oCliente;		
	}
	
	public function ActualizarPercepciones($oCliente)
	{
		$oPadronesArba = new PadronesArba();
		
		if ($oCliente->IdTipoIva == TipoIva::RI)
		{
			if (!$oCliente->PercepcionIIBB || $oCliente->FechaHastaPercepcion < date('Y-m-d'))
			{
				$oPadronArba = null;//$oPadronesArba->GetByIdCliente($oCliente->IdCliente, date('d-m-Y'));
				if (!$oPadronArba || $oPadronArba->CUIL != str_replace('-', '', $oCliente->ClaveFiscalNumero))
				{
					if ($oPadronArba = $oPadronesArba->GetByCUIL(str_replace('-', '', $oCliente->ClaveFiscalNumero), date('d-m-Y')))
					{
						$oCliente->PercepcionIIBB = $oPadronArba->Percepcion;
						$oCliente->FechaHastaPercepcion = $oPadronArba->FechaHasta;
						$oPadronArba->IdCliente = $oCliente->IdCliente;
						$oPadronesArba->Update($oPadronArba);
					}
				}
				else
				{
					$oCliente->PercepcionIIBB = $oPadronArba->Percepcion;
					$oCliente->FechaHastaPercepcion = $oPadronArba->FechaHasta;
				}
				$this->Update($oCliente);
			}
		}
	}
	
	public function GetByIdMigracion($IdClienteMigracion)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE IdCliente IN (SELECT IdCliente FROM TB_ClientesMigracion WHERE IdAntiguo = " . DB::Number($IdClienteMigracion) . ")";
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCliente = new Cliente();
		$oCliente->ParseFromArray($oRow);

		return $oCliente;		
	}


	public function GetByDocumentoNumero($DocumentoNumero)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE DocumentoNumero = " . DB::String($DocumentoNumero);	
		$sql.= " AND DocumentoNumero <> '0'";	
		$sql.= " AND DocumentoNumero <> ''";	
		$sql.= " AND DocumentoNumero IS NOT NULL";	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCliente = new Cliente();
		$oCliente->ParseFromArray($oRow);

		return $oCliente;		
	}


	public function GetByConyugeDocumentoNumero($ConyugeDocumentoNumero)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE ConyugeDocumentoNumero = " . DB::String($ConyugeDocumentoNumero);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCliente = new Cliente();
		$oCliente->ParseFromArray($oRow);

		return $oCliente;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Clientes";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetDBArray(Cliente $oCliente)
	{
		$arr = array
		(
			'IdTipoPersona'					=> DB::Number($oCliente->IdTipoPersona),
			'RazonSocial'					=> DB::String($oCliente->RazonSocial),
			'DomicilioCalle'				=> DB::String($oCliente->DomicilioCalle),
			'DomicilioNumero'				=> DB::String($oCliente->DomicilioNumero),
			'DomicilioPiso'					=> DB::String($oCliente->DomicilioPiso),
			'DomicilioDpto'					=> DB::String($oCliente->DomicilioDpto),
			'DomicilioIdLocalidad'			=> DB::Number($oCliente->DomicilioIdLocalidad) == 0 ? 'NULL' : DB::Number($oCliente->DomicilioIdLocalidad),
			'DomicilioCodigoPostal'			=> DB::String($oCliente->DomicilioCodigoPostal),
			'DomicilioCallePostal'			=> DB::String($oCliente->DomicilioCallePostal),
			'DomicilioNumeroPostal'			=> DB::String($oCliente->DomicilioNumeroPostal),
			'DomicilioPisoPostal'			=> DB::String($oCliente->DomicilioPisoPostal),
			'DomicilioDptoPostal'			=> DB::String($oCliente->DomicilioDptoPostal),
			'DomicilioIdLocalidadPostal'	=> DB::Number($oCliente->DomicilioIdLocalidadPostal) == 0 ? 'NULL' : DB::Number($oCliente->DomicilioIdLocalidadPostal),
			'DomicilioCodigoPostalPostal'	=> DB::String($oCliente->DomicilioCodigoPostalPostal),
			'NacimientoIdLocalidad'			=> DB::Number($oCliente->NacimientoIdLocalidad) == 0 ? 'NULL' : DB::Number($oCliente->NacimientoIdLocalidad),
			'NacimientoCodigoPostal'		=> DB::String($oCliente->NacimientoCodigoPostal),
			'TelefonoCodigoArea'			=> DB::String($oCliente->TelefonoCodigoArea),
			'Telefono'						=> DB::String($oCliente->Telefono),
			'FaxCodigoArea'					=> DB::String($oCliente->FaxCodigoArea),
			'Fax'							=> DB::String($oCliente->Fax),
			'DocumentoTipo'					=> DB::Number($oCliente->DocumentoTipo) == 0 ? 'NULL' : DB::Number($oCliente->DocumentoTipo),
			'DocumentoNumero'				=> DB::String($oCliente->DocumentoNumero),			
			'DocumentoExpedido'				=> DB::String($oCliente->DocumentoExpedido),
			'FechaNacimiento'				=> DB::Date($oCliente->FechaNacimiento),
			'Empresa'						=> DB::String($oCliente->Empresa),
			'ClaveFiscalTipo'				=> DB::Number($oCliente->ClaveFiscalTipo),
			'ClaveFiscalNumero'				=> DB::String($oCliente->ClaveFiscalNumero),
			'Email'							=> DB::String($oCliente->Email),
			'IdVendedor'					=> DB::Number($oCliente->IdVendedor) == 0 ? 'NULL' : DB::Number($oCliente->IdVendedor),
			'IdTipoIva'						=> DB::Number($oCliente->IdTipoIva) == 0 ? 'NULL' : DB::Number($oCliente->IdTipoIva),
			'IdProfesion'					=> DB::Number($oCliente->IdProfesion) == 0 ? 'NULL' : DB::Number($oCliente->IdProfesion),
			'IdNacionalidad'				=> DB::Number($oCliente->IdNacionalidad) == 0 ? 'NULL' : DB::Number($oCliente->IdNacionalidad),
			'IdEstadoCivil'					=> DB::Number($oCliente->IdEstadoCivil) == 0 ? 'NULL' : DB::Number($oCliente->IdEstadoCivil),
			'Nupcia'						=> DB::Number($oCliente->Nupcia),
			'ConyugeNombre'					=> DB::String($oCliente->ConyugeNombre),
			'ConyugeApellido'				=> DB::String($oCliente->ConyugeApellido),
			'ConyugeDocumentoTipo'			=> DB::Number($oCliente->ConyugeDocumentoTipo) == 0 ? 'NULL' : DB::Number($oCliente->ConyugeDocumentoTipo),
			'ConyugeDocumentoNumero'		=> DB::String($oCliente->ConyugeDocumentoNumero),
			'RepresentanteRazonSocial'		=> DB::String($oCliente->RepresentanteRazonSocial),
			'RepresentanteDocumentoTipo'	=> DB::Number($oCliente->RepresentanteDocumentoTipo) == 0 ? 'NULL' : DB::Number($oCliente->RepresentanteDocumentoTipo),
			'RepresentanteDocumentoNumero'	=> DB::String($oCliente->RepresentanteDocumentoNumero),
			'EnteJuridicoOtorgacion'		=> DB::String($oCliente->EnteJuridicoOtorgacion),
			'EnteJuridicoDatosInscripcion'	=> DB::String($oCliente->EnteJuridicoDatosInscripcion),
			'EnteJuridicoFechaInscripcion'	=> DB::Date($oCliente->EnteJuridicoFechaInscripcion),
			'IdAntiguo'						=> DB::Number($oCliente->IdAntiguo),
			'PercepcionIIBB'				=> DB::Number($oCliente->PercepcionIIBB),
			'FechaHastaPercepcion'			=> DB::Date($oCliente->FechaHastaPercepcion)
		);
		return $arr;
	}
	
	public function Create(Cliente $oCliente)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetDBArray($oCliente);
		
		if (!DBAccess::Insert('TB_Clientes', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oCliente->IdCliente = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oCliente;
	}
	
	
	public function Update(Cliente $oCliente)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetDBArray($oCliente);

		$where = " IdCliente = " . (int)$oCliente->IdCliente;
		
		if (!DBAccess::Update('TB_Clientes', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oCliente;
	}
	
	
	public function Delete($IdCliente)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCliente = " . DB::Number($IdCliente);
		if (!DBAccess::Delete('TB_ClienteContactos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		if (!DBAccess::Delete('TB_Clientes', $where))
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
		$oTiposDocumento 	= new TiposDocumento();
		$oTiposIva 			= new TiposIva();
		$oProfesiones 		= new Profesiones();
		$oEstadosCiviles 	= new EstadosCiviles();
		$oUsuarios 			= new Usuarios();

		/* obtenemos el listado de datos a exportar */			
		$arrClientes = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"TIPO DE CLIENTE",
			"RAZON SOCIAL",
			"CODIGO AREA",
			"TELEFONO",
			"CODIGO AREA",
			"FAX",
			"TIPO DOCUMENTO",
			"NUMERO DOCUMENTO",
			"FECHA NACIMIENTO",
			"EMPRESA",
			"CUIT/CUIL",
			"EMAIL",
			"VENDEDOR",
			"CONDICION IVA",
			"PROFESION",
			"ESTADO CIVIL"
		);
				
		foreach ($arrClientes as $oCliente)
		{	
			$oTipoDocumento = $oTiposDocumento->GetById($oCliente->DocumentoTipo);
			$oTipoIva 		= $oTiposIva->GetById($oCliente->IdTipoIva);
			$oProfesion 	= $oProfesiones->GetById($oCliente->IdProfesion);
			$oEstadoCivil 	= $oEstadosCiviles->GetById($oCliente->IdEstadoCivil);
			$oUsuario 		= $oUsuarios->GetById($oCliente->IdVendedor);

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim(PersonaTipos::GetById($oCliente->IdTipoPersona)),
				trim($oCliente->RazonSocial),
				trim($oCliente->TelefonoCodigoArea),
				trim($oCliente->Telefono),
				trim($oCliente->FaxCodigoArea),
				trim($oCliente->Fax),
				trim($oTipoDocumento->Nombre),
				trim($oCliente->DocumentoNumero),
				trim(CambiarFecha($oCliente->FechaNacimiento)),
				trim($oCliente->Empresa),
				trim($oCliente->ClaveFiscalNumero),
				trim($oCliente->Email),
				trim($oUsuario->Nombre . ' ' . $oUsuario->Apellido),
				trim($oTipoIva->Nombre),
				trim($oProfesion->Nombre),
				trim($oEstadoCivil->Nombre),
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'clientes';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ImportTxt($FileName)
	{
		/* declaramos variables necesarias */
		$oPaises = new Paises();
		$oProvincias = new Provincias();
		$oLocalidades = new Localidades();
		$oTiposDocumento = new TiposDocumento();
		$oEstadosCiviles = new EstadosCiviles();
		$oClientesMigracion = new ClientesMigracion();
		
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
			$oProvincia = '';
			$IdNacionalidad = '';
			$DomicilioIdLocalidad = '';
			$IdEstadoCivil = '';
			
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, '|');

			//print_r($Cliente);
			if ($count != 0)
			{
			
				$err						= 0;			
				$IdAntiguo			 		= trim($Cliente[0]);
				$RazonSocial		 		= trim($Cliente[1]);
				$DomicilioCalle				= trim($Cliente[2]);
				$DomicilioNumero			= trim($Cliente[3]);
				$DomicilioPiso				= trim($Cliente[4]);
				$DomicilioDpto				= trim($Cliente[5]);
				$Localidad					= trim($Cliente[6]);
				
				if ($Localidad)
				{
					if ($oLocalidad = $oLocalidades->GetByNombre($Localidad))
						$DomicilioIdLocalidad = $oLocalidad->IdLocalidad;
					else
						print_r($Localidad . '<br />');
				}
				
				$DomicilioCodigoPostal		= trim($Cliente[7]);
				$CodigoProvincia			= trim($Cliente[8]);
				
				if ($CodigoProvincia)
					$oProvincia = $oProvincias->GetByCodigo($CodigoProvincia);
				
				$Telefono					= trim($Cliente[9]);
				$Fax						= trim($Cliente[10]);
				$IdTipoIva					= trim($Cliente[11]);
				
				if ($IdTipoIva)
				{
					if ($IdTipoIva == '4')
						$IdTipoIva = 5;
					elseif ($IdTipoIva == '5')
						$IdTipoIva = 4;
				}
				else
				{
					$IdTipoIva = TipoIva::CF;
				}
				
				$DocumentoTipo				= trim($Cliente[13]);
				
				$IdTipoPersona = PersonaTipos::PersonaFisica;
				if ($DocumentoTipo)
				{
					if ($DocumentoTipo == 'IN')
						$IdTipoPersona = PersonaTipos::PersonaJuridica;
					else
					{
						$oTipoDocumento = $oTiposDocumento->GetByCodigoMigracion($CodigoMigracion);
						$DocumentoTipo = $oTipoDocumento->IdTipoDocumento;
					}
				}
				
				$DocumentoNumero			= trim($Cliente[14]);
				$DocumentoExpedido			= trim($Cliente[15]);
				$FechaNacimiento			= trim($Cliente[16]);
				$ClaveFiscalTipo			= trim($Cliente[17]);
				
				if ($ClaveFiscalTipo == 'CUIL')
					$ClaveFiscalTipo = 2;
				else
					$ClaveFiscalTipo = 1;
				
				$ClaveFiscalNumero			= trim($Cliente[18]);
				$CodigoNacionalidad			= trim($Cliente[19]);
				
				if ($CodigoNacionalidad)
				{
					if ($oNacionalidad = $oPaises->GetByNacionalidad($CodigoNacionalidad))
						$IdNacionalidad = $oNacionalidad->IdNacionalidad;
				}
				
				$CodigoEstadoCivil			= trim($Cliente[20]);
				
				if ($CodigoEstadoCivil)
				{
					if ($oEstadoCivil = $oEstadosCiviles->GetByNombre($CodigoEstadoCivil))
						$IdEstadoCivil = $oEstadoCivil->IdEstadoCivil;
				}
				
				$Nupcia						= trim($Cliente[21]);
				$ConyugeNombre				= trim($Cliente[22]);
				
				$oClienteAux = $this->GetByDocumentoNumero($DocumentoNumero);
				$oClienteMigracion = $oClientesMigracion->GetByIdAntiguo($IdAntiguo);
			
				if (!($IdAntiguo == '' && $RazonSocial == ''))
				{				
					if ($err == 0 && !$oClienteMigracion)
					{
						if (!$oClienteAux)
						{
							$oCliente = new Cliente();
							$oCliente->IdAntiguo 				= $IdAntiguo;
							$oCliente->IdTipoPersona			= $IdTipoPersona;
							$oCliente->IdTipoIva				= $IdTipoIva;
							$oCliente->RazonSocial 				= $RazonSocial;
							$oCliente->DomicilioCalle	 		= $DomicilioCalle;
							$oCliente->DomicilioNumero	 		= $DomicilioNumero;
							$oCliente->DomicilioPiso	 		= $DomicilioPiso;
							$oCliente->DomicilioDpto	 		= $DomicilioDpto;
							$oCliente->DomicilioIdLocalidad		= $DomicilioIdLocalidad;
							$oCliente->DomicilioCodigoPostal	= $DomicilioCodigoPostal;
							$oCliente->Telefono			 		= $Telefono;
							$oCliente->Fax					 	= $Fax;
							$oCliente->DocumentoTipo		 	= $DocumentoTipo;
							$oCliente->DocumentoNumero		 	= $DocumentoNumero;
							$oCliente->DocumentoExpedido		= $DocumentoExpedido;
							$oCliente->FechaNacimiento			= $FechaNacimiento;
							$oCliente->ClaveFiscalTipo			= $ClaveFiscalTipo;
							$oCliente->ClaveFiscalNumero		= $ClaveFiscalNumero;
							$oCliente->IdNacionalidad			= $IdNacionalidad;
							$oCliente->IdEstadoCivil			= $IdEstadoCivil;
							$oCliente->Nupcia					= $Nupcia;
							$oCliente->ConyugeNombre			= $ConyugeNombre;
							
								
							if ($oCliente = $this->Create($oCliente))
							{
								$CountCreate++;
								$FechaImportacion = $Fecha;
							}
						}
						else
							$oCliente = $oClienteAux;
						
						$oClienteMigracion = new ClienteMigracion();
						$oClienteMigracion->IdCliente = $oCliente->IdCliente;
						$oClienteMigracion->IdAntiguo = $IdAntiguo;
						
						$oClientesMigracion->Create($oClienteMigracion);
					}
					else
					{
						if ($err & 1)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el n&uacute;mero de prefijo de Vin es inv&aacute;lido " . $PrefijoVin . ". <br>";
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
}

?>