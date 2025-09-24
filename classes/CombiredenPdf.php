<?php

class CombiredenPdf extends Fpdf {
    function header(){


global $rapport;
global $headerWidth;
global $imageWidth;
global $array_d;
global $array_p;


        //dummy cel to put logo
        //$this->cell(12,0,'',0,0);
        //is equivalent to:
        //$this->Cell(18);

        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);


        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);
        $this->SetFont('Times','I',7);
        $this->Ln(5);

//dummy cell to give line spacing
        //$this->Cell(0,5,'',0,1);
        //is equivalent to:
        $this->Ln(5);

        $this->SetFont('Times','B',9);
// Regel 1 van hoofding
        $this->SetFillColor(166,198,235);
        $this->SetDrawColor(200,200,200);
        $this->Cell(10,6,'','',0,'',false);
if(isset($array_d)) {
        $this->Cell(40,6,'Redenen t.b.v. uitval','B',0,'C',false); } else { $this->Cell(40,6,'','',0,'',false);
    }
         $this->Cell(35,6,'','',0,'C',false); // Ruimte tussen twee blokken
if(isset($array_p)) {
        $this->Cell(90,6,'Medicijn met reden','B',1,'C',false); } else { $this->Cell(90,6,'','',1,'',false);
    }
// Einde Regel 1 van hoofding
        $this->Ln(5);
// Regel 2 van hoofding
        $this->SetFillColor(166,198,235);
        $this->SetDrawColor(50,50,100);
        $this->Cell(10,3,'','',0,'',false);
if(isset($array_d)) {
        $this->Cell(15,3,'Code','',0,'C',false); } else {    $this->Cell(15,3,'','',0,'',false);
    }
        $this->Cell(25,3,'','',0,'C',false);
         $this->Cell(35,3,'','',0,'C',false); // Ruimte tussen twee blokken
if(isset($array_p)) {
        $this->Cell(15,3,'Code','',0,'C',false);
        $this->Cell(35,3,'','',0,'C',false);
        $this->Cell(15,3,'Stand.','',0,'C',false); }
    else {
                                                            $this->Cell(15,3,'','',0,'',false);
                                                            $this->Cell(35,3,'','',0,'',false);
                                                            $this->Cell(15,3,'','',0,'',false);
    }
        $this->Cell(25,3,'','',1,'C',false);
    
// Einde Regel 2 van hoofding
// Regel 3 van hoofding
        $this->Cell(10,5,'','',0,'',false);
if(isset($array_d)) {
        $this->Cell(15,5,'reader','',0,'C',false);
        $this->Cell(25,5,'Reden','',0,'C',false); } else {
                                                            $this->Cell(15,5,'','',0,'',false);
                                                            $this->Cell(25,5,'','',0,'',false);
    }
         $this->Cell(35,5,'','',0,'',false); // Ruimte tussen twee blokken
if(isset($array_p)) {
        $this->Cell(15,5,'reader','',0,'C',false);
        $this->Cell(35,5,'Medicijn','',0,'C',false);
        $this->Cell(15,5,'aantal','',0,'C',false);
        $this->Cell(25,5,'Reden','',1,'C',false); }
    else {
                                                            $this->Cell(15,5,'','',0,'',false);
                                                            $this->Cell(35,5,'','',0,'',false);
                                                            $this->Cell(15,5,'','',0,'',false);
                                                            $this->Cell(25,5,'','',1,'',false);        
    }
// Einde Regel 3 van hoofding
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
