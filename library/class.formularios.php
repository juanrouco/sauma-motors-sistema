<?php 

require_once('class.dbaccess.php');
require_once('class.formulario.php');
require_once('class.formularioestados.php');
require_once('class.tiposformulario.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('fpdf/fpdf.php');
require_once('excel_export/class.xlsexport.php');


class Formularios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdTipoFormulario'])) && ($filter['IdTipoFormulario'] != ''))
			$sql.= " AND IdTipoFormulario = " . DB::Number($filter['IdTipoFormulario']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdTipoFormulario, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormulario = new Formulario();
			$oFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oFormulario);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetAllByGestoria(Gestoria $oGestoria)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdGestoria = " . DB::Number($oGestoria->IdGestoria);
		$sql.= " ORDER BY IdFormulario ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormulario = new Formulario();
			$oFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oFormulario);
			
			$oRes->MoveNext();
		}

		return $arr;
	}


	public function GetAllByIdTipoFormulario($IdTipoFormulario)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);
		$sql.= " ORDER BY IdFormulario ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormulario = new Formulario();
			$oFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oFormulario);
			
			$oRes->MoveNext();
		}

		return $arr;
	}


	public function GetAllForDeclaracionJurada($IdTipoFormulario)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);
		$sql.= " AND (IdDeclaracion = '' OR IdDeclaracion IS NULL OR IdGestoria = '' OR IdGestoria IS NULL)";
		$sql.= " ORDER BY IdFormulario ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormulario = new Formulario();
			$oFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oFormulario);
			
			$oRes->MoveNext();
		}

		return $arr;
	}


	public function GetAllByDeclaracionJurada(DeclaracionJurada $oDeclaracionJurada)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdDeclaracion = " . DB::Number($oDeclaracionJurada->IdDeclaracionJurada);
		$sql.= " ORDER BY IdFormulario ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oFormulario = new Formulario();
			$oFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oFormulario);
			
			$oRes->MoveNext();
		}

		return $arr;
	}


	public function GetById($IdFormulario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdFormulario = " . DB::Number($IdFormulario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormulario = new Formulario();
		$oFormulario->ParseFromArray($oRow);
		
		return $oFormulario;		
	}
	

	public function GetByIdGestoriaIdTipoFormulario($IdGestoria, $IdTipoFormulario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdGestoria = " . DB::Number($IdGestoria);	
		$sql.= " AND IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormulario = new Formulario();
		$oFormulario->ParseFromArray($oRow);
		
		return $oFormulario;		
	}


	public function GetByNumero($IdTipoFormulario, $Numero)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
		$sql.= " AND Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormulario = new Formulario();
		$oFormulario->ParseFromArray($oRow);
		
		return $oFormulario;		
	}


	public function GetNext($IdTipoFormulario)
	{
		$sql = "SELECT IdFormulario, IdTipoFormulario, MIN(Numero) AS Numero";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
		$sql.= " AND IdEstado = " . DB::Number(FormularioEstados::Libre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormulario = new Formulario();
		$oFormulario->ParseFromArray($oRow);
		
		return $oFormulario;		
	}


	public function GetNextCargaLote($IdTipoFormulario)
	{
		$sql = "SELECT IdFormulario, IdTipoFormulario, MAX(Numero) AS Numero";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFormulario = new Formulario();
		$oFormulario->ParseFromArray($oRow);
		
		return $oFormulario;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Formularios";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	

	public function CheckLoteLibre($IdTipoFormulario, $NumeroDesde, $NumeroHasta)
	{
		$sql = "SELECT MIN(Numero) AS MinimoUtilizado,";
		$sql.= " MAX(Numero) AS MaximoUtilizado";
		$sql.= " FROM TB_Formularios";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
		$sql.= " AND Numero >= " . DB::Number($NumeroDesde);	
		$sql.= " AND Numero <= " . DB::Number($NumeroHasta);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return true;

		return $oRow;
	}

	
	public function CreateLote($IdTipoFormulario, $NumeroDesde, $NumeroHasta)
	{
		$oTiposFormulario = new TiposFormulario();
		
		if (!DBAccess::$db->Begin())		
			return false;

		/* obtenemos los datos del tipo de formulario */
		if (!$oTipoFormulario = $oTiposFormulario->GetById($IdTipoFormulario))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		$NumeroDesde = (int)$NumeroDesde;
		$NumeroHasta = (int)$NumeroHasta;
		
		for ($j=$NumeroDesde; $j<=$NumeroHasta; $j++)
		{
			/* armamos el numero */
			$Numero = '';
			for ($k=1; $k<=$oTipoFormulario->Longitud; $k++)
			{
				if ($k > strlen($j))
				{
					$Numero.= '0';
				}
			}
			$Numero.= $j;
			
			/* cremaos el objeto */
			$oFormulario = new Formulario();
			
			$oFormulario->IdTipoFormulario 	= $IdTipoFormulario;
			$oFormulario->Numero 			= $Numero;
			$oFormulario->IdEstado 			= FormularioEstados::Libre;
							
			if (!$this->Create($oFormulario))
			{
				DBAccess::$db->Rollback();	
				return false;
			}
		}

		DBAccess::$db->Commit();
		
		return true;
	}
	
	
	public function Create(Formulario $oFormulario)
	{
		$arr = array
		(
			'IdTipoFormulario' 	=> DB::Number($oFormulario->IdTipoFormulario),
			'Numero' 			=> DB::String($oFormulario->Numero),
			'IdEstado' 			=> DB::Number($oFormulario->IdEstado)
		);
		
		if (!$this->Insert('TB_Formularios', $arr))
			return false;

		/* asignamos el id generado */
		$oFormulario->IdFormulario = DBAccess::GetLastInsertId();
			
		return $oFormulario;
	}
	
	
	public function Update(Formulario $oFormulario)
	{
		$where = " IdFormulario = " . DB::Number($oFormulario->IdFormulario);
		
		$arr = array
		(
			'IdGestoria' 	=> DB::Number($oFormulario->IdGestoria),
			'IdDeclaracion' => DB::Number($oFormulario->IdDeclaracion),
			'Fecha' 		=> DB::Date($oFormulario->Fecha),
			'IdEstado' 		=> DB::Number($oFormulario->IdEstado)
		);
		
		if (!DBAccess::Update('TB_Formularios', $arr, $where))
			return false;
		
		return $oFormulario;
	}
	

	public function LiberarByGestoria(Gestoria $oGestoria)
	{
		$where = " IdGestoria = " . DB::Number($oGestoria->IdGestoria);

		$arr = array
		(
			'IdGestoria' 	=> 'NULL',
			'Fecha' 		=> 'NULL',
			'IdEstado' 		=> DB::Number(FormularioEstados::Libre)
		);

		if (!DBAccess::Update('TB_Formularios', $arr, $where))
			return false;

		return true;
	}


	public function ExportXls(array $filter = NULL)
	{
		/* obtenemos el listado de datos a exportar */			
		$arrFormularios = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("Formulario");
				
		foreach ($arrFormularios as $oFormulario)
		{	
			/* almacenamos el registro */
			$arrData[] = array(trim($oFormulario->Nombre));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'formularios';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}


	public function ExportToPDF($IdFormulario, $OutputPdf = 'formulario.pdf')
	{
		$oFormularios 		= new Formularios();
		$oFacturaUnidades 	= new FacturaUnidades();
		$oComprobantes 		= new Comprobantes();
		$oGestorias 		= new Gestorias();
		$oMinutas 			= new Minutas();
		$oClientes 			= new Clientes();
		$oUnidades 			= new Unidades();
		$oModelos 			= new Modelos();
		$oLocalidades 		= new Localidades();
		$oPartidos 			= new Partidos();
		$oProvincias 		= new Provincias();
		$oPaises 			= new Paises();
		$oMarcas 			= new Marcas();
		$oTiposModelo 		= new TiposModelo();
		$oPrendas 			= new Prendas();

		/* obtenemos los datos del formulario */
		if (!$oFormulario = $oFormularios->GetById($IdFormulario))
			exit();
		
		/* obtenemos los datos de la gestoria */
		if (!$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria))
			exit();
		
		/* obtenemos los datos de la venta */
		if (!$oMinuta = $oMinutas->GetById($oGestoria->IdMinuta))
			exit();
		
		/* obtenemos los datos de la factura */
		$oFacturaUnidad = $oFacturaUnidades->GetById($oGestoria->IdMinuta);
		
		/* obtenemos los datos del comprobante de pago */
		$oComprobante = $oComprobantes->GetById($oFacturaUnidad->IdComprobante);
		
		/* obtenemos los datos de la unidad */
		if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
			exit();
		
		/* obtenemos los datos del modelo */
		if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
			exit();
		
		/* obtenemos los datos de la marca del vehiculo */
		if (!$oMarcaVehiculo = $oMarcas->GetById($oModelo->IdMarcaVehiculo))
			exit();
		
		/* obtenemos los datos de la marca del motor */
		if (!$oMarcaMotor = $oMarcas->GetById($oModelo->IdMarcaMotor))
			exit();
		
		/* obtenemos los datos de la marca del chasis */
		if (!$oMarcaChasis = $oMarcas->GetById($oModelo->IdMarcaChasis))
			exit();
		
		/* obtenemos los datos del tipo de modelo */
		if (!$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo))
			exit();
		
		/* obtenemos los datos del cliente */
		if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
			exit();
		
		/* obtenemos la nacionalidad */
		$oNacionalidad = $oPaises->GetById($oCliente->IdNacionalidad);
		
		/* obtenemos los datos de la localidad */
		$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
		
		/* obtenemos los datos del partido */
		$oPartido = $oPartidos->GetById($oLocalidad->IdPartido);
		
		/* obtenemos los datos de la provincia */
		$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);
		
		/* obtenemos informacion del condominio en caso de que existiera */
		$oClienteCondominio 	= $oClientes->GetById($oGestoria->IdClienteCondominio);
		$oLocalidadCondominio 	= $oLocalidades->GetById($oClienteCondominio->DomicilioIdLocalidad);
		$oPartidoCondominio 	= $oPartidos->GetById($oLocalidadCondominio->IdPartido);
		$oProvinciaCondominio 	= $oProvincias->GetById($oLocalidadCondominio->IdProvincia);
		
		/* obtenemos los datos de la prenda en caso de que existiera */
		$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria);
		
		/* armamos el detalle del comprobante */
		$Comprobante = ComprobanteTipos::GetById($oComprobante->IdTipoComprobante) . '/' . $oComprobante->Prefijo . '-' . $oComprobante->Numero;
		
		switch ($oFormulario->IdTipoFormulario)
		{
			case TipoFormulario::Formulario01Importado:
				$FileName = 'gestorias_pdf_1.php?IdFormulario=' . $IdFormulario;
				break;
		
			case TipoFormulario::Formulario01Nacional:
				$FileName = 'gestorias_pdf_2.php?IdFormulario=' . $IdFormulario;
				break;
				
			case TipoFormulario::TituloAutomotor:

				/* comenzamos la creacion del archivo pdf */
				$oPdf = new FPDF('P', 'cm', 'A4');
				
				$oPdf->AddPage();
				
				$oPdf->SetFont('Arial', '', 8);
				
				/* Identificacion del Titular */
				$oPdf->Text(1.5, 11.5, $oCliente->RazonSocial);
				$oPdf->Text(5.5, 13, number_format($oGestoria->PorcentajeTitularidad, 2));
				$oPdf->Text(9, 13, $oNacionalidad->Nombre);
				$oPdf->Text(4.5, 14.2, $oCliente->DocumentoNumero);
				$oPdf->Text(9.5, 14.2, $oCliente->DocumentoExpedido);
				$oPdf->Text(17.3, 14.2, date("d", strtotime($oCliente->FechaNacimiento)));
				$oPdf->Text(18.2, 14.2, date("m", strtotime($oCliente->FechaNacimiento)));
				$oPdf->Text(19.1, 14.2, substr(date("Y", strtotime($oCliente->FechaNacimiento)), 2, 2));
				$oPdf->Text(3, 15.2, $oCliente->EnteJuridicoOtorgacion . ' | ' . $oCliente->EnteJuridicoDatosInscripcion);
				$oPdf->Text(3, 16.4, $oCliente->DomicilioCalle);
				$oPdf->Text(18, 16.4, $oCliente->DomicilioNumero);
				$oPdf->Text(3.3, 17.5, $oCliente->DomicilioPiso);
				$oPdf->Text(4.8, 17.5, $oCliente->DomicilioDpto);
				$oPdf->Text(7, 17.5, $oLocalidad->Nombre);
				$oPdf->Text(3, 18.5, $oPartido->Nombre);
				$oPdf->Text(11, 18.5, $oLocalidad->CodigoPostal);
				$oPdf->Text(13.5, 18.5, $oProvincia->Nombre);
				$oPdf->Text(2.9, 19, $oCliente->Nupcia);
				$oPdf->Text(4.5, 19, $oCliente->ConyugeApellido . ' ' . $oCliente->ConyugeNombre);
				
				if (($oGestoria->PorcentajeTitularidad) < 100 && ($oGestoria->IdClienteCondominio != ''))
				{
					$oPdf->Text(0.5, 12.6, 'X');
				}
				else
				{
					$oPdf->Text(0.5, 13.1, 'X');
				}
				
				if ($oCliente->DocumentoTipo == TipoDocumento::DNI)
					$oPdf->Text(0.5, 15, 'X');
				if ($oCliente->DocumentoTipo == TipoDocumento::LE)
					$oPdf->Text(0.5, 15.5, 'X');
				if ($oCliente->DocumentoTipo == TipoDocumento::CI)
					$oPdf->Text(0.5, 16, 'X');
				if ($oCliente->DocumentoTipo == TipoDocumento::LC)
					$oPdf->Text(0.5, 16.5, 'X');
				if ($oCliente->DocumentoTipo == TipoDocumento::PA)
					$oPdf->Text(0.5, 17, 'X');
				
				if ($oCliente->IdEstadoCivil == EstadoCivil::Soltero)
					$oPdf->Text(0.5, 18, 'X');
				elseif ($oCliente->IdEstadoCivil == EstadoCivil::Casado)
					$oPdf->Text(0.5, 18.5, 'X');
				elseif ($oCliente->IdEstadoCivil == EstadoCivil::Viudo)
					$oPdf->Text(0.5, 19, 'X');
				elseif ($oCliente->IdEstadoCivil == EstadoCivil::Divorciado)
					$oPdf->Text(0.5, 19.5, 'X');
				
				/* forma de adquisicion */
				$oPdf->Text(6.7, 21, '1');
				if ($oPrenda)
				{
					$oPdf->Text(16.5, 21, 'X');
				}
				else
				{
					$oPdf->Text(17, 21, 'X');
				}
				$oPdf->Text(3, 22, number_format($oFacturaUnidad->Total));
				$oPdf->Text(7.5, 22, CambiarFecha($oFacturaUnidad->Fecha));
				$oPdf->Text(10.5, 22, $Comprobante . ' ' . $oDatosEmpresa->RazonSocial);
				
				if ($oPrenda)
				{
					$oPdf->Text(3, 23.1, number_format($oPrenda->FinanciacionCapital));
					$oPdf->Text(7.5, 23.1, $oPrenda->CantidadCuotas);
					$oPdf->Text(9.5, 23.1, CambiarFecha($oPrenda->FechaVencimientoPrimerCuota));
					$oPdf->Text(15.5, 23.1, number_format($oPrenda->ImporteCuota));
				}
				
				$oPdf->AddPage();
				
				$oPdf->SetFont('Arial', '', 8);
				
				/* Titular */
				$oPdf->Text(3, 9, $oCliente->RazonSocial);

				break;
			
			case TipoFormulario::Formulario12:
				$FileName = 'gestorias_pdf_4.php?IdFormulario=' . $IdFormulario;
				break;
			
			case TipoFormulario::Formulario13ACapital:
				$FileName = 'gestorias_pdf_6.php?IdFormulario=' . $IdFormulario;
				break;
			
			case TipoFormulario::Formulario13AProvincia:
				$FileName = 'gestorias_pdf_5.php?IdFormulario=' . $IdFormulario;
				break;
		
			case TipoFormulario::Formulario03:
				$FileName = 'gestorias_pdf_7.php?IdFormulario=' . $IdFormulario;
				break;
		
			case TipoFormulario::ContratoPrenda:
				$FileName = 'gestorias_pdf_8.php?IdFormulario=' . $IdFormulario;
				break;
		
			case TipoFormulario::ContratoPrendaStandardBank:
				$FileName = 'gestorias_pdf_9.php?IdFormulario=' . $IdFormulario;
				break;
		}

	  	$oPdf->Output($OutputPdf);
	}
}

?>