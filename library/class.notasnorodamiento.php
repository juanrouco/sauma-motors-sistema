<?php 

require_once('class.dbaccess.php');
require_once('class.notanorodamiento.php');
require_once('class.filter.php');
require_once('class.page.php');

class NotasNoRodamiento extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND nnr.IdUnidad = " . DB::Number($filter['IdUnidad']);
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT nnr.*";
		$sql.= " FROM TB_NotasNoRodamiento nnr";
		$sql.= " LEFT JOIN TB_Minutas m ON nnr.IdUnidad = m.IdUnidad";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY nnr.IdNota DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oNotaNoRodamiento = new NotaNoRodamiento();
			$oNotaNoRodamiento->ParseFromArray($oRow);
			
			array_push($arr, $oNotaNoRodamiento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdNota)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasNoRodamiento";
		$sql.= " WHERE IdNota = " . DB::Number($IdNota);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oNotaNoRodamiento = new NotaNoRodamiento();
		$oNotaNoRodamiento->ParseFromArray($oRow);
		
		return $oNotaNoRodamiento;		
	}
	

	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_NotasNoRodamiento";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oNotaNoRodamiento = new NotaNoRodamiento();
		$oNotaNoRodamiento->ParseFromArray($oRow);
		
		return $oNotaNoRodamiento;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT nnr.*";
		$sql.= " FROM TB_NotasNoRodamiento nnr";
		$sql.= " LEFT JOIN TB_Minutas m ON nnr.IdUnidad = m.IdUnidad";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(NotaNoRodamiento $oNotaNoRodamiento)
	{
		$arr = array
		(
			'IdUnidad' 	=> DB::Number($oNotaNoRodamiento->IdUnidad),
			'IdCliente' => DB::Number($oNotaNoRodamiento->IdCliente),
			'Fecha' 	=> DB::Date($oNotaNoRodamiento->Fecha)
		);
		
		if (!$this->Insert('TB_NotasNoRodamiento', $arr))
			return false;

		/* asignamos el id generado */
		$oNotaNoRodamiento->IdNota = DBAccess::GetLastInsertId();
			
		return $oNotaNoRodamiento;
	}
	
	public function Update(NotaNoRodamiento $oNotaNoRodamiento)
	{
		$where = " IdNota = " . DB::Number($oNotaNoRodamiento->IdNota);
		
		$arr = array
		(
			'IdUnidad' 	=> DB::Number($oNotaNoRodamiento->IdUnidad),
			'IdCliente' => DB::Number($oNotaNoRodamiento->IdCliente),
			'Fecha' 	=> DB::Date($oNotaNoRodamiento->Fecha)
		);
		
		if (!DBAccess::Update('TB_NotasNoRodamiento', $arr, $where))
			return false;
		
		return $oNotaNoRodamiento;
	}
	
	public function Delete($IdNota)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdNota = " . DB::Number($IdNota);

		if (!DBAccess::Delete('TB_NotasNoRodamiento', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>