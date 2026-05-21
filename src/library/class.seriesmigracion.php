<?php 

require_once('class.dbaccess.php');
require_once('class.seriemigracion.php');
require_once('class.modelosmigracion.php');
require_once('class.filter.php');
require_once('class.unidad.php');
require_once('class.marcas.php');
require_once('class.page.php');

class SeriesMigracion extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_SeriesMigracion";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oSerieMigracion = new SerieMigracion();
			$oSerieMigracion->ParseFromArray($oRow);
			
			array_push($arr, $oSerieMigracion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdSerieMigracion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_SeriesMigracion";
		$sql.= " WHERE IdSerieMigracion = " . DB::Number($IdSerieMigracion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oSerieMigracion = new SerieMigracion();
		$oSerieMigracion->ParseFromArray($oRow);
		
		return $oSerieMigracion;		
	}
	
	public function GetByCodigo($IdModeloMigracion, $Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_SeriesMigracion";
		$sql.= " WHERE IdModeloMigracion = " . DB::Number($IdModeloMigracion);	
		$sql.= " AND Codigo = " . DB::String($Codigo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oSerieMigracion = new SerieMigracion();
		$oSerieMigracion->ParseFromArray($oRow);
		
		return $oSerieMigracion;		
	}

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_SeriesMigracion";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oSerieMigracion = new SerieMigracion();
		$oSerieMigracion->ParseFromArray($oRow);
		
		return $oSerieMigracion;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_SeriesMigracion";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(SerieMigracion $oSerieMigracion)
	{
		$arr = array
		(
			'Descripcion' => DB::String($oSerieMigracion->Descripcion),
			'Descripcion2' => DB::String($oSerieMigracion->Descripcion2),
			'Codigo' => DB::String($oSerieMigracion->Codigo),
			'IdMarca' => DB::Number($oSerieMigracion->IdMarca),
			'IdModeloMigracion' => DB::Number($oSerieMigracion->IdModeloMigracion),
			'Iva' => DB::Number($oSerieMigracion->Iva)
		);
		
		if (!$this->Insert('TB_SeriesMigracion', $arr))
			return false;

		/* asignamos el id generado */
		$oSerieMigracion->IdSerieMigracion = DBAccess::GetLastInsertId();
			
		return $oSerieMigracion;
	}
	
	
	public function Update(SerieMigracion $oSerieMigracion)
	{
		$where = " IdSerieMigracion = " . DB::Number($oSerieMigracion->IdSerieMigracion);
		
		$arr = array
		(
			'Descripcion' => DB::String($oSerieMigracion->Descripcion),
			'Descripcion2' => DB::String($oSerieMigracion->Descripcion2),
			'Codigo' => DB::String($oSerieMigracion->Codigo),
			'IdMarca' => DB::Number($oSerieMigracion->IdMarca),
			'IdModeloMigracion' => DB::Number($oSerieMigracion->IdModeloMigracion),
			'Iva' => DB::Number($oSerieMigracion->Iva)
		);
		
		if (!DBAccess::Update('TB_SeriesMigracion', $arr, $where))
			return false;
		
		return $oSerieMigracion;
	}
	

	public function Delete($IdSerieMigracion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdSerieMigracion = " . DB::Number($IdSerieMigracion);

		if (!DBAccess::Delete('TB_SeriesMigracion', $where))
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
		$oModelosMigracion = new ModelosMigracion();
		
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
				$Codigo				 		= trim($Cliente[0]);
				$CodigoMarca		 		= trim($Cliente[1]);
				$CodigoModelo		 		= trim($Cliente[2]);
				$Descripcion		 		= trim($Cliente[3]);
				$Iva				 		= trim($Cliente[4]);
				
				$oMarca = $oMarcas->GetByCodigo($CodigoMarca);
				$oModeloMigracion = $oModelosMigracion->GetByCodigo($CodigoModelo);
				
				if (!(!$oMarca && !$oModeloMigracion && $Codigo == '' && $Denominacion == '' && $Iva == ''))
				{				
					if ($err == 0)
					{
						$oSerieMigracion = new SerieMigracion();
						$oSerieMigracion->IdMarca 		= $oMarca->IdMarca;
						$oSerieMigracion->Descripcion	= $oModeloMigracion->Denominacion;
						$oSerieMigracion->Descripcion2	= $Descripcion;
						$oSerieMigracion->Codigo 	= $Codigo;
						$oSerieMigracion->IdModeloMigracion 	= $oModeloMigracion->IdModeloMigracion;
						$oSerieMigracion->Iva	= $Iva;
						
							
						if ($oSerieMigracion = $this->Create($oSerieMigracion))
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