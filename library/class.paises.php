<?php 

require_once('class.dbaccess.php');
require_once('class.pais.php');
require_once('class.filter.php');
require_once('class.page.php');

class Paises extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Paises";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPais = new Pais();
			$oPais->ParseFromArray($oRow);
			
			array_push($arr, $oPais);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	

	public function GetAllAsArray()
	{
		$arr = array();

		foreach ($this->GetAllCategorias() as $oPais)
			$arr[$oPais->IdPais] = $oPais->Nombre;

		return $arr; 	
	}


	public function GetById($IdPais)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Paises";
		$sql.= " WHERE IdPais = " . DB::Number($IdPais);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPais = new Pais();
		$oPais->ParseFromArray($oRow);
		
		return $oPais;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Paises";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPais = new Pais();
		$oPais->ParseFromArray($oRow);
		
		return $oPais;		
	}
	
	public function GetByNacionalidad($Nacionalidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Paises";
		$sql.= " WHERE Nacionalidad RLIKE " . DB::String($Nacionalidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPais = new Pais();
		$oPais->ParseFromArray($oRow);
		
		return $oPais;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Paises";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
		
	public function Create(Pais $oPais)
	{
		$arr = array
		(
			'Codigo' 	=> DB::String($oPais->Codigo),
			'Nombre' 	=> DB::String($oPais->Nombre),
			'Nacionalidad' 	=> DB::String($oPais->Nacionalidad)
		);
		
		if (!$this->Insert('TB_Paises', $arr))
			return false;

		/* asignamos el id generado */
		$oPais->IdPais = DBAccess::GetLastInsertId();
			
		return $oPais;
	}
	
	
	public function Update(Pais $oPais)
	{
		$where = " IdPais = " . DB::Number($oPais->IdPais);
		
		$arr = array
		(
			'Codigo' 	=> DB::String($oPais->Codigo),
			'Nombre' 	=> DB::String($oPais->Nombre),
			'Nacionalidad' 	=> DB::String($oPais->Nacionalidad)
		);
		
		if (!DBAccess::Update('TB_Paises', $arr, $where))
			return false;
		
		return $oPais;
	}
	

	public function Delete($IdPais)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPais = " . DB::Number($IdPais);
		if (!DBAccess::Delete('TB_Paises', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>