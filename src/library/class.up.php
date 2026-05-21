<?php

class Up
{	
	const MaxSize = 20000000000;

	public $FileName;
	public $FileTmpName;
	public $FileSize;
	public $FileType;
	public $Directory;
	public $arrTypes;
	public $strError;
	
	protected $Type;
	protected $name;
	protected $MaxSize;
	
	
	public function __construct($FileName, $FileTmpName, $FileSize, $FileType, $Directory, $arrTypes = array())
	{
		$this->FileName		= $FileName;
		$this->FileTmpName	= $FileTmpName;
		$this->FileSize		= $FileSize;
		$this->FileType		= $FileType;
		$this->Directory	= $Directory;
		$this->arrTypes		= $arrTypes;

		$this->Type		= self::GetFileType($FileName);
		$this->MaxSize	= self::MaxSize;
		$this->Name		= self::CreateFileName() . '.' . $this->Type;

		$this->strError = false;
				
		if (!(is_array($arrTypes)) || (count($arrTypes) == 0))
		{
			$this->arrTypes = array
			(
				'pdf', 
				'doc', 
				'docx', 
				'xls', 
				'xlsx', 
				'txt', 
				'ppt', 
				'pptx', 
				'csv', 
				'jpg', 
				'jpeg', 
				'gif', 
				'png'
			);
		}
	}


	public function GetNombre()
	{
		return (string)$this->Name;
	}


	static public function GetFileType($FileName)
	{
		$Type = '';
		
		if (!(empty($FileName)))
			$Type = end(explode('.', $FileName));
			
		$Type = strtolower($Type);

		return $Type;
	}


	static public function CreateFileName()
	{
		$Name = '';
		
		$Name = date('Ymdhis') . rand(0, 99999);

		return $Name;
	}


	private function CheckType()
	{
		if (in_array($this->Type, $this->arrTypes))
			return true;

		return false;
	}


	private function CheckSize()
	{		
		if ($this->FileSize > $this->MaxSize)
			return false;

		return true;
	}	


	private function CheckFolder()
	{
		if ( (!(is_dir($this->Directory))) && (is_writeable($this->Directory)))
			return false;
			
		return true;
	}


	public function UploadFile()
	{	
		/* verificamos tamaño */
		if (!($this->CheckSize()))
			$this->strError.= "El tamaño del archivo sobrepasa el permitido que es de 2 MB.\n";
		
		/* verificamos tipo de archivo */
		if (!($this->CheckType()))
			$this->strError.= "Los formatos permitidos son: " . (implode(",", $this->arrTypes)) . ".\n";
		
		/* verificamos existencia de directorio */
		if (!($this->CheckFolder()))
			$this->strError.= "El directorio destino no existe o no posee permisos de escritura.\n";
		
		/* verificamos que el archivo no exista */
		if (file_exists($this->Directory . $this->Name))
			$this->strError.= "El archivo ya existe almacenado.\n";
		
		/* verificamos que el archivo este subido en el servidor */
		if (!(is_uploaded_file($this->FileTmpName)))
			$this->strError.= "No se cargo el archivo en el servidor.\n";
		
		/* movemos el archivo al directorio especificado */
		
		if (!(move_uploaded_file($this->FileTmpName, $this->Directory . $this->Name)))
			$this->strError.= "No se pudo copiar el archivo.\n";
		
		if (!empty($this->strError))
			return false;
		
		return true;
	}


	static public function DeleteFile($Directory, $Name)
	{
		if (!(file_exists($Directory . $Name)))
			return false;
			
		unlink($Directory . $Name);
		
		return true;
	}
}
?>