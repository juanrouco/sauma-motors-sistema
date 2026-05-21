<?php 

require_once('class.dbaccess.php');
require_once('class.tallerunidad.php');
require_once('class.colores.php');
require_once('class.clientes.php');
require_once('class.marcas.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class TallerUnidades extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdTallerUnidad'])) && ($filter['IdTallerUnidad'] != ''))
			$sql.= " AND IdTallerUnidad = " . DB::Number($filter['IdTallerUnidad']);

		if ((isset($filter['IdMarca'])) && ($filter['IdMarca'] != ''))
			$sql.= " AND IdMarca = " . DB::Number($filter['IdMarca']);

		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
			$sql.= " AND Modelo LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%'";
			
		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND Dominio LIKE '%" . DB::StringUnquoted($filter['Dominio']) . "%'";
		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
			
		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdTallerUnidad DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTallerUnidad = new TallerUnidad();
			$oTallerUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oTallerUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}

	public function GetAllByColor(Color $oColor)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE IdColor = " . DB::Number($oColor->IdColor);
		$sql.= " ORDER BY IdTallerUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTallerUnidad = new TallerUnidad();
			$oTallerUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oTallerUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE c.IdCliente = " . DB::Number($oCliente->IdCliente);
		$sql.= " ORDER BY IdTallerUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTallerUnidad = new TallerUnidad();
			$oTallerUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oTallerUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByMarca(Marca $oMarca)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE IdMarca = " . DB::Number($oMarca->IdMarca);
		$sql.= " ORDER BY IdTallerUnidad DESC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTallerUnidad = new TallerUnidad();
			$oTallerUnidad->ParseFromArray($oRow);
			
			array_push($arr, $oTallerUnidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdTallerUnidad)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE IdTallerUnidad = " . DB::Number($IdTallerUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTallerUnidad = new TallerUnidad();
		$oTallerUnidad->ParseFromArray($oRow);
		
		return $oTallerUnidad;		
	}


	public function GetByIdUnidad($IdUnidad)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTallerUnidad = new TallerUnidad();
		$oTallerUnidad->ParseFromArray($oRow);
		
		return $oTallerUnidad;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT tu.*";
		$sql.= " FROM TB_TallerUnidades tu";
		$sql.= " INNER JOIN TB_Clientes c ON tu.IdCliente = c.IdCliente";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function ActualizarUnidades()
	{
		$sql = "INSERT INTO TB_TallerUnidades";
		$sql.= " (IdMarca, IdColor, Modelo, ModeloAnio, IdCliente, Dominio, PrefijoVin, NumeroVin, NumeroMotor, FechaInicioGarantia, Concesionario, IdUnidad)";
		$sql.= " (";
		$sql.= " SELECT m.IdMarcaVehiculo, u.IdColor, m.DenominacionComercial, m.Anio, v.IdCliente, u.Patente, u.NumeroVinPrefijo, CONCAT(u.NumeroVinPrefijo, u.NumeroVin), u.NumeroMotor, v.FechaMinuta, 'Victor H. Tolosa', u.IdUnidad";
		$sql.= " FROM TB_Unidades u";
		$sql.= " INNER JOIN TB_Modelos m ON u.IdModelo = m.IdModelo";
		$sql.= " INNER JOIN TB_Minutas v ON v.IdUnidad = u.IdUnidad";
		$sql.= " WHERE u.IdUnidad NOT IN (SELECT IdUnidad FROM TB_TallerUnidades WHERE IdUnidad IS NOT NULL))";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		return true;
	}
	
	private function GetArrayDB(TallerUnidad $oTallerUnidad)
	{
		$arr = array
		(
			'IdMarca' 				=> DB::Number($oTallerUnidad->IdMarca),
			'IdColor' 				=> DB::Number($oTallerUnidad->IdColor),
			'Modelo' 				=> DB::String($oTallerUnidad->Modelo),
			'ModeloAnio' 			=> DB::Number($oTallerUnidad->ModeloAnio),
			'IdCliente' 			=> DB::Number($oTallerUnidad->IdCliente),
			'Dominio'				=> DB::String($oTallerUnidad->Dominio),
			'PrefijoVin'			=> DB::String($oTallerUnidad->PrefijoVin),
			'NumeroVin'				=> DB::String($oTallerUnidad->NumeroVin),
			'NumeroMotor'			=> DB::String($oTallerUnidad->NumeroMotor),
			'FechaInicioGarantia'	=> DB::Date($oTallerUnidad->FechaInicioGarantia),
			'Concesionario'			=> DB::String($oTallerUnidad->Concesionario),
			'IdUnidad'				=> DB::Number($oTallerUnidad->IdUnidad)
		);
		
		return $arr;
	}
	
	public function Create(TallerUnidad $oTallerUnidad)
	{
		$arr = $this->GetArrayDB($oTallerUnidad);
		
		if (!$this->Insert('TB_TallerUnidades', $arr))
			return false;

		/* asignamos el id generado */
		$oTallerUnidad->IdTallerUnidad = DBAccess::GetLastInsertId();
			
		return $oTallerUnidad;
	}
	
	
	public function Update(TallerUnidad $oTallerUnidad)
	{
		$where = " IdTallerUnidad = " . DB::Number($oTallerUnidad->IdTallerUnidad);
		
		$arr = $this->GetArrayDB($oTallerUnidad);
		
		if (!DBAccess::Update('TB_TallerUnidades', $arr, $where))
			return false;
		
		return $oTallerUnidad;
	}
	

	public function Delete($IdTallerUnidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTallerUnidad = " . DB::Number($IdTallerUnidad);

		if (!DBAccess::Delete('TB_TallerUnidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	

	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oMarcas 	= new Marcas();
		$oColores 	= new Colores();
		$oClientes 	= new Clientes();
		
		/* obtenemos el listado de datos a exportar */			
		$arrUsuarios = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NUMERO INTERNO",
			"MARCA",
			"MODELO",
			"COLOR",
			"MODELO ANIO",
			"CLIENTE"
		);
				
		foreach ($arrUsuarios as $oUsuario)
		{
			$oMarca = $oMarcas->GetById($oTallerUnidad->IdMarca);
			$oColor = $oColores->GetById($oTallerUnidad->IdColor);
			$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente);
			
			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oTallerUnidad->IdTallerUnidad),
				trim($oMarca->Nombre),
				trim($oTallerUnidad->Modelo),
				trim($oColor->Nombre),
				trim($oTallerUnidad->ModeloAnio),
				trim($oCliente->RazonSocial)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'usados';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>