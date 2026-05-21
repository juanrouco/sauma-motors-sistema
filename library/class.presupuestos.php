<?php 

require_once('class.dbaccess.php');
require_once('class.presupuesto.php');
require_once('class.minuta.php');
require_once('class.modelos.php');
require_once('class.clientes.php');
require_once('class.usuarios.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.ordenessalida.php');
require_once('excel_export/class.xlsexport.php');


class Presupuestos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND v.IdMinuta = " . DB::Number($filter['IdMinuta']);
		
		if ((isset($filter['IdModelo'])) && ($filter['IdModelo'] != ''))
			$sql.= " AND v.IdModelo = " . DB::Number($filter['IdModelo']);
		
		if ((isset($filter['Modelo'])) && ($filter['Modelo'] != ''))
			$sql.= " AND v.IdModelo IN (SELECT IdModelo FROM TB_Modelos WHERE DenominacionComercial RLIKE " . DB::String($filter['Modelo']) . ")";

		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND v.IdCliente = " . DB::Number($filter['IdCliente']);

		if ((isset($filter['IdUsuario'])) && ($filter['IdUsuario'] != ''))
			$sql.= " AND v.IdUsuario = " . DB::Number($filter['IdUsuario']);
			
		if ((isset($filter['arrIdUsuario'])) && ($filter['arrIdUsuario'] != '')  && (count($filter['arrIdUsuario']) != 0))
		{
			$arr = $filter['arrIdUsuario'];
			$sql.= " AND (v.IdUsuario = " . DB::Number($arr[0]);
			$sql.= " OR v.IdUsuario = " . DB::Number($arr[1]) . ")";
		}
			
		if ($filter['IdEstado'] != '')
			$sql.= " AND v.IdEstado = " . DB::Number($filter['IdEstado']);
			
		if ($filter['IdOrigenCliente'] != '')
			$sql.= " AND v.IdOrigenCliente = " . DB::Number($filter['IdOrigenCliente']);

		if ((isset($filter['NumeroVin'])) && ($filter['NumeroVin'] != ''))
			$sql.= " AND u.NumeroVin LIKE '%" . DB::StringUnquoted($filter['NumeroVin']) . "%'";
			
		if ((isset($filter['NumeroPedido'])) && ($filter['NumeroPedido'] != ''))
			$sql.= " AND u.NumeroPedido = " . DB::String($filter['NumeroPedido']);

		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND v.Fecha >= " . DB::Date($filter['FechaDesde']);

		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND v.Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaVencimientoDesde'])) && ($filter['FechaVencimientoDesde'] != ''))
			$sql.= " AND v.FechaVencimiento >= " . DB::Date($filter['FechaVencimientoDesde']);

		if ((isset($filter['FechaVencimientoHasta'])) && ($filter['FechaVencimientoHasta'] != ''))
			$sql.= " AND v.FechaVencimiento <= " . DB::Date($filter['FechaVencimientoHasta']);

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
		{
			$sql.= " AND (";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= " OR";
			$sql.= " c2.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
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

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Presupuestos v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdPresupuesto";
		$sql.= " ORDER BY v.Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPresupuesto = new Presupuesto();
			$oPresupuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPresupuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Presupuestos v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdPresupuesto";
		$sql.= " ORDER BY IF (v.IdEstado = 1, 0, IF (v.IdEstado = 3, 1, 2)) ASC, v.Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPresupuesto = new Presupuesto();
			$oPresupuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPresupuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetReporte($FechaDesde, $FechaHasta)
	{
		$sql = "SELECT COUNT(v.IdPresupuesto) AS TotalPresupuestos,";
		$sql.= " SUM(v.Precio) AS CostoTotalPresupuestos,";
		$sql.= " SUM(IF (v.IdMinuta > 0, 1, 0)) AS TotalGanados,";
		$sql.= " SUM(IF (v.IdMinuta > 0, v.Precio, 0)) AS CostoTotalGanados,";
		$sql.= " us.IdUsuario";
		$sql.= " FROM TB_Presupuestos v";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= " WHERE 1";
		$sql.= " AND v.Fecha >= " . DB::Date($FechaDesde);
		$sql.= " AND v.Fecha <= " . DB::Date($FechaHasta);
		$sql.= " GROUP BY v.IdUsuario";
		$sql.= " ORDER BY v.IdPresupuesto ASC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRepore = new stdClass();
			$oRepore->TotalPresupuestos = $oRow['TotalPresupuestos'];
			$oRepore->CostoTotalPresupuestos = $oRow['CostoTotalPresupuestos'];
			$oRepore->TotalGanados = $oRow['TotalGanados'];
			$oRepore->CostoTotalGanados = $oRow['CostoTotalGanados'];
			$oRepore->IdUsuario = $oRow['IdUsuario'];
			
			array_push($arr, $oRepore);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByUsuario(Usuario $oUsuario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Presupuestos";
		$sql.= " WHERE IdUsuario = " . DB::Number($oUsuario->IdUsuario);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPresupuesto = new Presupuesto();
			$oPresupuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPresupuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function ActualizarEstados()
	{
		$sql = "UPDATE TB_Presupuestos";
		$sql.= " SET IdEstado = " . DB::Number(PresupuestoEstados::Perdido);
		$sql.= " WHERE IdEstado = " . DB::Number(PresupuestoEstados::Pendiente);
		$sql.= " AND FechaVencimiento < " . DB::Date(date('d-m-Y'));
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
			
		return true;
	}


	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Presupuestos";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPresupuesto = new Presupuesto();
			$oPresupuesto->ParseFromArray($oRow);
			
			array_push($arr, $oPresupuesto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdPresupuesto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Presupuestos";
		$sql.= " WHERE IdPresupuesto = " . DB::Number($IdPresupuesto);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPresupuesto = new Presupuesto();
		$oPresupuesto->ParseFromArray($oRow);
		
		return $oPresupuesto;		
	}
	

	public function GetByModelo(Modelo $oModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Presupuestos";
		$sql.= " WHERE IdModelo = " . DB::Number($oModelo->IdModelo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPresupuesto = new Presupuesto();
		$oPresupuesto->ParseFromArray($oRow);
		
		return $oPresupuesto;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_Presupuestos v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usuarios us ON v.IdUsuario = us.IdUsuario";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdPresupuesto";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Presupuesto $oPresupuesto)
	{
		$arr = array
		(
			'IdModelo' 					=> DB::Number($oPresupuesto->IdModelo),
			'IdColor' 					=> DB::Number($oPresupuesto->IdColor),
			'IdUsuario' 				=> DB::Number($oPresupuesto->IdUsuario),
			'IdCliente' 				=> DB::Number($oPresupuesto->IdCliente),
			'Financia' 					=> DB::Bool($oPresupuesto->Financia),
			'FinanciacionCapital'		=> DB::Number($oPresupuesto->FinanciacionCapital),
			'FinanciacionCuotas'		=> DB::Number($oPresupuesto->FinanciacionCuotas),
			'FinanciacionAcreedor'		=> DB::String($oPresupuesto->FinanciacionAcreedor),
			'FinanciacionValorCuota'	=> DB::Number($oPresupuesto->FinanciacionValorCuota),
			'EntregaUsado' 				=> DB::Bool($oPresupuesto->EntregaUsado),
			'UsadoIdMarca' 				=> DB::Number($oPresupuesto->UsadoIdMarca),
			'UsadoModelo' 				=> DB::String($oPresupuesto->UsadoModelo),
			'UsadoAnio' 				=> DB::Number($oPresupuesto->UsadoAnio),
			'UsadoKm' 					=> DB::Number($oPresupuesto->UsadoKm),
			'UsadoPrecioTomado' 		=> DB::Number($oPresupuesto->UsadoPrecioTomado),
			'Fecha' 					=> DB::Date($oPresupuesto->Fecha),
			'IdEstado' 					=> DB::Number($oPresupuesto->IdEstado),
			'FechaVencimiento' 			=> DB::Date($oPresupuesto->FechaVencimiento),
			'IdMinuta' 					=> DB::Number($oPresupuesto->IdMinuta),
			'GastosFlete' 				=> DB::Number($oPresupuesto->GastosFlete),
			'GastosPatentamiento' 		=> DB::Number($oPresupuesto->GastosPatentamiento),
			'GastosOtorgamiento' 		=> DB::Number($oPresupuesto->GastosOtorgamiento),
			'GastosPrenda' 				=> DB::Number($oPresupuesto->GastosPrenda),
			'Circular' 					=> DB::Number($oPresupuesto->Circular),
			'Anticipo' 					=> DB::Number($oPresupuesto->Anticipo),
			'DepositoGarantia' 			=> DB::Number($oPresupuesto->DepositoGarantia),
			'Precio' 					=> DB::Number($oPresupuesto->Precio),
			'Rentas' 					=> DB::Number($oPresupuesto->Rentas),
			'Observaciones' 			=> DB::String($oPresupuesto->Observaciones),
			'IdCausaPerdida' 			=> DB::Number($oPresupuesto->IdCausaPerdida),
			'IdOrigenCliente' 			=> DB::Number($oPresupuesto->IdOrigenCliente)
		);
		
		return $arr;
	}
	
	
	public function Create(Presupuesto $oPresupuesto)
	{
		$arr = $this->GetArrayDB($oPresupuesto);
		
		
		if (!$this->Insert('TB_Presupuestos', $arr))
			return false;

		/* asignamos el id generado */
		$oPresupuesto->IdPresupuesto = DBAccess::GetLastInsertId();
			
		return $oPresupuesto;
	}
	
	
	public function Update(Presupuesto $oPresupuesto)
	{
		$where = " IdPresupuesto = " . DB::Number($oPresupuesto->IdPresupuesto);
		
		$arr = $this->GetArrayDB($oPresupuesto);
		
		if (!DBAccess::Update('TB_Presupuestos', $arr, $where))
			return false;
		
		return $oPresupuesto;
	}
	

	public function Delete($IdPresupuesto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPresupuesto = " . DB::Number($IdPresupuesto);

		if (!DBAccess::Delete('TB_Presupuestos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>