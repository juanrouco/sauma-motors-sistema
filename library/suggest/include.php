<?php
function IncludeSUGGEST()
{
	?>
	<style>
	.suggest {
		background-color: #FFFFFF;
		border: 1px solid #CCCCFF;
	}
	.suggest .select {
		background-color: #325182;
		color: #FFFFFF;
		cursor: pointer;
	}
	.suggest .unselect {
		background-color: #D2DDEE;
		color: #FFFFFF;
		cursor: pointer;
	}
	.suggest .over {
		background-color: #325182;
		color: #FFFFFF;
		cursor: pointer;
	}
	.suggest .out {
		background-color: #FFFFFF;
		color: #666666;
		cursor: pointer;
	}
	</style>
	
	<script type="text/javascript" src="/library/suggest/prototype.js"></script>
	<script type="text/javascript" src="/library/suggest/suggest.js"></script>
	
	<script type="text/javascript">
	
	function SUGGESTRefresh(Module, Command, TextBoxID, ActiveHandler, IdField, ValueField, FilterField, arrParams)
	{
		var extra = new Array();

		eval(TextBoxID + '_suggest').extraInput 	= extra;
		eval(TextBoxID + '_suggest').arrParams 		= arrParams;
		eval(TextBoxID + '_suggest').module 		= Module;
		eval(TextBoxID + '_suggest').command 		= Command;
		eval(TextBoxID + '_suggest').activeHandler 	= ActiveHandler;
		eval(TextBoxID + '_suggest').idField 		= IdField;
		eval(TextBoxID + '_suggest').valueField 	= ValueField;
		eval(TextBoxID + '_suggest').filterField 	= FilterField;
	}

	function SUGGESTRequest(Module, Command, TextBoxID, ActiveHandler, IdField, ValueField, FilterField, arrParams)
	{
		var Element = document.getElementById(TextBoxID);
		Element.autocomplete = 'off';
		
		if (Element == undefined)
			return undefined;

		document.write("<div class=suggest id=div" + TextBoxID + " style='position: absolute;'></div>");
		eval(TextBoxID + '_suggest = new IncSearch.Suggest(Element, document.getElementById(\'div\' + TextBoxID), null, arrParams);');

		SUGGESTRefresh(Module, Command, TextBoxID, ActiveHandler, IdField, ValueField, FilterField, arrParams);
	}
	
	function absoluteLeft(o)
	{
		var value = 0;
		while (o != undefined)
		{
			if (o.id != 'tabs')
				value += o.offsetLeft;
			o = o.offsetParent;
		}
		return value;
	}
	
	function absoluteTop(o)
	{
		var value = 0;
		while (o != undefined)
		{
			if (o.id != 'tabs')
				value += o.offsetTop;
			o = o.offsetParent;
		}
		return value;
	}
	
	function roundVal(val){
		var dec = 2;
		var result = Math.round(val*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}
	
	</script>
	<?php
}
?>