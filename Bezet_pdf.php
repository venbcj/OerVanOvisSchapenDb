<?php

require_once("autoload.php");

//https://www.youtube.com/watch?v=CamDi3Syjy4
/* 20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam 
09-05-2022 : Werknrs gesorteerd en sql beveiligd met quotes 
30-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie 
03-03-2024 : Laatst gewogen gewicht toegevoegd 
11-03-2024 : Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 
*/

include "just_connect_db.php";

if(isset($_GET['Id'])) { $hokId = $_GET['Id']; } // via pagina Bezet.php bestaat $_GET['Id'] niet 

$rapport = 'Verblijf';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

Session::start();
    $lidId = Session::get('I1');

$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }

$pdf = new BezetPdf($Afdrukstand,'mm','A4'); //use new class

$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

/****** BODY ******/


$pdf->SetDrawColor(200,200,200); // Grijs

if(!isset($hokId)) {
$zoek_verblijven_in_gebruik = mysqli_query($db,"
SELECT h.hokId, h.hoknr, count(distinct schaap_geb) maxgeb, count(distinct schaap_spn) maxspn, min(dmin) eerste_in, max(dmuit) laatste_uit
FROM (
    SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, NULL dmuit
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     left join 
     (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         left join (
            SELECT st.schaapId, h.datum dmspn
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 4 and h.skip = 0
         ) spn on (spn.schaapId = st.schaapId)
         left join (
            SELECT st.schaapId, h.datum dmprnt
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3 and h.skip = 0
         ) prnt on (prnt.schaapId = st.schaapId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
         and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and isnull(uit.bezId)
    and isnull(spn.schaapId)
    and isnull(prnt.schaapId)

    UNION

    SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, NULL dmuit
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     left join 
     (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and isnull(uit.bezId)
    and isnull(prnt.schaapId)

    UNION

    SELECT b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, ht.datum dmuit
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join 
     (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         left join (
            SELECT st.schaapId, h.datum dmspn
            FROM tblStal st join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 4 and h.skip = 0
         ) spn on (spn.schaapId = st.schaapId)
         left join (
            SELECT st.schaapId, h.datum dmprnt
            FROM tblStal st join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3 and h.skip = 0
         ) prnt on (prnt.schaapId = st.schaapId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
         and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     join tblHistorie ht on (ht.hisId = uit.hist)
     join tblStal st on (st.stalId = h.stalId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
     left join (
        SELECT p.hokId, max(p.dmafsluit) dmstop
        FROM tblPeriode p
         join tblHok h on (h.hokId = p.hokId)
        WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = 1 and dmafsluit is not null
        GROUP BY p.hokId
     ) endgeb on (endgeb.hokId = b.hokId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and ht.datum > coalesce(dmstop,'1973-09-11') 
     and (isnull(spn.schaapId)  or spn.datum  > coalesce(dmstop,'1973-09-11') and h.datum < spn.datum) 
     and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11'))

    UNION

    SELECT b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, ht.datum dmuit
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join 
     (
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     join tblHistorie ht on (ht.hisId = uit.hist)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = st.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
     left join (
        SELECT p.hokId, max(p.dmafsluit) dmstop
        FROM tblPeriode p
         join tblHok h on (h.hokId = p.hokId)
        WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.doelId = 2 and dmafsluit is not null
        GROUP BY p.hokId
     ) endspn on (endspn.hokId = b.hokId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId /*9-1-2019 weggehaald and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11')) */)."' and h.skip = 0 and ht.datum > coalesce(dmstop,'1973-09-11') 
     and h.datum >= spn.datum and (h.datum < prnt.datum or isnull(prnt.schaapId))
     

    UNION

    SELECT b.hokId, NULL schaap_geb, NULL schaap_spn, NULL dmin, NULL dmuit
    FROM (
        SELECT b.hisId, b.hokId
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join (
            SELECT st.schaapId, h.hisId, h.datum
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3 and h.skip = 0
        ) prnt on (prnt.schaapId = st.schaapId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and h.datum >= prnt.datum
     ) b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     left join 
     (
        SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY b.bezId, h1.hisId
     ) uit on (uit.hisv = b.hisId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.bezId)

 ) ingebr
 join tblHok h on (ingebr.hokId = h.hokId)
GROUP BY h.hokId, h.hoknr
ORDER BY hoknr
") or die (mysqli_error($db));

    $doorloop_verblijf = $zoek_verblijven_in_gebruik;
}
else
{
$zoek_verblijf_gegevens = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok
WHERE hokId = '".mysqli_real_escape_string($db,$hokId)."'
") or die (mysqli_error($db));

    $doorloop_verblijf = $zoek_verblijf_gegevens;
}
$i = 1;

        while($row = mysqli_fetch_assoc($doorloop_verblijf))
        {    $hokId = $row['hokId'];
            $hok = $row['hoknr'];
 

if($i >1) { $pdf->AddPage(); } // hier wordt een nieuwe pagina gemaakt
$i++;

    $pdf->SetFont('Times','B',12);
        $pdf->Cell(75,3,'','',0,'',false);
        $pdf->Cell(30,3,$hok,'',1,'',false);


// LAMMEREN VOOR SPENEN
$zoek_nu_in_verblijf_geb = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb))
        { $nu_geb = $nu['aantin']; }

    if($nu_geb > 0) { // Als er lammeren voor spenen in het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(40,3,'Aantal lammeren voor spenen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$nu_geb,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);
    // kopregel 1    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'Laatst','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);
    // kopregel 2    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'gewogen','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'Fictieve','',1,'C',false);
    // kopregel 3    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(12,3,'gewicht (kg)','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',0,'C',false);
        $pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
        $pdf->Cell(24,3,'Datum in verblijf','',0,'C',false);
        $pdf->Cell(24,3,'speendatum','',0,'C',false);
        $pdf->Cell(24,3,'Moeder','',1,'C',false);

$hok_inhoud_geb = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, right(mdr.levensnummer,$Karwerk) mdr, lastkg.kg lstkg
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
  left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_geb))
        {
         $werknr = $row['werknr'];
         $lstkg = $row['lstkg'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $vanaf = $row['van'];
         $gebdm = $row['geb'];
         $geslacht = $row['geslacht'];
         $ficdm = $row['ficspn'];
         $moeder = $row['mdr'];



       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(12,3,$lstkg,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',0,'C',false);
        $pdf->Cell(24,3,$gebdm,'T',0,'C',false);
        $pdf->Cell(24,3,$vanaf,'T',0,'C',false);
        $pdf->Cell(24,3,$ficdm,'T',0,'C',false);
        $pdf->Cell(24,3,$moeder,'T',1,'C',false);

        }

    } // Einde if($nu_geb > 0)
// EINDE LAMMEREN VOOR SPENEN
// LAMMEREN NA SPENEN
$zoek_nu_in_verblijf_spn = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));

    while($n = mysqli_fetch_assoc($zoek_nu_in_verblijf_spn))
        { $nu_spn = $n['aantin']; }

if($nu_spn > 0) { // Als er lammeren na spenen in het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(38,3,'Aantal lammeren na spenen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$nu_spn,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);
    // kopregel 1    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'Laatst','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);
    // kopregel 2    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'gewogen','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'Fictieve','',1,'C',false);
    // kopregel 3    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(12,3,'gewicht (kg)','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',0,'C',false);
        $pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
        $pdf->Cell(24,3,'Datum in verblijf','',0,'C',false);
        $pdf->Cell(24,3,'afleverdatum','',1,'C',false);

$hok_inhoud_spn = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(spn.datum,'%d-%m-%Y') spn, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, date_format(hg.datum + interval 130 day,'%d-%m-%Y') ficafv, right(mdr.levensnummer,$Karwerk) mdr, lastkg.kg lstkg
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_spn))
        {
         $werknr = $row['werknr'];
         $lstkg = $row['lstkg'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $gebdm = $row['geb'];
         $vanaf = $row['van'];
         $ficdm = $row['ficafv'];

       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(12,3,$lstkg,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',0,'C',false);
        $pdf->Cell(24,3,$gebdm,'T',0,'C',false);
        $pdf->Cell(24,3,$vanaf,'T',0,'C',false);
        $pdf->Cell(24,3,$ficdm,'T',1,'C',false);

        }
    } // if($nu_spn > 0)
// EINDE LAMMEREN NA SPENEN
// VOLWASSEN DIEREN
$zoek_nu_in_verblijf_prnt = mysqli_query($db,"
SELECT count(b.hisId) aantin
FROM (
    SELECT b.hisId, b.hokId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT st.schaapId, h.hisId, h.datum
        FROM tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
    ) prnt on (prnt.schaapId = st.schaapId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId)
") or die (mysqli_error($db));
        
    while($nu = mysqli_fetch_assoc($zoek_nu_in_verblijf_prnt))
        { $nu_prnt = $nu['aantin']; }

if($nu_prnt > 0) { // Als er volwassen schapenin het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(36,3,'Aantal volwassen schapen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$nu_prnt,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);
        // kopregel 1    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'Laatst','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);
    // kopregel 2    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(12,3,'gewogen','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);
    // kopregel 3
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(12,3,'gewicht (kg)','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',0,'C',false);
        $pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
        $pdf->Cell(24,3,'Datum in verblijf','',1,'C',false);

$hok_inhoud_vanaf_aanwas = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(prnt.datum,'%d-%m-%Y') aanw, date_format(h.datum,'%d-%m-%Y') van, b.hisId,
    lastkg.kg lstkg
FROM (
    SELECT b.hisId, b.hokId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT st.schaapId, h.hisId, h.datum
        FROM tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
    ) prnt on (prnt.schaapId = st.schaapId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0 and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.bezId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_vanaf_aanwas))
        {
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $gebdm = $row['geb'];
         $vanaf = $row['van'];
         $lstkg = $row['lstkg'];


       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(12,3,$lstkg,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',0,'C',false);
        $pdf->Cell(24,3,$gebdm,'T',0,'C',false);
        $pdf->Cell(24,3,$vanaf,'T',1,'C',false);

    }

} // Einde if($nu_prnt > 0)
// EINDE VOLWASSEN DIEREN

        } // Einde fetch_assoc($zoek_verblijven_in_gebruik)

/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
?>
