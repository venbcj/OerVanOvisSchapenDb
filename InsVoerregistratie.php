<?php 
$versie = '20-6-2021'; /* Gekopieerd van insOmnummeren.php */
$versie = '5-9-2021'; /* func_artikelnuttigen.php toegevoegd en eenheid toegevoegd */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <style type="text/css">
        .selectt {
            display: none;
            font-size: 11px;
        }
        .cursor {
        cursor: pointer;
    	}
    </style>
</head>
<body>



<center>
<?php
$titel = 'Inlezen Voerregistratie';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "InsVoerregistratie.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

include"kalender.php";
include "func_artikelnuttigen.php"; 

?>

<?php
if (isset($_POST['knpVervers_']) || isset($_POST['knpSaveVoer_'])) { 
	$periode = $_POST["txtDatumPeriode_"];
}

if (isset ($_POST['knpInsert_'])) {
	// 	Include "url.php"; Zit al in header.php
	Include "post_readerVoer.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsOverplaats.php");
	}

if (isset ($_POST['knpSaveVoer_'])) {
	// 	Include "url.php"; Zit al in header.php
	Include "save_readerVoer.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsOverplaats.php");
	}

$velden = "rd.Id, date_format(dmlaatst,'%d-%m-%Y') datum, dmlaatst sort, rd.hokId, rd.artId, md.toedtot, rd.doelId,
a.naam, a.actief, 
ntot.totat, i.actief a_act, i.vrdat, 
hk.hoknr";

$tabel = "
impAgrident rd
 join (
	SELECT min(datum) dmeerst, max(datum) dmlaatst, hokId, artId, sum(coalesce(toedat_upd, toedat)) toedtot, doelId
	FROM impAgrident
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 8888 and isnull(verwerkt)
	GROUP BY hokId, artId, doelId
 ) md on (md.dmeerst = rd.datum and md.hokId = rd.hokId and md.artId = rd.artId and md.doelId = rd.doelId)
 join tblHok hk on (rd.hokId = hk.hokId) 
 join tblArtikel a on (rd.artId = a.artId)
  join (
	SELECT artId, sum(coalesce(toedat_upd, toedat)) totat
	FROM impAgrident
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 8888 and isnull(verwerkt)
	GROUP BY artId
) ntot on (ntot.artId = rd.artId)
 left join 
(
	SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, a.actief, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
	FROM tblEenheiduser eu
	 join tblInkoop i on (i.enhuId = eu.enhuId)
	 join tblArtikel a on (i.artId = a.artId)
	 left join (
		SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
		FROM tblVoeding v
		 join tblInkoop i on (v.inkId = i.inkId)
		 join tblArtikel a on (a.artId = i.artId)
		 join tblEenheiduser eu on (a.enhuId = eu.enhuId)
		WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY v.inkId
	 ) n on (i.inkId = n.inkId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)/* deze query betreft min_inkId_met_vrd */."' and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'voer'
	GROUP BY a.artId, a.naam, a.stdat
) i on (rd.artId = i.artId)
";

$WHERE = "WHERE rd.lidId = ".mysqli_real_escape_string($db,$lidId)." and rd.actId = 8888 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");
 ?>
<table border = 0>
<tr> <form action="InsVoerregistratie.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Laatste<br>voerdatum<hr></th>
 <th>Verblijf<hr></th>
 <th>Voer<hr></th>
 <th colspan="2">Totale<br>hoeveelheid<hr></th>
 <th><hr></th>
 <th align="left">Einddatum<br>Voerperiode
 	<input id="datepicker1" type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatumPeriode_"; ?> value = <?php echo $periode; ?> > <br>
 <hr></th>
</tr>
<?php

// Declaratie HOKNUMMER			// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, scan, hoknr
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db)); 

