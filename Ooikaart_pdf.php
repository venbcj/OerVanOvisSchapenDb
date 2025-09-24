<?php

require_once("autoload.php");

//https://www.youtube.com/watch?v=CamDi3Syjy4

include "just_connect_db.php";

$ooi = $_GET['Id'];

$rapport = 'Ooikaart';
$Afdrukstand = 'L';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

Session::start();

    $lidId = Session::get("I1");

$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }



$pdf = new OoikaartPdf($Afdrukstand,'mm','A4'); //use new class

$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

/****** BODY ******/

/* Gegevens moederdier */

    $pdf->SetFont('Times','B',12);
    $pdf->SetDrawColor(200,200,200); // Grijs
    $pdf->Cell(80,5,'Moederdier','',1,'C',false);
    $pdf->Ln(5);

    $pdf->SetFont('Times','B',10);
    $pdf->Cell(80,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Aantal',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Aantal',0,0,'C',false);
    $pdf->Cell(15,3,'%',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Gem.',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Gem.',0,0,'C',false);
    $pdf->Cell(20,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Gem.',0,1,'C',false);

    $pdf->Cell(60,3,'',0,0,'C',false);
    $pdf->Cell(20,3,'Geboorte',0,0,'C',false);
    $pdf->Cell(15,3,'dagen',0,0,'C',false);
    $pdf->Cell(15,3,'Aantal',0,0,'C',false);
    $pdf->Cell(15,3,'levend',0,0,'C',false);
    $pdf->Cell(15,3,'levend',0,0,'C',false);
    $pdf->Cell(15,3,'Aantal',0,0,'C',false);
    $pdf->Cell(15,3,'Aantal',0,0,'C',false);
    $pdf->Cell(15,3,'geboorte',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'speen',0,0,'C',false);
    $pdf->Cell(20,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'aflever',0,1,'C',false);

    $pdf->Cell(10,3,'','',0,'C',false);
    $pdf->Cell(25,3,'Levensnummer',0,0,'C',false);
    $pdf->Cell(15,3,'Werknr',0,0,'C',false);
    $pdf->Cell(10,3,'Ras',0,0,'C',false);
    $pdf->Cell(20,3,'datum',0,0,'C',false);
    $pdf->Cell(15,3,'moeder',0,0,'C',false);
    $pdf->Cell(15,3,'lammeren',0,0,'C',false);
    $pdf->Cell(15,3,'geboren',0,0,'C',false);
    $pdf->Cell(15,3,'geboren',0,0,'C',false);
    $pdf->Cell(15,3,'ooien',0,0,'C',false);
    $pdf->Cell(15,3,'rammen',0,0,'C',false);
    $pdf->Cell(15,3,'gewicht',0,0,'C',false);
    $pdf->Cell(15,3,'Gespeend',0,0,'C',false);
    $pdf->Cell(15,3,'gewicht',0,0,'C',false);
    $pdf->Cell(20,3,'Afgeleverd',0,0,'C',false);
    $pdf->Cell(15,3,'gewicht','',1,'C',false);

    $pdf->Ln(1);

$zoek_moederdier = mysqli_query($db,"
SELECT mdr.levensnummer, right(mdr.levensnummer,$Karwerk) werknr, r.ras, date_format(hg.datum,'%d-%m-%Y') geb_datum, date_format(hop.datum,'%d-%m-%Y') aanvoerdm, count(lam.schaapId) lammeren, datediff(current_date(),ouder.datum) dagen, count(ooi.schaapId) aantooi, count(ram.schaapId) aantram,
 count(lam.levensnummer) levend, round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven, round(avg(hg_lm.kg),2) gemgewicht,
 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn, min(hs_lm.kg) minspnkg, max(hs_lm.kg) maxspnkg, round(avg(hs_lm.kg),2) gemspnkg,
 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg
FROM tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join (
     select s.schaapId, s.levensnummer, s.volwId
     from tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
     where st.lidId = ".mysqli_real_escape_string($db,$lidId)."
 ) lam on (v.volwId = lam.volwId)
 join (
    select max(stalId) stalId, mdr.schaapId
    from tblStal st
     join tblSchaap mdr on (st.schaapId = mdr.schaapId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and mdr.schaapId = ".mysqli_real_escape_string($db,$ooi)."
    group by mdr.schaapId
 ) maxst on (maxst.schaapId = mdr.schaapId)
 join (
    select st.schaapId, h.datum
    from tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    where h.actId = 3 and h.skip =0
 ) ouder on (mdr.schaapId = ouder.schaapId)
 left join tblHistorie hg on (maxst.stalId = hg.stalId and hg.actId = 1)
 left join tblHistorie hop on (maxst.stalId = hop.stalId and (hop.actId = 2 or hop.actId = 11) )
 left join tblRas r on (r.rasId = mdr.rasId)
 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1)
 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4)
 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12)
 
