<?php 

require_once('class.dbaccess.php');
require_once('class.recepcionusado.php');
require_once('class.usados.php');
require_once('class.clientes.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class RecepcionesUsados extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdRecepcionUsado'])) && ($filter['IdRecepcionUsado'] != ''))
			$sql.= " AND v.IdRecepcionUsado = " . DB::Number($filter['IdRecepcionUsado']);
		
		if ((isset($filter['IdCliente'])) && ($filter['IdCliente'] != ''))
			$sql.= " AND v.IdCliente = " . DB::Number($filter['IdCliente']);

		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND v.Fecha >= " . DB::Date($filter['FechaDesde']);

		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND v.Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['Dominio'])) && ($filter['Dominio'] != ''))
			$sql.= " AND u.Dominio RLIKE " . DB::String($filter['Dominio']);

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
		{
			$sql.= " AND (";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= " OR";
			$sql.= " c.RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%'";
			$sql.= ")";
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_RecepcionesUsados v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdRecepcionUsado";
		$sql.= " ORDER BY v.Fecha DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRecepcionUsado = new RecepcionUsado();
			$oRecepcionUsado->ParseFromArray($oRow);
			
			array_push($arr, $oRecepcionUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	public function GetAllByCliente(Cliente $oCliente)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionesUsados";
		$sql.= " WHERE IdCliente = " . DB::Number($oCliente->IdCliente);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRecepcionUsado = new RecepcionUsado();
			$oRecepcionUsado->ParseFromArray($oRow);
			
			array_push($arr, $oRecepcionUsado);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdRecepcionUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionesUsados";
		$sql.= " WHERE IdRecepcionUsado = " . DB::Number($IdRecepcionUsado);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRecepcionUsado = new RecepcionUsado();
		$oRecepcionUsado->ParseFromArray($oRow);
		
		return $oRecepcionUsado;		
	}

	public function GetByIdUsado($IdUsado)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_RecepcionesUsados";
		$sql.= " WHERE IdUsado = " . DB::Number($IdUsado);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRecepcionUsado = new RecepcionUsado();
		$oRecepcionUsado->ParseFromArray($oRow);
		
		return $oRecepcionUsado;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT v.*";
		$sql.= " FROM TB_RecepcionesUsados v";
		$sql.= " INNER JOIN TB_Clientes c ON v.IdCliente = c.IdCliente";
		$sql.= " INNER JOIN TB_Usados u ON v.IdUsado = u.IdUsado";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY v.IdRecepcionUsado";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(RecepcionUsado $oRecepcionUsado)
	{
		return array
		(
			'IdRecepcionUsado' 			=> DB::Number($oRecepcionUsado->IdRecepcionUsado),
			'IdUsado' 					=> DB::Number($oRecepcionUsado->IdUsado),
			'IdCliente' 				=> DB::Number($oRecepcionUsado->IdCliente),
			'Observaciones'				=> DB::String($oRecepcionUsado->Observaciones),
			'Fecha' 					=> DB::Date($oRecepcionUsado->Fecha),
			'EntregaTitulo' 			=> DB::Bool($oRecepcionUsado->EntregaTitulo),
			'EntregaCedula' 			=> DB::Bool($oRecepcionUsado->EntregaCedula),
			'Entrega08' 				=> DB::Bool($oRecepcionUsado->Entrega08),
			'EntregaInformeDominio' 	=> DB::Bool($oRecepcionUsado->EntregaInformeDominio),
			'Entrega13I' 				=> DB::Bool($oRecepcionUsado->Entrega13I),
			'EntregaVerificacionBomberos' 			=> DB::Bool($oRecepcionUsado->EntregaVerificacionBomberos),
			'EntregaPatentes' 			=> DB::Bool($oRecepcionUsado->EntregaPatentes),
			'EntregaManualLlaves' 		=> DB::Bool($oRecepcionUsado->EntregaManualLlaves),
			'EntregaManual'		 		=> DB::Bool($oRecepcionUsado->EntregaManual),
			'EntregaClaveFiscal' 		=> DB::Bool($oRecepcionUsado->EntregaClaveFiscal)
		);
		
	}
	
	public function Create(RecepcionUsado $oRecepcionUsado)
	{
		$arr = $this->GetArrayDB($oRecepcionUsado);
		
		if (!$this->Insert('TB_RecepcionesUsados', $arr))
			return false;

		/* asignamos el id generado */
		$oRecepcionUsado->IdRecepcionUsado = DBAccess::GetLastInsertId();
			
		return $oRecepcionUsado;
	}
	
	
	public function Update(RecepcionUsado $oRecepcionUsado)
	{
		$where = " IdRecepcionUsado = " . DB::Number($oRecepcionUsado->IdRecepcionUsado);
		
		$arr = $this->GetArrayDB($oRecepcionUsado);
		
		if (!DBAccess::Update('TB_RecepcionesUsados', $arr, $where))
			return false;
		
		return $oRecepcionUsado;
	}
	

	public function Delete($IdRecepcionUsado)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdRecepcionUsado = " . DB::Number($IdRecepcionUsado);

		if (!DBAccess::Delete('TB_RecepcionesUsados', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>