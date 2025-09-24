<?php

require_once("autoload.php");

/* 11-11-2014 : header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is 
8-3-2015 : Login toegevoegd */
$versie = '28-12-2016'; /* Vinkje bij toevoegen medicijnen wordt nu opgeslagen. Underscore ontbrak in de naam van het veld. Hidden velden verwijderd zoals txtId en ctrUitval */
$versie = '11-03-2017'; /* Naast Id ook it (item) toegevoegd aan naam van de velden om opslaan reden en moment te kunnen splitsen. Hidden velden verwijderd. */
$versie = '11-03-2017'; /* Aanvullen reden als beheerder toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer bij reader Agrident verwijderd bij momenten. 1-6 veld afvoer toegevoegd */
$versie = '20-6-2020'; /* Verschillende redenen disabled bij reader Agrident */
$versie = '12-02-2021'; /* Uitval gesplitst in Doodgeboren en sterfte SQL beveiligd met quotes. Titel gewijzigd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>beheer</title>
</head>
<body>

<?php
$titel = 'Redenen en momenten';
$file = "Uitval.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { 

if (isset($_POST['knpSaveUitv__']) || isset($_POST['knpSaveReden__'])) { include "save_uitvalredenen.php"; } ?>

<table border = 0 ><tr><td>
<?php
//    ****************************************
//    ** REDENEN UITVAL EN MEDICATIE **
//    ****************************************

    $redId = 0; // TODO: FIXME: variabele wordt alleen gezet met knpinsertreden, maar verderop klakkeloos gebruikt?
if (isset($_POST['knpInsertReden__'])) {
    $redId = $_POST['kzlReden__'];
    
    if (empty($redId))                         { $fout = "U heeft geen reden geselecteerd."; }
    else
    {
// Zoeken naar reden op duplicaten
$controle = mysqli_query($db,"
SELECT count(redId) aantal
FROM tblRedenuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and redId = '".mysqli_real_escape_string($db,$redId)."'
GROUP BY redId
") or die (mysqli_error($db));
        while ($row = mysqli_fetch_assoc($controle))
        {
            $dubbel = ("{$row['aantal']}");
        } // Einde Zoeken naar reden op duplicaten    
    
    
        if (isset($_POST['boxUitval__']))    { $insUitv = 1; } else { $insUitv = 0; }
        if (isset($_POST['boxPil__']))        { $insPil = 1; } else { $insPil = 0; }
        if (isset($_POST['boxSterfte__']))    { $insSterf = 1; } else { $insSterf = 0; }
  
$query_reden_toevoegen= "INSERT INTO tblRedenuser SET 
 lidId = '".mysqli_real_escape_string($db,$lidId)."', 
 redId = '".mysqli_real_escape_string($db,$redId)."', 
 uitval = '".mysqli_real_escape_string($db,$insUitv)."', 
 pil = '".mysqli_real_escape_string($db,$insPil)."', 
 sterfte = '".mysqli_real_escape_string($db,$insSterf)."' ";

        mysqli_query($db,$query_reden_toevoegen) or die (mysqli_error($db));
    }
}


?>
<form action= "Uitval.php" method="post">
<?php
if($modbeheer == 1 ) { ?>
<table border = 0 >
<th colspan = 5> Aanvullen Redenen <br><hr></th>

<tr align = "center">
 <td><input type="text" name="txtNaam__" ></td>
 <td colspan = 5 align = "center"><input type = "submit" name="knpNewReden__" value = "Aanvullen" >
</td></tr>
<tr height= 20><td></td></tr>
</table>
<?php if(isset($_POST['knpNewReden__']) && !empty($_POST['txtNaam__'])) {

$naam = $_POST['txtNaam__'];

$insert_tblReden = "INSERT into tblReden set reden = '".mysqli_real_escape_string($db,$naam)."' ";
    
 mysqli_query($db,$insert_tblReden) or die (mysqli_error($db));
    } ?>
<?php } ?>

<table border = 0 >
<th colspan = 5> Redenen tbv uitval en medicatie <br><hr></th>
<tr style ="font-size:12px;"><td>Nieuwe reden</td><td>Dood-<br>geboren</td><td>Medicatie</td><td>Afvoer</td><td>Sterfte</td></tr>

<tr align = "center">
<td>
<!-- KZLREDEN -->
<?php

$qryreden = mysqli_query($db,"
SELECT r.redId, r.reden
FROM tblReden r 
 left join tblRedenuser ru on (ru.redId = r.redId and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."')
WHERE isnull(ru.redId) and r.actief = 1
ORDER BY r.reden
 ") or die (mysqli_error($db));?>
 <select style="width:180;" name="kzlReden__" value = "" style = "font-size:12px;">
  <option></option>
<?php        while($rd = mysqli_fetch_array($qryreden))
        {
            $raak = $rd['redId'];


            $opties= array($rd['redId']=>$rd['reden']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if( (!isset($_POST['knpInsert__']) && $redId == $raak) || (isset($_POST['kzlReden__']) && $_POST['kzlReden__'] == $key) )
        {
            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
        }
        else
        {        
        echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            
        }
}
?>
</select>
<!-- EINDE KZLREDEN -->
</td>

 <td><input type="checkbox" name="boxUitval__" id="c1" value="1" title = "Is reden tbv uitval ja/nee ?"></td>
 <td><input type="checkbox" name="boxPil__" id="c2" value="1" title = "Is reden tbv medicatie ja/nee ?"></td>
 <td></td>
 <td><input type="checkbox" name="boxSterfte__" id="c4" value="1" title = "Is reden tbv sterfte ja/nee ?"></td>
</tr>
<tr><td colspan = 5 align = "center"><input type = "submit" name="knpInsertReden__" value = "Toevoegen" ><hr>
</td></tr>

<tr><td></td></tr>
<tr align = "center" style ="font-size:12px;"> 
 <td align="left"><b><i>&nbsp&nbspReden</i></b></td> 
 <td><b><i>Dood-<br>geboren</i></b></td> 
 <td><b><i>Medicatie</i></b></td>
 <td><b><i>Afvoer</i></b></td>
 <td><b><i>Sterfte</i></b></td>
</tr>


<tr><td colspan = 5></td><td colspan = 8 align = right ><input type = "submit" name="knpSaveReden__" value = "Opslaan" style = "font-size:12px;"></td></tr>
<?php
$it = 'reden';
// START LOOP
$loop = mysqli_query($db,"
SELECT ru.reduId, r.redId, r.reden, ru.uitval, ru.pil, ru.afvoer, ru.sterfte
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY if(uitval+pil = 2, 1 ,uitval+pil) desc, reden
") or die (mysqli_error($db));

    while($rij = mysqli_fetch_assoc($loop))
    {
            $Id = $rij['reduId']; 
            $rId = $rij['redId']; 
            $uitv = $rij['uitval'];
            $pil = $rij['pil']; 
            $afvoer = $rij['afvoer'];
            $sterfte = $rij['sterfte']; ?>

<tr>
 <td>
 <?php echo $rij['reden']; ?>
 </td>
 <td align = "center">

    
    <input type = "hidden" name = <?php echo "chbUitval_$it"."_$Id"; ?> size = 1 value = 0 > <!-- hiddden --> 
    <input type="checkbox" name= <?php echo "chbUitval_$it"."_$Id"; ?> id="c1" value= 1 
        <?php echo $uitv == 1 ? 'checked' : ''; ?> title = "Is reden tbv uitval ja/nee ?">
 </td>
 <td align = "center">
    <input type = "hidden" name = <?php echo "chbPil_$it"."_$Id"; ?> size = 1 value = 0 > <!-- hiddden -->
     <input type="checkbox" name= <?php echo "chbPil_$it"."_$Id"; ?> id="c1" value="1" <?php echo $pil == 1 ? 'checked' : ''; ?> title = "Is reden tbv medicatie ja/nee ?">
 </td>
 <td>
     <input type = "hidden" name = <?php echo "chbAfvoer_$it"."_$Id"; ?> size = 1 value = 0 > <!-- hiddden -->
     <input type="checkbox" name= <?php echo "chbAfvoer_$it"."_$Id"; ?> id="c1" value="1" 
         <?php echo $afvoer == 1 ? 'checked' : ''; ?> >
 </td>
 <td>
     <input type = "hidden" name = <?php echo "chbSterfte_$it"."_$Id"; ?> size = 1 value = 0 > <!-- hiddden -->
     <input type="checkbox" name= <?php echo "chbSterfte_$it"."_$Id"; ?> id="c1" value="1" 
         <?php echo $sterfte == 1 ? 'checked' : ''; ?> >
 </td>
</tr>

        
<?php    } ?>    
</table>
 </td>
 <td width = 100></td>
 <td valign = top>
<?php
//    ****************************************
//    **      MOMENTEN VAN UITVAL       **
//    ****************************************
?>
<table border = 0 >
<th colspan = 5> Momenten tbv uitval <br><hr></th>
<tr height = 30 style ="font-size:12px;">
 <td></td>
<?php if($reader != 'Agrident') { ?>
 <td width = 40 align = "center" ><b><i> scan code </i></b></td>
<?php } ?>
 <td align = "center" ><b><i>actief</i></b></td>

</td>

 <td width = 100 align = right><input type = "submit" name= "knpSaveUitv__" value = "Opslaan" style = "font-size:12px;"></td>
</tr>

<?php
$it = 'moment';
// START LOOP
$qry_lus = mysqli_query($db,"
SELECT momuId, scan, mu.actief
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE mu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and m.actief = 1
ORDER BY momuId
") or die (mysqli_error($db));

    while($lus = mysqli_fetch_assoc($qry_lus))
    {
            $Id = ("{$lus['momuId']}"); 
            $scan = ("{$lus['scan']}"); 
            $actief = ("{$lus['actief']}"); 

$query = mysqli_query($db,"
SELECT moment, scan, mu.actief
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE momuId = '".mysqli_real_escape_string($db,$Id)."' and m.actief = 1
ORDER BY moment
") or die (mysqli_error($db));
        while($lus = mysqli_fetch_assoc($query))
        {    ?>
<tr>
 <td><?php echo $lus['moment']; ?> </td>
<?php if($reader != 'Agrident') { ?>
 <td>
 <input type="text" name= <?php echo "txtScan_$it"."_$Id"; ?> size = 1 value = <?php echo $lus['scan']; ?>    > 
 </td>
<?php } ?> 
 <td align = "center">
 <input type="hidden" name= <?php echo "chbActief_$it"."_$Id"; ?> size = 1 value = 0 > <!-- hiddden -->
 <input type="checkbox" name= <?php echo "chbActief_$it"."_$Id"; ?> id="c1" value="1"    <?php echo $actief == 1 ? 'checked' : ''; ?> title = "Is dit uitvalmoment te gebruiken ja/nee ?"/>
 </td>
</tr>
        
<?php    }
    } ?>
</table>
</form>
 </td>
</tr>
</table><?php
//    ****************************************
//    **    MOMENTEN VAN UITVAL       ** end
//    ****************************************
?>

    </TD>
<?php
include "menuBeheer.php"; } ?>
</tr>
</table>

</body>
</html>
