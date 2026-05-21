<?php

define(TABLECOL_STRING,				0);
define(TABLECOL_NUMBER,				1);
define(TABLECOL_CURRENCY,			2);
define(TABLECOL_INT,				3);
define(TABLECOL_DATE,				4);
define(TABLECOL_CHECK,				5);
define(TABLECOL_HTML,				6);
define(TABLECOL_ARRAY,				7);
define(TABLECOL_SHORTDATE,			8);
define(TABLECOL_CURRENCY_USD, 		9);
define(TABLECOL_CURRENCY_EUR, 		10);
define(TABLECOL_CURRENCY_USD_EUR, 	11);

define(TABLECOL_ALIGN_LEFT,		0);
define(TABLECOL_ALIGN_CENTER,	1);
define(TABLECOL_ALIGN_RIGHT,	2);

class TableColumn 
{
	public $key;
	public $descripcion;
	public $coltype;	// TABLECOL_
	public $align;		// TABLECOL_ALIGN
	public $width;
	public $visible;
	
	function TableColumn($key, $descripcion, $coltype=TABLECOL_STRING, $align=TABLECOL_ALIGN_LEFT, $width='', $visible=true)
	{
		$this->key = $key;
		$this->descripcion = $descripcion;
		$this->coltype = $coltype;
		$this->align = $align;
		$this->width = $width;
		$this->visible = $visible;
	}

}

class TableRow 
{
	private $cols;
	public $ExtraHTML;
	public $values;

	function TableRow($oCols)
	{
		$this->ExtraHTML = '';
		$this->values = array();
		//$this->cols = array();
		$this->SetCols($oCols);
	}

	public function SetCols($oCols)
	{
		$this->cols = $oCols;
	}

	public function SetValue($key, $value)
	{
		$this->values[$key] = $value;
	}

	public function GetValue($key)
	{
		return $this->values[$key];
	}

}

class Table 
{
	public $cols;
	public $rows;
	public $Name;
	public $onRowClick;
	public $onCheckClick;
	public $onHeaderClick;
	public $cellsNeedID;
	public $showScroll;
	public $allowSelection;

	function Table()
	{
		$this->cols = array();
		$this->rows = array();
		$this->Name = 'lst__';
		$this->cellsNeedID = true;
		$this->showScroll = false;
		$this->onHeaderClick = '';
		$this->onRowClick = '';
		$this->allowSelection = false;
	}

	public function AddColumn($key,
	                          $descripcion,
	                          $coltype = TABLECOL_STRING,
	                          $align = TABLECOL_ALIGN_LEFT,
	                          $width = '',
	                          $visible = true)
	{
		$col = new TableColumn($key, $descripcion, $coltype, $align, $width, $visible);
		$this->cols[$key] = $col;
		return $col;
	}

	public function NewRow()
	{
		$row = new TableRow($this->cols);
		return $row;
	}
	
	public function AddRow($oRow)
	{
		array_push($this->rows, $oRow);
	}

