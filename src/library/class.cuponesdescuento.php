<?php 

require_once('class.dbaccess.php');
require_once('class.cupondescuento.php');
require_once('class.comprobanteestados.php');
require_once('class.operaciontipos.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_export/class.xlsexport.php');


class CuponesDescuento extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdCuponDescuento'])) && ($filter['IdCuponDescuento'] != ''))
			$sql.= " AND IdCuponDescuento = " . DB::Number($filter['IdCuponDescuento']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdCuponDescuento, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCuponDescuento = new CuponDescuento();
			$oCuponDescuento->ParseFromArray($oRow);
			
			array_push($arr, $oCuponDescuento);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	

	public function GetById($IdCuponDescuento)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= " WHERE IdCuponDescuento = " . DB::Number($IdCuponDescuento);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuponDescuento = new CuponDescuento();
		$oCuponDescuento->ParseFromArray($oRow);
		
		return $oCuponDescuento;		
	}
	

	public function GetByNumero($Numero)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= " WHERE Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuponDescuento = new CuponDescuento();
		$oCuponDescuento->ParseFromArray($oRow);
		
		return $oCuponDescuento;		
	}


	public function GetNext($IdCuponDescuento)
	{
		$sql = "SELECT IdCuponDescuento, IdEstado, Descuento, MIN(Numero) AS Numero";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= " WHERE IdCuponDescuento = " . DB::Number($IdCuponDescuento);	
		$sql.= " AND IdEstado = " . DB::Number(ComprobanteEstados::Libre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuponDescuento = new CuponDescuento();
		$oCuponDescuento->ParseFromArray($oRow);
		
		return $oCuponDescuento;		
	}


	public function GetNextCargaLote($IdCuponDescuento)
	{
		$sql = "SELECT IdCuponDescuento, IdEstado, Descuento, MAX(Numero) AS Numero";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= " WHERE IdCuponDescuento = " . DB::Number($IdCuponDescuento);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCuponDescuento = new CuponDescuento();
		$oCuponDescuento->ParseFromArray($oRow);
		
		return $oCuponDescuento;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CuponesDescuento";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function Create(CuponDescuento $oCuponDescuento)
	{
		$arr = array
		(
			'Numero' 			=> DB::String($oCuponDescuento->Numero),
			'IdEstado' 			=> DB::Number($oCuponDescuento->IdEstado),
			'Descuento'			=> DB::Number($oCuponDescuento->Descuento)
		);
		
		if (!$this->Insert('TB_CuponesDescuento', $arr))
			return false;

		/* asignamos el id generado */
		$oCuponDescuento->IdCuponDescuento = DBAccess::GetLastInsertId();
			
		return $oCuponDescuento;
	}
	
	
	public function Update(CuponDescuento $oCuponDescuento)
	{
		$where = " IdCuponDescuento = " . DB::Number($oCuponDescuento->IdCuponDescuento);
		
		$arr = array
		(
			'IdEstado' 		=> DB::Number($oCuponDescuento->IdEstado)
		);
		
		if (!DBAccess::Update('TB_CuponesDescuento', $arr, $where))
			return false;
		
		return $oCuponDescuento;
	}
}

?>