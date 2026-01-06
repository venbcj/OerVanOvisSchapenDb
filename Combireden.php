<?php

require_once("autoload.php");

/* 12-12-2014 gemaakt 
8-3-2015 : Login toegevoegd
31-7-2016 : uitvalmoment niet verplicht gemaakt. */
$versie = '28-12-2016';/* toevoegen en wijzigen mogelijk gemaakt. Combi's t.b.v. medicijnen alleen mogelijk gemaakt bij technische module */
$versie = '7-1-2016';/* pdf-printen toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '28-9-2018'; /* Standaard aantal (stdat) tonen in 2 decimalen  */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?> 
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php 
if ( isset($_POST['knpSave_d']) || isset($_POST['knpDelete_d']) || isset($_POST['knpSave_p']) || isset($_POST['knpDelete_p']) ) { 
    // @TODO: hoe moet dit nou werken? $fout kan nooit gezet zijn op dit punt.
    # if(!isset($fout)) { header("Location: ". Url::getWebroot() ."Combireden.php"); }
 }
 else $hier_mist_een_brace = true;

$titel = 'Combinaties met redenen';
$file = "Combireden.php";
include "login.php"; ?>

            <TD valign = "top" align = "center">
<?php
if (Auth::is_logged_in()) { 
$combireden_gateway = new CombiredenGateway();
    $artikel_gateway = new ArtikelGateway();
        $reden_gateway = new RedenGateway();

//***********************************
// NIEUWE COMBINATIE INLEZEN (PHP)    Zowel combinaties t.b.v. uitval als t.b.v. medicijnen  
//***********************************
IF ( isset($_POST['knpInsert_d']) || isset($_POST['knpInsert_p']) ) {
    // Variabele t.b.v. insert into
if (isset($_POST['knpInsert_d'])) 
    { $insScan = $_POST['insScan_d']; $insArtId = 'NULL';           $insRed = $_POST['insReden_d']; $fldTbl = 'd'; }
if (isset($_POST['knpInsert_p'])) 
    { $insScan = $_POST['insScan_p']; $insArtId = $_POST['insPil']; $insRed = $_POST['insReden_p']; $fldTbl = 'p'; $insStdat = $_POST['insStdat']; 

if(!empty($insArtId)) {
    $dbStdat = $artikel_gateway->zoek_stdat($insArtId);
}
} //Einde isset($_POST['knpInsert_p'])) 
// Variabele t.b.v. de controle query's
if (empty($insScan)) 
     { $fldScan = 'scan = NULL';        $whereScan = 'ISNULL(scan)';    } 
else { $fldScan = "scan = $insScan ";    $whereScan = $fldScan;            }

if ((empty($insArtId) && $fldTbl == 'p') || ($fldTbl == 'd'))    
     { $fldArtId = 'artId = NULL';        $whereArtId = 'ISNULL(artId)';    }
else { $fldArtId = "artId = $insArtId ";    $whereArtId = $fldArtId;    }

if (empty($insStdat) && !empty($insArtId) && $fldTbl == 'p') //Als het standaard aantal leeg is en het artikel niet en een medicijn betreft
     { $fldStdat = "stdat = $dbStdat";     $whereStdat = $fldStdat;         }
else if ((empty($insStdat) && empty($insArtId) && $fldTbl == 'p') || ($fldTbl == 'd')) //Als het standaard aantal en artikel leeg is en medicijn betreft of het betreft uitval
     { $fldStdat = 'stdat = NULL';        $whereStdat = 'ISNULL(stdat)';  }
else { $fldStdat = "stdat = $insStdat";     $whereStdat = $fldStdat;     }

if (empty($insRed))     
     { $fldReden = 'reduId = NULL'; $whereRed = 'ISNULL(reduId)';    }
else { $fldReden = "reduId = $insRed ";    $whereRed = "reduId = $insRed "; }

$rows = $combireden_gateway->bestaat_reden($whereArtId, $whereStdat, $whereRed, $fldTbl);
$rows_scan = $combireden_gateway->bestaat_scannr($lidId, $whereScan, $fldTbl);

if(!empty($insArtId) && empty($insStdat) && $fldTbl == 'p') {  $insStdat = $dbStdat; }
if(!empty($insArtId) && $fldTbl == 'd')                     {  $insStdat = 'NULL'; }

    if ($fldTbl == 'p' && empty($insArtId)){         $fout = "Medicijn is niet geselecteerd.";    }
    else if ($fldTbl == 'd' && empty($insRed)){     $fout = "Reden is niet geselecteerd.";    }
    else if ($rows > 0)        {        $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0){        $fout = "Dit scannummer is al in gebruik.";    }
    else 
    {
        if(empty($insRed)) { $insRed = 'NULL'; }        if(empty($insScan)) { $insScan = 'NULL'; }
        $combireden_gateway->insert($fldTbl, $insArtId, $insStdat, $insRed, $insScan);
        unset($insScan);
    }

}
/*******************************************
 EINDE NIEUWE COMBINATIE INLEZEN (PHP)
*******************************************








***********************************************************************  CODE T.B.V. COMBINATIES M.B.T. UITVAL *********************************************************************** 









***************************************
** REDENEN TBV UITVAL IN GEBRUIK (HTML)
***************************************/

?>
 <table border = 0 width = 150 align =  "left" > 
 <tr> 
 <td colspan =  5 align = center> 
 <b>Redenen t.b.v. uitval :</b><hr> 
 </td></tr> 
 <tr> 
 <td colspan =  3 > 
In gebruik :
 </td></tr> 


 <tr style =  "font-size:12px;" align = "left" valign =  "bottom"> 
         <th>Scannr</th>
         <th>Reden</th>

 </tr> 
<?php        
// START LOOP
    $zoek_reden_uitval = $combireden_gateway->zoek_reden_uitval_combi($lidId);
    while($lus = $zoek_reden_uitval->fetch_assoc()) {
            $Id = $lus['comrId'];  

if (empty($_POST['txtId_d']))        {    $rowid_d = NULL;    }
  else        {    $rowid_d = $_POST['txtId_d'];    }
  
            $query = $combireden_gateway->find($Id);
    while($row = $query->fetch_assoc()) {
        $scan = $row['scan'];
        $reduId = $row['reduId'];
?>
<form action="Combireden.php" method="post" > 
        <tr style = "font-size:12px;">

<td ><input type= "hidden" name= "txtId_d" size = 1 value = <?php echo "$Id";?> > <!--hiddden-->
<!-- Scannummer -->
<input type= "text" name= "txtScan_d" size = 1 style = "font-size:12px;" title = "Nummer voor in de reader" value = <?php echo $scan; ?> >
        

</td>    
<td>
<?php
        $qReden = $reden_gateway->kzlReden_combi($lidId, $reduId);
        // TODO: vervalt
$count = $qReden->num_rows;

$index = 0; 
while ($red = $qReden->fetch_array()) { 
   $redId_1[$index] = $red['reduId'];
   $redn_1[$index] = $red['reden'];
   $index++;  
} 
unset($index);
//dan het volgende: 
// EINDE Declaratie REDEN
?>
<!-- KZL REDEN -->
 <select style="width:145;" <?php echo " name=\"kzlReden_d\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
// TODO: hier is $count niet nodig als je foreach toepast.
for ($i = 0; $i < $count; $i++){

    $opties = array($redId_1[$i]=>$redn_1[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpSave_d']) && $reduId == $redId_1[$i]) || (isset($_POST["kzlReden_d"]) && $_POST["kzlReden_d"] == $key && $_POST['txtId_d'] == $rowid_d)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL REDEN -->

 </td>
 <td ><input type = "submit" name= "knpSave_d" value = "Opslaan"  style = "font-size:10px;"></td>
 <td ><input type = "submit" name= "knpDelete_d" value = "Verwijder" style = "font-size:10px;"></td>
</tr>
<?php
// Controle of reden bij uitval hoort
[$reden, $redActief] = $reden_gateway->reden_actief($lidId, $reduId);

?>
<tr>
 <td colspan = 5 style = "font-size:12px; color:red;">
<?php if($redActief == 0)    { echo $reden." hoort niet bij uitval !"; } ?>
 </td>
 </form> 
 <td></td>
</tr>

<?php        
    }
/**********************************************
 ** EINDE REDENEN TBV UITVAL IN GEBRUIK (HTML)
 **********************************************
 
 *******************************************
 ** REDENEN BIJ UITVAL WIJZIGEN (PHP)
 *******************************************/

If (isset($_POST['knpSave_d']))    {

    $txtScan = $_POST['txtScan_d'];  $txtRed = $_POST['kzlReden_d']; 
    
if (empty($txtScan)) {    $fldScan = 'scan = NULL';    $whereScan = 'ISNULL(scan)';    }  else    { $fldScan = "scan = $txtScan ";    $whereScan = $fldScan;}
if (empty($txtRed))     {    $fldReden = 'reduId = NULL'; $whereRed = 'ISNULL(ru.reduId)';}  else    { $fldReden = "reduId = $txtRed ";    $whereRed = "ru.reduId = $txtRed "; }

$rows = $combireden_gateway->bestaat_combireden2($lidId, $whereRed, $rowid_d);
$rows_scan = $combireden_gateway->bestaat_scannr2($lidId, $whereScan, $rowid_d);
    if ($rows > 0)
    {         $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0)
    {         $fout = "Dit scannummer is al in gebruik.";    }

    else if (empty($fout))
    {
        $combireden_gateway->update($rowid_d, $fldScan, $fldReden);
    }
}

if (isset($_POST['knpDelete_d'])) {
    $combireden_gateway->delete($rowid_d);
}

/**************************************************
 ** EINDE REDENEN BIJ UITVAL WIJZIGEN (PHP)
 **************************************************/

    } // EINDE LOOP    ?>

<tr><td></td></tr>
<tr><td colspan = 15><hr></td></tr>

<!--

*****************************************
 NIEUWE REDENEN TBV UITVAL INVOEREN (PHP)
***************************************** -->


 <form action="Combireden.php" method="post" >
<tr><td colspan = 2 style = "font-size:13px;"><i> Nieuwe reden : </i></td></tr>

<td><input type="text" name= "insScan_d" size = 1 style = "font-size:13px;" title = "Nummer voor in de reader" value = <?php if(isset($insScan) && $fldTbl == 'd') { echo $insScan; } ?> ></td>


<td>
<?php
// Declaratie REDEN
    $qReden = $reden_gateway->uitval_lijst_voor($lidId);
$count = $qReden->num_rows;
$index = 0; 
while ($red = $qReden->fetch_array()) { 
   $redId_2[$index] = $red['redId']; // gateway-method hergebruikt, zelfde betekenis, andere kant van de join
   $redn_2[$index] = $red['reden'];
   $index++;  
} 
unset($index); 
//dan het volgende: 
// EINDE Declaratie REDEN
?>
<!-- KZL REDEN -->
 <select style="width:145;" <?php echo " name=\"insReden_d\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
for ($i = 0; $i < $count; $i++){

    $opties = array($redId_2[$i]=>$redn_2[$i]);
            foreach($opties as $key => $waarde)
            {
  if ( isset($_POST["insReden_d"]) && $_POST["insReden_d"] == $key ){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL REDEN -->
    </td> 
<td colspan = 2><input type = "submit" name="knpInsert_d" value = "Toevoegen" style = "font-size:10px;"></td></tr>
<!--
************************************************
 EINDE  NIEUWE REDENEN TBV UITVAL INVOEREN (PHP)
************************************************     -->
    
<tr><td colspan = 15><hr></td></tr>
</table>
</form>



<?php


/***********************************************************************  CODE T.B.V. COMBINATIES M.B.T. MEDICIJNEN ***********************************************************************/

if($modtech == 1) {

/***************************************
** COMBINATIES IN GEBRUIK TONEN (HTML)
***************************************/
    $stal_gateway = new StalGateway();
// TODO: #0004218 lijst-query levert toch wel meer dan 1 record? wat is hier de bedoeling?
$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = ".mysqli_real_escape_string($db,$lidId)."  
") or die (mysqli_error($db));
$pdf = '';
    while($record = mysqli_fetch_assoc($zoek_stalId)) { $pdf = $record['stalId']; }
?>
<table border = 0 align =  "left" > 
<tr> 
 <td width = 100 align =center valign =top > 
<?php echo View::link_to('print pagina', 'Combireden_pdf.php?Id='. $pdf, ['style' => 'color : blue']) ?>
 <td colspan =  5 align = center> 
 <b>Medicijn met reden :</b><hr> 
 </td>
</tr> 
<tr>
 <td width = 100> 
 <td colspan =  3 > 
Combinaties in gebruik :
 </td>
</tr> 


<tr style =  "font-size:12px;" align = "left" valign =  "bottom"> 
 <td width = 100> 
 <th>Scannr</th>
 <th>Medicijn</th> 
 <th width = 20>&nbsp&nbspStand. &nbsp&nbspaantal</th>
 <th>Reden</th>

</tr> 
<?php        
// START LOOP
$loop = $combireden_gateway->p_list_for($lidId);

    while($lus = $loop->fetch_assoc()) {
            $Id = $lus['comrId'];  
if (empty($_POST['txtId_p']))        {    $rowid_p = NULL;    }
  else        {    $rowid_p = $_POST['txtId_p'];    }

            $query1 = $combireden_gateway->c_list_for($lidId, $Id);

    while($row = $query1->fetch_assoc()) {
        $scan = $row['scan'];
        $artId = $row['artId'];
        $stdat = $row['stdat'];
        $reduId = $row['reduId'];
?>
<form action="Combireden.php" method="post" > 
        <tr style = "font-size:12px;">
<td width = 100>
<td ><input type= "hidden" name= "txtId_p" size = 1 value = <?php echo "$Id";?> > <!--hiddden-->
<!-- Scannummer -->
<input type= "text" name= "txtScan_p" size = 1 style = "font-size:12px;" title = "Nummer voor in de reader" value = <?php echo $scan; ?> >
</td><td>
<?php
        $pillen = $artikel_gateway->kzlMedicijn_combi($lidId, $artId);
$count = $pillen->num_rows;

$index = 0; 
while ($pil = $pillen->fetch_array()) { 
   $pilId[$index] = $pil['artId'];
   $pilln[$index] = $pil['naam'];
   //$pilRaak[$index] = $pil['itemId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MEDICIJN
?>
 <!-- KZL MEDICIJN -->
 <select style="width:145;" <?php echo " name=\"kzlPil\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
for ($i = 0; $i < $count; $i++){

    $opties = array($pilId[$i]=>$pilln[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers']) && $artId == $pilId[$i]) || (isset($_POST["kzlPil"]) && $_POST["kzlPil"] == $key && $_POST['txtId_p'] == $rowid_p)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL MEDICIJN -->

</td>
<td>
<!-- Standaard verbruiksaantal -->
<input type= "text" name= <?php echo 'txtStdat'; ?> size = 1 style = "font-size:12px; text-align : right;" title = "Standaard hoeveelheid per toedienen" value = <?php if(isset($stdat)) { echo $stdat; } ?> >
        

</td>
<td>
<?php
// Declaratie REDEN  Met union all kan ik een niet actieve (aangeduid als pil) reden toch tonen en kan dit (en andere) inactieve artikelen niet worden gekozen !!
if(empty($reduId)) {
    $qReden = $reden_gateway->pil_lijst_voor($lidId);
} else {
    $qReden = $reden_gateway->kzlReden_combi($lidId, $reduId);
} 

$count = $qReden->num_rows;
$index = 0; 
while ($red = $qReden->fetch_array()) { 
   $redId_3[$index] = $red['reduId'];
   $redn_3[$index] = $red['reden'];
   $index++;  
} 
unset($index); 
//dan het volgende: 
// EINDE Declaratie REDEN
?>
<!-- KZL REDEN -->
 <select style="width:145;" <?php echo " name=\"kzlReden_p\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
for ($i = 0; $i < $count; $i++){

    $opties = array($redId_3[$i]=>$redn_3[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpSave_p']) && $reduId == $redId_3[$i]) || (isset($_POST["kzlReden_p"]) && $_POST["kzlReden_p"] == $key && $_POST['txtId_p'] == $rowid_p)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL REDEN -->

    </td> 

        
        <td ><input type = "submit" name= "knpSave_p" value = "Opslaan"  style = "font-size:10px;"></td>
        <td ><input type = "submit" name= "knpDelete_p" value = "Verwijder" style = "font-size:10px;"></td>
</tr>
<?php
// Controle of medicijn actief is
[$pilActief, $medicijn] = $artikel_gateway->medicijn_actief($lidId, $artId);
// Controle of medicijn actief is
// Controle of reden bij medicijn hoort
// TODO: uitzoeken of vorige assignment van redActief hier niet in lekt
// (later) wel, kennelijk niet. Er faalt nu een test op de afwezigheid van redActief.
if(!empty($reduId)) {
    [$redActief, $reden] = $reden_gateway->pil_actief($lidId, $reduId);
}
// Controle of reden bij medicijn hoort

?>
<tr>
<td width = 100>
<td colspan = 5 style = "font-size:12px; color:red;">
<?php if($pilActief == 0 && $redActief == 1) { echo $medicijn." is niet in gebruik !"; } 
    else if(isset($reden) && $pilActief == 1 && $redActief == 0) { echo $reden." hoort niet bij een medicijn !"; unset($reden); }
    else if($pilActief == 0 && $redActief == 0) { echo "Zowel ".$medicijn." als ".$reden." is buiten gebruik !"; unset($reden); } ?>
</td>
</form> 
<td></td></tr>

<?php        
    }
/**********************************************
 ** EINDE COMBINATIES IN GEBRUIK TONEN (HTML)
 **********************************************
 
 *******************************************
 ** COMBINATIES IN GEBRUIK WIJZIGEN (PHP)
 *******************************************/

If (isset($_POST['knpSave_p']))    {

    $txtScan = $_POST['txtScan_p'];  $kzlPil = $_POST['kzlPil'];  $txtStdat = $_POST['txtStdat']; $txtRed = $_POST['kzlReden_p']; 

if(!empty($kzlPil)) {
    $dbStdat = $artikel_gateway->zoek_stdat($kzlPil);
}
    
if (empty($txtScan)) {    $fldScan = 'scan = NULL';    $whereScan = 'ISNULL(scan)';    }  else    { $fldScan = "scan = $txtScan ";    $whereScan = $fldScan;}
if (empty($kzlPil))     {    $fldArtId = 'artId = NULL';    $whereArtId = 'ISNULL(artId)';    }  else    { $fldArtId = "artId = $kzlPil ";    $whereArtId = $fldArtId; }
if (empty($txtStdat)){    $fldStdat = "stdat = $dbStdat"; $whereStdat = $fldStdat; } else { $fldStdat = "stdat = $txtStdat"; $whereStdat = $fldStdat; }
if (empty($txtRed))     {    $fldReden = 'reduId = NULL'; $whereRed = 'ISNULL(ru.reduId)';}  else    { $fldReden = "reduId = $txtRed ";    $whereRed = "ru.reduId = $txtRed "; }
$rows = $combireden_gateway->bestaat_combireden3($lidId, $whereStdat, $whereRed, $rowid_p);
$rows_scan = $combireden_gateway->bestaat_scan3($lidId, $fldScan, $rowid_p);

    if (empty($kzlPil)) {         $fout = "Medicijn is niet geselecteerd.";    }

    else if ($rows > 0)
    {         $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0)
    {         $fout = "Dit scannummer is al in gebruik.";    }

    else if (empty($fout))
    {
        $combireden_gateway->update2($rowid_p, $fldScan, $fldArtId, $fldStdat, $fldReden);
    }
}

if (isset($_POST['knpDelete_p'])) {
    $combireden_gateway->delete($rowid_p);
}

/**************************************************
 ** EINDE COMBINATIES IN GEBRUIK WIJZIGEN (PHP)
 **************************************************/

    } // EINDE LOOP    ?>

<tr><td></td></tr>
<tr>
 <td width = 100> 
 <td colspan = 15><hr></td></tr>

<!--

***************************************
 VELDEN TBV NIEUWE INVOER COMBINATIES (HTML)
*************************************** -->


 <form action="Combireden.php" method="post" >
<tr>
 <td width = 100> 
 <td colspan = 2 style = "font-size:13px;"><i> Nieuwe combinatie : </i>
 </td>
</tr>

<tr>
 <td width = 100> 
 <td><input type="text" name= "insScan_p" size = 1 style = "font-size:13px;" title = "Nummer voor in de reader" value = <?php if(isset($insScan) && $fldTbl == 'p') { echo $insScan; } ?> >
 </td>
 <td>

<?php 
// Declaratie MEDICIJN 
$pillen = $artikel_gateway->new_medicijn($lidId);
$count = $pillen->num_rows;
$index = 0; 
while ($pil = $pillen->fetch_array()) { 
   $pilId[$index] = $pil['artId'];
   $pilln[$index] = $pil['naam'];
   //$pilRaak[$index] = $pil['itemId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MEDICIJN
 ?>
<!-- KZL MEDICIJN -->
 <select style="width:145;" <?php echo " name=\"insPil\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
for ($i = 0; $i < $count; $i++){

    $opties = array($pilId[$i]=>$pilln[$i]);
            foreach($opties as $key => $waarde)
            {
  if ( isset($_POST["insPil"]) && $_POST["insPil"] == $key ){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL MEDICIJN -->
 
 </td>

 <td><input type= "text" name= "insStdat" size = 1 style = "text-align : right; font-size:13px;" title = "Standaard hoeveelheid per toedienen" value = <?php if(isset($insStdat) && $insStdat <> 'NULL') echo $insStdat; ?> ></td>

 <td>
<?php
// Declaratie REDEN
$qReden = $reden_gateway->alle_lijst_voor($lidId);
$count = $qReden->num_rows;
$index = 0; 
while ($red = $qReden->fetch_array()) { 
   $redId_4[$index] = $red['reduId'];
   $redn_4[$index] = $red['reden'];
   $index++;  
} 
unset($index); 
//dan het volgende: 
// EINDE Declaratie REDEN
?>
<!-- KZL REDEN -->
 <select style="width:145;" <?php echo " name=\"insReden_p\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
for ($i = 0; $i < $count; $i++){

    $opties = array($redId_4[$i]=>$redn_4[$i]);
            foreach($opties as $key => $waarde)
            {
  if ( isset($_POST["insReden_p"]) && $_POST["insReden_p"] == $key ){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select>
<!-- EINDE KZL REDEN -->
 </td> 
 <td colspan = 2><input type = "submit" name="knpInsert_p" value = "Toevoegen" style = "font-size:10px;">
 </td>
</tr>
<!--
***********************************************
 EINDE  VELDEN TBV NIEUWE INVOER COMBINATIES (HTML)
***********************************************      -->
    
<tr>
 <td width = 100> 
 <td colspan = 15><hr></td></tr>


</table>
</form>
<?php }
/***************************** EINDE *********************************  CODE T.B.V. COMBINATIES M.B.T. MEDICIJNEN ******************************* EINDE ****************************************/
?>

    </TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
