<?php 

require_once('class.dbaccess.php');
require_once('class.padronarbaretencion.php');
require_once('class.filter.php');
require_once('class.page.php');

class PadronesArbaRetenciones extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PadronesArbaRetenciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY FechaDesde DESC, CUIL";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPadronArbaRetencion = new PadronArbaRetencion();
			$oPadronArbaRetencion->ParseFromArray($oRow);
			
			array_push($arr, $oPadronArbaRetencion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllAsArray()
	{
		$arr = array();

		foreach ($this->GetAllCategorias() as $oPadronArbaRetencion)
			$arr[$oPadronArbaRetencion->IdPadronArbaRetencion] = $oPadronArbaRetencion->Nombre;

		return $arr; 	
	}


	public function GetById($IdPadronArba)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PadronesArbaRetenciones";
		$sql.= " WHERE IdPadronArba = " . DB::Number($IdPadronArba);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPadronArbaRetencion = new PadronArbaRetencion();
		$oPadronArbaRetencion->ParseFromArray($oRow);
		
		return $oPadronArbaRetencion;		
	}
	
	public function GetByCUIL($Cuil, $Fecha)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PadronesArbaRetenciones";
		$sql.= " WHERE CUIL = " . DB::String($Cuil);	
		$sql.= " AND FechaDesde <= " . DB::Date($Fecha);	
		$sql.= " AND FechaHasta >= " . DB::Date($Fecha);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPadronArbaRetencion = new PadronArbaRetencion();
		$oPadronArbaRetencion->ParseFromArray($oRow);
		
		return $oPadronArbaRetencion;		
	}
	
	public function GetByIdCliente($IdCliente, $Fecha)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PadronesArbaRetenciones";
		$sql.= " WHERE IdCliente = " . DB::Number($IdCliente);	
		$sql.= " AND FechaDesde <= " . DB::Date($Fecha);	
		$sql.= " AND FechaHasta >= " . DB::Date($Fecha);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPadronArbaRetencion = new PadronArbaRetencion();
		$oPadronArbaRetencion->ParseFromArray($oRow);
		
		return $oPadronArbaRetencion;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PadronesArbaRetenciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY FechaDesde DESC, CUIL";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
		
	public function Create(PadronArbaRetencion $oPadronArbaRetencion)
	{
		$arr = array
		(
			'CUIL' 			=> DB::String($oPadronArbaRetencion->CUIL),
			'Fecha' 		=> DB::Date($oPadronArbaRetencion->Fecha),
			'FechaDesde' 	=> DB::Date($oPadronArbaRetencion->FechaDesde),
			'FechaHasta' 	=> DB::Date($oPadronArbaRetencion->FechaHasta),
			'Percepcion' 	=> DB::Number($oPadronArbaRetencion->Percepcion),
			'Retencion' 	=> DB::Number($oPadronArbaRetencion->Retencion)
		);
		
		if (!$this->Insert('TB_PadronesArbaRetenciones', $arr))
			return false;

		/* asignamos el id generado */
		$oPadronArbaRetencion->IdPadronArba = DBAccess::GetLastInsertId();
			
		return $oPadronArbaRetencion;
	}
	
	
	public function Update(PadronArbaRetencion $oPadronArbaRetencion)
	{
		$where = " IdPadronArba = " . DB::Number($oPadronArbaRetencion->IdPadronArba);
		
		$arr = array
		(
			'CUIL' 			=> DB::String($oPadronArbaRetencion->CUIL),
			'Fecha' 		=> DB::Date($oPadronArbaRetencion->Fecha),
			'FechaDesde' 	=> DB::Date($oPadronArbaRetencion->FechaDesde),
			'FechaHasta' 	=> DB::Date($oPadronArbaRetencion->FechaHasta),
			'Percepcion' 	=> DB::Number($oPadronArbaRetencion->Percepcion),
			'Retencion' 	=> DB::Number($oPadronArbaRetencion->Retencion),
			'IdCliente' 	=> DB::Number($oPadronArbaRetencion->IdCliente)
		);
		
		if (!DBAccess::Update('TB_PadronesArbaRetenciones', $arr, $where))
			return false;
		
		return $oPadronArbaRetencion;
	}
	

	public function Delete($IdPadronArba)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPadronArba = " . DB::Number($IdPadronArba);
		if (!DBAccess::Delete('TB_PadronesArbaRetenciones', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	public function ImportTxtRetenciones($FileName)
	{
		/* declaramos variables necesarias */
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
		$sqlHeader = 'INSERT INTO TB_PadronesArbaRetenciones (CUIL, Fecha, FechaDesde, FechaHasta, Percepcion, Retencion) VALUES ';
		
		while ( !feof($fp) )
		{
			$sql = '';
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, ';');

			//print_r($Cliente);
			if ($count != 0)
			{
			
				$err						= 0;			
				$Regimen			 		= trim($Cliente[0]);
				$Fecha				 		= trim($Cliente[1]);
				$dia = substr($Fecha,0,2);
				$mes = substr($Fecha,2,2);
				$anio = substr($Fecha,4,4);

				$Fecha = $dia.'/'.$mes.'/'.$anio;
				$FechaDesde			 		= trim($Cliente[2]);
				$dia = substr($FechaDesde,0,2);
				$mes = substr($FechaDesde,2,2);
				$anio = substr($FechaDesde,4,4);

				$FechaDesde = $dia.'/'.$mes.'/'.$anio;
				
				$FechaHasta			 		= trim($Cliente[3]);
				$dia = substr($FechaHasta,0,2);
				$mes = substr($FechaHasta,2,2);
				$anio = substr($FechaHasta,4,4);

				$FechaHasta = $dia.'/'.$mes.'/'.$anio;
				
				$CUIL				 		= trim($Cliente[4]);
				$Retencion			 		= trim($Cliente[8]);
				$Retencion			 		= str_replace(',', '.', $Retencion);
				
				if (($CUIL))
				{				
					if ($err == 0)
					{
						//if ($sql != '')
						//	$sql.= ", ";
						$sql.= "(" . DB::String($CUIL) . ",";
						$sql.= " " . DB::Date($Fecha) . ",";
						$sql.= " " . DB::Date($FechaDesde) . ",";
						$sql.= " " . DB::Date($FechaHasta) . ",";
						$sql.= " " . DB::Number(0) . ",";
						$sql.= " " . DB::Number($Retencion) . ")";
						/*$oPadronArba = new PadronArba();
						$oPadronArba->Fecha 		= $Fecha;
						$oPadronArba->FechaDesde	= $FechaDesde;
						$oPadronArba->FechaHasta	= $FechaHasta;
						$oPadronArba->CUIL			= $CUIL;
						$oPadronArba->Retencion		= $Retencion;
						$oPadronArba->Percepcion	= $Percepcion;
			
						if ($oPadronArba = $this->Create($oPadronArba))
						{
							
							$FechaImportacion = $Fecha;
						}*/
						
						$sqlFinal = $sqlHeader . $sql;
						
						if (!($oRes = $this->GetQuery($sqlFinal)))
							$strError.= "Ha sucedido un erorr. Reintente por favor.";
						else
							$CountCreate++;
						
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
	
	public function ImportTxtPercepciones($FileName)
	{
		/* declaramos variables necesarias */
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
		$sqlHeader = 'INSERT INTO TB_PadronesArbaRetenciones (CUIL, Fecha, FechaDesde, FechaHasta, Percepcion, Retencion) VALUES ';
		
		while ( !feof($fp) )
		{
			$sql = '';
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, ';');

			//print_r($Cliente);
			if ($count != 0)
			{
			
				$err						= 0;			
				$Regimen			 		= trim($Cliente[0]);
				$Fecha				 		= trim($Cliente[1]);
				$dia = substr($Fecha,0,2);
				$mes = substr($Fecha,2,2);
				$anio = substr($Fecha,4,4);

				$Fecha = $dia.'/'.$mes.'/'.$anio;
				$FechaDesde			 		= trim($Cliente[2]);
				$dia = substr($FechaDesde,0,2);
				$mes = substr($FechaDesde,2,2);
				$anio = substr($FechaDesde,4,4);

				$FechaDesde = $dia.'/'.$mes.'/'.$anio;
				
				$FechaHasta			 		= trim($Cliente[3]);
				$dia = substr($FechaHasta,0,2);
				$mes = substr($FechaHasta,2,2);
				$anio = substr($FechaHasta,4,4);

				$FechaHasta = $dia.'/'.$mes.'/'.$anio;
				
				$CUIL				 		= trim($Cliente[4]);
				$Percepcion			 		= trim($Cliente[8]);
				$Percepcion			 		= str_replace(',', '.', $Percepcion);
				
				if (($CUIL))
				{				
					if ($err == 0)
					{
						//if ($sql != '')
						//	$sql.= ", ";
						$sql.= "(" . DB::String($CUIL) . ",";
						$sql.= " " . DB::Date($Fecha) . ",";
						$sql.= " " . DB::Date($FechaDesde) . ",";
						$sql.= " " . DB::Date($FechaHasta) . ",";
						$sql.= " " . DB::Number($Percepcion) . ",";
						$sql.= " " . DB::Number(0) . ")";
						/*$oPadronArba = new PadronArba();
						$oPadronArba->Fecha 		= $Fecha;
						$oPadronArba->FechaDesde	= $FechaDesde;
						$oPadronArba->FechaHasta	= $FechaHasta;
						$oPadronArba->CUIL			= $CUIL;
						$oPadronArba->Retencion		= $Retencion;
						$oPadronArba->Percepcion	= $Percepcion;
			
						if ($oPadronArba = $this->Create($oPadronArba))
						{
							
							$FechaImportacion = $Fecha;
						}*/
						
						$sqlFinal = $sqlHeader . $sql;
						
						if (!($oRes = $this->GetQuery($sqlFinal)))
							$strError.= "Ha sucedido un erorr. Reintente por favor.";
						else
							$CountCreate++;
						
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