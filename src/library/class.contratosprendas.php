<?php 

require_once('class.dbaccess.php');
require_once('class.contratoprenda.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.acreedores.php');
require_once('class.modelos.php');
require_once('class.filter.php');
require_once('class.page.php');

class ContratosPrendas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND IdMinuta = " . DB::Number($filter['IdMinuta']);

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
		$sql.= " FROM TB_ContratosPrendas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdContratoPrenda DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oContratoPrenda = new ContratoPrenda();
			$oContratoPrenda->ParseFromArray($oRow);
			
			array_push($arr, $oContratoPrenda);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdContratoPrenda)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendas";
		$sql.= " WHERE IdContratoPrenda = " . DB::Number($IdContratoPrenda);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrenda = new ContratoPrenda();
		$oContratoPrenda->ParseFromArray($oRow);
		
		return $oContratoPrenda;		
	}
	

	public function GetByIdMinuta($IdMinuta)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_ContratosPrendas fu";
		$sql.= " WHERE fu.IdMinuta = " . DB::Number($IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrenda = new ContratoPrenda();
		$oContratoPrenda->ParseFromArray($oRow);
		
		return $oContratoPrenda;		
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendas";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oContratoPrenda = new ContratoPrenda();
		$oContratoPrenda->ParseFromArray($oRow);
		
		return $oContratoPrenda;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ContratosPrendas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(ContratoPrenda $oContratoPrenda)
	{
		$arr = array
		(
			'IdMinuta' 			=> DB::Number($oContratoPrenda->IdMinuta),
			'NumeroContrato'	=> DB::String($oContratoPrenda->NumeroContrato),
			'FechaLiquidacion' 	=> DB::Date($oContratoPrenda->FechaLiquidacion),
			'MontoSolicitado' 	=> DB::Number($oContratoPrenda->MontoSolicitado),
			'CostoOtorgamiento'	=> DB::Number($oContratoPrenda->CostoOtorgamiento),
			'Comision' 			=> DB::Number($oContratoPrenda->Comision),
			'MontoAcreditado'	=> DB::Number($oContratoPrenda->MontoAcreditado),
			'GastoOtorgamiento'	=> DB::Number($oContratoPrenda->GastoOtorgamiento),
			'MontoOtorgado'		=> DB::Number($oContratoPrenda->MontoOtorgado),
			'Resultado'			=> DB::Number($oContratoPrenda->Resultado),
			'IdAcreedor'		=> DB::Number($oContratoPrenda->IdAcreedor),
			'FechaEnvioCarpeta'	=> DB::Date($oContratoPrenda->FechaEnvioCarpeta),
			'FechaAprobado'		=> DB::Date($oContratoPrenda->FechaAprobado),
			'FechaRechazado'	=> DB::Date($oContratoPrenda->FechaRechazado),
			'FechaObservacion'	=> DB::Date($oContratoPrenda->FechaObservacion),
			'Observacion'		=> DB::String($oContratoPrenda->Observacion),
			'CarpetaCompleta'	=> DB::Bool($oContratoPrenda->CarpetaCompleta),
			'PrePrenda'			=> DB::Bool($oContratoPrenda->PrePrenda),
			'PrendaInscripta'	=> DB::Bool($oContratoPrenda->PrendaInscripta),
			'IdEstado'			=> DB::Number($oContratoPrenda->IdEstado),
			'FechaGestoria'		=> DB::Date($oContratoPrenda->FechaGestoria),
			'FechaEnvioPrenda'	=> DB::Date($oContratoPrenda->FechaEnvioPrenda)
		);
		
		return $arr;
	}
	
	public function Create(ContratoPrenda $oContratoPrenda)
	{
		$arr = $this->GetArrayDB($oContratoPrenda);
		
		if (!$this->Insert('TB_ContratosPrendas', $arr))
			return false;

		/* asignamos el id generado */
		$oContratoPrenda->IdContratoPrenda = DBAccess::GetLastInsertId();
			
		return $oContratoPrenda;
	}
	
	public function Update(ContratoPrenda $oContratoPrenda)
	{
		$where = " IdContratoPrenda = " . DB::Number($oContratoPrenda->IdContratoPrenda);
		
		$arr = $this->GetArrayDB($oContratoPrenda);
		
		if (!DBAccess::Update('TB_ContratosPrendas', $arr, $where))
			return false;
		
		return $oContratoPrenda;
	}
	
	public function Delete($IdContratoPrenda)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdContratoPrenda = " . DB::Number($IdContratoPrenda);

		if (!DBAccess::Delete('TB_ContratosPrendas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	public function ExportCsv(array $filter = NULL)
	{
		$oMinutas = new Minutas();
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
		foreach ($arrContratosPrenda as $oContratoPrenda)
		{
			$oMinuta = $oMinutas->GetById($oContratoPrenda->IdMinuta);
			$oCliente = $oClientes->GetById($oMinuta->IdCliente);
			$cliente = $oCliente->RazonSocial;
			$oAcreedor = $oAcreedores->GetById($oContratoPrenda->IdAcreedor);
			if ($oMinuta->Condominio)
			{
				$oClienteCondominio = $oClientes->GetById($oMinuta->IdClienteCondominio);
				$cliente.= " / " . $oClienteCondominio->RazonSocial;
			}
			
			$arrData[] = array(
				trim($oContratoPrenda->IdMinuta), 
				trim($oContratoPrenda->NumeroContrato), 
				trim($cliente),
				trim($oAcreedor->RazonSocial),
				trim(number_format($oContratoPrenda->MontoSolicitado, 2, ',', '.')),
				trim(number_format($oContratoPrenda->GastoOtorgamiento, 2, ',', '.')),
				trim(number_format($oContratoPrenda->CostoOtorgamiento, 2, ',', '.')),
				trim(number_format($oContratoPrenda->Comision, 2, ',', '.')),
				trim(number_format($oContratoPrenda->MontoOtorgado, 2, ',', '.')),
				trim(number_format($oContratoPrenda->MontoAcreditado, 2, ',', '.')),
				
				trim(CambiarFecha($oContratoPrenda->FechaLiquidacion)),
				trim(number_format($oContratoPrenda->Resultado, 2, ',', '.'))
				
				);
				
			$TotalResultado += $oContratoPrenda->Resultado;
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