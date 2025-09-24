<?php 

$versie = '27-4-2023'; /* Rina de mogelijkheid gegeven zelf de demo omgeving te ledigen*/
$versie = '02-07-2025'; /* DELETE FROM tblUbn toegevoegd */

Session::start();
 ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Database legen';
$subtitel = '';
include "header.tpl.php"; ?>

<TD width = 960 height = 400 valign = "center" align = "center" >
<?php 
$file = "demo_database_legen.php";
include "login.php";
if (Auth::is_logged_in()) {

$name = Session::get("U1");  

/* Verwijderen rassen en redenen */
if(isset($_POST['chbAlles'])) {

$verw_Ras = 
"DELETE
FROM tblRasuser 
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Ras) or die (mysqli_error($db));

$verw_Reden = 
"DELETE
FROM tblRedenuser 
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Reden) or die (mysqli_error($db));

}

/* Verwijderen schapen */
if(isset($_POST['chbSchaap']) || isset($_POST['chbHok']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$verw_Reader = 
"DELETE
FROM impAgrident
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Reader) or die (mysqli_error($db));

$zoek_draId = mysqli_query($db,"
SELECT d.draId
FROM tblDracht d
 join tblVolwas v on (d.volwId = v.volwId)
 join tblSchaap s on (v.volwId = s.volwId)
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY d.draId
") or die (mysqli_error($db));

$draId = array();
while( $dra = mysqli_fetch_assoc($zoek_draId)) { $draId[] = $dra['draId'];  
    
$draIds = implode(',',$draId);
    }
    if(isset($draIds)) {
    $del_tblDracht = "DELETE FROM tblDracht WHERE draId IN (".mysqli_real_escape_string($db,$draIds).") ";
    /*echo $del_tblDracht.'<br>'; */    mysqli_query($db,$del_tblDracht);
    }


$zoek_volwId = mysqli_query($db,"
SELECT v.volwId
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
GROUP BY v.volwId
ORDER BY v.volwId
") or die (mysqli_error($db));

$volwId = array();
while( $volw = mysqli_fetch_assoc($zoek_volwId)) { $volwId[] = $volw['volwId'];  
    
$volwIds = implode(',',$volwId);
    }
    if(isset($volwIds)) {
    $del_tblVolwas = "DELETE FROM tblVolwas WHERE volwId IN (".mysqli_real_escape_string($db,$volwIds).") ";
    /*echo $del_tblVolwas.'<br>'; */    mysqli_query($db,$del_tblVolwas);
    }


$zoek_schaapId = mysqli_query($db,"
SELECT s.schaapId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
GROUP BY s.schaapId
ORDER BY s.schaapId
") or die (mysqli_error($db));

$schaapId = array();
while( $schaap = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId[] = $schaap['schaapId'];  
    
$schaapIds = implode(',',$schaapId);
    }
    if(isset($schaapIds)) {
    $del_tblSchaap = "DELETE FROM tblSchaap WHERE schaapId IN (".mysqli_real_escape_string($db,$schaapIds).") ";
    /*echo $del_tblSchaap.'<br>'; */    mysqli_query($db,$del_tblSchaap);
    }

$zoek_hisId = mysqli_query($db,"
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and h.actId = 8
") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
    
$hisIds = implode(',',$hisId);
    }
    if(isset($hisIds)) {
    $del_tblHistorie = "DELETE FROM tblHistorie WHERE hisId IN (".mysqli_real_escape_string($db,$hisIds).") ";
    /*echo $del_tblHistorie.'<br>'; */    mysqli_query($db,$del_tblHistorie);
    }

} 

/* Verwijderen meldingen, request en response n.a.v. verwijderen bezetting */
if(isset($_POST['chbSchaap']) || isset($_POST['chbBezet']) || isset($_POST['chbHok']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_reqId = mysqli_query($db,"
SELECT m.reqId
FROM tblMelding m
 join tblBezet b on (b.hisId = m.hisId)
 join tblHok h on (h.hokId = b.hokId)
WHERE h.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
GROUP BY m.reqId
ORDER BY m.reqId
") or die (mysqli_error($db));

$reqId = array();
while( $req = mysqli_fetch_assoc($zoek_reqId)) { $reqId[] = $req['reqId'];  
    
$reqIds = implode(',',$reqId);
    }
    if(isset($reqIds)) {
    $del_tblRequest = "DELETE FROM tblRequest WHERE reqId IN (".mysqli_real_escape_string($db,$reqIds).") ";
    /*echo $del_tblRequest.'<br>'; */    mysqli_query($db,$del_tblRequest);

    $del_impRespons = "DELETE FROM impRespons WHERE reqId IN (".mysqli_real_escape_string($db,$reqIds).") ";
    /*echo $del_impRespons.'<br>'; */    mysqli_query($db,$del_impRespons);

    }

$zoek_meldId = mysqli_query($db,"
SELECT m.meldId
FROM tblMelding m
 join tblBezet b on (b.hisId = m.hisId)
 join tblHok h on (h.hokId = b.hokId)
WHERE h.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY m.meldId
") or die (mysqli_error($db));

$meldId = array();
while( $meld = mysqli_fetch_assoc($zoek_meldId)) { $meldId[] = $meld['meldId'];  
    
$meldIds = implode(',',$meldId);
    }
    if(isset($meldIds)) {
    $del_tblMelding = "DELETE FROM tblMelding WHERE meldId IN (".mysqli_real_escape_string($db,$meldIds).") ";
    /*echo $del_tblMelding.'<br>'; */    mysqli_query($db,$del_tblMelding);
    }

$zoek_hisId = mysqli_query($db,"
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
GROUP BY h.hisId
ORDER BY h.hisId
") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
    
$hisIds = implode(',',$hisId);
    }
    if(isset($hisIds)) {
    $del_tblHistorie = "DELETE FROM tblHistorie WHERE hisId IN (".mysqli_real_escape_string($db,$hisIds).") ";
    /*echo $del_tblHistorie.'<br>'; */    mysqli_query($db,$del_tblHistorie);
    }

}

/* Verwijderen bezetting */
if(isset($_POST['chbSchaap']) || isset($_POST['chbBezet']) || isset($_POST['chbHok']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$verw_Reader = 
"DELETE
FROM impAgrident
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' and hokId is not null ";

mysqli_query($db,$verw_Reader) or die (mysqli_error($db));

$zoek_bezId = mysqli_query($db,"
SELECT b.bezId
FROM tblBezet b
 join tblHok h on (h.hokId = b.hokId)
WHERE h.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY b.bezId
") or die (mysqli_error($db));

$bezId = array();
while( $bez = mysqli_fetch_assoc($zoek_bezId)) { $bezId[] = $bez['bezId'];  
    
$bezIds = implode(',',$bezId);
    }
    if(isset($bezIds)) {
    $del_tblBezet = "DELETE FROM tblBezet WHERE bezId IN (".mysqli_real_escape_string($db,$bezIds).") ";
    /*echo $del_tblBezet.'<br>'; */    mysqli_query($db,$del_tblBezet);
    }

$zoek_hisId = mysqli_query($db,"
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY h.hisId
") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
    
$hisIds = implode(',',$hisId);
    }
    if(isset($hisIds)) {
    $del_tblHistorie = "DELETE FROM tblHistorie WHERE hisId IN (".mysqli_real_escape_string($db,$hisIds).") ";
    /*echo $del_tblHistorie.'<br>'; */    mysqli_query($db,$del_tblHistorie);
    }

}


/* Verwijderen meldingen, request en response */
if(isset($_POST['chbRvo']) || isset($_POST['chbAlles'])) {

$zoek_reqId = mysqli_query($db,"
SELECT m.reqId
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
GROUP BY m.reqId
ORDER BY m.reqId
") or die (mysqli_error($db));

$reqId = array();
while( $req = mysqli_fetch_assoc($zoek_reqId)) { $reqId[] = $req['reqId'];  
    
$reqIds = implode(',',$reqId);
    }
    if(isset($reqIds)) {
    $del_tblRequest = "DELETE FROM tblRequest WHERE reqId IN (".mysqli_real_escape_string($db,$reqIds).") ";
    /*echo $del_tblRequest.'<br>'; */    mysqli_query($db,$del_tblRequest);

    $del_impRespons = "DELETE FROM impRespons WHERE reqId IN (".mysqli_real_escape_string($db,$reqIds).") ";
    /*echo $del_impRespons.'<br>'; */    mysqli_query($db,$del_impRespons);

    }

$zoek_meldId = mysqli_query($db,"
SELECT m.meldId
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY m.meldId
") or die (mysqli_error($db));

$meldId = array();
while( $meld = mysqli_fetch_assoc($zoek_meldId)) { $meldId[] = $meld['meldId'];  
    
$meldIds = implode(',',$meldId);
    }
    if(isset($meldIds)) {
    $del_tblMelding = "DELETE FROM tblMelding WHERE meldId IN (".mysqli_real_escape_string($db,$meldIds).") ";
    /*echo $del_tblMelding.'<br>'; */    mysqli_query($db,$del_tblMelding);
    }

}



/* Verwijderen medicijnregistratie */
if(isset($_POST['chbSchaap']) || isset($_POST['chbPil']) || isset($_POST['chbInkoop']) || isset($_POST['chbArtikel']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$verw_Reader = 
"DELETE
FROM impAgrident
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' and actId = 8";

mysqli_query($db,$verw_Reader) or die (mysqli_error($db));


$zoek_NutId = mysqli_query($db,"
SELECT n.nutId
FROM tblNuttig n
 join tblHistorie h on (n.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY n.nutId
") or die (mysqli_error($db));

$nutId = array();
while( $nut = mysqli_fetch_assoc($zoek_NutId)) { $nutId[] = $nut['nutId'];  
    
$nutIds = implode(',',$nutId);
    }
    if(isset($nutIds)) {
    $del_tblNuttig = "DELETE FROM tblNuttig WHERE nutId IN (".mysqli_real_escape_string($db,$nutIds).") ";
    /*echo $del_tblNuttig.'<br>'; */    mysqli_query($db,$del_tblNuttig);
    }

$zoek_hisId = mysqli_query($db,"
SELECT h.hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and h.actId = 8
") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
    
$hisIds = implode(',',$hisId);
    }
    if(isset($hisIds)) {
    $del_tblHistorie = "DELETE FROM tblHistorie WHERE hisId IN (".mysqli_real_escape_string($db,$hisIds).") ";
    /*echo $del_tblHistorie.'<br>'; */    mysqli_query($db,$del_tblHistorie);
    }

}

/* Verwijderen Voerregistratie */
if(isset($_POST['chbSchaap']) || isset($_POST['chbVoer']) || isset($_POST['chbInkoop']) || isset($_POST['chbArtikel']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_VoedId = mysqli_query($db,"
SELECT v.voedId
FROM tblVoeding v
 join tblPeriode p on (v.periId = p.periId)
 join tblHok h on (h.hokId = p.hokId)
WHERE h.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY v.voedId
") or die (mysqli_error($db));

$voedId = array();
while( $voed = mysqli_fetch_assoc($zoek_VoedId)) { $voedId[] = $voed['voedId'];  
    
$voedIds = implode(',',$voedId);
    }
    if(isset($voedIds)) {
    $del_tblVoeding = "DELETE FROM tblVoeding WHERE voedId IN (".mysqli_real_escape_string($db,$voedIds).") ";
    /*echo $del_tblVoeding.'<br>'; */    mysqli_query($db,$del_tblVoeding);
    }

}

/* Verwijderen Inkopen */
if(isset($_POST['chbInkoop']) || isset($_POST['chbArtikel']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_InkId = mysqli_query($db,"
SELECT i.inkId
FROM tblInkoop i
 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
WHERE eu.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
") or die (mysqli_error($db));

$inkId = array();
while( $ink = mysqli_fetch_assoc($zoek_InkId)) { $inkId[] = $ink['inkId'];  
    
$inkIds = implode(',',$inkId);
    }
    if(isset($inkIds)) {
    $del_tblInkoop = "DELETE FROM tblInkoop WHERE inkId IN (".mysqli_real_escape_string($db,$inkIds).") ";
    /*echo $del_tblInkoop.'<br>'; */    mysqli_query($db,$del_tblInkoop);
    }

}

/* Verwijderen Artikelen */
if(isset($_POST['chbArtikel']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_artId = mysqli_query($db,"
SELECT a.artId
FROM tblArtikel a
 join tblEenheiduser eu on (a.enhuId = eu.enhuId)
WHERE eu.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY a.artId
") or die (mysqli_error($db));

$artId = array();
while( $art = mysqli_fetch_assoc($zoek_artId)) { $artId[] = $art['artId'];  
    
$artIds = implode(',',$artId);
    }
    if(isset($artIds)) {
    $del_tblArtikel = "DELETE FROM tblArtikel WHERE artId IN (".mysqli_real_escape_string($db,$artIds).") ";
    /*echo $del_tblArtikel.'<br>'; */    mysqli_query($db,$del_tblArtikel);
    }

} 

/* Verwijderen Periodes */
if(isset($_POST['chbBezet']) || isset($_POST['chbSchaap']) || isset($_POST['chbHok']) || isset($_POST['chbVoer']) || isset($_POST['chbInkoop']) || isset($_POST['chbArtikel']) || isset($_POST['chbAlles'])) {

$zoek_periId = mysqli_query($db,"
SELECT p.periId
FROM tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
WHERE h.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY p.periId
") or die (mysqli_error($db));

$periId = array();
while( $peri = mysqli_fetch_assoc($zoek_periId)) { $periId[] = $peri['periId'];  
    
$periIds = implode(',',$periId);
    }
    if(isset($periIds)) {
    $del_tblPeriode = "DELETE FROM tblPeriode WHERE periId IN (".mysqli_real_escape_string($db,$periIds).") ";
    /*echo $del_tblPeriode.'<br>'; */    mysqli_query($db,$del_tblPeriode);
    }

} 

/* Verwijderen Verblijven */
if(isset($_POST['chbHok']) || isset($_POST['chbAlles'])) {

$verw_Verblijf = 
"DELETE
FROM tblHok
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Verblijf) or die (mysqli_error($db));

} 


/* Verwijderen Stallijst */
if(isset($_POST['chbSchaap']) || isset($_POST['chbHok']) || isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$verw_Stal = 
"DELETE
FROM tblStal st
WHERE st.lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Stal) or die (mysqli_error($db));

$verw_Ubn = 
"DELETE
FROM tblUbn
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Ubn) or die (mysqli_error($db));

} 

/* Verwijderen Crediteuren */
if(isset($_POST['chbCredit']) || isset($_POST['chbAlles'])) {

$zoek_readId = mysqli_query($db,"
SELECT g.Id
FROM impAgrident g
 join tblPartij p on (p.ubn = g.ubn)
 join tblRelatie r on (p.partId = r.partId)
 join tblActie a on (a.actId = g.actId)
WHERE p.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and r.relatie = 'cred' and (a.op = 1 or g.actId = 14)
ORDER BY g.Id
") or die (mysqli_error($db));

$readId = array();
while( $read = mysqli_fetch_assoc($zoek_readId)) { $readId[] = $read['Id']; 
    
$readIds = implode(',',$readId);
    }
    if(isset($readIds)) {
    $del_impAgrident = "DELETE FROM impAgrident WHERE Id IN (".mysqli_real_escape_string($db,$readIds).") ";
    /*echo $del_impAgrident.'<br>'; */    mysqli_query($db,$del_impAgrident);
    }

$zoek_relId = mysqli_query($db,"
SELECT r.relId
FROM tblRelatie r
 join tblPartij p on (p.partId = r.partId)
WHERE p.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and r.relatie = 'cred' and isnull(r.uitval)
ORDER BY r.relId
") or die (mysqli_error($db));

$relId = array();
while( $rel = mysqli_fetch_assoc($zoek_relId)) { $relId[] = $rel['relId']; 
    
$relIds = implode(',',$relId);
    }
    if(isset($relIds)) {
    $del_tblRelatie = "DELETE FROM tblRelatie WHERE relId IN (".mysqli_real_escape_string($db,$relIds).") ";
    /*echo $del_tblRelatie.'<br>'; */    mysqli_query($db,$del_tblRelatie);
    }

} 

/* Verwijderen Debiteuren */
if(isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_readId = mysqli_query($db,"
SELECT g.Id
FROM impAgrident g
 join tblPartij p on (p.ubn = g.ubn)
 join tblRelatie r on (p.partId = r.partId)
 join tblActie a on (a.actId = g.actId)
WHERE p.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and r.relatie = 'deb' and a.af = 1 and g.actId != 14
ORDER BY g.Id
") or die (mysqli_error($db));

$readId = array();
while( $read = mysqli_fetch_assoc($zoek_readId)) { $readId[] = $read['Id']; 
    
$readIds = implode(',',$readId);
    }
    if(isset($readIds)) {
    $del_impAgrident = "DELETE FROM impAgrident WHERE Id IN (".mysqli_real_escape_string($db,$readIds).") ";
    /*echo $del_impAgrident.'<br>'; */    mysqli_query($db,$del_impAgrident);
    }

$zoek_relId = mysqli_query($db,"
SELECT r.relId
FROM tblRelatie r
 join tblPartij p on (p.partId = r.partId)
WHERE p.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and r.relatie = 'deb'
ORDER BY r.relId
") or die (mysqli_error($db));

$relId = array();
while( $rel = mysqli_fetch_assoc($zoek_relId)) { $relId[] = $rel['relId']; 
    
$relIds = implode(',',$relId);
    }
    if(isset($relIds)) {
    $del_tblRelatie = "DELETE FROM tblRelatie WHERE relId IN (".mysqli_real_escape_string($db,$relIds).") ";
    /*echo $del_tblRelatie.'<br>'; */    mysqli_query($db,$del_tblRelatie);
    }

}

/* Verwijderen Partij */
if(isset($_POST['chbCredit']) || isset($_POST['chbDebet']) || isset($_POST['chbAlles'])) {

$zoek_partId = mysqli_query($db,"
SELECT p.partId
FROM tblPartij p
 left join tblRelatie r on (p.partId = r.partId)
WHERE p.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and isnull(r.partId)
ORDER BY p.partId
") or die (mysqli_error($db));

$partId = array();
while( $part = mysqli_fetch_assoc($zoek_partId)) { $partId[] = $part['partId']; 
    
$partIds = implode(',',$partId);
    }
    if(isset($partIds)) {
    $del_tblPartij = "DELETE FROM tblPartij WHERE partId IN (".mysqli_real_escape_string($db,$partIds).") ";
    /*echo $del_tblPartij.'<br>'; */    mysqli_query($db,$del_tblPartij);
    }

}



/* Verwijderen deklijst */
if(isset($_POST['chbDeklijst']) || isset($_POST['chbAlles'])) {

$verw_Deklijst = 
"DELETE 
FROM tblDeklijst
WHERE lidId = '". mysqli_real_escape_string($db,$lidId) ."' ";

mysqli_query($db,$verw_Deklijst) or die (mysqli_error($db));

} 

/* Verwijderen Liquiditeit */
if(isset($_POST['chbLiq']) || isset($_POST['chbAlles'])) {

$zoek_liqId = mysqli_query($db,"
SELECT l.liqId
FROM tblLiquiditeit l
 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY l.liqId
") or die (mysqli_error($db));

$liqId = array();
while( $liq = mysqli_fetch_assoc($zoek_liqId)) { $liqId[] = $liq['liqId']; 
    
$liqIds = implode(',',$liqId);
    }
    if(isset($liqIds)) {
    $del_tblLiquiditeit = "DELETE FROM tblLiquiditeit WHERE liqId IN (".mysqli_real_escape_string($db,$liqIds).") ";
    /*echo $del_tblLiquiditeit.'<br>'; */    mysqli_query($db,$del_tblLiquiditeit);
    }

}


/* Verwijderen Opgaaf */
if(isset($_POST['chbOpg']) || isset($_POST['chbAlles'])) {

$zoek_opgId = mysqli_query($db,"
SELECT o.opgId
FROM tblOpgaaf o
 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."'
ORDER BY o.opgId
") or die (mysqli_error($db));

$opgId = array();
while( $opg = mysqli_fetch_assoc($zoek_opgId)) { $opgId[] = $opg['opgId']; 
    
$opgIds = implode(',',$opgId);
    }
    if(isset($opgIds)) {
    $del_tblOpgaaf = "DELETE FROM tblOpgaaf WHERE opgId IN (".mysqli_real_escape_string($db,$opgIds).") ";
    /*echo $del_tblOpgaaf.'<br>'; */    mysqli_query($db,$del_tblOpgaaf);
    }

}


/* Verwijderen Saldoberekening */
if(isset($_POST['chbSalber']) || isset($_POST['chbAlles'])) {

$zoek_salbId = mysqli_query($db,"
SELECT s.salbId
FROM tblSalber s
 join tblRubriekuser ru on (s.tblId = ru.rubuId)
WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and s.tbl = 'ru'

UNION

SELECT s.salbId
FROM tblSalber s
 join tblEenheiduser eu on (s.tblId = eu.enhuId)
WHERE eu.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and s.tbl = 'eu'
ORDER BY salbId
") or die (mysqli_error($db));

$salbId = array();
while( $salb = mysqli_fetch_assoc($zoek_salbId)) { $salbId[] = $salb['salbId']; 
    
$salbIds = implode(',',$salbId);
    }
    if(isset($salbIds)) {
    $del_tblSalber = "DELETE FROM tblSalber WHERE salbId IN (".mysqli_real_escape_string($db,$salbIds).") ";
    /*echo $del_tblSalber.'<br>'; */    mysqli_query($db,$del_tblSalber);
    }

}
?>

<form method="POST" action=" <?php echo $file; ?> ">
<p>
<table>
<tr>
    <td> <input type="checkbox" name="chbAlles" > Verwijderen Alles (incl. eigen rassen en redenen. Excl. eenheden en relatie Rendac en Vermist)</td>
</tr>
<tr>
 <td height="15"> </td> 
</tr>
<tr>
    <td> <input type="checkbox" name="chbBezet" > Verwijderen Bezetting met gerelateerde historie, meldingen RVO, periodes, medicijn- en voerregistratie en readergegevens</td>
</tr>
<tr>
    <td> <input type="checkbox" name="chbSchaap" > Verwijderen Schapen, alle historie, bezetting, meldingen RVO, periodes, medicijn- en voerregistratie en readergegevens </td>
</tr>
<tr>
    <td> <input type="checkbox" name="chbHok" > Verwijderen Verblijven incl. alle schapen, historie, bezetting, meldingen RVO, periodes, medicijn- en voerregistratie en readergegevens </td>
</tr>
<tr>
 <td height="15"> </td> 
</tr>
<tr>
    <td> <input type="checkbox" name="chbRvo" > Verwijderen Meldingen RVO </td>
</tr>


<tr>
 <td height="15"> </td> 
</tr>
<tr>
 <td> <input type="checkbox" name="chbPil"      > Verwijderen medicijnregistratie, readergegevens </td> 
</tr>
<tr>
 <td> <input type="checkbox" name="chbVoer"     > Verwijderen Voerregistratie incl. periodes </td>
</tr>
<tr>
 <td> <input type="checkbox" name="chbInkoop"   > Verwijderen Inkopen, medicijn- en voerregistratie en periodes </td>
</tr>
<tr>
 <td> <input type="checkbox" name="chbArtikel"  > Verwijderen Artikelen, inkopen, medicijn- en voerregistratie, periodes en reader </td>
</tr>
<tr>
 <td height="15"> </td> 
</tr>
<tr>
    <td> <input type="checkbox" name="chbCredit" > Verwijderen Crediteuren met gerelateerde schapen, historie, bezetting, artikelen, inkopen, medicijn- en voerregistratie, meldingen RVO, readergegevens </td>
</tr>
<tr>
    <td> <input type="checkbox" name="chbDebet" > Verwijderen Debiteuren, schapen, historie, bezetting, artikelen, inkopen, medicijn- en voerregistratie, meldingen RVO, readergegevens </td>
</tr>


<tr>
 <td height="15"> </td> 
</tr>
<tr>
 <td> <input type="checkbox" name="chbDeklijst" > Verwijderen Deklijsten </td>
</tr>
<tr>
 <td> <input type="checkbox" name="chbLiq"      > Verwijderen Liquiditeit </td> 
</tr>
<tr>
 <td> <input type="checkbox" name="chbOpg"      > Verwijderen Facturen </td> 
</tr>
<tr>
 <td> <input type="checkbox" name="chbSalber"   > Verwijderen Saldoberekening </td> 
</tr>
<tr height = 100 >
 <td colspan = 2 align = 'center'> <input type=submit  value="Verwijder" name="knpDelete"></td>
</tr>
 </table></p>
 </form>
 
 
    </TD>
<?php
include "menuBeheer.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
