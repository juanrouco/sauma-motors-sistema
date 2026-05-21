<?php 

require_once('class.dbaccess.php');
require_once('class.categoriamodelo.php');
require_once('class.filter.php');
require_once('class.page.php');

class CategoriasModelo extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		$sql.= " OR Codigo LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CategoriasModelo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oCategoriaModelo = new CategoriaModelo();
			$oCategoriaModelo->ParseFromArray($oRow);
			
			array_push($arr, $oCategoriaModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdCategoriaModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CategoriasModelo";
		$sql.= " WHERE IdCategoriaModelo = " . DB::Number($IdCategoriaModelo);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCategoriaModelo = new CategoriaModelo();
		$oCategoriaModelo->ParseFromArray($oRow);
		
		return $oCategoriaModelo;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CategoriasModelo";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oCategoriaModelo = new CategoriaModelo();
		$oCategoriaModelo->ParseFromArray($oRow);
		
		return $oCategoriaModelo;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_CategoriasModelo";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(CategoriaModelo $oCategoriaModelo)
	{
		$arr = array
		(
			'Nombre' => DB::String($oCategoriaModelo->Nombre),
			'Codigo' => DB::String($oCategoriaModelo->Codigo)
		);
		
		if (!$this->Insert('TB_CategoriasModelo', $arr))
			return false;

		/* asignamos el id generado */
		$oCategoriaModelo->IdCategoriaModelo = DBAccess::GetLastInsertId();
			
		return $oCategoriaModelo;
	}
	
	
	public function Update(CategoriaModelo $oCategoriaModelo)
	{
		$where = " IdCategoriaModelo = " . DB::Number($oCategoriaModelo->IdCategoriaModelo);
		
		$arr = array
		(
			'Nombre' => DB::String($oCategoriaModelo->Nombre),
			'Codigo' => DB::String($oCategoriaModelo->Codigo)
		);
		
		if (!DBAccess::Update('TB_CategoriasModelo', $arr, $where))
			return false;
		
		return $oCategoriaModelo;
	}
	

	public function Delete($IdCategoriaModelo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCategoriaModelo = " . DB::Number($IdCategoriaModelo);

		if (!DBAccess::Delete('TB_CategoriasModelo', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>