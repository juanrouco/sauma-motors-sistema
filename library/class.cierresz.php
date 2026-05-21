<?php 

require_once('class.dbaccess.php');
require_once('class.clientes.php');
require_once('class.cierrez.php');
require_once('class.comprobante.php');
require_once('class.comprobanteestados.php');
require_once('class.comprobantetipos.php');
require_once('class.misc.php');
require_once('class.operaciontipos.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class CierresZ extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CierresZ";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCierreZ";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCierreZ = new CierreZ();
			$oCierreZ->ParseFromArray($oRow);
			
			array_push($arr, $oCierreZ);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdCierreZ)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CierresZ";
		$sql.= " WHERE IdCierreZ = " . DB::Number($IdCierreZ);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCierreZ = new CierreZ();
		$oCierreZ->ParseFromArray($oRow);
		
		return $oCierreZ;		
	}
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CierresZ";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(CierreZ $oCierreZ)
	{
		$arr = array
		(
			'Fecha' 			=> DB::Date($oCierreZ->Fecha),
			'IdUsuario' 		=> DB::String($oCierreZ->IdUsuario)
		);
		
		if (!$this->Insert('TB_CierresZ', $arr))
			return false;

		/* asignamos el id generado */
		$oCierreZ->IdCierreZ = DBAccess::GetLastInsertId();
			
		return $oCierreZ;
	}
	
	
	public function Update(CierreZ $oCierreZ)
	{
		$where = " IdCierreZ = " . DB::Number($oCierreZ->IdCierreZ);
		
		$arr = array
		(
			'Fecha' 			=> DB::Date($oCierreZ->Fecha),
			'IdUsuario' 		=> DB::String($oCierreZ->IdUsuario)
		);
		
		if (!DBAccess::Update('TB_CierresZ', $arr, $where))
			return false;
		
		return $oCierreZ;
	}
}

?>