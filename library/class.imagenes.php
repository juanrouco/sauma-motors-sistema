<?php 

require_once('class.dbaccess.php');
require_once('class.imagen.php');
require_once('class.page.php');

class Imagenes extends DBAccess
{
	public function GetAll(Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Imagenes";
		$sql.= " ORDER BY IdImagen";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oImagen = new Imagen();
			$oImagen->ParseFromArray($oRow);
			
			array_push($arr, $oImagen);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdImagen)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Imagenes";
		$sql.= " WHERE IdImagen = " . DB::Number($IdImagen);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oImagen = new Imagen();
		$oImagen->ParseFromArray($oRow);
		
		return $oImagen;		
	}
	
	
	public function GetCountRows()
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Imagenes";
		$sql.= " ORDER BY IdImagen";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Imagen $oImagen)
	{
		$arr = array
		(
			'Imagen' 	=> DB::String($oImagen->Imagen),
			'Url' 		=> DB::String($oImagen->Url)
		);
		
		if (!$this->Insert('TB_Imagenes', $arr))
			return false;
			
		return $oImagen;
	}
	
	
	public function Update(Imagen $oImagen)
	{
		$where = " IdImagen = " . DB::Number($oImagen->IdImagen);
		
		$arr = array
		(
			'Imagen' 	=> DB::String($oImagen->Imagen),
			'Url' 		=> DB::String($oImagen->Url)
		);
		
		if (!DBAccess::Update('TB_Imagenes', $arr, $where))
			return false;
		
		return $oImagen;
	}
	

	public function Delete($IdImagen)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdImagen = " . DB::Number($IdImagen);
		if (!DBAccess::Delete('TB_Imagenes', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>