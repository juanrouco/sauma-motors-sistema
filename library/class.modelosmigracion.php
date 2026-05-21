<?php 

require_once('class.dbaccess.php');
require_once('class.modelomigracion.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.marcas.php');
require_once('class.page.php');

class ModelosMigracion extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosMigracion";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloMigracion = new ModeloMigracion();
			$oModeloMigracion->ParseFromArray($oRow);
			
			array_push($arr, $oModeloMigracion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdModeloMigracion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosMigracion";
		$sql.= " WHERE IdModeloMigracion = " . DB::Number($IdModeloMigracion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModeloMigracion = new ModeloMigracion();
		$oModeloMigracion->ParseFromArray($oRow);
		
		return $oModeloMigracion;		
	}
	

	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosMigracion";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModeloMigracion = new ModeloMigracion();
		$oModeloMigracion->ParseFromArray($oRow);
		
		return $oModeloMigracion;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModelosMigracion";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(ModeloMigracion $oModeloMigracion)
	{
		$arr = array
		(
			'Denominacion' => DB::String($oModeloMigracion->Denominacion),
			'Codigo' => DB::String($oModeloMigracion->Codigo),
			'IdMarca' => DB::Number($oModeloMigracion->IdMarca)
		);
		
		if (!$this->Insert('TB_ModelosMigracion', $arr))
			return false;

		/* asignamos el id generado */
		$oModeloMigracion->IdModeloMigracion = DBAccess::GetLastInsertId();
			
		return $oModeloMigracion;
	}
	
	
	public function Update(ModeloMigracion $oModeloMigracion)
	{
		$where = " IdModeloMigracion = " . DB::Number($oModeloMigracion->IdModeloMigracion);
		
		$arr = array
		(
			'Denominacion' => DB::String($oModeloMigracion->Denominacion),
			'Codigo' => DB::String($oModeloMigracion->Codigo),
			'IdMarca' => DB::Number($oModeloMigracion->IdMarca)
		);
		
		if (!DBAccess::Update('TB_ModelosMigracion', $arr, $where))
			return false;
		
		return $oModeloMigracion;
	}
	

	public function Delete($IdModeloMigracion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdModeloMigracion = " . DB::Number($IdModeloMigracion);

		if (!DBAccess::Delete('TB_ModelosMigracion', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function ImportTxt($FileName)
	{
		/* declaramos variables necesarias */
		$FechaImportacion = '';
		
		$oMarcas = new Marcas();
		
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
			
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, '|');

			
			if ($count != 0)
			{
			
				$err						= 0;			
				$CodigoMarca		 		= trim($Cliente[0]);
				$Codigo				 		= trim($Cliente[1]);
				$Denominacion		 		= trim($Cliente[2]);
				
				$oMarca = $oMarcas->GetByCodigo($CodigoMarca);
								
								print_r($Cliente);
								print_r('<br />');
								print_r($oMarca);
								print_r('<br />');
								print_r('<br />');
				if (!(!$oMarca && $Codigo == '' && $Denominacion == ''))
				{				
					if ($err == 0)
					{
						$oModeloMigracion = new ModeloMigracion();
						$oModeloMigracion->IdMarca 	= $oMarca->IdMarca;
						$oModeloMigracion->Codigo 	= $Codigo;
						$oModeloMigracion->Denominacion	= $Denominacion;
						
							
						if ($oModeloMigracion = $this->Create($oModeloMigracion))
						{
							$CountCreate++;
							$FechaImportacion = $Fecha;
						}
						
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