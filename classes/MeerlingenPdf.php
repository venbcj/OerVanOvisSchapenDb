<?php

class MeerlingenPdf extends Fpdf {
    function header(){


global $rapport;
global $headerWidth;
global $imageWidth;

        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);

        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);
        $this->SetFont('Times','I',7);
        $this->Ln(5);


        $this->Ln(5);

        $this->SetFont('Times','B',9);

        $this->SetFillColor(166,198,235);
        $this->SetDrawColor(50,50,100);

        $this->Cell(30,5,'','',0,'',false);
        $this->Cell(20,5,'Lammeren','',0,'C',false);
        $this->Cell(15,5,'Geslacht','',0,'C',false);
        $this->Cell(16,5,'Worpgrootte','',0,'C',false);
        $this->Cell(21,5,'Werpdatum','',0,'C',false);
        $this->Cell(40,5,'Gem. groei per dag','',0,'',false);
        $this->Cell(15,5,'Ooi','',1,'C',false);
        
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
