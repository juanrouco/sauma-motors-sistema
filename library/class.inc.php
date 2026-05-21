<?php

function __autoload($ClassName)
{
	$ClassFile 	= 'class.' . $ClassName . '.php';
	$Path 		= 'library/';
	
	require_once($Path . $ClassFile);
	
	if (!(class_exists($ClassName)))	
		return false;
		
	return true;
}

?>