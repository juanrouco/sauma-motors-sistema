<?php 

require_once('class.dbaccess.php');
require_once('class.facturaunidad.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.facturaunidad.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.planillasrecepcion.php');
require_once('class.clientetipos.php');
require_once('class.comprobantes.php');
require_once('class.tiposmodelo.php');
require_once('class.notascredito.php');
require_once('class.filter.php');
require_once('class.page.php');

class FacturaUnidades extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND fu.IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['NumeroComprobante'])) && ($filter['NumeroComprobante'] != ''))
			$sql.= " AND fu.NumeroComprobante LIKE '%" . DB::StringUnquoted($filter['NumeroComprobante']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND c. Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND c.Fecha >= " . DB::Date($filter['FechaDesde']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUnidades fu";
		$sql.= " INNER JOIN TB_Comprobantes c ON fu.IdComprobante = c.IdComprobante";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY fu.IdFactura DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaUnidad = new FacturaUnidad();
			$oFacturaUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUnidades";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUnidad = new FacturaUnidad();
		$oFacturaUnidad->ParseFromArray($oRow);
		
		return $oFacturaUnidad;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUnidades fu";
		$sql.= " INNER JOIN TB_Comprobantes c on fu.IdComprobante = c.IdComprobante";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
		$sql.= " AND c.IdEstado <> " . DB::Number(3);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUnidad = new FacturaUnidad();
		$oFacturaUnidad->ParseFromArray($oRow);
		
		return $oFacturaUnidad;		
	}
	
	public function GetByIdComprobante($IdComprobante)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUnidades fu";
		$sql.= " WHERE fu.IdComprobante = " . DB::Number($IdComprobante);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUnidad = new FacturaUnidad();
		$oFacturaUnidad->ParseFromArray($oRow);
		
		return $oFacturaUnidad;		
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaUnidades";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaUnidad = new FacturaUnidad();
		$oFacturaUnidad->ParseFromArray($oRow);
		
		return $oFacturaUnidad;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_FacturaUnidades fu";
		$sql.= " INNER JOIN TB_Comprobantes c ON fu.IdComprobante = c.IdComprobante";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(FacturaUnidad $oFacturaUnidad)
	{
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oFacturaUnidad->IdMinuta),
			'IdComprobante' 	=> DB::Number($oFacturaUnidad->IdComprobante),
			'NumeroComprobante' => DB::String($oFacturaUnidad->NumeroComprobante),
			'Fecha' 			=> DB::Date($oFacturaUnidad->Fecha),
			'Subtotal' 			=> DB::Number($oFacturaUnidad->Subtotal),
			'Iva10' 			=> DB::Number($oFacturaUnidad->Iva10),
			'Iva21' 			=> DB::Number($oFacturaUnidad->Iva21),
			'ImpuestoInterno'	=> DB::Number($oFacturaUnidad->ImpuestoInterno),
			'Total' 			=> DB::Number($oFacturaUnidad->Total),
			'OtrosTitulares' 	=> DB::String($oFacturaUnidad->OtrosTitulares),
			'Observaciones' 	=> DB::String($oFacturaUnidad->Observaciones)
		);
		
		if (!$this->Insert('TB_FacturaUnidades', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaUnidad->IdFactura = DBAccess::GetLastInsertId();
			
		return $oFacturaUnidad;
	}
	
	
	public function Update(FacturaUnidad $oFacturaUnidad)
	{
		$where = " IdFactura = " . DB::Number($oFacturaUnidad->IdFactura);
		
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oFacturaUnidad->IdMinuta),
			'IdComprobante' 	=> DB::Number($oFacturaUnidad->IdComprobante),
			'NumeroComprobante' => DB::String($oFacturaUnidad->NumeroComprobante),
			'Fecha' 			=> DB::Date($oFacturaUnidad->Fecha),
			'Subtotal' 			=> DB::Number($oFacturaUnidad->Subtotal),
			'Iva10' 			=> DB::Number($oFacturaUnidad->Iva10),
			'Iva21' 			=> DB::Number($oFacturaUnidad->Iva21),
			'ImpuestoInterno'	=> DB::Number($oFacturaUnidad->ImpuestoInterno),
			'Total' 			=> DB::Number($oFacturaUnidad->Total),
			'OtrosTitulares' 	=> DB::String($oFacturaUnidad->OtrosTitulares),
			'Observaciones' 	=> DB::String($oFacturaUnidad->Observaciones)
		);
		
		if (!DBAccess::Update('TB_FacturaUnidades', $arr, $where))
			return false;
		
		return $oFacturaUnidad;
	}
	
	
	public function Delete($IdFactura)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFactura = " . DB::Number($IdFactura);

		if (!DBAccess::Delete('TB_FacturaUnidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	private function Completar($valor, $caracter, $largo)
	{
		$relleno = '';
     
		if (strlen($valor) < $largo)
		{
			$l = intval($largo) - strlen($valor);
			$relleno = str_repeat($caracter, $l);
		}
		return substr($relleno . $valor, 0, $largo);
	}
	
	public function GenerarArchivoFacturacion($oFact)
	{
		$oMinutas 			= new Minutas();
		$oUnidades 			= new Unidades();
		$oClientes 			= new Clientes();
		$oLocalidades		= new Localidades();
		$oProvincias		= new Provincias();
		$oComprobantes		= new Comprobantes();
		$oPlanillasRecepcion = new PlanillasRecepcion();
		
		$SaltoLinea = "\n";
		
		$txt = '';
		
		$FileName = "FACTPUBL.txt";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
		
		$oMinuta 				= $oMinutas->GetById($oFact->IdMinuta);
		$oUnidad 				= $oUnidades->GetById($oMinuta->IdUnidad);
		$oCliente 				= $oClientes->GetById($oMinuta->IdCliente);
		$oLocalidad 			= $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
		$oProvincia 			= $oProvincias->GetById($oLocalidad->IdProvincia);
		$oPlanillaRecepcion		= $oPlanillasRecepcion ->GetById($oUnidad->IdPlanillaRecepcion);
		$oComprobante			= $oComprobantes->GetById($oFact->IdComprobante);
		
		$Concesionario 		= $this->Completar("00244", "0", 5);;
		$CodSucursal	 	= "1";
		$AnioFabricacion	= $oUnidad->NumeroVinPrefijo[9];		
		$Planta				= $oUnidad->NumeroVin[0];
		$NumeroSerie		= substr($oUnidad->NumeroVin, 1, 6);
		$NumeroFactura		= $oFact->NumeroComprobante;
		$TipoDocumento		= "";
		$NumeroDocumento	= "";
		
		if ($oCliente->IdTipoPersona == ClienteTipos::PersonaFisica)
		{
			switch ($oCliente->DocumentoTipo)
			{
				case TipoDocumento::DNI:
					$TipoDocumento = "DNI";
				break;
				case TipoDocumento::CI:
					$TipoDocumento = " CI";
				break;
				case TipoDocumento::LC:
					$TipoDocumento = " LC";
				break;
				case TipoDocumento::LE:
					$TipoDocumento = " LE";
				break;
				case TipoDocumento::PA:
					$TipoDocumento = "   ";
				break;
			}
			$NumeroDocumento = $this->Completar($oCliente->DocumentoNumero, " ", 15);
		}
		else
		{
			$TipoDocumento = "CUI";
			$NumeroDocumento = $this->Completar($oCliente->ClaveFiscalNumero, " ", 15);
		}
		$Apellido 		= $this->Completar("", " ", 30);
		$Nombre 		= $this->Completar($oCliente->RazonSocial, " ", 40);
		$Domicilio		= $this->Completar($oCliente->DomicilioCalle, " ", 40);
		$DomicilioNro	= $this->Completar($oCliente->DomicilioNumero, "0", 5);
		$DomicilioPiso	= $this->Completar($oCliente->DomicilioPiso, "0", 3);
		$DomicilioDpto	= $this->Completar($oCliente->DomicilioDpto, " ", 4);
		$Localidad		= $this->Completar($oLocalidad->Nombre, " ", 25);
		if ($Localidad == 'CIUDAD AUTONOMA DE BUENOS AIRES')
			$Localidad = $this->Completar('CABA', " ", 25);
		$Provincia 		= " ";
		switch ($oProvincia->IdProvincia)
		{
			case 1:
				$Provincia = "B";
			break;
			case 2:
				$Provincia = "C";
			break;
			case 3:
				$Provincia = "K";
			break;
			case 4:
				$Provincia = "H";
			break;
			case 5:
				$Provincia = "U";
			break;				
			case 6:
				$Provincia = "X";
			break;
			case 7:
				$Provincia = "W";
			break;
			case 8:
				$Provincia = "E";
			break;
			case 9:
				$Provincia = "P";
			break;
			case 28:
				$Provincia = "B";
			break;
			case 10:
				$Provincia = "Y";
			break;
			case 11:
				$Provincia = "L";
			break;
			case 12:
				$Provincia = "F";
			break;
			case 13:
				$Provincia = "M";
			break;
			case 14:
				$Provincia = "N";
			break;				
			case 15:
				$Provincia = "Q";
			break;
			case 16:
				$Provincia = "R";
			break;
			case 17:
				$Provincia = "A";
			break;
			case 18:
				$Provincia = "J";
			break;
			case 19:
				$Provincia = "D";
			break;
			case 20:
				$Provincia = "Z";
			break;
			case 21:
				$Provincia = "S";
			break;
			case 22:
				$Provincia = "G";
			break;
			case 23:
				$Provincia = "V";
			break;
			case 24:
				$Provincia = "T";
			break;
		}
		
		$CodigoPostal 	= $this->Completar($oLocalidad->CodigoPostal, "0", 4);
		$PrefijoTel 	= $this->Completar($oCliente->TelefonoCodigoArea, " ", 5);
		$NumeroTel 		= $this->Completar($oCliente->Telefono, " ", 20);
		$TipoIva		= "";
		switch ($oCliente->IdTipoIva)
		{
			case 1:
				$TipoIva = " RI";
			break;
			case 2:
				$TipoIva = "RNI";
			break;
			case 3:
				$TipoIva = " CF";
			break;
			case 4:
				$TipoIva = "MON";
			break;
			case 5:
				$TipoIva = " EX";
			break;
		}
		$Precio		 	= $this->Completar(number_format((float)$oFact->Total, 2, ".", ""), "0", 12);
		$FechaArribo 	= $this->Completar(str_replace("-", "", $oPlanillaRecepcion->FechaRecepcion), " ", 8);
		$FechaFact		= str_replace("-", "", $oFact->Fecha);
		$FechaCanc		= $this->Completar(str_replace("-", "", $oFact->Fecha), " ", 8);
		$FechaReti		= $this->Completar(str_replace("-", "", $oUnidad->FechaRetiro), " ", 8);
		$TipoVenta		= "T";
		
		$txt .= $Concesionario;
		$txt .= $CodSucursal;
		$txt .= $AnioFabricacion;
		$txt .= $Planta;
		$txt .= $NumeroSerie;
		$txt .= $NumeroFactura;
		$txt .= $TipoDocumento;
		$txt .= $NumeroDocumento;
		$txt .= $Apellido;
		$txt .= $Nombre;
		$txt .= $Domicilio;
		$txt .= $DomicilioNro;
		$txt .= $DomicilioPiso;
		$txt .= $DomicilioDpto;
		$txt .= $Localidad;
		$txt .= $Provincia;
		$txt .= $CodigoPostal;
		$txt .= $PrefijoTel;
		$txt .= $NumeroTel;
		$txt .= $TipoIva;
		$txt .= $Precio;
		$txt .= $FechaArribo;
		$txt .= $FechaFact;
		$txt .= $FechaCanc;
		$txt .= $FechaReti;
		$txt .= $TipoVenta;
		$txt .= $SaltoLinea;
		
		print_r($txt);
	}
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$oComprobantes = new Comprobantes();
		$oMinutas 		= new Minutas();
		$oClientes		= new Clientes();
		$oUnidades		= new Unidades();
		$oModelos		= new Modelos();
		$oTiposModelo 	= new TiposModelo();
		$oNotasCredito 	= new NotasCredito();
		
		$arrFacturas = $this->GetAll($filter);
		
		$filterNC = array();
		$filterNC['Unidad'] = 1;
		$filterNC['FechaDesde'] = $filter['FechaDesde'];
		$filterNC['FechaHasta'] = $filter['FechaHasta'];
		$arrNotasCredito = $oNotasCredito->GetAll($filterNC);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIT", 
			"NETO", 
			'IVA 10.5%', 
			'IVA 21%',
			'IMPUESTO INTERNO',
			'TOTAL',
			'CIRCULAR',
			'NRO VIN',
			'MODELO',
			'EQUIPO',
			'TIPO',
			'NRO FACTURA COMPRA',
			'COSTO',
			'NETO',
			'TIPO VENTA');
		
		foreach ($arrFacturas as $oFactura)
		{
			$oComprobante = $oComprobantes->GetById($oFactura->IdComprobante);
			$oMinuta = $oMinutas->GetById($oFactura->IdMinuta);
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);
			
			$subtotal = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno;
			$iva10 = $oComprobante->ImporteIva10;
			$iva21 = $oComprobante->ImporteIva21;
			if (($oComprobante->ImporteIva10 == 0 || $oComprobante->ImporteIva10 == '' || !$oComprobante->ImporteIva10) && ($oComprobante->ImporteIva21 == 0 || $oComprobante->ImporteIva21 == '' || !$oComprobante->ImporteIva21))
			{
				if (($oComprobante->ImporteIva10 == 0 || $oComprobante->ImporteIva10 == '' || !$oComprobante->ImporteIva10) && $oModelo->Iva == 10.5)
				{
					$subtotal = $subtotal / 1.105;
					$iva10 = $subtotal * 0.105;
				}
				elseif (($oComprobante->ImporteIva21 == 0 || $oComprobante->ImporteIva21 == '' || !$oComprobante->ImporteIva21) && $oModelo->Iva == 21)
				{
					$subtotal = $subtotal / 1.21;
					$iva21 = $subtotal * 0.21;
				}
			}
			
			$TipoVenta = 'NORMAL';
			if ($oUnidad->Plan)
				$TipoVenta = 'PLAN';
			
			$arrData[] = array(
				trim(CambiarFecha($oComprobante->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				
				
					
				trim(number_format($subtotal, 2, ',', '.')),
				trim(number_format($iva10, 2, ',', '.')),
				trim(number_format($iva21, 2, ',', '.')),
				trim(number_format($oComprobante->ImpuestoInterno, 2, ',', '.')),
				trim(number_format($oComprobante->Importe, 2, ',', '.')),
				trim(number_format($oMinuta->Circular, 2, ',', '.')),
				trim($oUnidad->NumeroVin),
				trim($oModelo->DenominacionModelo),
				trim($oModelo->DenominacionComercial),
				trim($oTipoModelo->Nombre),
				trim($oUnidad->NumeroFacturaCompra),
				trim(number_format($oUnidad->ImporteCompraBruto, 2, ',', '.')),
				trim(number_format($oUnidad->ImporteCompraNeto, 2, ',', '.')),
				$TipoVenta
				);
		}
				
				
		foreach ($arrNotasCredito as $oNotaCredito)
		{
			$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante);
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			$oFactura = $this->GetByIdComprobante($oNotaCredito->IdFactura);
			$oMinuta = $oMinutas->GetById($oFactura->IdMinuta);
			$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
			$oModelo = $oModelos->GetById($oUnidad->IdModelo);
			$oTipoModelo = $oTiposModelo->GetById($oModelo->IdTipoModelo);
			
			$subtotal = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno;
			$iva10 = $oComprobante->ImporteIva10;
			$iva21 = $oComprobante->ImporteIva21;
			if (($oComprobante->ImporteIva10 == 0 || $oComprobante->ImporteIva10 == '' || !$oComprobante->ImporteIva10) && ($oComprobante->ImporteIva21 == 0 || $oComprobante->ImporteIva21 == '' || !$oComprobante->ImporteIva21))
			{
				if (($oComprobante->ImporteIva10 == 0 || $oComprobante->ImporteIva10 == '' || !$oComprobante->ImporteIva10) && $oModelo->Iva == 10.5)
				{
					$subtotal = $subtotal / 1.105;
					$iva10 = $subtotal * 0.105;
				}
				elseif (($oComprobante->ImporteIva21 == 0 || $oComprobante->ImporteIva21 == '' || !$oComprobante->ImporteIva21) && $oModelo->Iva == 21)
				{
					$subtotal = $subtotal / 1.21;
					$iva21 = $subtotal * 0.21;
				}
			}
			
				
			$arrData[] = array(
				trim(CambiarFecha($oComprobante->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
					
					
						
				trim(number_format($subtotal, 2, ',', '.')),
				trim(number_format($iva10, 2, ',', '.')),
				trim(number_format($iva21, 2, ',', '.')),
				trim(number_format($oComprobante->ImpuestoInterno, 2, ',', '.')),
				trim(number_format($oComprobante->Importe, 2, ',', '.')),
				trim(number_format($oMinuta->Circular, 2, ',', '.')),
				trim($oUnidad->NumeroVin),
				trim($oModelo->DenominacionModelo),
				trim($oModelo->DenominacionComercial),
				trim($oTipoModelo->Nombre),
				trim($oUnidad->NumeroFacturaCompra),
				trim(number_format($oUnidad->ImporteCompraBruto, 2, ',', '.')),
				trim(number_format($oUnidad->ImporteCompraNeto, 2, ',', '.')),
				$TipoVenta
				);
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'factura_unidades';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>