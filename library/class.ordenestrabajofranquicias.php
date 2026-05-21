<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajofranquicia.php');
require_once('class.filter.php');
require_once('class.page.php');

class OrdenesTrabajoFranquicias extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($filter['IdOrdenTrabajo']);
		
		return $sql;
	}


	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) / " . DB::Number($oPage->Size) . " AS Count";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$Count = $oRow['Count'];
		
		return ceil($Count);
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdOrdenTrabajoFranquicia";

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
			$oOrdenTrabajoFranquicia->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoFranquicia);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetById($IdOrdenTrabajoFranquicia)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";
		$sql.= " WHERE IdOrdenTrabajoFranquicia = " . DB::Number($IdOrdenTrabajoFranquicia);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
		$oOrdenTrabajoFranquicia->ParseFromArray($oRow);
		
		return $oOrdenTrabajoFranquicia;		
	}
	

	public function GetByIdComprobante($IdComprobante)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";
		$sql.= " WHERE IdComprobante = " . DB::Number($IdComprobante);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
		$oOrdenTrabajoFranquicia->ParseFromArray($oRow);
		
		return $oOrdenTrabajoFranquicia;		
	}
	

	public function GetByIdFactura($IdFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
		$oOrdenTrabajoFranquicia->ParseFromArray($oRow);
		
		return $oOrdenTrabajoFranquicia;		
	}
	


	public function GetByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoFranquicia = new OrdenTrabajoFranquicia();
			$oOrdenTrabajoFranquicia->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoFranquicia);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_OrdenesTrabajoFranquicias";

		if ($filter)
			$sql.= $this->ParseFilter($filter);

		$sql.= " ORDER BY IdOrdenTrabajo";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	private function GetArrayDB(OrdenTrabajoFranquicia $oOrdenTrabajoFranquicia)
	{
		$arr = array
		(
			'IdOrdenTrabajo' 	=> DB::Number($oOrdenTrabajoFranquicia->IdOrdenTrabajo),
			'IdCliente' 		=> DB::Number($oOrdenTrabajoFranquicia->IdCliente),
			'Descripcion' 		=> DB::String($oOrdenTrabajoFranquicia->Descripcion),
			'Importe'			=> DB::Number($oOrdenTrabajoFranquicia->Importe),
			'IdComprobante'		=> DB::Number($oOrdenTrabajoFranquicia->IdComprobante),
			'Anulado'			=> DB::Bool($oOrdenTrabajoFranquicia->Anulado),
			'IdFactura'			=> DB::Number($oOrdenTrabajoFranquicia->IdFactura)
		);
		
		return $arr;
	}
	
	public function Create(OrdenTrabajoFranquicia $oOrdenTrabajoFranquicia)
	{
		$arr = $this->GetArrayDB($oOrdenTrabajoFranquicia);
		
		if (!$this->Insert('TB_OrdenesTrabajoFranquicias', $arr))
			return false;
		$oOrdenTrabajoFranquicia->IdOrdenTrabajoFranquicia = DBAccess::GetLastInsertId();
			
		return $oOrdenTrabajoFranquicia;
	}
	
	
	public function Update(OrdenTrabajoFranquicia $oOrdenTrabajoFranquicia)
	{
		$where = " IdOrdenTrabajoFranquicia = " . DB::Number($oOrdenTrabajoFranquicia->IdOrdenTrabajoFranquicia);
		
		$arr = $this->GetArrayDB($oOrdenTrabajoFranquicia);
		
		if (!DBAccess::Update('TB_OrdenesTrabajoFranquicias', $arr, $where))
			return false;
		
		return $oOrdenTrabajoFranquicia;
	}
	

	public function Delete($IdOrdenTrabajoFranquicia)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdOrdenTrabajoFranquicia = " . DB::Number($IdOrdenTrabajoFranquicia);
		if (!DBAccess::Delete('TB_OrdenesTrabajoFranquicias', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>