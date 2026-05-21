<?php 

require_once('class.dbaccess.php');
require_once('class.pagos.php');
require_once('class.minutas.php');
require_once('class.clientes.php');
require_once('class.unidades.php');
require_once('class.modelos.php');
require_once('class.pagomayorista.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.planillasrecepcion.php');
require_once('class.clientetipos.php');
require_once('class.comprobantes.php');
require_once('class.filter.php');
require_once('class.page.php');

class PagosMayorista extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdPedidoMayorista'])) && ($filter['IdPedidoMayorista'] != ''))
			$sql.= " AND IdPedidoMayorista = " . DB::Number($filter['IdPedidoMayorista']);

		if ((isset($filter['NumeroCheque'])) && ($filter['NumeroCheque'] != ''))
			$sql.= " AND NumeroCheque LIKE '%" . DB::StringUnquoted($filter['NumeroCheque']) . "%'";
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosMayorista";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPagoMayorista DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPagoMayorista = new PagoMayorista();
			$oPagoMayorista->ParseFromArray($oRow);
			
			array_push($arr, $oPagoMayorista);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdPagoMayorista)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosMayorista";
		$sql.= " WHERE IdPagoMayorista = " . DB::Number($IdPagoMayorista);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPagoMayorista = new PagoMayorista();
		$oPagoMayorista->ParseFromArray($oRow);
		
		return $oPagoMayorista;		
	}
	

	public function GetByIdPedidoMayorista($IdPedidoMayorista)
	{
		$sql = "SELECT fu.*";
		$sql.= " FROM TB_PagosMayorista fu";
		$sql.= " WHERE fu.IdPedidoMayorista = " . DB::Number($IdPedidoMayorista);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPagoMayorista = new PagoMayorista();
			$oPagoMayorista->ParseFromArray($oRow);
			
			array_push($arr, $oPagoMayorista);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}


	public function GetByPedidoMayorista(PedidoMayorista $oPedidoMayorista)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosMayorista";
		$sql.= " WHERE IdPedidoMayorista = " . DB::Number($oPedidoMayorista->IdPedidoMayorista);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPagoMayorista = new PagoMayorista();
			$oPagoMayorista->ParseFromArray($oRow);
			
			array_push($arr, $oPagoMayorista);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PagosMayorista";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(PagoMayorista $oPagoMayorista)
	{
		$arr = array
		(
			'IdPedidoMayorista' => DB::Number($oPagoMayorista->IdPedidoMayorista),
			'Fecha' 			=> DB::Date($oPagoMayorista->Fecha),
			'NumeroCheque' 		=> DB::String($oPagoMayorista->NumeroCheque),
			'BancoDesde' 		=> DB::String($oPagoMayorista->BancoDesde),
			'BancoDestino' 		=> DB::String($oPagoMayorista->BancoDestino),
			'Cliente' 			=> DB::String($oPagoMayorista->Cliente),
			'FechaEmision' 		=> DB::Date($oPagoMayorista->FechaEmision),
			'FechaDeposito'		=> DB::Date($oPagoMayorista->FechaDeposito),
			'Importe' 			=> DB::Number($oPagoMayorista->Importe),
			'IdTipoPago'	 	=> DB::Number($oPagoMayorista->IdTipoPago),
			'Observaciones' 	=> DB::String($oPagoMayorista->Observaciones),
			'ImporteAsignado' 	=> DB::Number($oPagoMayorista->ImporteAsignado)
		);
		
		return $arr;
	}
	
	public function Create(PagoMayorista $oPagoMayorista)
	{
		$arr = $this->GetArrayDB($oPagoMayorista);
		
		if (!$this->Insert('TB_PagosMayorista', $arr))
			return false;

		/* asignamos el id generado */
		$oPagoMayorista->IdPagoMayorista = DBAccess::GetLastInsertId();
			
		return $oPagoMayorista;
	}
	
	public function Update(PagoMayorista $oPagoMayorista)
	{
		$where = " IdPagoMayorista = " . DB::Number($oPagoMayorista->IdPagoMayorista);
		
		$arr = $this->GetArrayDB($oPagoMayorista);
		
		if (!DBAccess::Update('TB_PagosMayorista', $arr, $where))
			return false;
		
		return $oPagoMayorista;
	}
	
	
	public function Delete($IdPagoMayorista)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPagoMayorista = " . DB::Number($IdPagoMayorista);

		if (!DBAccess::Delete('TB_PagosMayorista', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
	
	
}

?>