group by mdr.levensnummer, mdr.geslacht, r.ras, date_format(hg.datum,'%d-%m-%Y'), date_format(hop.datum,'%d-%m-%Y')
order by right(mdr.levensnummer,$Karwerk) desc
") or die (mysqli_error($db));    


while($row = mysqli_fetch_assoc($zoek_moederdier))
            {
                $levnr = $row['levensnummer'];
                $werknr = $row['werknr'];
                $ras = $row['ras'];
                $gebdm = $row['geb_datum'];
                $aanvdm = $row['aanvoerdm']; if(isset($gebdm)) { $opdm = $gebdm; } else { $opdm = $aanvdm; }
                $dagen = $row['dagen'];
                $lammeren = $row['lammeren'];
                $levend = $row['levend'];
                $percleven = $row['percleven'];
                $aantooi = $row['aantooi'];
                $aantram = $row['aantram'];
                $gemkg = $row['gemgewicht'];
                $aantspn = $row['aantspn'];
                $gemspn = $row['gemspnkg'];
                $aantafv = $row['aantafv'];
                $gemafv = $row['gemafvkg'];
            
 

       $pdf->SetFont('Times','',8);
        $pdf->Cell(10,10,'','',0,'',false);
        $pdf->Cell(25,10,$levnr,'TB',0,'',false);
        $pdf->Cell(15,10,$werknr,'TB',0,'C',false);
        $pdf->Cell(10,10,$ras,'TB',0,'C',false);
        $pdf->Cell(20,10,$gebdm,'TB',0,'C',false);
        $pdf->Cell(15,10,$dagen,'TB',0,'C',false);
        $pdf->Cell(15,10,$lammeren,'TB',0,'C',false);
        $pdf->Cell(15,10,$levend,'TB',0,'C',false);
        $pdf->Cell(15,10,$percleven,'TB',0,'C',false);
        $pdf->Cell(15,10,$aantooi,'TB',0,'C',false);
        $pdf->Cell(15,10,$aantram,'TB',0,'C',false);
        $pdf->Cell(15,10,$gemkg,'TB',0,'C',false);
        $pdf->Cell(15,10,$aantspn,'TB',0,'C',false);
        $pdf->Cell(15,10,$gemspn,'TB',0,'C',false);
        $pdf->Cell(20,10,$aantafv,'TB',0,'C',false);
        $pdf->Cell(15,10,$gemafv,'TB',1,'C',false);

}


/* Einde Gegevens moederdier */

        $pdf->Ln(10);

