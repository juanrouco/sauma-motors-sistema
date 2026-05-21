<?php 

require_once('class.dbaccess.php');
require_once('class.marca.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.estados.php');
require_once('excel_export/class.xlsexport.php');


class Marcas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
		{
			$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
			$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMarca = new Marca();
			$oMarca->ParseFromArray($oRow);
			
			array_push($arr, $oMarca);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdMarca)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= " WHERE IdMarca = " . DB::Number($IdMarca);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMarca = new Marca();
		$oMarca->ParseFromArray($oRow);
		
		return $oMarca;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMarca = new Marca();
		$oMarca->ParseFromArray($oRow);
		
		return $oMarca;		
	}
	
	public function GetByNombreExacto($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= " WHERE Nombre = " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMarca = new Marca();
		$oMarca->ParseFromArray($oRow);
		
		return $oMarca;		
	}


	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= " WHERE Codigo = " . DB::String($Codigo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMarca = new Marca();
		$oMarca->ParseFromArray($oRow);
		
		return $oMarca;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Marcas";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Marca $oMarca)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oMarca->Codigo),
			'Nombre' 	=> DB::String($oMarca->Nombre),
			'Imagen' 	=> DB::String($oMarca->Imagen)
		);
		
		if (!$this->Insert('TB_Marcas', $arr))
			return false;

		/* asignamos el id generado */
		$oMarca->IdMarca = DBAccess::GetLastInsertId();
			
		return $oMarca;
	}
	
	
	public function Update(Marca $oMarca)
	{
		$where = " IdMarca = " . DB::Number($oMarca->IdMarca);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oMarca->Codigo),
			'Nombre' 	=> DB::String($oMarca->Nombre),
			'Imagen' 	=> DB::String($oMarca->Imagen)
		);
		
		if (!DBAccess::Update('TB_Marcas', $arr, $where))
			return false;
		
		return $oMarca;
	}
	

	public function UpdateChecks(Marca $oMarca)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$where = " IdMarca = " . DB::Number($oMarca->IdMarca);
		
		$arr = array('IdEstado' => DB::Number($oMarca->IdEstado));
		
		if (!DBAccess::Update('TB_Marcas', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
			
		DBAccess::$db->Commit();
			
		return $oMarca;
	}


	public function Delete($IdMarca)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMarca = " . DB::Number($IdMarca);
		if (!DBAccess::Delete('TB_Marcas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	

	public function ExportXls(array $filter = NULL)
	{
		/* obtenemos el listado de datos a exportar */			
		$arrMarcas = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("SaltoLinea");
				
		foreach ($arrMarcas as $oMarca)
		{	
			/* almacenamos el registro */
			$arrData[] = array(trim($oMarca->Nombre));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'marcas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>