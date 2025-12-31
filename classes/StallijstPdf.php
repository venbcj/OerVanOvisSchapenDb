<?php

class StallijstPdf extends Fpdf {
    function header(){

global $rapport;
global $headerWidth;
global $imageWidth;
global $lidId;
global $stapel;
global $db;
global $lammer;
global $moeders;
global $vaders;
global $PageNo;

/****** Header *******/


        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);

        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);
$p = $this->PageNo();
if($p == 1) {
    $this->SetFont('Times','',12);
    $this->Cell(100,5,'Aantal schapen '. $stapel,'',0,'R',false);
    $this->SetFont('Times','',9);
    $this->Cell(10,5,'waarvan','',1,'',false);
    $this->Ln(2);

    $this->SetFont('Times','',10);
    $this->Cell(80,4,'',0,0,'',false);
    $this->Cell(15,4,'- '.$lammer.' lammeren',0,1,'',false);

    $this->Cell(80,4,'',0,0,'',false);
    $this->Cell(15,4,'- '.$moeders.' moeders',0,1,'',false);

    $this->Cell(80,4,'','',0,'',false);
    $this->Cell(15,4,'- '.$vaders.' vaders','',1,'',false);

    $this->Ln(5);
}
else {     $this->Ln(5); }

    $this->SetFont('Times','',10);
    $this->Cell(45,5,'','',0,'C',false);
    $this->Cell(15,5,'Werknr',0,0,'C',false);
    $this->Cell(25,5,'Levensnummer',0,0,'C',false);
    $this->Cell(15,5,'Geboren',0,0,'C',false);
    $this->Cell(15,5,'Geslacht',0,0,'C',false);
    $this->Cell(15,5,'Generatie',0,1,'C',false);

    //$this->Ln(1);

/****** EINDE Header *******/
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
