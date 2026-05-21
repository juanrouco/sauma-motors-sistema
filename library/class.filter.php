<?php

require_once('class.config.php');

interface IFilterable
{
	function ParseFilter(array $filter);
}


abstract class Filterable
{
	const FilterMaxColumn = 2;
	
	static private $Filters;
	static private $Visible;

	
	private function IsFilterable()
	{
		if (!is_array(Filterable::$Filters))
			return false;
			
		return count(Filterable::$Filters) > 0;
	}


	private function IsVisible()
	{
		if (!is_array($this->filters))
			return false;

		foreach (Filterable::$Filters as $oFilter) 
		{
			if ($oFilter->Value != '')
				return true;
		}
		
		return false;
	}

	
	static function AddFilter($Id, $Description, $Type, $Value, $Attributes = false, $Options = false)
	{
		if ($Attributes == false)
			$Attributes = array();
			
		if ($Options == false)
			$Options = array();
			
		$oFilter = new Filter
		(
			$Id, 
			$Description, 
			$Type, 
			$Value, 
			$Attributes, 
			$Options
		);
		
		Filterable::$Filters[] = $oFilter;
			
		return true;
	}

	
	static function GetAllFilters()
	{
		return Filterable::$Filters;
	}

	
	static function GetFiltersCount()
	{
		return count(Filterable::$Filters);
	}
	

	static function RenderFilter()
	{
		$NewRow = true;
		
		if (Filterable::IsFilterable())
		{
			?>
			<script language="javascript" src="<?=Config::UrlSitioEspanol?>js/jquery_.js"></script>
			
			<script language="javascript">
			
			$(document).ready(function () 
			{
				$('#OcultarFiltro').click(function() 
				{
					$("#FilterMain").fadeOut("normal");
				});			
			});
	
			function ClearValues()
			{
				var frmData = Get('frmData');
			
				if (frmData == undefined)
					return false;
			
				try
				{
					<?php foreach (Filterable::$Filters as $oFilter) { ?>
					frmData.<?=$oFilter->Name?>.value = '';
					<?php } ?>			
				}
				catch (e)
				{
				}
				
				frmData.submit();
				return true;
			}
			
			function ShowFilter()
			{
				HideSection('ShownFilter');
				ShowSection('HiddenFilter');
				ShowSection('FilterMain');
			}
			
			function HideFilter()
			{
				ShowSection('ShownFilter');
				HideSection('HiddenFilter');
				HideSection('FilterMain');
			}				
			</script>
			
			<div class="bordeGrisFondo" id="ShownFilter" style="<?=(Filterable::IsVisible() === true) ? 'display:none' : ''?> padding-right:10px; padding-left:10px; padding-top:10px; padding-bottom:10px; display:none;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"><b>Mostrar b&uacute;squeda y filtros</b></a></td>
						<td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
					</tr>
				</table>
			</div>
			<div class="bordeGrisFondo" id="HiddenFilter" style="<?=(Filterable::IsVisible() === false) ? 'display:none' : ''?> padding-right:10px; padding-left:10px; padding-top:10px; padding-bottom:10px;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>[-] <a href="#bottom" class="linkMenu" id="OcultarFiltro"><b>Ocultar b&uacute;squeda y filtros</b></a></td>
						<td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
					</tr>
				</table>
			</div>
			<div align="center">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="1"><div align="center"></div></td>
					</tr>
				</table>
			</div>
			<div id="FilterMain" style="<?=(Filterable::IsVisible() === false) ? 'display:none' : ''?>">
				<table border="0" class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%">
					<tr>
						<td class="tituloMenu">
							<table border="0" align="left" cellpadding="0" cellspacing="0">

							<?php $Count = 0; ?>
							<?php foreach (Filterable::$Filters as $oFilter) { ?>
								<?php if ($Count == 0) { ?>
                                <tr>
                                <?php } ?>
                                
									<td class="tituloMenu"><?=$oFilter->Description?>:</td>
									<td>

								<?php 
								switch ($oFilter->Type)
								{
									case FilterTypes::Text:
								?>
	
										<input name="<?=$oFilter->Id?>" id="<?=$oFilter->Id?>" type="text" class="camporFormularioSimple" value="<?=$oFilter->Value?>" maxlength="128" />
	
								<?php
										break;
										
									case FilterTypes::Select:
	
								?>
										<select name="<?=$oFilter->Id?>" id="<?=$oFilter->Id?>" class="campoFormularioSimple">
											<option value="">[SELECCIONE]</option>
											<?php foreach ($oFilter->Attributes  as $key => $value) { ?>
											<option value="<?=$key?>" <?=($oFilter->Value == $key) ? 'selected="selected"' : ''?> ><?=$value?></option>
											<?php } ?>
										</select>
	
								<?php
										break;
								}
								?>
									</td>
								<?php $Count++; ?>
								<?php if ($Count == Filterable::FilterMaxColumn) { ?>
								</tr>
                                	<?php $Count = 0; ?>
								<?php } ?>
							<?php } ?>
							<?php if ($Count == 0) { ?>
								<tr colspan="<?=(Filterable::FilterMaxColumn * 2)?>">
							<?php } ?>
									<td valign="middle">
										<div align="right">
											<input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
										</div>
									</td>
							<?php if ($Count == 0) { ?>
								</tr>
							<?php } ?>
							</table>
						</td>
					</tr>
				</table>
			</div>
			
			<?php
		}
	}	
}


class Filter
{
	public $Id;
	public $Description;
	public $Type;
	public $Value;
	public $Attributes;
	public $Options;
	
	public function __construct($Id, $Description, $Type, $Value, $Attributes, $Options)
	{
		$this->Name 		= $Id;
		$this->Description 	= $Description;
		$this->Type 		= $Type;
		$this->Value 		= $Value;
		$this->Attributes 	= $Attributes;
		$this->Options 		= $Options;
	}
}

?>