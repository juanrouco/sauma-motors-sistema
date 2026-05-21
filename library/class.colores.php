<?php 

require_once('class.dbaccess.php');
require_once('class.color.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.page.php');

class Colores extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_Colores";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oColor = new Color();
			$oColor->ParseFromArray($oRow);
			
			array_push($arr, $oColor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdColor)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Colores";
		$sql.= " WHERE IdColor = " . DB::Number($IdColor);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oColor = new Color();
		$oColor->ParseFromArray($oRow);
		
		return $oColor;		
	}
	
	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Colores";
		$sql.= " WHERE Codigo RLIKE " . DB::String($Codigo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oColor = new Color();
		$oColor->ParseFromArray($oRow);
		
		return $oColor;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Colores";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oColor = new Color();
		$oColor->ParseFromArray($oRow);
		
		return $oColor;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Colores";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Color $oColor)
	{
		$arr = array
		(
			'Nombre' => DB::String($oColor->Nombre),
			'Codigo' => DB::String($oColor->Codigo),
			'Imagen' => DB::String($oColor->Imagen)
		);
		
		if (!$this->Insert('TB_Colores', $arr))
			return false;

		/* asignamos el id generado */
		$oColor->IdColor = DBAccess::GetLastInsertId();
			
		return $oColor;
	}
	
	
	public function Update(Color $oColor)
	{
		$where = " IdColor = " . DB::Number($oColor->IdColor);
		
		$arr = array
		(
			'Nombre' => DB::String($oColor->Nombre),
			'Codigo' => DB::String($oColor->Codigo),
			'Imagen' => DB::String($oColor->Imagen)
		);
		
		if (!DBAccess::Update('TB_Colores', $arr, $where))
			return false;
		
		return $oColor;
	}
	

	public function Delete($IdColor)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdColor = " . DB::Number($IdColor);

		if (!DBAccess::Delete('TB_Colores', $where))
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

			//print_r($Cliente);
			if ($count != 0)
			{
			
				$err						= 0;			
				$Codigo				 		= trim($Cliente[0]);
				$Nombre				 		= trim($Cliente[1]);
								
				if (!($Codigo == '' && $Nombre == ''))
				{				
					if ($err == 0)
					{
						$oColor = new Color();
						$oColor->Codigo 	= $Codigo;
						$oColor->Nombre	= $Nombre;
						
							
						if ($oColor = $this->Create($oColor))
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