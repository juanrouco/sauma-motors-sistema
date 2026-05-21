<?php

require_once('class.dropdown.php');

class DropDownProvider extends DropDown
{
	public function __construct(IDataProvider $oProvider, $DescriptionRow, $ValueRow)
	{
		DropDown::__construct();
		
		$oProvider->MoveFirst();
		
		while ($oRow = $oProvider->GetRow())
		{
			$this->AddElement($oRow[$DescriptionRow], $oRow[$ValueRow]);
			$oProvider->MoveNext();
		}
	}
}

?>