<?php

class OoikaartPdf extends FPDF {
    function header(){
        
global $rapport;
global $headerWidth;
global $imageWidth;
global $lidId;
global $ooi;
/****** Header *******/

        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);

        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);

/****** EINDE Header *******/

        $this->SetFont('Times','I',7);
        $this->Ln(5);

        $this->Cell(145,4,'',0,0,'',false);         $this->Cell(40,4,'',0,1,'L',false);
        

        $this->Ln(5);

    }
    function Footer(){

        //Go to 1.5 cm from bottom
        $this->SetY(-15);

        $this->SetFont('Times','',8);

        //Cell(float w, float h, string txt, mixed border, int ln, string align, boolean fill, mixed link)
        //width = 0 means the cell is extended up to the right margin
        $this->Cell(0,10,'Pagina '.$this->PageNo()." / {pages}",0,0,'C');
    }
}
