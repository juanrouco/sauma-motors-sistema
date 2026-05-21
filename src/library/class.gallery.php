<?php

class Gallery
{
	public $Colums;
	public $SizeThumb;
	public $SizeMiddle;
	public $arrOriginal;
	public $arrResized;


	public function __construct($Colums 		= 3, 
								$SizeThumb 		= 50, 
								$SizeMiddle 	= 170, 
								$arrOriginal 	= array(), 
								$arrResized 	= array())
	{
		$this->Colums		= $Colums;
		$this->SizeThumb	= $SizeThumb;
		$this->SizeMiddle	= $SizeMiddle;
		$this->arrOriginal	= $arrOriginal;
		$this->arrResized 	= $arrResized;
	}


	public function Create()
	{
		?>
		<link rel="stylesheet" href="../css/lightbox.css" type="text/css" media="screen" />
		<script src="js/prototype.js" type="text/javascript"></script>
		<script src="js/scriptaculous.js?load=effects" type="text/javascript"></script>
		<script src="js/lightbox.js" type="text/javascript"></script>
		<script languaje="javascript">
	
			function ShowImage(id)
			{
				var img_principal 		= Get('ImagenPrincipal');
				var EpigrafePrincipal 	= Get('EpigrafePrincipal');
				
				img_principal.style.display 	= 'none';
				EpigrafePrincipal.style.display = 'none';
				
				for (var i=0; i<100; i++)
				{
					var img_ocultar 	= Get(i + '_G');
					var EpigrafeOcultar = Get(i + '_Epigrafe');
					
					if (img_ocultar != undefined)
						img_ocultar.style.display = 'none';
						
					if (EpigrafeOcultar != undefined)
						EpigrafeOcultar.style.display = 'none';
				}
	
				if (id == 'ImagenPrincipal')
				{
					var img = Get('ImagenPrincipal');			
					
					img.style.display = '';
					EpigrafePrincipal.style.display = '';
				}
				else
				{
					var img 		= Get(id + '_G');			
					var Epigrafe 	= Get(id + '_Epigrafe');			
					
					img.style.display = '';
					Epigrafe.style.display = '';
				}
			}
	
		</script>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="200" valign="top">
					<div align="left">
	
		<?php
		$Create = false;
	
		if ($this->arrOriginal)
		{	
			foreach ($this->arrOriginal as $oImagen)
			{
				if (file_exists($oImagen->Imagen))
					$Create = true;
			}
			
			reset($this->arrOriginal);
			reset($this->arrResized);
		}
		
		$Create = true;
	
		if ($Create)	
		{
			for ($Count=0; $Count<count($this->arrResized); $Count++)
			{	
				$oImagenOriginal 	= $this->arrOriginal[$Count];
				$oImagenResized 	= $this->arrResized[$Count];
					
				if ($Count == 0)
				{
					$ImagenPrincipal = $oImagen->Imagen;
					$EpigrafePrincipal = $oImagen->Epigrafe;
				}
		?>
		
						<a href="<?=$oImagenOriginal->Imagen?>" title="<?=$oImagenOriginal->Epigrafe?>" rel="lightbox[gallery]"><img src="<?=(!empty($oImagenResized->Imagen)) ? $oImagenResized->Imagen : 'images/no_foto.jpg'?>" id="<?=$Count?>_G" name="<?=$Count?>_G" width="<?=$this->SizeMiddle?>" height="<?=$this->SizeMiddle?>" border="0" style="display:none;" /></a>
						<span id="<?=$Count?>_Epigrafe" style="display:none;"><br /><?=$oImagenResized->Epigrafe?></span>
		
		<?php
			}
		?>
		
						<a href="<?=$ImagenPrincipal?>" title="<?=$EpigrafePrincipal?>" rel="lightbox[gallery]"><img src="<?=(!empty($oImagenResized->Imagen)) ? $oImagenResized->Imagen : 'images/no_foto.jpg'?>" id="ImagenPrincipal" name="ImagenPrincipal" width="<?=$this->SizeMiddle?>" height="<?=$this->SizeMiddle?>" border="0" /></a>
						<span id="EpigrafePrincipal"><br /><?=$EpigrafePrincipal?></span>
						<br /><br />
						
		<?php	  
			$i=1;
			$Count=0;
			reset($this->arrOriginal);
			reset($this->arrResized);
		?>
		
					</div>
					<div>
						<table border="0" cellspacing="0" cellpadding="0">
						
		<?php
			foreach ($this->arrResized as $oImagenResized)
			{
				if ($i == 1)
				{
		?>
		
							<tr>
							
		<?php
				}
		?>
								
								<td valign="top">
									<img src="<?=(!empty($oImagenResized->Imagen)) ? $oImagenResized->Imagen : 'images/no_foto.jpg'?>" id="<?=$Count?>_C" name="<?=$Count?>_C" width="<?=$this->SizeThumb?>" height="<?=$this->SizeThumb?>" border="0" align="top" onclick="ShowImage('<?=$Count?>');" />
								</td>
		
		<?php														
				if ($i == $this->Colums)
				{
		?>
		
							</tr>
                            <tr>
                            	<td height="10"></td>
                            </tr>
							
		<?php
					$i=0;
				}
				else
				{
		?>

                                <td width="10">&nbsp;</td>

		<?php
				}
		
				$i++;
				$Count++;
			}
		?>
		
                        </table>
					</div>
							
		<?php
		}
		else
		{
		?>
		
						</table>
					</div>
					<div>
						<table>
							<tr>
								<td>
									<img src="<?=Config::ImagenDefault?>" name="ImagenPrincipal" width="<?=$this->SizeMiddle?>" border="0" id="ImagenPrincipal" />
									<br /><br />
								</td>
							</tr>
						</table>
					</div>
						
		<?php
		}
		?>
		
				</td>
			</tr>
		</table>
	
		<?php
	}
}