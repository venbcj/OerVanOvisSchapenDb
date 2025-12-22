<?php

require_once("autoload.php");

$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is */
$versie = '8-3-2015'; /*Login toegevoegd*/ 
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer t.b.v. reader Agrident aangepast. Hidden velden scan en actief verwijderd */
$versie = '30-5-2020'; /* function db_null_input toegevoegd en pagina opgebouwd/ingedeeld als Hok.php */
$versie = '13-6-2020'; /* Mogelijkheid eigen rassen toevoegen */
$versie = '20-4-2024'; /* Sortering rassen in gebruik */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Invoer rassen';
$file = "Ras.php";
include "login.php"; ?>

            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    include "validate-ras.js.php";
if (isset ($_POST['knpSave_'])) { include "save_ras.php"; }


if (isset ($_POST['knpInsert2_'])) { 

if (empty($_POST['txtRas_']))            { $fout = "Er is geen ras ingevoerd."; }
else {
$txtRas = $_POST['txtRas_'];

$zoek_ras = mysqli_query($db,"
SELECT ras
FROM tblRas r
 join tblRasuser ru
WHERE r.ras = '". mysqli_real_escape_string($db,$txtRas)."' and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

    while( $z = mysqli_fetch_assoc($zoek_ras)) { $eigenras_db = $z['ras']; }

if(isset($eigenras_db)) { $fout = "Dit ras bestaat al."; }
else{

$query_ras_toevoegen = "
  INSERT INTO tblRas
  SET ras = '".mysqli_real_escape_string($db,$txtRas)."',
      eigen = '".mysqli_real_escape_string($db,$lidId)."'";

                /*echo $query_ras_toevoegen;*/ mysqli_query($db,$query_ras_toevoegen) or die (mysqli_error($db));

$zoekRasId = mysqli_query($db,"
SELECT rasId
FROM tblRas
WHERE eigen = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
    while( $zr = mysqli_fetch_assoc($zoekRasId)) { $rasId = $zr['rasId']; }

$query_rasuser_toevoegen = "
  INSERT INTO tblRasuser
  SET lidId = '".mysqli_real_escape_string($db,$lidId)."',
      rasId = '".mysqli_real_escape_string($db,$rasId)."'";

                /*echo $query_ras_toevoegen;*/ mysqli_query($db,$query_rasuser_toevoegen) or die (mysqli_error($db));

$update_tblRas = "
UPDATE tblRas set eigen = 1 WHERE eigen = '".mysqli_real_escape_string($db,$lidId)."' ";

        mysqli_query($db,$update_tblRas) or die (mysqli_error($db));
} // Einde else van if(isset($eigenras_db))

} // Einde else van if (empty($_POST['txtRas_']))

} // Einde if (isset ($_POST['knpInsert2_']))

if (isset ($_POST['knpInsert_']))
{

    if (empty($_POST['kzlRas_']))            { $fout = "U heeft geen ras geselecteerd."; }    
    //else if( isset($aantso) && $aantso > 0)    { $fout = "Dit sorteringsnummer bestaat al."; }
    else 
    {
        
$query_ras_toevoegen = "
  INSERT INTO tblRasuser
  SET lidId = '".mysqli_real_escape_string($db,$lidId)."',
      rasId = '".mysqli_real_escape_string($db,$_POST['kzlRas_'])."',
      scan  = ".db_null_input($_POST['insScan_']).",
      sort  = ".db_null_input($_POST['insSort_']);

                /*echo $query_ras_toevoegen;*/ mysqli_query($db,$query_ras_toevoegen) or die (mysqli_error($db));
    }
}

$zoek_rasuId = mysqli_query($db,"
SELECT rasuId
FROM tblRasuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

$pdf = 0;
    while($line = mysqli_fetch_assoc($zoek_rasuId))
    {
            $pdf = $line['rasuId']; 
    } ?>

<form action="Ras.php" method="post">
<table border = 0>
<tr>
 <td valign = 'top'>
<table border = 0>
<tr>
 <td>
     <?php if($reader == 'Agrident') { $kop = 'sortering reader'; } else { $kop = 'code tbv reader'; }  ?>
     <b> Nieuw ras :</b> <td align = "center" width = 10 style ="font-size:12px;"> <b> <?php echo $kop; ?> </b>
 </td>
 <td colspan = 2>
 </td>
  <td>
      <b> Eigen Ras</b>
 </td>
</tr>

<?php
// DECLARATIE RAS
$qryRas = mysqli_query($db,"
SELECT r.rasId, r.ras
FROM tblRas r 
 left join tblRasuser ru on (ru.rasId = r.rasId and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."')
WHERE isnull(ru.rasId) and r.actief = 1 and eigen = 0
ORDER BY r.ras
 ") or die (mysqli_error($db));

   $index = 0; 
while ($qr = mysqli_fetch_assoc($qryRas)) 
{ 
   $rasId[$index] = $qr['rasId'];
   $rasnm[$index] = $qr['ras'];
   $index++; 
}
// EINDE DECLARATIE RAS
 ?>
<tr>
 <td>
<!-- KZLRAS -->
 <select style="width:180;" name="kzlRas_" value = "" style = "font-size:12px;">
  <option></option>
<?php   $count = count($rasId);
for ($i = 0; $i < $count; $i++) {

            $opties= array($rasId[$i]=>$rasnm[$i]);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if( (isset($_POST['kzlRas_']) && $_POST['kzlRas_'] == $key) )
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
<!-- EINDE KZLRAS -->
 </td>
  <td> <?php if($reader == 'Agrident') { ?>
    <input type= "text" name= "insSort_" size = 1 title = "Leg hier het nummer vast om de volgorde in de reader te bepalen." > 
<?php } else { ?> 
    <input type= "text" name= "insScan_" size = 1 title = "Leg hier de code vast die u tijdens het scannen met de reader gaat gebruiken." value = <?php if(isset($txtScan)) { echo $txtScan; } ?> >
<?php } ?>
 </td>
 <td align = "center"><input type = "submit" name="knpInsert_" value = "Toevoegen" > </td>
  <td width="125">
  </td>
  <td>
      <input type="text" name="txtRas_" id="txtRas">
 </td>
 <td> <input type = "submit" name= "knpInsert2_" onfocus = "verplicht()" value = "Toevoegen" style = "font-size:12px;"> </td>
 <td width="125">
  </td>
</tr>
</table>

 </td>
 <td>
<table border = 0 align = 'left' >
<tr>
 <td> <b> Rassen</b> </td>
 <td align = "center" style ="font-size:12px;"> <?php echo $kop; ?> </td>
 <td align = "center" style ="font-size:12px;"> in gebruik </td>
 <td> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> </td>
 <td width= 100 align = "right">
<?php echo View::link_to('print pagina', 'Ras_pdf.php?Id='.$pdf, ['style' => 'color: blue']); ?>
 </td>
 <td width="50">
 </td>
</tr>
<tr>
 <td colspan = 5><hr> </td>
</tr>


<?php
// START LOOP
$query = mysqli_query($db,"
SELECT r.rasId, r.ras, ru.scan, ru.sort, ru.actief 
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1
ORDER BY actief desc, coalesce(sort,ras) asc ") or die (mysqli_error($db));
    while($rij = mysqli_fetch_assoc($query))
    { 
        $Id = $rij['rasId'];
        $ras = $rij['ras'];
        $scan = $rij['scan'];
        $sort = $rij['sort'];
        $actief = $rij['actief'];        
        ?>

<tr>
 <td> <?php echo $ras; ?> </td>
 <td width = 100 align = "center">
<?php if ($reader == 'Agrident') { ?>
    <input type = text name = <?php echo "txtSort_$Id"; ?> size = 1 value = <?php echo $sort; ?>  >
<?php } else { ?>
    <input type = text name = <?php echo "txtScan_$Id"; ?> size = 1 title = "Wijzig hier de code die u tijdens het scannen met de reader gaat gebruiken." value = <?php echo $scan; ?>  > <?php } ?>
 </td>
 <td>
    <input type = hidden name = <?php echo "chbActief_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = "checkbox" name = <?php echo "chbActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?>         title = "Is dit ras te gebruiken ja/nee ?"/>
 </td>
</tr>        
<?php    } ?>
 </td>
</tr>
</table>

</td>
</tr>
</table>


</form>



    </TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