/* Gegevens lammeren van moederdier */

        $pdf->SetFont('Times','B',12);
        $pdf->Cell(80,5,'Lammeren van moederdier','',1,'C',false);
        $pdf->Ln(5);

    $pdf->SetFont('Times','B',10);
    $pdf->Cell(160,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Gem.',0,0,'C',false);
    $pdf->Cell(45,3,'',0,0,'C',false);
    $pdf->Cell(20,3,'Gem.',0,1,'C',false);

    $pdf->Cell(130,3,'',0,0,'C',false);
    $pdf->Cell(15,3,'Speen',0,0,'C',false);
    $pdf->Cell(15,3,'Speen',0,0,'C',false);
    $pdf->Cell(15,3,'groei',0,0,'C',false);
    $pdf->Cell(15,3,'Aflever',0,0,'C',false);
    $pdf->Cell(15,3,'Aflever',0,0,'C',false);
    $pdf->Cell(15,3,'',0,0,'C',false);
    $pdf->Cell(20,3,'groei',0,1,'C',false);

    $pdf->Cell(10,3,'','',0,'C',false);
    $pdf->Cell(25,3,'Levensnummer',0,0,'C',false);
    $pdf->Cell(15,3,'Werknr',0,0,'C',false);
    $pdf->Cell(15,3,'Generatie',0,0,'C',false);
    $pdf->Cell(15,3,'Geslacht',0,0,'C',false);
    $pdf->Cell(20,3,'Ras',0,0,'C',false);
    $pdf->Cell(15,3,'Geboren',0,0,'C',false);
    $pdf->Cell(15,3,'Gewicht',0,0,'C',false);
    $pdf->Cell(15,3,'datum',0,0,'C',false);
    $pdf->Cell(15,3,'gewicht',0,0,'C',false);
    $pdf->Cell(15,3,'spenen',0,0,'C',false);
    $pdf->Cell(15,3,'datum',0,0,'C',false);
    $pdf->Cell(15,3,'gewicht',0,0,'C',false);
    $pdf->Cell(15,3,'Reden',0,0,'C',false);
    $pdf->Cell(20,3,'afleveren',0,1,'C',false);

    $pdf->Ln(1);


$zoek_lammeren = mysqli_query($db,"
select s.levensnummer, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, ouder.datum dmaanw, date_format(hg.datum,'%d-%m-%Y') gebrndm, date_format(hg.datum,'%Y-%m-%d') dmgebrn, hg.kg gebrnkg, date_format(hs.datum,'%d-%m-%Y') speendm, hs.kg speenkg, 

case when hs.kg-hg.kg > 0 and datediff(hs.datum,hg.datum) > 0 then round(((hs.kg-hg.kg)/datediff(hs.datum,hg.datum)*1000),2) end gemgr_s,

date_format(haf.datum,'%d-%m-%Y') afvdm, haf.kg afvkg, date_format(hdo.datum,'%d-%m-%Y') uitvaldm, re.reden, 

case when haf.kg-hg.kg > 0 and datediff(haf.datum,hg.datum) > 0 then round(((haf.kg-hg.kg)/datediff(haf.datum,hg.datum)*1000),2) end gemgr_a
from tblSchaap s
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId) 
 join tblStal st on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join tblReden re on (s.redId = re.redId)
 join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1)
 left join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4)
 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12)
 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14)
 join tblStal st_all on (st_all.schaapId = s.schaapId)
 left join tblHistorie ouder on (st_all.stalId = ouder.stalId and ouder.actId = 3)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and v.mdrId = ".mysqli_real_escape_string($db,$ooi)."
order by hg.datum        ") or die (mysqli_error($db));    
    while($lam = mysqli_fetch_assoc($zoek_lammeren))
            {
                if (empty($lam['levensnummer'])) {$Llevnr = 'Geen';} else {$Llevnr = $lam['levensnummer'];}
                $Lwerknr = $lam['werknr'];
                $Lsekse = $lam['geslacht'];
                $Ldmaanw = $lam['dmaanw'];    if(isset($Ldmaanw))    { if($Lsekse == 'ooi') { $Lfase = 'moeder'; } if($Lsekse == 'ram') { $Lfase = 'vader'; }  } else { $Lfase = 'lam'; }
                $Lras = $lam['ras'];
                $Ldatum = $lam['gebrndm'];
                $Lkg = $lam['gebrnkg'];
                $Lspndm = $lam['speendm'];
                $Lspnkg = $lam['speenkg'];
                $gemgr_s = $lam['gemgr_s'];
                $Lafvdm = $lam['afvdm'];
                $Lafvkg = $lam['afvkg'];
                $Luitvdm = $lam['uitvaldm'];
                $Lreden= $lam['reden'];
                $gemgr_a = $lam['gemgr_a'];

       $pdf->SetFont('Times','',8);
        $pdf->Cell(10,10,'','',0,'',false);
        $pdf->Cell(25,10,$Llevnr,'TB',0,'',false);
        $pdf->Cell(15,10,$Lwerknr,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lfase,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lsekse,'TB',0,'C',false);
        $pdf->Cell(20,10,$Lras,'TB',0,'C',false);
        $pdf->Cell(15,10,$Ldatum,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lkg,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lspndm,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lspnkg,'TB',0,'C',false);
        $pdf->Cell(15,10,$gemgr_s,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lafvdm,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lafvkg,'TB',0,'C',false);
        $pdf->Cell(15,10,$Lreden,'TB',0,'C',false);
        $pdf->Cell(20,10,$gemgr_a,'TB',1,'C',false);
//        $pdf->Cell(15,10,$gemafv,'TB',1,'C',false);

}    
/* Einde Gegevens lammeren van moederdier */


/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
?>
