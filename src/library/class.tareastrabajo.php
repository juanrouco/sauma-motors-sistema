<?php 

require_once('class.dbaccess.php');
require_once('class.tareatrabajo.php');
require_once('class.modelos.php');
require_once('class.modelospv.php');
require_once('class.services.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.unidad.php');
require_once('excel_export/class.xlsexport.php');


class TareasTrabajo extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['NumeroVinPrefijo'])) && ($filter['NumeroVinPrefijo'] != ''))
			$sql.= " AND m.NumeroVinPrefijo LIKE '%" . DB::StringUnquoted($filter['NumeroVinPrefijo']) . "%'";
			
		if ((isset($filter['IdTipoCosto'])) && ($filter['IdTipoCosto'] != ''))
			$sql.= " AND tt.IdTipoCosto = " . DB::Number($filter['IdTipoCosto']);

		if ((isset($filter['CodigoComercial'])) && ($filter['CodigoComercial'] != ''))
			$sql.= " AND tt.Modelo = " . DB::String($filter['CodigoComercial']);

		if ((isset($filter['Anio'])) && ($filter['Anio'] != ''))
			$sql.= " AND tt.AnioDesde <= " . DB::Number($filter['Anio']);
			
		if ((isset($filter['Anio'])) && ($filter['Anio'] != ''))
			$sql.= " AND tt.AnioHasta >= " . DB::Number($filter['Anio']);

		if ((isset($filter['PalabraClave'])) && ($filter['PalabraClave'] != ''))
		{
			$sql.= " AND (tt.Titulo LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%'";
			$sql.= " OR tt.Descripcion LIKE '%" . DB::StringUnquoted($filter['PalabraClave']) . "%')";
		}
		
		if ((isset($filter['IdCodigoTarea'])) && ($filter['IdCodigoTarea'] != ''))
			$sql.= " AND tt.IdCodigoTarea = " . DB::Number($filter['IdCodigoTarea']);
			
		if ((isset($filter['IdModeloPV'])) && ($filter['IdModeloPV'] != ''))
			$sql.= " AND tt.IdModeloPV = " . DB::Number($filter['IdModeloPV']);
			
		if ((isset($filter['NotCero'])) && ($filter['NotCero'] != ''))
			$sql.= " AND tt.Importe > 0 ";
			
		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
		{
			$sql.= " AND (tt.Modelo LIKE '%" . DB::StringUnquoted($filter['IdModeloPV']) . "%'";
			$sql.= " OR tt.IdModeloPV IN (SELECT IdModeloPV FROM TB_ModelosPV WHERE Modelo LIKE '%" . DB::StringUnquoted($filter['IdModeloPV']) . "%'))";
		}

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT tt.*";
		$sql.= " FROM TB_TareasTrabajo tt";
		$sql.= " LEFT JOIN TB_ModelosPV m ON tt.IdModeloPV = m.IdModeloPV";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tt.IdTareaTrabajo, m.IdModeloPV";
		$sql.= " ORDER BY m.Modelo ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajo = new TareaTrabajo();
			$oTareaTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByModeloPV(ModeloPV $oModeloPV)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TareasTrabajo";
		$sql.= " WHERE IdModeloPV = " . DB::String($oModeloPV->IdModeloPV);
		$sql.= " ORDER BY IdTareaTrabajo DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTareaTrabajo = new TareaTrabajo();
			$oTareaTrabajo->ParseFromArray($oRow);
			
			array_push($arr, $oTareaTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetById($IdTareaTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TareasTrabajo";
		$sql.= " WHERE IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTareaTrabajo = new TareaTrabajo();
		$oTareaTrabajo->ParseFromArray($oRow);
		
		return $oTareaTrabajo;		
	}
	
	public function GetByIdModeloPVIdService($IdModeloPV, $IdService)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TareasTrabajo";
		$sql.= " WHERE IdModeloPV = " . DB::Number($IdModeloPV);	
		$sql.= " AND IdService = " . DB::Number($IdService);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTareaTrabajo = new TareaTrabajo();
		$oTareaTrabajo->ParseFromArray($oRow);
		
		return $oTareaTrabajo;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT tt.*";
		$sql.= " FROM TB_TareasTrabajo tt";
		$sql.= " LEFT JOIN TB_Modelos m ON tt.Modelo = m.CodigoComercial";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY tt.IdTareaTrabajo, m.CodigoComercial";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(TareaTrabajo $oTareaTrabajo)
	{
		$arr = array
		(
			'Modelo' 				=> DB::String($oTareaTrabajo->Modelo),
			'AnioDesde' 			=> DB::Number($oTareaTrabajo->AnioDesde),
			'AnioHasta' 			=> DB::Number($oTareaTrabajo->AnioHasta),
			'Importe' 				=> DB::Number($oTareaTrabajo->Importe),
			'Titulo' 				=> DB::String($oTareaTrabajo->Titulo),
			'Descripcion' 			=> DB::String($oTareaTrabajo->Descripcion),
			'HorasEstimadas' 		=> DB::Number($oTareaTrabajo->HorasEstimadas),
			'IdTipoCosto'			=> DB::Number($oTareaTrabajo->IdTipoCosto),
			'IdCodigoTrabajo'		=> DB::Number($oTareaTrabajo->IdCodigoTrabajo),
			'IdModeloPV'			=> DB::Number($oTareaTrabajo->IdModeloPV),
			'IdService'				=> DB::Number($oTareaTrabajo->IdService)
		);
		return $arr;
	}
	
	public function Create(TareaTrabajo $oTareaTrabajo)
	{
		$arr = $this->GetArrayDB($oTareaTrabajo);
		
		if (!$this->Insert('TB_TareasTrabajo', $arr))
			return false;

		/* asignamos el id generado */
		$oTareaTrabajo->IdTareaTrabajo = DBAccess::GetLastInsertId();
			
		return $oTareaTrabajo;
	}
	
	
	public function Update(TareaTrabajo $oTareaTrabajo)
	{
		$where = " IdTareaTrabajo = " . DB::Number($oTareaTrabajo->IdTareaTrabajo);
		
		$arr = $this->GetArrayDB($oTareaTrabajo);
		
		if (!DBAccess::Update('TB_TareasTrabajo', $arr, $where))
			return false;
		
		return $oTareaTrabajo;
	}

	public function Delete($IdTareaTrabajo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTareaTrabajo = " . DB::Number($IdTareaTrabajo);

		if (!DBAccess::Delete('TB_TareasTrabajo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function ExportCsv()
	{
		$oModelosPV = new ModelosPv();
		$oServices = new Services();
	
		/* obtenemos el listado de datos a exportar */			
		$arrModelos = $oModelosPV->GetAll();
		$arrServices = $oServices->GetAll();
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrHeader = array(
			"Codigo", 
			"Modelo");
			
		foreach ($arrServices as $oService)
		{
			$arrHeader[] = $oService->Nombre;
		}
		
		$arrData[] = $arrHeader;
				
		foreach ($arrModelos as $oModelo)
		{	
			$arrModelo = array($oModelo->IdModeloPV, $oModelo->Modelo);
			
			foreach ($arrServices as $oService)
			{
				$oTareaTrabajo = $this->GetByIdModeloPVIdService($oModelo->IdModeloPV, $oService->IdService);
				$arrModelo[] = $oTareaTrabajo->Importe;
			}
			
			$arrData[] = $arrModelo;
		}
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'Base Services';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function Import($FileName)
	{
		/* declaramos variables necesarias */
		$oModelosPV			= new ModelosPV();
		$oServices			= new Services();
		
		/* processamos el archivo */		 
		$arrData = new Spreadsheet_Excel_Reader(Unidad::PathFile . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		try
		{
		$strError = '';
		for ($i=2; $i<=$arrData->sheets[0]['numRows']; $i++) 
		{	
			$Tarea = $arrData->sheets[0]['cells'][$i];
			if ($Tarea[1] == '')
			{
				$i = $arrData->sheets[0]['numRows'] + 1;
				continue;
			}
			$err						= 0;			
			$IdModeloPV			 		= trim($Tarea[1]);
			$Modelo		 				= trim($Tarea[2]);
			$Service1					= trim($Tarea[3]);
			$Service2					= trim($Tarea[4]);
			$Service3					= trim($Tarea[5]);
			$Service4		 			= trim($Tarea[6]);
			$Service5		 			= trim($Tarea[7]);
			$Service6		 			= trim($Tarea[8]);
			$Service7		 			= trim($Tarea[9]);
			$Service8		 			= trim($Tarea[10]);
			$Service9		 			= trim($Tarea[11]);
			$Service10		 			= trim($Tarea[12]);
			
			if (!($IdModeloPV == ''))
			{
				$Service1 = floatval($Service1);
				$Service2 = floatval($Service2);
				$Service3 = floatval($Service3);
				$Service4 = floatval($Service4);
				$Service5 = floatval($Service5);
				$Service6 = floatval($Service6);
				$Service7 = floatval($Service7);
				$Service8 = floatval($Service8);
				$Service9 = floatval($Service9);
				$Service10 = floatval($Service10);
				if (!$oModeloPV = $oModelosPV->GetById($IdModeloPV))
					$err|= 1024;
							
				if ($err == 0)
				{
					$arrServices = $oServices->GetAll();
					$j = 1;
					foreach ($arrServices as $oService)
					{
						$oTareaTrabajo = $this->GetByIdModeloPVIdService($oModeloPV->IdModeloPV, $oService->IdService);
						
						$Service					= floatval(trim($Tarea[$j + 2]));
						//$Campo = 'Service' . $j;
						$oTareaTrabajo->Importe = $Service;
						$this->Update($oTareaTrabajo);
						$j++;
					}
					$Edit++;
				}
				else
				{
					if ($err & 1 || $err & 2 || $err & 4 || $err & 8 || $err & 16 || $err & 32 || $err & 64 || $err & 128 || $err & 256 || $err & 512)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el modelo " . $oModeloPV->Modelo . " tiene valores incorrectos. <br>";
					if ($err & 1024)
						$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el codigo de modelo " . $IdModeloPV . " es incorrecto. <br>";
				}					
				
				$Row++;
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
		
		if ($Creados)
		{
			$strError.= "<br> Se actualizaron " . $Edit . " services.";		
		}		
		
		return $strError;
	}
}

?>