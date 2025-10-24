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
    
    if(!isset($fout)) { header("Location: ". $url ."Combireden.php"); }
 }
 else 

$titel = 'Combinaties met redenen';
$file = "Combireden.php";
include "login.php"; ?>

            <TD valign = "top" align = "center">
<?php
if (Auth::is_logged_in()) { 

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
$zoek_stdat = mysqli_query($db,"
SELECT round(a.stdat) stdat
FROM tblArtikel a
WHERE a.artId = ".mysqli_real_escape_string($db,$insArtId)."
") or die (mysqli_error($db));
    while ( $row_stdat = mysqli_fetch_assoc($zoek_stdat))  { $dbStdat = $row_stdat['stdat']; }
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

$bestaat_combireden_al = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
WHERE $whereArtId and $whereStdat and $whereRed and cr.tbl = '$fldTbl'
GROUP BY cr.artId, cr.reduId
") or die (mysqli_error($db));
                $rows = mysqli_num_rows($bestaat_combireden_al);
    
$bestaat_scannr_al = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and $whereScan and cr.tbl = '$fldTbl'
GROUP BY cr.scan
") or die (mysqli_error($db));
            $rows_scan = mysqli_num_rows($bestaat_scannr_al);

if(!empty($insArtId) && empty($insStdat) && $fldTbl == 'p') {  $insStdat = $dbStdat; }
if(!empty($insArtId) && $fldTbl == 'd')                     {  $insStdat = 'NULL'; }

    if ($fldTbl == 'p' && empty($insArtId)){         $fout = "Medicijn is niet geselecteerd.";    }
    else if ($fldTbl == 'd' && empty($insRed)){     $fout = "Reden is niet geselecteerd.";    }
    else if ($rows > 0)        {        $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0){        $fout = "Dit scannummer is al in gebruik.";    }
    else 
    {
        if(empty($insRed)) { $insRed = 'NULL'; }        if(empty($insScan)) { $insScan = 'NULL'; }
        $query_insert_tblCombireden = "INSERT INTO tblCombireden SET tbl = '$fldTbl', artId = ".$insArtId.", stdat = ".$insStdat.", reduId = ".$insRed.", scan = ".$insScan." ";
                                    
            mysqli_query($db,$query_insert_tblCombireden) or die (mysqli_error($db));
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
$zoek_reden_uitval = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and cr.tbl = 'd'
ORDER BY cr.scan
") or die (mysqli_error($db));

    while($lus = mysqli_fetch_assoc($zoek_reden_uitval))
    {
            $Id = $lus['comrId'];  

if (empty($_POST['txtId_d']))        {    $rowid_d = NULL;    }
  else        {    $rowid_d = $_POST['txtId_d'];    }
  


$query = mysqli_query($db,"
SELECT scan, artId, reduId
FROM tblCombireden
WHERE comrId = ".mysqli_real_escape_string($db,$Id)."
ORDER BY scan
") or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($query))
    {
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
// Declaratie REDEN  Met union all kan ik een niet actieve (aangeduid als pil) reden toch tonen en kan dit (en andere) inactieve artikelen niet worden gekozen !!
$qryReden = ("
SELECT u.reduId, u.reden
FROM (
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.uitval = 1 
   Union all
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.reduId = '$reduId'
) u
GROUP BY u.reduId, u.reden
ORDER BY u.reden
") ; 
$qReden = mysqli_query($db,$qryReden) or die (mysqli_error($db));

$count = mysqli_num_rows($qReden);

$index = 0; 
while ($red = mysqli_fetch_array($qReden)) 
{ 
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
$reden_actief = mysqli_query($db,"
SELECT r.reden, ru.uitval
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.reduId = '$reduId'
") or die (mysqli_error($db));
    while ($red_act = mysqli_fetch_assoc($reden_actief))
    {    $redActief = $red_act['uitval'];    
        $reden = $red_act['reden'];    }
// Controle of reden bij uitval hoort

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

$bestaat_combireden_al = mysqli_query($db,"
SELECT cr.comrId 
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and $whereRed and cr.comrId != ".$rowid_d." and cr.tbl = 'd'
GROUP BY cr.artId, cr.reduId
") or die (mysqli_error($db));
            $rows = mysqli_num_rows($bestaat_combireden_al);

$bestaat_scannr_al = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and $whereScan and cr.comrId != $rowid_d and cr.tbl = 'd'
GROUP BY cr.scan
") or die (mysqli_error($db));
            $rows_scan = mysqli_num_rows($bestaat_scannr_al);

    if ($rows > 0)
    {         $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0)
    {         $fout = "Dit scannummer is al in gebruik.";    }

    else if (empty($fout))
    {

        $query_bewerk_tblCombireden = "UPDATE tblCombireden set ".$fldScan.", ".$fldReden." WHERE comrId = ".$rowid_d."     " ;
            
            mysqli_query($db,$query_bewerk_tblCombireden) or die (mysqli_error($db));    

    }
    //if (empty($fout)) { header("Location: ". $url ."Combireden.php"); }
}

if (isset($_POST['knpDelete_d']))
{
$delete_rec = "DELETE FROM tblCombireden WHERE comrId = ".$rowid_d." ";
    mysqli_query($db,$delete_rec) or die (mysqli_error($db));
    
    //if (empty($fout)) { header("Location: ". $url ."Combireden.php"); }
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
$qryReden = ("
SELECT ru.reduId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.uitval = 1
ORDER BY r.reden
");
$qReden = mysqli_query($db,$qryReden) or die (mysqli_error($db));

$count = mysqli_num_rows($qReden);

$index = 0; 
while ($red = mysqli_fetch_array($qReden)) 
{ 
   $redId_2[$index] = $red['reduId'];
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
    <a href= '<?php echo $url;?>Combireden_pdf.php?Id=<?php echo $pdf; ?>' style = 'color : blue'>
    print pagina </a>
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
$loop = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and cr.tbl = 'p'
ORDER BY cr.scan
") or die (mysqli_error($db));

    while($lus = mysqli_fetch_assoc($loop))
    {
            $Id = $lus['comrId'];  

if (empty($_POST['txtId_p']))        {    $rowid_p = NULL;    }
  else        {    $rowid_p = $_POST['txtId_p'];    }
  


$query = 
"SELECT cr.scan, cr.artId, cr.stdat, cr.reduId
FROM tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and cr.comrId = ".mysqli_real_escape_string($db,$Id)."
ORDER BY cr.scan";

//echo $query;
$query1 = mysqli_query($db,$query) or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($query1))
    {
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
// Declaratie MEDICIJN  Met union all kan ik een niet actief/pil reden toch tonen en kan dit en andere inactieve artikelen niet worden gekozen !!
$qryMedicijn = ("
SELECT u.artId, u.naam
FROM (
    SELECT a.artId, a.naam
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (eu.enhuId = a.enhuId)
    Where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'pil' and a.actief = 1
    Union all
    SELECT a.artId, a.naam
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (eu.enhuId = a.enhuId)
    WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.artId = '$artId'
    ) u
GROUP BY u.artId, u.naam
ORDER BY u.naam
"); 
$pillen = mysqli_query($db,$qryMedicijn) or die (mysqli_error($db)); 

$count = mysqli_num_rows($pillen);

$index = 0; 
while ($pil = mysqli_fetch_array($pillen)) 
{ 
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
if(!empty($reduId)) {
$qryReden = ("
SELECT u.reduId, u.reden
FROM (
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.pil = 1 
   union all
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.reduId = ".mysqli_real_escape_string($db,$reduId)."
) u
GROUP BY u.reduId, u.reden
ORDER BY u.reden") ;
}
else // als reden leeg is
{
$qryReden = ("
SELECT ru.reduId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.pil = 1 
ORDER BY r.reden
") ;
} 
$qReden = mysqli_query($db,$qryReden) or die (mysqli_error($db));

$count = mysqli_num_rows($qReden);

$index = 0; 
while ($red = mysqli_fetch_array($qReden)) 
{ 
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
$medicijn_actief = mysqli_query($db,"
SELECT a.naam, a.actief 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.artId = '$artId'
") or die (mysqli_error($db));
    while ($med_act = mysqli_fetch_assoc($medicijn_actief))
    {    $pilActief = $med_act['actief'];    
        $medicijn = $med_act['naam'];    }
// Controle of medicijn actief is
// Controle of reden bij medicijn hoort
if(!empty($reduId)) {
$reden_actief = mysqli_query($db,"
SELECT r.reden, ru.pil
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.reduId = ".mysqli_real_escape_string($db,$reduId)."
") or die (mysqli_error($db));
    while ($red_act = mysqli_fetch_assoc($reden_actief))
    {    $redActief = $red_act['pil'];    
        $reden = $red_act['reden'];    }
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
$zoek_stdat = mysqli_query($db,"
SELECT round(a.stdat) stdat
FROM tblArtikel a
WHERE a.artId = ".mysqli_real_escape_string($db,$kzlPil)."
") or die (mysqli_error($db));
    while ( $row_stdat = mysqli_fetch_assoc($zoek_stdat))  { $dbStdat = $row_stdat['stdat']; }
    }
    
if (empty($txtScan)) {    $fldScan = 'scan = NULL';    $whereScan = 'ISNULL(scan)';    }  else    { $fldScan = "scan = $txtScan ";    $whereScan = $fldScan;}
if (empty($kzlPil))     {    $fldArtId = 'artId = NULL';    $whereArtId = 'ISNULL(artId)';    }  else    { $fldArtId = "artId = $kzlPil ";    $whereArtId = $fldArtId; }
if (empty($txtStdat)){    $fldStdat = "stdat = $dbStdat"; $whereStdat = $fldStdat; } else { $fldStdat = "stdat = $txtStdat"; $whereStdat = $fldStdat; }
if (empty($txtRed))     {    $fldReden = 'reduId = NULL'; $whereRed = 'ISNULL(ru.reduId)';}  else    { $fldReden = "reduId = $txtRed ";    $whereRed = "ru.reduId = $txtRed "; }

$bestaat_combireden_al = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and $whereStdat and $whereRed and cr.comrId != $rowid_p and cr.tbl = 'p'
GROUP BY cr.artId, cr.reduId
") or die (mysqli_error($db));
            $rows = mysqli_num_rows($bestaat_combireden_al);

$bestaat_scannr_al = mysqli_query($db,"
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and $fldScan and cr.comrId != $rowid_p and cr.tbl = 'p'
GROUP BY cr.scan
") or die (mysqli_error($db));
            $rows_scan = mysqli_num_rows($bestaat_scannr_al);

    if (empty($kzlPil))
    {         $fout = "Medicijn is niet geselecteerd.";    }

    else if ($rows > 0)
    {         $fout = "Deze combinatie bestaat al.";    }
    else if ($rows_scan > 0)
    {         $fout = "Dit scannummer is al in gebruik.";    }

    else if (empty($fout))
    {

        $query_bewerk_tblCombireden = "UPDATE tblCombireden set ".$fldScan.", ".$fldArtId.", ".$fldStdat.", ".$fldReden." WHERE comrId = ".$rowid_p."     " ;
            
            mysqli_query($db,$query_bewerk_tblCombireden) or die (mysqli_error($db));    

    }
    //if (empty($fout)) { header("Location: ". $url ."Combireden.php"); }
}

if (isset($_POST['knpDelete_p']))
{
$delete_rec = "DELETE FROM tblCombireden WHERE comrId = ".$rowid_p." ";
    mysqli_query($db,$delete_rec) or die (mysqli_error($db));
    
    //if (empty($fout)) { header("Location: ". $url ."Combireden.php"); }
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
$qryNewMedicijn = ("
SELECT a.artId, a.naam
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.soort = 'pil' and a.actief = 1
ORDER BY a.naam "); 
$pillen = mysqli_query($db,$qryNewMedicijn) or die (mysqli_error($db)); 

$count = mysqli_num_rows($pillen);

$index = 0; 
while ($pil = mysqli_fetch_array($pillen)) 
{ 
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
$qryReden = ("
SELECT ru.reduId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.pil = 1
ORDER BY reden
") ;
$qReden = mysqli_query($db,$qryReden) or die (mysqli_error($db));

$count = mysqli_num_rows($qReden);

$index = 0; 
while ($red = mysqli_fetch_array($qReden)) 
{ 
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
