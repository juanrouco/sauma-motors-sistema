<?php

class XlsExport
{
    public $FileName; 	#Nombre del archivo 
    public $Xls;       	#Contenido del archivo 


	public function __construct()
	{
		$this->FileName   = "export";
		$this->Xls        = "";
	}


    private function Head($file_name = "")
	{
        $this->FileName = ($file_name == "") ? $this->FileName : $file_name; 
        $file = $this->FileName; 

        header("Pragma: no-cache"); 
        header("Expires: -1"); 
        header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Content-Type: application/x-unknown");
        header("Content-Disposition: attachment; filename=$file.xls;");
    } 


    private function BOF()
	{
        /* inicio de archivo */
        return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
    } 


    private function EOF()
	{
        /* fin de archivo */
        return pack("ss", 0x0A, 0x00); 
    } 


    public function Number($Row, $Col, $Value)
	{
        $this->Xls.= pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
        $this->Xls.= pack("d", $Value); 
    } 


    public function Text($Row, $Col, $Value)
	{
        $this->Xls.= pack("ssssss", 0x204, 8 + strlen($Value), $Row, $Col, 0x0, strlen($Value));
        $this->Xls.= $Value;

        //$Value2UTF8 = utf8_decode($Value);
		
        //$this->Xls.= pack("ssssss", 0x204, 8 + strlen($Value2UTF8), $Row, $Col, 0x0, strlen($Value2UTF8));
        //$this->Xls.= $Value2UTF8;
    } 


    public function Write($Row, $Col, $Value)
	{
        if (is_numeric($Value)) 
		{
			$this->Number($Row, $Col, $Value);
		}
        else 
		{
			$this->Text($Row, $Col, $Value);
		}
    } 


    public function WriteArray(array $arrData)
	{
        $this->Xls = ""; 
        $nRow = 0; 
        $nCol = 0; 
		
        foreach ($arrData as $Row)
		{
            foreach ($Row as $Value)
			{
                $this->Write($nRow, $nCol, $Value); 
                $nCol++; 
            }
			
            $nCol = 0; 
            $nRow++; 
        } 
    }


    public function Download($file_name = "")
	{
		/* limpiamos el buffer de salida */
		ob_clean();
		
        /* escribe el archivo y agrega las cabeceras para generar la descarga */
        $this->Head($file_name);
		
        echo $this->BOF();
        echo $this->Xls;
        echo $this->EOF();
    }


    public function Save($loc_file)
	{
        //Crea archivo, borrando el que existe si ya existia 
        //$loc_file : Ruta del archivo. Ej: "./downloads/archivo.xls" 
        $f = fopen($loc_file, 'w'); 
		
        fwrite($f, $this->BOF()); 
        fwrite($f, $this->Xls); 
        fwrite($f, $this->EOF()); 
		
        fclose($f); 
    } 
}

?>