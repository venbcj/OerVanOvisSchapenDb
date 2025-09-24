<?php

class AfleverlijstPdf extends Fpdf {
    function header(){


global $rapport;
global $headerWidth;
global $imageWidth;
global $bestemming;
global $afvDatum;
global $schpn;

        //dummy cel to put logo
        //$this->cell(12,0,'',0,0);
        //is equivalent to:
        //$this->Cell(18);

        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);


        //dummy cel to put logo
        //$this->cell(12,0,'',0,0);
        //is equivalent to:
        //$this->Cell(12);

        //put logo
        //$this->Image('schaap.jpg',5,7,18);
        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);
        $this->SetFont('Times','I',7);
        $this->Ln(5);

        $this->Cell(145,4,'',0,0,'',false);         $this->Cell(40,4,'Bestemming : '.$bestemming,0,1,'L',false);
        $this->Cell(145,4,'',0,0,'',false);            $this->Cell(40,4,'Afleverdatum : '.$afvDatum,0,1,'L',false);
        $this->Cell(145,4,'',0,0,'',false);            $this->Cell(40,4,'Aantal schapen : '.$schpn,0,1,'L',false);

//dummy cell to give line spacing
        //$this->Cell(0,5,'',0,1);
        //is equivalent to:
        $this->Ln(5);

        $this->SetFont('Times','B',8);

        $this->SetFillColor(166,198,235);
        $this->SetDrawColor(50,50,100);
        $this->Cell(23,3,'','',0,'',false);
        $this->Cell(15,3,'','',0,'',false);
        $this->Cell(15,3,'','',0,'',false);
        $this->Cell(30,3,'','',0,'',false);
        $this->Cell(18,3,'Datum','',0,'',false);
         $this->Cell(20,3,'','',1,'',false);
        $this->Cell(23,3,'Levensnummer','',0,'',false);
        $this->Cell(15,3,'Werknr','',0,'C',false);
        $this->Cell(15,3,'Gewicht','',0,'C',false);
        $this->Cell(30,3,'Medicijn','',0,'',false);
        $this->Cell(18,3,'toepassing','',0,'',false);
         $this->Cell(20,3,'Wachtdagen','',1,'',false);
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
