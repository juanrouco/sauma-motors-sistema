<?php 

require_once('class.dbaccess.php');
require_once('class.tipodocumento.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposDocumento extends DBAccess implements IFilterable
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
		$sql.= " FROM TB_TiposDocumento";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoDocumento = new TipoDocumento();
			$oTipoDocumento->ParseFromArray($oRow);
			
			array_push($arr, $oTipoDocumento);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdTipoDocumento)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposDocumento";
		$sql.= " WHERE IdTipoDocumento = " . DB::Number($IdTipoDocumento);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoDocumento = new TipoDocumento();
		$oTipoDocumento->ParseFromArray($oRow);
		
		return $oTipoDocumento;		
	}
	
	public function GetByCodigoMigracion($CodigoMigracion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposDocumento";
		$sql.= " WHERE CodigoMigracion = " . DB::String($CodigoMigracion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoDocumento = new TipoDocumento();
		$oTipoDocumento->ParseFromArray($oRow);
		
		return $oTipoDocumento;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposDocumento";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoDocumento = new TipoDocumento();
		$oTipoDocumento->ParseFromArray($oRow);
		
		return $oTipoDocumento;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposDocumento";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(TipoDocumento $oTipoDocumento)
	{
		$arr = array
		(
			'Nombre' 	=> DB::String($oTipoDocumento->Nombre),
			'Codigo' 	=> DB::String($oTipoDocumento->Codigo),
			'CodigoMigracion' 	=> DB::String($oTipoDocumento->CodigoMigracion),
			'Expedido' 	=> DB::String($oTipoDocumento->Expedido)
		);
		
		if (!$this->Insert('TB_TiposDocumento', $arr))
			return false;

		/* asignamos el id generado */
		$oTipoDocumento->IdTipoDocumento = DBAccess::GetLastInsertId();
			
		return $oTipoDocumento;
	}
	
	
	public function Update(TipoDocumento $oTipoDocumento)
	{
		$where = " IdTipoDocumento = " . DB::Number($oTipoDocumento->IdTipoDocumento);
		
		$arr = array
		(
			'Nombre' 	=> DB::String($oTipoDocumento->Nombre),
			'Codigo' 	=> DB::String($oTipoDocumento->Codigo),
			'CodigoMigracion' 	=> DB::String($oTipoDocumento->CodigoMigracion),
			'Expedido' 	=> DB::String($oTipoDocumento->Expedido)
		);
		
		if (!DBAccess::Update('TB_TiposDocumento', $arr, $where))
			return false;
		
		return $oTipoDocumento;
	}
	

	public function Delete($IdTipoDocumento)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdTipoDocumento = " . DB::Number($IdTipoDocumento);

		if (!DBAccess::Delete('TB_TiposDocumento', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>