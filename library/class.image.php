<?php

require_once('../thumbnail/thumblib.inc.php');

class Image
{
	const MaxSize = 2000000;

	public $FileImageName;
	public $FileTmpName;
	public $FileSize;
	public $FileType;
	public $Directory;
	public $arrTypes;
	public $Size;
	public $Width;
	public $Height;
	public $Quality;
	public $NewWidth;
	public $NewHeight;
	public $TypeUpload;
	public $strError;
	
	protected $Type;
	protected $name;
	protected $MaxSize;

	
	public function __construct($FileImageName	= '', 
								$FileTmpName	= '', 
								$FileSize		= '', 
								$FileType		= '', 
								$Directory		= array(), 
								$arrTypes 		= array(), 
								$NewWidth		= array(800), 
								$NewHeight		= array(600),
								$TypeUpload		= array('Resize'),
								$Quality 		= 100)
	{
		$this->FileImageName	= $FileImageName;
		$this->FileTmpName		= $FileTmpName;
		$this->FileSize			= $FileSize;
		$this->FileType			= $FileType;
		$this->Directory		= $Directory;
		$this->arrTypes			= $arrTypes;
		$this->Size 			= ($this->FileTmpName != '') ? getimagesize($this->FileTmpName) : array();
		$this->Width 			= $this->Size[0];
		$this->Height 			= $this->Size[1];
		$this->NewWidth 		= $NewWidth;
		$this->NewHeight 		= $NewHeight;
		$this->TypeUpload 		= $TypeUpload;
		$this->Quality 			= $Quality;

		$this->Type		= self::GetFileType($FileImageName);
		$this->MaxSize	= self::MaxSize;
		$this->Name		= self::CreateFileName() . '.' . $this->Type;

		$this->strError = false;
				
		if (!(is_array($arrTypes)) || (count($arrTypes) == 0))
		{
			$this->arrTypes = array
			(
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
		
		if ($FileName != '')
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
		foreach ($this->Directory as $Directory)
			if ( (!(is_dir($Directory))) && (is_writeable($Directory)))
				return false;
			
		return true;
	}


	private function Validate()
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
		foreach ($this->Directory as $Directory)
			if (file_exists($Directory . $this->Name))
				$this->strError.= "El archivo ya existe almacenado.\n";
		
		/* verificamos que el archivo este subido en el servidor */
		if (!(is_uploaded_file($this->FileTmpName)))
			$this->strError.= "No se cargo el archivo en el servidor.\n";
	}


	public function UploadImage()
	{	
		/* realizamos las validaciones */
		$this->Validate();		
		
		try
		{
			 $oThumb = PhpThumbFactory::create($this->FileTmpName);
		}
		catch (Exception $e)
		{
			 $this->strError.= "No se pudo copiar el archivo.\n";
		}

		if ($this->strError != '')
			return false;

		for ($i=0; $i<count($this->Directory); $i++)
		{
			switch ($this->TypeUpload[$i])
			{
				case 'Resize':
					$oThumb->resize($this->NewWidth[$i], $this->NewHeight[$i]);
					break;
					
				case 'Adaptive':
	
					if (($this->Width < $this->NewWidth[$i]) || ($this->Height < $this->NewHeight[$i]))
					{
						if ($this->Width < $this->Height)
						{
							$this->NewWidth[$i] 	= $this->Width;
							$this->NewHeight[$i] 	= $this->Width;
						}
						elseif ($this->Height < $this->Width)
						{
							$this->NewWidth[$i] 	= $this->Height;
							$this->NewHeight[$i] 	= $this->Height;
						}
						else
						{
							$this->NewWidth[$i] 	= $this->Height;
							$this->NewHeight[$i] 	= $this->Height;
						}
			
						$oThumb->adaptiveResize($this->NewWidth[$i], $this->NewHeight[$i]);
					}
					else
					{
						$oThumb->adaptiveResize($this->NewWidth[$i], $this->NewHeight[$i]);
					}
	
					break;
			}
			
			/* guardamos el archivo */
			$oThumb->save($this->Directory[$i] . $this->Name);
		}
		
		if ($this->strError != '')
			return false;
		
		return true;
	}

	
	public function ResizeImage($NewImage, $MaxWidth, $MaxHeight)
	{
		if (!(file_exists($NewImage)))
		{			
			if ($MaxWidth == '')
			{
				$MaxWidth = round($this->Width * ($MaxHeight / $this->Height));
			}
			if ($MaxHeight == '')
			{
				$MaxHeight = round($this->Height * ($MaxWidth / $this->Width));
			}
			
			if(($MaxWidth && $this->Width > $MaxWidth) || ($MaxHeight && $this->Height > $MaxHeight))
			{
				switch ($this->Type)
				{
					case "gif":
						$ImageCreate = imagecreatefromgif($this->FileImageName);
						break;
						
					case "png":
						$ImageCreate = imagecreatefrompng($this->FileImageName);
						break;
						
					case "jpg":
						$ImageCreate = imagecreatefromjpeg($this->FileImageName);
						break;
						
					case "jpeg":
						$ImageCreate = imagecreatefromjpeg($this->FileImageName);
						break;
				}

				if ($MaxWidth && $this->Width > $MaxWidth)
				{
					$WidthRatio  = $MaxWidth / $this->Width;
					$ResizeWidth = true;
				}
				
				if ($MaxHeight && $this->Height > $MaxHeight)
				{
					$HeightRatio  = $MaxHeight / $this->Height;
					$ResizeHeight = true;
				}
				
				if ($ResizeWidth && $ResizeHeight)
				{
					if ($WidthRatio < $HeightRatio)
					{
						$Ratio = $WidthRatio;
					}
					else
					{
						$Ratio = $HeightRatio;
					}
				}
				elseif ($ResizeWidth)
				{
					$Ratio = $WidthRatio;
				}
				elseif ($ResizeHeight)
				{
					$Ratio = $HeightRatio;
				}
				
				$NewWidth 	= $this->Width * $Ratio;
				$NewHeight 	= $this->Height * $Ratio;
			}
			else
			{
				$NewWidth 	= $MaxWidth;
				$NewHeight 	= $MaxHeight;
			}
			
			if (function_exists("imagecopyresampled"))
			{
				$Thumb = imagecreatetruecolor($MaxWidth, $NewHeight);
				imagecopyresampled($Thumb, $ImageCreate, 0, 0, 0, 0, $NewWidth, $NewHeight, $this->Width, $this->Height);
			}
			else
			{
				$Thumb = imagecreate($MaxWidth, $NewHeight);
				imagecopyresized($newim, $ImageCreate, 0, 0, 0, 0, $NewWidth, $NewHeight, $this->Width, $this->Height);
			}

			switch ($this->Type)
			{
				case "gif":
					imagegif($Thumb, $NewImage);
					break;
					
				case "png":
					imagepng($Thumb, $NewImage);
					break;
					
				case "jpg":
					imagejpeg($Thumb, $NewImage, $this->Quality);
					break;
					
				case "jpeg":
					imagejpeg($Thumb, $NewImage, $this->Quality);
					break;
			}

			imagedestroy($Thumb);
		}
	}
	
	
	public function AdaptiveResize($Image, $Path, $NewPath, $NewWidth, $NewHeight)
	{
		$this->FileImageName 	= $Path . $Image;
		$this->Size 			= getimagesize($this->FileImageName);
		$this->Width 			= $this->Size[0];
		$this->Height 			= $this->Size[1];

		try
		{
			 $oThumb = PhpThumbFactory::create($Path . $Image);
		}
		catch (Exception $e)
		{
			exit;
		}
		
		if (($this->Width < $NewWidth) || ($this->Height < $NewHeight))
		{
			if ($this->Width < $this->Height)
			{
				$NewWidth 	= $this->Width;
				$NewHeight 	= $this->Width;
			}
			elseif ($this->Height < $this->Width)
			{
				$NewWidth 	= $this->Height;
				$NewHeight 	= $this->Height;
			}
			else
			{
				$NewWidth 	= $this->Height;
				$NewHeight 	= $this->Height;
			}

			$oThumb->adaptiveResize($NewWidth, $NewHeight);
		}
		else
		{
			$oThumb->adaptiveResize($NewWidth, $NewHeight);
		}
		
		$oThumb->save($NewPath . $Image);
	}
}
?>