	public function Write()
	{
		?>
		<?php if ($this->showScroll) { ?>
			<div class=table id='<?=$this->Name?>_div' style='float: left; width: 95%; wisdth: expression(this.parentElement.clientWidth - 38); overflow-x: hidden;'>
		<?php } else { ?>
			<div class=table>
		<?php } ?>
		<table cellspacing=1 id='<?=$this->Name?>_table' background=#0000ff>
			<?php
			foreach ($this->cols as $col)
			{
				if ($col->key == '')
					continue;
					
				print '<col ';
				switch ($col->align)
				{
				default:
				case TABLECOL_ALIGN_LEFT:
					print 'align=left ';
					break;
				case TABLECOL_ALIGN_CENTER:
					print 'align=center ';
					break;
				case TABLECOL_ALIGN_RIGHT:
					print 'align=right ';
					break;
				}
				print 'style=\'width: '.$col->width.';\' ';
				print 'style=\'white-space: nowrap;\' ';
				/*if (!$col->visible)
					print 'style=\'display: none;\' ';*/
				print '>';
			} ?>

			<thead>
				<tr>
					<?php
					foreach ($this->cols as $col)
					{
						if ($col->key == '')
							continue;
							
						print '<td ';
						if (!$col->visible)
							print 'style=\'display: none;\' ';
						if ($this->onHeaderClick != '') {
						  print 'style="cursor: pointer;" ';
						  print 'onclick="javascript: '.$this->onHeaderClick.'(\''.$col->key.'\');"';
						}
						print '>';

						/*if ($this->onHeaderClick != '') {
						  print '<a href="javascript: '.$this->onHeaderClick.'(\''.$col->key.'\');">';
						}*/
	
						if ($col->coltype == TABLECOL_CHECK)
						{
							print '<input type=checkbox ';
							if ($this->onCheckClick != '')
								print 'onclick="javascript: '.$this->Name.'_onCheckAll();" ';
							print 'id="'.$this->Name.'"-call>';
						}
						else
							print htmlentities($col->descripcion);
						/*if ($this->onHeaderClick != '') {
						  print '</a>';
						}*/
						print '</td>';
					} ?>
				</tr>
			</thead>

			<tbody>
				<?php
				$index = 0;
				
				//print_r($this->rows);
				
				foreach ($this->rows as $row)
				{
					print '<tr ';
					if ($index % 2)
						print 'class=odd ';
					else
						print 'class=even ';
					if ($this->cellsNeedID)
						print "id='".$this->Name."-i$index' ";
					print 'onmouseover="javascript: '.$this->Name.'_u(this);" ';
					print 'onmouseout="javascript: '.$this->Name.'_d(this);" ';
					if ($this->allowSelection || $this->onRowClick)
					{
						print 'onclick="javascript: ';
						if ($this->allowSelection) print $this->Name.'_onSelect('.$index.'); ';
						if ($this->onRowClick) print $this->Name.'_onClick('.$index.'); ';
						print '" ';
					}
					print $row->ExtraHTML.' ';
					print ">";

					$colindex = 0;
					foreach ($this->cols as $col)
					{
						if ($col->key == '')
							continue;
							
						print '<td ';
						if (!$col->visible)
							print 'style=\'display: none;\' ';
						if ($this->cellsNeedID)
							print "id='".$this->Name."-i$index-".$col->key."' ";

						switch ($col->coltype)
						{
						case TABLECOL_NUMBER:
						case TABLECOL_INT:
							print '>';
							if ($row->GetValue($col->key) != null)
								print number_format($row->GetValue($col->key));
							break;

						case TABLECOL_CURRENCY:
							print '>';
							if ($row->GetValue($col->key) != null)
								print '$ '.number_format($row->GetValue($col->key), 2, '.', '');
								//print '$ '.number_format($row->GetValue($col->key));
							break;

						case TABLECOL_CURRENCY_USD:
							print '>';
							if ($row->GetValue($col->key) != null)
								print 'u$s '.number_format($row->GetValue($col->key), 2, '.', '');
								//print 'U$S '.number_format($row->GetValue($col->key));
							break;							

						case TABLECOL_CURRENCY_EUR:
							print '>';
							if ($row->GetValue($col->key) != null)
								print '€ '.number_format($row->GetValue($col->key), 2, '.', '');
								//print 'U$S '.number_format($row->GetValue($col->key));
							break;							

						case TABLECOL_CURRENCY_USD_EUR:
							print '>';
							if ($row->GetValue($col->key) != null)
							{
								$dolar = substr($row->GetValue($col->key), 0, (strpos($row->GetValue($col->key), '//') - 1));
								$euro = substr($row->GetValue($col->key), (strpos($row->GetValue($col->key), '//') + 3), strlen($row->GetValue($col->key)));
								
								print 'u$s '.number_format($dolar, 2, '.', '').' - € '.number_format($euro, 2, '.', '');
							}
							break;							

						case TABLECOL_DATE:
							print '>';
							if ($row->GetValue($col->key) != null)
								print $row->GetValue($col->key);
							break;

						case TABLECOL_SHORTDATE:
							print '>';
							if ($row->GetValue($col->key) != null)
								print substr($row->GetValue($col->key), 0, 10);
							break;

						case TABLECOL_STRING:
							print '>';
							if ($row->GetValue($col->key) != null)
								print htmlentities($row->GetValue($col->key));	
							break;
							
						case TABLECOL_CHECK:
							print '<input type=checkbox value=1 name=a>aa';
							break;

						default:
						case TABLECOL_HTML:
							print '>';
							if ($row->GetValue($col->key) != null)
								print $row->GetValue($col->key);
						}
						print '</td>';
						$colindex++;
					}
					$index++;
					print "</tr>\n";
	
				}
				?>
			</tbody>

		</table>
		</div>
		<?php if ($this->showScroll) { ?>
		<div class=scroll onclick="javascript: <?=$this->Name?>_scrollLeft();" onmousedown="javascript: <?=$this->Name?>_onDown(this);" onmouseup="javascript: <?=$this->Name?>_onUp(this);"  ondblclick="javascript: <?=$this->Name?>_scrollLeft();" style="height: expression(<?=$this->Name?>_table.clientHeight - 2);"></div>
		<div class=scroll onclick="javascript: <?=$this->Name?>_scrollRight();" onmousedown="javascript: <?=$this->Name?>_onDown(this);" onmouseup="javascript: <?=$this->Name?>_onUp(this);" ondblclick="javascript: <?=$this->Name?>_scrollRight();" style="height: expression(<?=$this->Name?>_table.clientHeight - 2);"></div>
		<?php } ?>

		<script language=javascript>
		<?=$this->Name?>_length = <?=$index?>;

		<?php if ($this->showScroll) { ?>
		function <?=$this->Name?>_scrollRight()
		{
			<?=$this->Name?>_div.scrollLeft += 75;
		}		
		function <?=$this->Name?>_scrollLeft()
		{
			<?=$this->Name?>_div.scrollLeft -= 75;
		}		
		function <?=$this->Name?>_onDown(o)
		{
			o.style.borderLeftColor = 'buttonshadow'; 
			o.style.borderTopColor = 'buttonshadow'; 
			o.style.borderRightColor = 'buttonhighlight'; 
			o.style.borderBottomColor = 'buttonhighlight'; 
		}
		function <?=$this->Name?>_onUp(o)
		{
			o.style.borderLeftColor = 'buttonhighlight'; 
			o.style.borderTopColor = 'buttonhighlight'; 
			o.style.borderRightColor = 'buttonshadow'; 
			o.style.borderBottomColor = 'buttonshadow'; 
		}
		<?php } ?>
		function <?=$this->Name?>_u(o)
		{
			<?php if ($this->onRowClick != '' || $this->allowSelection) { ?>
				if (document.all) {
				o.style.cursor = 'hand';
				} else {
				o.style.cursor = 'pointer';
				}
			<?php } else { ?>
				o.style.cursor = 'default';
			<?php } ?>
			<?php if ($this->allowSelection) { ?>
			if (parseInt(o.id.replace('<?=$this->Name?>-i', '')) == indexSelected)
				return;
			<?php } ?>
			o.style.backgroundColor='#d2ddee';
		}
		function <?=$this->Name?>_d(o)
		{
			<?php if ($this->allowSelection) { ?>
			if (parseInt(o.id.replace('<?=$this->Name?>-i', '')) == indexSelected)
				return;
			<?php } ?>
			o.style.backgroundColor=o.bgColor;
		}
		function <?=$this->Name?>_getRow(index)
		{
			var o = new Object();
			o.index = new Number();
			o._row = undefined;
			<?php
			foreach ($this->cols as $col)
			{
				if ($col->key == '')
					continue;
					
				switch ($col->coltype)
				{
				case TABLECOL_NUMBER:
				case TABLECOL_CURRENCY:
				case TABLECOL_INT:
					print 'o.'.$this->ScriptString($col->key)." = new Number();\n";
					break;
				default:
					print 'o.'.$this->ScriptString($col->key)." = new String();\n";
				}
			}
			?>
			try {
				o.index	= index;
				o._row = document.getElementById('<?=$this->Name?>-i' + index);
				<?php
				foreach ($this->cols as $col)
				{
					if ($col->key == '')
						continue;
						
					switch ($col->coltype)
					{
					case TABLECOL_CHECK:
						print 'o.'.$this->ScriptString($col->key)." = document.getElementById('".$this->Name."-citem' + index).checked;\n";
						break;
					
					case TABLECOL_NUMBER:
					case TABLECOL_CURRENCY:
					Case TABLECOL_INT:
						print 'if(document.all){';
						print 'o.'.$this->ScriptString($col->key)." = parseInt(document.getElementById('".$this->Name."-i' + index + '-".$this->ScriptString($col->key)."').innerText);\n";
						print '} else {';
						print 'o.'.$this->ScriptString($col->key)." = parseInt(document.getElementById('".$this->Name."-i' + index + '-".$this->ScriptString($col->key)."').textContent);\n";
						print '}';
						break;
					default:
						print 'if(document.all){';
						print 'o.'.$this->ScriptString($col->key)." = document.getElementById('".$this->Name."-i' + index + '-".$this->ScriptString($col->key)."').innerText;\n";
						print '} else {';
						print 'o.'.$this->ScriptString($col->key)." = document.getElementById('".$this->Name."-i' + index + '-".$this->ScriptString($col->key)."').textContent;\n";
						print '}';
					}
				}
				?>

			} catch (e)
			{
				return undefined;
			}
			
			return o;
		}
		function <?=$this->Name?>_toXML()
		{
			return document.getElementById('<?=$this->Name?>_table').outerHTML;
		}
		function <?=$this->Name?>_Export2Excel()
		{
			waitCursor();
			window.setTimeout(<?=$this->Name?>__Export2ExcelPart2, 1000);	
		}
		function <?=$this->Name?>__Export2ExcelPart2()
		{
			var excel;
			var book;
			var sheet;
			var e;
			
			try {
				excel = new ActiveXObject('Excel.Application');
			} catch(e) {
				alert('Debe estar instalado Microsoft Excel en tu computadora y este sitio debe configurarse como Sitio de confianza');
				document.body.style.cursor = 'default';
				return -1;
			}
			
			window.clipboardData.clearData();
			window.clipboardData.setData('Text', <?=$this->Name?>_toXML());
			
			book = excel.Workbooks.Add();
			book.Activate();

			sheet = book.Worksheets.Add();
			sheet.Paste();
			
			excel.Visible = true;
			
			defaultCursor();
			return 0;
		}
		<?php if ($this->allowSelection != '') { ?>
		var indexSelected = -1;
		function <?=$this->Name?>_onSelect(index)
		{
			var oS = <?=$this->Name?>_getRow(index);
			var oP = <?=$this->Name?>_getRow(indexSelected);
			indexSelected = index;

			if (oP != undefined && oP._row != undefined)
				oP._row.style.backgroundColor='';
			if (oS != undefined && oS._row != undefined)
				oS._row.style.backgroundColor='#ddeed2';
		}
		<?php } ?>
		<?php if ($this->onRowClick != '') { ?>
		function <?=$this->Name?>_onClick(index)
		{
			var o = <?=$this->Name?>_getRow(index);
			<?=$this->onRowClick?>(o);
		}
		<?php } ?>
		</script>
		<?php
	}

	function ScriptString($string)
	{
		$string = str_replace(" ", "_", $string);
		
		return $string;
	}

}	

?>