$index = 0; 
while ($hnr = mysqli_fetch_assoc($qryHoknummer)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

// Declaratie VOER
$zoek_artId_op_voorraad = mysqli_query($db," 
SELECT a.artId, a.naam
FROM tblEenheiduser eu
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
    SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
    FROM tblVoeding v
    GROUP BY v.inkId
 ) n on (i.inkId = n.inkId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'voer'
GROUP BY a.artId, a.naam
ORDER BY a.naam
") or die (mysqli_error($db));

$index = 0;
while ($vr = mysqli_fetch_array($zoek_artId_op_voorraad))
{
   $voerId[$index] = $vr['artId'];
   $voerln[$index] = $vr['naam'];
   $voerRaak[$index] = $vr['artId'];
   $index++;
}
unset($index);
// EINDE Declaratie VOER 

if(isset($data))  {	foreach($data as $key => $array)
	{
	$Id = $array['Id'];
	$datum = $array['datum'];
	$date = $array['sort'];
	$hok_rd = $array['hokId'];
	$hoknr = $array['hoknr']; 
	$artId_rd = $array['artId'];
	$artikel = $array['naam'];
	$v_act = $array['actief'];

	$totat = $array['totat'];
	$doel_rd = $array['doelId'];
	$toedtot = $array['toedtot'];
	$vrdat = $array['vrdat'];




if($doel_rd == 1 ) { $doelgroep = "Foklammeren"; }
if($doel_rd == 2 ) { $doelgroep = "Vleeslammeren"; }
if($doel_rd == 3 ) { $doelgroep = "Moederdieren"; }




// Controleren of ingelezen waardes worden gevonden .
// Waardes na verversen
if (isset($_POST['knpVervers_']) || isset($_POST['knpSaveVoer_'])) {

	$kzlHok = $_POST["kzlHok_$Id"];
	$kzlVoer = $_POST["kzlVoer_$Id"];

	if(!empty($_POST["txtAfslDatum_$Id"])) { $txtPeriode = $_POST["txtAfslDatum_$Id"]; }
	else { $txtPeriode = $_POST["txtDatumPeriode_"]; }

unset($artId);
//unset($vrdat);

if(!empty($kzlVoer)) {
// Totale voorraad controleren
$zoek_voorraad = mysqli_query($db," 
SELECT sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblInkoop i
 left join (
    SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
    FROM tblVoeding v
     join tblInkoop i on (v.inkId = i.inkId)
    WHERE i.artId = '".mysqli_real_escape_string($db,$kzlVoer)."'
    GROUP BY v.inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '".mysqli_real_escape_string($db,$kzlVoer)."' and i.inkat-coalesce(n.vbrat,0) > 0
") or die (mysqli_error($db));

while ($zv = mysqli_fetch_array($zoek_voorraad))
{
   $vrdat = $zv['vrdat'];
}
// Einde Totale voorraad controleren
} // Einde if(!empty($kzlVoer))

	 } // Einde if (isset($_POST['knpVervers_']) || isset($_POST['knpSaveVoer_']))
else { $kzlHok = $hok_rd; 
	$kzlVoer = $artId_rd;  
}
// Einde Waardes na verversen

	$voorraad = str_replace('.00', '', $vrdat);
	$voorraad = str_replace('.', ',', $voorraad);
	/*
echo '$kzlVoer = '.$kzlVoer.'<br>';
echo '$artId = '.$artId.'<br>';
echo '$voorraad = '.$voorraad.'<br>';*/

$afsldag = date_create($txtPeriode); $dmPeriode =  date_format($afsldag, 'Y-m-d');

unset($periId);
$zoek_periId = mysqli_query ($db,"
SELECT periId
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and doelId= '".mysqli_real_escape_string($db,$doel_rd)."' and dmafsluit = '".mysqli_real_escape_string($db,$dmPeriode)."'
") or die (mysqli_error($db));
	while ($pi = mysqli_fetch_assoc($zoek_periId)) { $periId = $pi['periId']; }

	 If	 
	 (  empty($kzlHok)	|| # verblijf is leeg
	 	 empty($kzlVoer)	|| # er is geen voer bekend
	 	( /*!isset($_POST['knpVervers_']) &&*/ $vrdat < $totat)	|| # onvoldoende voorraad
	 	( empty($txtPeriode) )	||	# datum periode is niet gevuld
	 	 isset($periId) 			# afsluitdatum bestaat al
	 )
	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>


<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:13px;">
 <td align = center>
	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
	<?php echo $datum; ?>
 </td>

 <td style = "font-size : 9px;">
 <!-- KZLHOKNR --> 
 <select style="width:68;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php	$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';
  }		
			}
}
?>	</select>
<?php
if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_']) && $hok_rd <> NULL && empty($_POST["kzlHok_$Id"])  ) {

if($reader != 'Agrident') { echo $hok_rd; } ?> <b style = "color : red;"> ! </b>  <?php } ?>

 </td> <!-- EINDE KZLHOKNR -->

 <td style = "font-size : 9px;" >
<!-- KZLVOER -->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlVoer_$Id"; ?> >
  <option></option>
<?php	$count = count($voerln);
for ($i = 0; $i < $count; $i++){

	$opties = array($voerId[$i]=>$voerln[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $artId_rd == $voerRaak[$i]) || (isset($_POST["kzlVoer_$Id"]) && $_POST["kzlVoer_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>
 </td> <!-- EINDE KZLVOER -->	

 <td align="center">
 <?php echo $toedtot.' kg'; ?> 
 	
 </td>
 <td>
</td>

 <td align="center"><?php echo $doelgroep; ?>
 </td>

 <td align="right"> 
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAfslDatum_$Id"; ?> value = <?php echo $txtPeriode; ?>  >
 </td>

 <td style = "color : red"><center> 

<?php  	
if (isset($artikel) && $vrdat == 0){  echo $artikel. " is niet op voorraad."; }
else if ( /*!isset($_POST['knpVervers_']) &&*/ $vrdat < $totat) {  echo "Nog maar ".$voorraad." kg op voorraad."; }
// deze melding niet perse tonen else if ( (isset($_POST['knpVervers_']) || isset($_POST['knpSaveVoer_']) ) && empty($txtPeriode)) { echo "Datum periode is niet gevuld"; }
else if (isset($periId))						{ echo "Deze afsluitdatum bestaat al"; } 
else if (!empty($artId_rd) && $v_act <> 1)	{ echo "Dit voer is uitlopend."; }
?>

 </center>
	
 </td>
 <td style = "color : red"> 
 </td>	
</tr>


<tr style = "font-size : 11px;"> 
<td colspan="2" id="toon_ <?php echo $Id; ?> " valign="top" style="color: blue;" value= <?php echo $Id; ?> > 
 
	<u class="cursor"> Toon details </u>

 </td>
 <td colspan="3"> <!-- hier volgt een tabel met detail regels-->
<?php $zoek_voerregels_reader = mysqli_query($db,"
SELECT Id, date_format(datum,'%d-%m-%Y') dag, hokId, artId, coalesce(toedat_upd, toedat) toedat, doelId
FROM impAgrident
WHERE hokId = '".mysqli_real_escape_string($db,$hok_rd)."' and artId = '".mysqli_real_escape_string($db,$artId_rd)."' and doelId = '".mysqli_real_escape_string($db,$doel_rd)."' and isnull(verwerkt)
ORDER BY datum
") or die (mysqli_error($db));

while ($regel = mysqli_fetch_array($zoek_voerregels_reader))
{
   $regelId = $regel['Id'];
   $dag = $regel['dag'];
   $toedat = $regel['toedat'];
   
 ?>
<table border="0">
<tr> <!-- Voerregels uit de reader -->
 <td class= "<?php echo $Id; ?> selectt" >
 <?php echo $dag; ?>
 </td>
 <td class= "<?php echo $Id; ?> selectt"> <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtAantal_$regelId"; ?> value = <?php echo $toedat; ?> > kg
 </td>
 <td class= "<?php echo $Id; ?> selectt" width="150" align= "right" >
 <?php
 if(!isset($hokId_details)  || $hokId_details <> $hok_rd)  { $hokId_details = $hok_rd; 	$toon1 = 1; } else { $toon1 = 0; } 
if(!isset($artId_details)  || $artId_details <> $artId_rd) { $artId_details = $artId_rd; $toon2 = 1; } else { $toon2 = 0; }  
if(!isset($doelId_details) || $doelId_details <> $doel_rd) { $doelId_details = $doel_rd; $toon3 = 1; } else { $toon3 = 0; }  
if($toon1 == 1 || $toon2 == 1 || $toon3 == 1) { ?>

	<input type="submit" name= "<?php echo "knpSaveVoer_"; ?>" value = "Opslaan" style = "font-size: 10px;" >

<?php } ?>
 </td>
</tr>  <!-- Einde Voerregels uit de reader -->
<?php } ?>
<tr height = 20  class= "<?php echo $Id; ?> selectt"></tr>
</table>
</td><!-- EInde hier volgt een tabel met detail regels-->
</tr>

<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->
<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 




</TD>
<?php
Include "menu1.php"; } ?>
</tr>

</table>
</center>



</body>
</html>
<SCRIPT language="javascript">
$(function(){

	// add multiple select / deselect functionality
	$("#selectall").click(function () {
		  $('.checkall').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".checkall").click(function(){

		if($(".checkall").length == $(".checkall:checked").length) {
			$("#selectall").attr("checked", "checked");
		} else {
			$("#selectall").removeAttr("checked");
		}

	});
});

$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});
</SCRIPT>



<script type="text/javascript">

$(document).ready(function() {
        $('td').click(function() {
            var inputValue = $(this).attr("value");
            //alert(inputValue);
            $("." + inputValue).toggle();
        });
    });

</script>
 






