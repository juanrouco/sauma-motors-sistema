<?php

class Page
{
	public $Current;
	public $Size;
	public $CurrentSection;
	public $SizeSection;
	
	public function __construct($Current = 1, $Size = 20, $CurrentSection = 1, $SizeSection = 5)
	{
		$this->Current 			= ($Current != 0) ? $Current : 1;
		$this->Size 			= ($Size != 0) ? $Size : 20;
		$this->CurrentSection 	= $CurrentSection;
		$this->SizeSection 		= $SizeSection;
	}
}


class Section
{
	public $Current;
	public $Size;
	
	public function __construct($Current = 0, $Size = 4)
	{
		$this->Current 	= $Current;
		$this->Size 	= $Size;
	}
}


abstract class Pageable
{
	static function ParsePage(Page $oPage)
	{
		$sql = '';

		if ($oPage->Current <= 0)
			$oPage->Current = 1;

		if (isset($oPage->Current))
			$sql.= " LIMIT " . DB::Number(($oPage->Current - 1) * $oPage->Size);
			
		if (isset($oPage->Size))
			$sql.= ", " . DB::Number($oPage->Size);
				
		return $sql;
	}
	
	static function PrintPaginator(Page $oPage, $CountRows, $CountRowsEdit = false, $Alias = '')
	{
		$CountRowsEdit = false;
		/* determinamos la catnitdad de paginas disponibles */
		$PagesCount = $CountRows / $oPage->Size;
		$PagesCount = ceil($PagesCount);
		
		/* si la pagina es superior a la maxima existente, entonces asignamos esta */
		if ($oPage->Current > $PagesCount)
			$oPage->Current = $PagesCount;
		
		if ($oPage->Size == '' || $oPage->Size == '0')
			$oPage->Size = 5;
		
		$str 			= '';
		$PagePrevious	= $oPage->Current - 1;
		$PageNext		= $oPage->Current + 1;

		if ($PagePrevious <= 1)
			$PagePrevious = 1;

		/* si hay mas de una pagina... */		
		if ($PagesCount > 1) 
		{			
			if ($PageNext >= $PagesCount)
			{
				$PageNext = $PagesCount;
			}
			
			$CurrentSection 	= ceil($oPage->Current / $oPage->SizeSection)-1;
			$CountSections 		= ceil($PagesCount / $oPage->SizeSection);
			$Page 				= $oPage->Current;
			
			$str.= "<span class='PaginadorPaginas'>P&aacute;ginas </span>";
			
			if ($Page != 1) 
			{				
				$str.= "<a href='javascript: SetPage" . $Alias . "(1);' class='PaginadorLinks'>&lt;&lt;</a>&nbsp;&nbsp;";
				$str.= "<a href='javascript: SetPage" . $Alias . "(" . $PagePrevious . ");' class='PaginadorLinks'>Anterior</a>&nbsp;&nbsp;";
			}

			$Page = ($CurrentSection % $oPage->Current * $oPage->SizeSection ) + 1;

			for ($i=0; $i<$oPage->SizeSection; $i++)
			{
				if ($Page != 0 && $Page <= $PagesCount)
				{
					if ($Page != $oPage->Current)
					{
						if ($Page <= $PagesCount)
						{
							$str.= "<a href='javascript: SetPage" . $Alias . "(" . $Page . ");' class='PaginadorLinks'>" . $Page . "</a>&nbsp;&nbsp;";
						}
					}
					else
					{
						$str.= "<b>" . $Page . "</b>&nbsp;&nbsp;";
					}		
				}
				
				$Page ++;
			}

			/* fechas de anterior y siguiente */
			if (($oPage->Current != $PagesCount) &&( $PagesCount != "")) 
			{ 
				$str.= "<a href='javascript: SetPage" . $Alias . "(" . $PageNext . ");' class='PaginadorLinks'>Siguiente</a>&nbsp;&nbsp;";
				$str.= "<a href='javascript: SetPage" . $Alias . "(" . $PagesCount . ");' class='PaginadorLinks'>&gt;&gt;</a>&nbsp;&nbsp;";
			}
			
			//$str.= " - Total: " . $PagesCount ;
		}
		elseif ($PagesCount == 1)
		{
			$str = "<span class='PaginadorPaginas'>P&aacute;gina 1</span>";
		}
		
		if ($CountRows != NULL)
		{
			$str.= "<span class='PaginadorPaginas'> - Total registros " . $CountRows . "</span>";
		}

		/* si se puede editar la cantidad de registros a visualizar... */
		if ($CountRowsEdit)
		{
			if ($CountRows > 5)
			{
				$str.= "<span class='PaginadorPaginas'> | Mostrar </span>";
				$str.= "<select class='campoPaginador' onchange='javascript: SetPageSize(this.value);'>";
				$selected = ($oPage->Size == 5) ? "selected='selected'" : "";
				$str.= "<option value='" . 5 . "'" . $selected . ">" . 5 . "</option>";
				$selected = ($oPage->Size == 10) ? "selected='selected'" : "";
				$str.= "<option value='" . 10 . "'" . $selected . ">" . 10 . "</option>";
				$selected = ($oPage->Size == 20) ? "selected='selected'" : "";
				$str.= "<option value='" . 20 . "'" . $selected . ">" . 20 . "</option>";
				$selected = ($oPage->Size == 25) ? "selected='selected'" : "";
				$str.= "<option value='" . 25 . "'" . $selected . ">" . 25 . "</option>";
				$selected = ($oPage->Size == 50) ? "selected='selected'" : "";
				$str.= "<option value='" . 50 . "'" . $selected . ">" . 50 . "</option>";
				$selected = ($oPage->Size == 100) ? "selected='selected'" : "";
				$str.= "<option value='" . 100 . "'" . $selected . ">" . 100 . "</option>";
				$selected = ($oPage->Size == 250) ? "selected='selected'" : "";
				$str.= "<option value='" . 250 . "'" . $selected . ">" . 250 . "</option>";
				$selected = ($oPage->Size == 500) ? "selected='selected'" : "";
				$str.= "<option value='" . 500 . "'" . $selected . ">" . 500 . "</option>";
				$selected = ($oPage->Size == 1000) ? "selected='selected'" : "";
				$str.= "<option value='" . 1000 . "'" . $selected . ">" . 1000 . "</option>";
				$str.= "</select>";
				$str.= "<span class='PaginadorPaginas'> registros</span>";
			}
		}

		return $str;
	}	
}

?>