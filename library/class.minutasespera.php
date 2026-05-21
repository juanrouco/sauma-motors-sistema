<?php 

require_once('class.dbaccess.php');
require_once('class.minutaespera.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.clientes.php');
require_once('class.usuarios.php');
require_once('class.colores.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class MinutasEspera extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinutaEspera'])) && ($filter['IdMinutaEspera'] != ''))
			$sql.= " AND v.IdMinutaEspera = " . DB::Number($filter['IdMinutaEspera']);
		
		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND v.IdCliente = " . DB::Number($filter['IdCliente']);

		if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
			$sql.= " AND v.IdUsuario = " . DB::Number($filter['IdUsuario']);

		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND v.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
			
		if ((isset($filter['NumeroPedido'])) && ($filter['NumeroPedido'] != ''))
			$sql.= " AND v.NumeroPedido = " . DB::String($filter['NumeroPedido']);

		if ((isset($filter['FechaMinutaDesde'])) && ($filter['FechaMinutaDesde'] != ''))
			$sql.= " AND v.FechaMinuta >= " . DB::Date($filter['FechaMinutaDesde']);

		if ((isset($filter['FechaMinutaHasta'])) && ($filter['FechaMinutaHasta'] != ''))
			$sql.= " AND v.FechaMinuta <= " . DB::Date($filter['FechaMinutaHasta']);

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
		{
			$sql.= " AND (";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= " OR";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= ")";
		}

		if ((isset($filter['Usuario'])) && ($filter['Usuario'] != ''))
		{
			$sql.= " AND ";
			$sql.= " ( ";
			$sql.= " 	us.Nombre LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " 	OR ";
			$sql.= " 	us.Apellido LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " 	OR ";
			$sql.= " 	CONCAT(us.Nombre, ' ', us.Apellido) LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " ) ";
		}
		
		if ((isset($filter['Reportado'])) && ($filter['Reportado'] != ''))
			$sql.= " AND v.Reportado = " . DB::Number($filter['Reportado']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasEspera v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinutaEspera";
		$sql.= " ORDER BY v.IdMinutaEspera DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaEspera = new MinutaEspera();
			$oMinutaEspera->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaEspera);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByUsuario(Usuario $oUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasEspera";
		$sql.= " WHERE IdUsuario = " . DB::Number($oUsuario->IdUsuario);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaEspera = new MinutaEspera();
			$oMinutaEspera->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaEspera);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasEspera";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oMinutaEspera = new MinutaEspera();
			$oMinutaEspera->ParseFromArray($oRow);
			
			array_push($arr, $oMinutaEspera);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdMinutaEspera)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasEspera";
		$sql.= " WHERE IdMinutaEspera = " . DB::Number($IdMinutaEspera);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaEspera = new MinutaEspera();
		$oMinutaEspera->ParseFromArray($oRow);
		
		return $oMinutaEspera;		
	}
	
	public function GetByNumeroVin($NumeroVin)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_MinutasEspera";
		$sql.= " WHERE NumeroVin = " . DB::String($NumeroVin);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oMinutaEspera = new MinutaEspera();
		$oMinutaEspera->ParseFromArray($oRow);
		
		return $oMinutaEspera;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_MinutasEspera v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdMinutaEspera";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(MinutaEspera $oMinutaEspera)
	{
		return array
		(
			'IdMinutaEspera' 			=> DB::Number($oMinutaEspera->IdMinutaEspera),
			'IdUsuario' 				=> DB::Number($oMinutaEspera->IdUsuario),
			'IdCliente' 				=> DB::Number($oMinutaEspera->IdCliente),
			'IdModelo'					=> DB::Number($oMinutaEspera->IdModelo),
			'IdColor'					=> DB::Number($oMinutaEspera->IdColor),
			'IdColor2'					=> DB::Number($oMinutaEspera->IdColor2),
			'IdColor3'					=> DB::Number($oMinutaEspera->IdColor3),
			'FechaMinuta' 				=> DB::Date($oMinutaEspera->FechaMinuta),
			'NumeroPedido' 				=> DB::String($oMinutaEspera->NumeroPedido),
			'NumeroVin' 				=> DB::String($oMinutaEspera->NumeroVin),
			'IdEstado' 					=> DB::Number($oMinutaEspera->IdEstado),
			'Anticipo' 					=> DB::Number($oMinutaEspera->Anticipo),
			'Reportado'					=> DB::Bool($oMinutaEspera->Reportado),
			'Financia'					=> DB::Bool($oMinutaEspera->Financia),
			'FinanciacionCapital'		=> DB::Number($oMinutaEspera->FinanciacionCapital),
			'FinanciacionCuotas'		=> DB::Number($oMinutaEspera->FinanciacionCuotas),
			'IdAcreedor'				=> DB::Number($oMinutaEspera->IdAcreedor),
			'FinanciacionValorCuota'	=> DB::Number($oMinutaEspera->FinanciacionValorCuota),
			'EntregaUsado'				=> DB::Bool($oMinutaEspera->EntregaUsado),
			'UsadoIdMarca'				=> DB::Number($oMinutaEspera->UsadoIdMarca),
			'UsadoModelo'				=> DB::String($oMinutaEspera->UsadoModelo),
			'UsadoAnio'					=> DB::Number($oMinutaEspera->UsadoAnio),
			'UsadoKm'					=> DB::String($oMinutaEspera->UsadoKm),
			'UsadoDominio'				=> DB::String($oMinutaEspera->UsadoDominio),
			'UsadoPrecioTomado'			=> DB::Number($oMinutaEspera->UsadoPrecioTomado),
			'IdMinuta'					=> DB::Number($oMinutaEspera->IdMinuta),
			'Precio'					=> DB::Number($oMinutaEspera->Precio),
			'GastosFlete'				=> DB::Number($oMinutaEspera->GastosFlete),
			'GastosPatentamiento'		=> DB::Number($oMinutaEspera->GastosPatentamiento),
			'GastosOtorgamiento'		=> DB::Number($oMinutaEspera->GastosOtorgamiento),
			'GastosPrenda'				=> DB::Number($oMinutaEspera->GastosPrenda),
			'Circular'					=> DB::Number($oMinutaEspera->Circular),
			'DepositoGarantia'			=> DB::Number($oMinutaEspera->DepositoGarantia),
			'Rentas'					=> DB::Number($oMinutaEspera->Rentas),
			'Observaciones'				=> DB::String($oMinutaEspera->Observaciones)
		);
		
	}
	
	public function Create(MinutaEspera $oMinutaEspera)
	{
		$arr = $this->GetArrayDB($oMinutaEspera);
		
		if (!$this->Insert('TB_MinutasEspera', $arr))
			return false;

		/* asignamos el id generado */
		$oMinutaEspera->IdMinutaEspera = DBAccess::GetLastInsertId();
			
		return $oMinutaEspera;
	}
	
	
	public function Update(MinutaEspera $oMinutaEspera)
	{
		$where = " IdMinutaEspera = " . DB::Number($oMinutaEspera->IdMinutaEspera);
		
		$arr = $this->GetArrayDB($oMinutaEspera);
		
		if (!DBAccess::Update('TB_MinutasEspera', $arr, $where))
			return false;
		
		return $oMinutaEspera;
	}
	

	public function Delete($IdMinutaEspera)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdMinutaEspera = " . DB::Number($IdMinutaEspera);

		if (!DBAccess::Delete('TB_MinutasEspera', $where))
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
		$oUnidades 	= new Unidades();
		$oModelos 	= new Modelos();
		$oClientes 	= new Clientes();
		$oUsuarios 	= new Usuarios();
		$oColores	= new Colores();

		/* obtenemos el listado de datos a exportar */			
		$arrMinutas = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"NRO. MINUTA",
			"FECHA",
			"MODELO",
			"COLOR",
			"NRO. VIN",
			"NRO. PEDIDO",
			"CLIENTE",
			"VENDEDOR",
			"ADELANTO"
		);
				
		foreach ($arrMinutas as $oMinuta)
		{	
			$oModelo 	= $oModelos->GetById($oMinuta->IdModelo);
			$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);
			$oUsuario 	= $oUsuarios->GetById($oMinuta->IdUsuario);
			$oColor		= $oColores->GetById($oMinuta->IdColor);
			
			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oMinuta->IdMinutaEspera),
				trim(CambiarFecha($oMinuta->FechaMinuta)),
				trim($oModelo->DenominacionComercial),
				trim($oColor->Nombre),
				trim($oMinuta->NumeroVin),
				trim($oMinuta->NumeroPedido),				
				trim($oCliente->RazonSocial),
				trim($oUsuario->Nombre . ', ' . $oUsuario->Apellido),
				trim($oMinuta->Anticipo)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'ventas es espera';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
}

?>