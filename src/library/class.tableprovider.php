<?php 

require_once 'class.table.php';

class TableProvider extends Table
{
	private $pageSize;
	private $pageNum;
	public $PageSection;
	private $isEmpty;
	private $pageCount;

	function TableProvider(IDataProvider $oProvider, $pageSize=0, $pageNum=0)
	{
		Table::Table();

		$oCols = $oProvider->GetCols();
		
		/* si no tiene ninguna columna */
		if (!$oCols)
		{
			$this->isEmpty = true;
			return;
		}
		
		foreach($oCols as $col)
		{
			$this->AddColumn($col->GetName(), $col->GetName());
		}

		if ($pageSize > 0)
			$this->pageCount = ceil($oProvider->GetRowsCount() / $pageSize);

		if ($pageNum < 1)
			$this->pageNum = 0;
			
		if ($pageNum >= $this->pageCount && $this->pageCount > 0)
			$pageNum = $this->pageCount - 1;

		$this->pageSize = $pageSize;
		$this->pageNum = $pageNum;	
		
		if ($pageNum == 0)
			$oProvider->MoveFirst();
		
		if ($pageNum > 0)
			$oProvider->Seek($pageNum * $pageSize);
			
		$nreg = 1;
		while ($oRow = $oProvider->GetRow())
		{
			if ($nreg > $pageSize && $pageSize != 0)
				break;

			$row = $this->NewRow();
			foreach($oCols as $col)
			{
				if (is_array($oRow[$col->GetName()]))
					continue;
				if (is_object($oRow[$col->GetName()]))
					continue;
				$row->SetValue($col->GetName(), $oRow[$col->GetName()]);
			}
			$this->AddRow($row);

			$oProvider->MoveNext();
			$nreg++;
		}
	}

	public function Write()
	{
		if ($this->isEmpty)
		{
			return;
		}
		
		Table::Write();
		
		if ($this->pageSize <= 0)
			return;
			
			
		if ($this->PageSection == '')
		{
			if ($this->GetCurrentPage() > 0)
				print "&nbsp;<a href='javascript: try { ".$this->Name."_SetPage(".($this->GetCurrentPage() - 1)."); } catch(e) { }'>&lt;</a>&nbsp;";
			
			print "Pagina "."<input type='text' name='Page' id='Page' value='".($this->GetCurrentPage() + 1)."' size='3' maxlength='3' style='background-color:#4DC7DF; font-size: 11px; color:#000000; border:none;' onKeyPress='javascript: if (event.keyCode == 13) ".$this->Name."_SetPage(this.value - 1); if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;' />"." de ".$this->GetPagesCount();			

			if ($this->GetCurrentPage() + 1 < $this->GetPagesCount())
				print "&nbsp;<a href='javascript: try { ".$this->Name."_SetPage(".($this->GetCurrentPage() + 1)."); } catch(e) { }'>&gt;</a>&nbsp;";

			return;
		}
		
		print $this->PageSection;

	}
	
	public function GetCurrentPage()
	{
		return $this->pageNum;
	}

	public function SetCurrentPage($page)
	{
		$this->pageNum = $page;
	}
	
	public function GetPagesCount()
	{
		return $this->pageCount;
	}
	
}

?>