<?php 

require_once('class.dbaccess.php');
require_once('class.producto.php');
require_once('class.productoimagen.php');
require_once('class.filter.php');

class ModeloImagenes extends DBAccess
{
	public function GetAll(Page $oPage = NULL)
	{
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdModelo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe";
		$sql.= " FROM TB_ModeloImagenes ";
		$sql.= " ORDER BY IdImagen ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloImagen = new ModeloImagen();
			$oModeloImagen->ParseFromArray($oRow);
			
			array_push($arr, $oModeloImagen);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetLastId()
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_ModeloImagenes";
		$sql.= " ORDER BY IdImagen DESC";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oModeloImagen = new ModeloImagen();
		$oModeloImagen->ParseFromArray($oRow);
		
		return $oModeloImagen;		
	}
	
	
	public function GetById($IdImagen)
	{
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdModelo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe";
		$sql.= " FROM TB_ModeloImagenes ";
		$sql.= " WHERE IdImagen = " . DB::Number($IdImagen);	
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModeloImagen = new ModeloImagen();
		$oModeloImagen->ParseFromArray($oRow);
		
		return $oModeloImagen;		
	}
	
	
	public function GetAllByModelo(Modelo $oModelo, Page $oPage = NULL)
	{
		$arr = array();
	
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdModelo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe";
		$sql.= " FROM TB_ModeloImagenes ";
		$sql.= " WHERE IdModelo = " . DB::Number($oModelo->IdModelo);
		$sql.= " ORDER BY IdImagen ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloImagen = new ModeloImagen();
			$oModeloImagen->ParseFromArray($oRow);
			
			array_push($arr, $oModeloImagen);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	public function GetAllByIdModelo($IdModelo)
	{
		$arr = array();

		$sql = "SELECT ";
		$sql.= " *";
		$sql.= " FROM TB_ModeloImagenes I ";
		$sql.= " INNER JOIN TB_Modelos N ON N.IdModelo = I.IdModelo ";
		$sql.= " WHERE N.IdModelo = " . $IdModelo;
		$sql.= " ORDER BY IdImagen ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oModeloImagen = new ModeloImagen();
			$oModeloImagen->ParseFromArray($oRow);

			array_push($arr, $oModeloImagen);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetCountRows($IdModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ModeloImagenes";
		$sql.= " WHERE IdModelo = " . DB::Number($IdModelo);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(ModeloImagen $oModeloImagen)
	{
		$arr = array
		(
			'IdModelo' 	=> DB::Number($oModeloImagen->IdModelo),
			'Imagen' 		=> DB::String($oModeloImagen->Imagen),
			'Epigrafe' 		=> DB::String($oModeloImagen->Epigrafe)
		);
		
		if (!$this->Insert('TB_ModeloImagenes', $arr))
			return false;
			
		return $oModeloImagen;
	}
	
	
	public function Update(ModeloImagen $oModeloImagen)
	{
		$where = " IdImagen = " . DB::Number($oModeloImagen->IdImagen);
		
		$arr = array
		(
			'IdModelo'	=> DB::Number($oModeloImagen->IdModelo),
			'Imagen' 		=> DB::String($oModeloImagen->Imagen),
			'Epigrafe' 		=> htmlentities(DB::String($oModeloImagen->Epigrafe)) //FIXME:: LLEVA htmlentities para que guarde bien desde AJAX
		);
		
		if (!DBAccess::Update('TB_ModeloImagenes', $arr, $where))
			return false;
		
		return $oModeloImagen;
	}
	

	public function Delete($IdImagen)
	{
		if (!DBAccess::$db->Begin())
			return false;

		/* obtiene los datos de la imagen */
		$oModeloImagen = $this->GetById($IdImagen);
		if (!$oModeloImagen)
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		/* elimina la imangen del directorio */
		Up::DeleteFile(Modelo::PathImageThumbBack, $oModeloImagen->Imagen);
		Up::DeleteFile(Modelo::PathImageBigBack, $oModeloImagen->Imagen);
			
		/* elimina la imagen de la tabla */	
		$where = " IdImagen = " . DB::Number($IdImagen);
		if (!DBAccess::Delete('TB_ModeloImagenes', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>