<?php 

require_once('class.dbaccess.php');
require_once('class.ubicacion.php');
require_once('class.filter.php');
require_once('class.page.php');

class Ubicaciones extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_Ubicaciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUbicacion = new Ubicacion();
			$oUbicacion->ParseFromArray($oRow);
			
			array_push($arr, $oUbicacion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdUbicacion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ubicaciones";
		$sql.= " WHERE IdUbicacion = " . DB::Number($IdUbicacion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUbicacion = new Ubicacion();
		$oUbicacion->ParseFromArray($oRow);
		
		return $oUbicacion;		
	}
	
	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ubicaciones";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUbicacion = new Ubicacion();
		$oUbicacion->ParseFromArray($oRow);
		
		return $oUbicacion;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ubicaciones";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUbicacion = new Ubicacion();
		$oUbicacion->ParseFromArray($oRow);
		
		return $oUbicacion;		
	}


	public function GetByNombreExacto($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ubicaciones";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUbicacion = new Ubicacion();
		$oUbicacion->ParseFromArray($oRow);
		
		return $oUbicacion;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Ubicaciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Ubicacion $oUbicacion)
	{
		$arr = array
		(
			'Nombre' => DB::String($oUbicacion->Nombre),
			'Codigo' => DB::String($oUbicacion->Codigo)
		);
		
		if (!$this->Insert('TB_Ubicaciones', $arr))
			return false;

		/* asignamos el id generado */
		$oUbicacion->IdUbicacion = DBAccess::GetLastInsertId();
			
		return $oUbicacion;
	}
	
	
	public function Update(Ubicacion $oUbicacion)
	{
		$where = " IdUbicacion = " . DB::Number($oUbicacion->IdUbicacion);
		
		$arr = array
		(
			'Nombre' => DB::String($oUbicacion->Nombre),
			'Codigo' => DB::String($oUbicacion->Codigo)
		);
		
		if (!DBAccess::Update('TB_Ubicaciones', $arr, $where))
			return false;
		
		return $oUbicacion;
	}
	

	public function Delete($IdUbicacion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUbicacion = " . DB::Number($IdUbicacion);

		if (!DBAccess::Delete('TB_Ubicaciones', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	public function ImportTxtMigracion($FileName)
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
print_r($Cliente);
print_r('<br />');
			
			if ($count != 0)
			{
			
				$err						= 0;			
				$Codigo			 		= trim($Cliente[0]);
				$Nombre			 		= trim($Cliente[1]);
				
				if (!(!$Codigo && !$Nombre))
				{
					if ($err == 0)
					{
						$oUbicacion = new Ubicacion();
						$oUbicacion->Codigo = $Codigo;
						$oUbicacion->Nombre = $Nombre;
						
						if ($oUbicacion = $this->Create($oUbicacion))
						{
							$CountCreate++;
							$FechaImportacion = $Fecha;
						}
						
					}
					else
					{
						print_r('NO ENCONTRADO<br /><br />');		exit;	
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