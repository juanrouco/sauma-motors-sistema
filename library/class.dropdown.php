<?php

class DropDown
{
	public $Name;
	public $Values;
	public $ExtraHTML;
	public $NullValue;
	public $NullDescription;
	public $Enabled;
	private $Index;
	
	public function __construct()
	{
		$this->Name 			= 'lst';
		$this->Index 			= 0;
		$this->Values 			= array();
		$this->NullDescription 	= '[SELECCIONE]';
		$this->NullValue 		= '';
		$this->Enabled 			= true;
	}
	

	public function Write()
	{
		print "<SELECT NAME='" . $this->Name ."' ID='". $this->Name ."' ";
		print $this->ExtraHTML;
		if (!$this->Enabled)
			print " DISABLED";
		print " >";
		
		if ($this->NullValue != '' || $this->NullDescription != '')
		{
			print "<OPTION";
			print " VALUE='" . $this->NullValue . "'";
			print ">" . $this->NullDescription . "</OPTION>";
		}
		
		foreach ($this->Values as $element)
		{
			print "<OPTION";
			print " VALUE='" . $element->Value . "'";
			if ($element->Selected)
				print " SELECTED";
			print ">" . $element->Description . "</OPTION>";
		}
		
		print "</SELECT>";
	}


	public function AddElement($description, $value)
	{
		$element = new DropDownElement();
		$element->Description = $description;
		$element->Value = $value;
		array_push($this->Values, $element);
	}


	public function SetSelected($value)
	{
		foreach ($this->Values as $element)
			$element->Selected = ($element->Value == $value);
	}
} 


class DropDownElement
{
	public $Description;
	public $Value;
	public $Selected;
	
	public function __construct()
	{
		$this->Description = '';
		$this->Value = '';
		$this->Selected = false;
	}
}

?>