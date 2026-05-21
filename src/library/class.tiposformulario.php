<?php 

require_once('class.dbaccess.php');
require_once('class.tipoformulario.php');
require_once('class.filter.php');
require_once('class.page.php');

class TiposFormulario extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		return $sql;
	}


	public function GetAll()
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposFormulario";
		$sql.= " ORDER BY IdTipoFormulario ASC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoFormulario = new TipoFormulario();
			$oTipoFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oTipoFormulario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllForRepositorio()
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposFormulario";
		$sql.= " WHERE Repositorio = " . DB::Bool(true);
		$sql.= " ORDER BY IdTipoFormulario ASC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoFormulario = new TipoFormulario();
			$oTipoFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oTipoFormulario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllForDeclaracionJurada()
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposFormulario";
		$sql.= " WHERE DeclaracionJurada = " . DB::Bool(true);
		$sql.= " ORDER BY IdTipoFormulario ASC";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoFormulario = new TipoFormulario();
			$oTipoFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oTipoFormulario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllForGestoria($IdJurisdiccion, $IdOrigen, $Prenda)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposFormulario";
		$sql.= " WHERE";
		$sql.= " (IdJurisdiccion = " . DB::Number($IdJurisdiccion);
		$sql.= " OR IdJurisdiccion = " . DB::Number(Jurisdicciones::Indistinto) . ")";
		$sql.= " AND (IdOrigen = " . DB::Number($IdOrigen);
		$sql.= " OR IdOrigen = " . DB::Number(Origen::Indistinto) . ")";
		$sql.= " AND (Prenda = " . DB::Bool($Prenda);
		$sql.= " OR Prenda = " . DB::Bool(false) . ")";
		$sql.= " ORDER BY IdTipoFormulario ASC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oTipoFormulario = new TipoFormulario();
			$oTipoFormulario->ParseFromArray($oRow);
			
			array_push($arr, $oTipoFormulario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdTipoFormulario)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_TiposFormulario";
		$sql.= " WHERE IdTipoFormulario = " . DB::Number($IdTipoFormulario);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oTipoFormulario = new TipoFormulario();
		$oTipoFormulario->ParseFromArray($oRow);
		
		return $oTipoFormulario;		
	}
}

?>