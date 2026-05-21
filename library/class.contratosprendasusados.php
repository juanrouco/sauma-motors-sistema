<?php 

require_once('class.dbaccess.php');
require_once('class.contratoprendausado.php');
require_once('class.minutasusados.php');
require_once('class.clientes.php');
require_once('class.usados.php');
require_once('class.modelos.php');
require_once('class.filter.php');
require_once('class.acreedores.php');
require_once('class.page.php');

class ContratosPrendasUsados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);

		if ((isset($filter['NumeroContrato'])) && ($filter['NumeroContrato'] != ''))
			$sql.= " AND NumeroContrato LIKE '%" . DB::StringUnquoted($filter['NumeroContrato']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND FechaLiquidacion <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND FechaLiquidacion >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['IdAcreedor'])) && ($filter['IdAcreedor'] != ''))
			$sql.= " AND IdAcreedor = " . DB::Number($filter['IdAcreedor']);
			
		if ((isset($filter['PrePrenda'])) && ($filter['PrePrenda'] != ''))
			$sql.= " AND PrePrenda = " . DB::Bool($filter['PrePrenda']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendasUsados";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdContratoPrenda DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oContratoPrendaUsado = new ContratoPrendaUsado();
			$oContratoPrendaUsado->ParseFromArray($oRow);
			
			array_push($arr, $oContratoPrendaUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdContratoPrenda)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendasUsados";
		$sql.= " WHERE IdContratoPrenda = " . DB::Number($IdContratoPrenda);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrendaUsado = new ContratoPrendaUsado();
		$oContratoPrendaUsado->ParseFromArray($oRow);
		
		return $oContratoPrendaUsado;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_ContratosPrendasUsados fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrendaUsado = new ContratoPrendaUsado();
		$oContratoPrendaUsado->ParseFromArray($oRow);
		
		return $oContratoPrendaUsado;		
	}


	public function GetByMinuta(MinutaUsado $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendasUsados";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrendaUsado = new ContratoPrendaUsado();
		$oContratoPrendaUsado->ParseFromArray($oRow);
		
		return $oContratoPrendaUsado;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendasUsados";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(ContratoPrendaUsado $oContratoPrendaUsado)
	{
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oContratoPrendaUsado->IdMinuta),
			'NumeroContrato'	=> DB::String($oContratoPrendaUsado->NumeroContrato),
			'FechaLiquidacion' 	=> DB::Date($oContratoPrendaUsado->FechaLiquidacion),
			'MontoSolicitado' 	=> DB::Number($oContratoPrendaUsado->MontoSolicitado),
			'CostoOtorgamiento'	=> DB::Number($oContratoPrendaUsado->CostoOtorgamiento),
			'Comision' 			=> DB::Number($oContratoPrendaUsado->Comision),
			'MontoAcreditado'	=> DB::Number($oContratoPrendaUsado->MontoAcreditado),
			'GastoOtorgamiento'	=> DB::Number($oContratoPrendaUsado->GastoOtorgamiento),
			'MontoOtorgado'		=> DB::Number($oContratoPrendaUsado->MontoOtorgado),
			'Resultado'			=> DB::Number($oContratoPrendaUsado->Resultado),
			'IdAcreedor'		=> DB::Number($oContratoPrendaUsado->IdAcreedor),
			'FechaEnvioCarpeta'	=> DB::Date($oContratoPrendaUsado->FechaEnvioCarpeta),
			'FechaAprobado'		=> DB::Date($oContratoPrendaUsado->FechaAprobado),
			'FechaRechazado'	=> DB::Date($oContratoPrendaUsado->FechaRechazado),
			'FechaObservacion'	=> DB::Date($oContratoPrendaUsado->FechaObservacion),
			'Observacion'		=> DB::String($oContratoPrendaUsado->Observacion),
			'CarpetaCompleta'	=> DB::Bool($oContratoPrendaUsado->CarpetaCompleta),
			'PrePrenda'			=> DB::Bool($oContratoPrendaUsado->PrePrenda),
			'PrendaInscripta'	=> DB::Bool($oContratoPrendaUsado->PrendaInscripta),
			'IdEstado'			=> DB::Number($oContratoPrendaUsado->IdEstado),
			'FechaGestoria'		=> DB::Date($oContratoPrendaUsado->FechaGestoria),
			'FechaEnvioPrenda'	=> DB::Date($oContratoPrendaUsado->FechaEnvioPrenda)
		);
		
		return $arr;
	}
	
	public function Create(ContratoPrendaUsado $oContratoPrendaUsado)
	{
		$arr = $this->GetArrayDB($oContratoPrendaUsado);
		
		if (!$this->Insert('TB_ContratosPrendasUsados', $arr))
			return false;

		/* asignamos el id generado */
		$oContratoPrendaUsado->IdContratoPrenda = DBAccess::GetLastInsertId();
			
		return $oContratoPrendaUsado;
	}
	
	public function Update(ContratoPrendaUsado $oContratoPrendaUsado)
	{
		$where = " IdContratoPrenda = " . DB::Number($oContratoPrendaUsado->IdContratoPrenda);
		
		$arr = $this->GetArrayDB($oContratoPrendaUsado);
		
		if (!DBAccess::Update('TB_ContratosPrendasUsados', $arr, $where))
			return false;
		
		return $oContratoPrendaUsado;
	}
	
	public function Delete($IdContratoPrenda)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdContratoPrenda = " . DB::Number($IdContratoPrenda);

		if (!DBAccess::Delete('TB_ContratosPrendasUsados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	public function ExportCsv(array $filter = NULL)
	{
		$oMinutasUsados = new MinutasUsados();
		$oClientes = new Clientes();
		$oAcreedores = new Acreedores();
		
		$arrContratosPrenda = $this->GetAll($filter);
		
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"NRO CARPETA", 
			"NRO CONTRATO", 
			"CLIENTE", 
			"ACREEDOR", 
			"MONTO SOLICITADO", 
			"GASTO PRENDARIO", 
			"COSTO SUBSIDIO", 
			"COMISION", 
			"MONTO OTORGADO", 
			"MONTO ACREDITADO", 
			"FECHA LIQUIDACION",
			"UTILIDAD");
		
		$TotalResultado = 0;
		foreach ($arrContratosPrenda as $oContratoPrendaUsado)
		{
			$oMinutaUsado = $oMinutasUsados->GetById($oContratoPrendaUsado->IdMinuta);
			$oCliente = $oClientes->GetById($oMinutaUsado->IdCliente);
			$cliente = $oCliente->RazonSocial;
			$oAcreedor = $oAcreedores->GetById($oContratoPrendaUsado->IdAcreedor);
			if ($oMinutaUsado->Condominio)
			{
				$oClienteCondominio = $oClientes->GetById($oMinutaUsado->IdClienteCondominio);
				$cliente.= " / " . $oClienteCondominio->RazonSocial;
			}
			
			$arrData[] = array(
				trim($oContratoPrendaUsado->IdMinuta), 
				trim($oContratoPrendaUsado->NumeroContrato), 
				trim($cliente),
				trim($oAcreedor->RazonSocial),
				trim(number_format($oContratoPrendaUsado->MontoSolicitado, 2, ',', '.')),
				trim(number_format($oContratoPrendaUsado->GastoOtorgamiento, 2, ',', '.')),
				trim(number_format($oContratoPrendaUsado->CostoOtorgamiento, 2, ',', '.')),
				trim(number_format($oContratoPrendaUsado->Comision, 2, ',', '.')),
				trim(number_format($oContratoPrendaUsado->MontoOtorgado, 2, ',', '.')),
				trim(number_format($oContratoPrendaUsado->MontoAcreditado, 2, ',', '.')),
				
				trim(CambiarFecha($oContratoPrendaUsado->FechaLiquidacion)),
				trim(number_format($oContratoPrendaUsado->Resultado, 2, ',', '.'))
				
				);
				
			$TotalResultado += $oContratoPrendaUsado->Resultado;
		}
		
		$arrData[] = array(
				trim(''), 
				trim(''), 
				trim(''),
				trim(''),
				trim(''),
				trim(''),
				trim(''),
				trim(''),
				trim(''),
				
				trim('TOTAL'),
				trim(number_format($TotalResultado, 2, ',', '.'))
				
				);
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'contratos_prenda';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>