<?php 
$versie = '23-12-2019'; /* Gekopieerd van HokOverpl.php */

 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'In verblijf plaatsen';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "Bezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {


if(isset($_POST['knpVerder_']) && isset($_POST['kzlHokall_']))	{
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum;
	$hokkeuze = $_POST['kzlHokall_']; $_SESSION["BST"] = $hokkeuze; } 
 else { $hokkeuze = $_SESSION["BST"];  } $sess_dag = $_SESSION["DT1"]; $sess_bestm = $_SESSION["BST"];

$zoek_nu_in_verblijf_geb_spn = mysqli_query($db,"
SELECT count(hin.schaapId) aant
FROM (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
	GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
		
	while($nu_l = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb_spn))
		{ $nu_lam = $nu_l['aantin']; }
		
$zoek_nu_in_verblijf_parent = mysqli_query($db,"
SELECT count(hin.schaapId) aant
FROM (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
	GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
") or die (mysqli_error($db));
		
	while($nu_p = mysqli_fetch_assoc($zoek_nu_in_verblijf_parent))
		{ $nu_prnt = $nu_p['aantin']; }		
		
	$nu = $nu_lam + $nu_prnt;

if(isset($_POST['knpSave_'])) { include "save_overpl.php"; } // staat hier omdat $doelId moet zijn gedeclareerd !
	
// Declaratie HOKNUMMER KEUZE

$qryHokkeuze = mysqli_query($db,"
select hokId, hoknr
from tblHok h
where lidId = ".mysqli_real_escape_string($db,$lidId)." and actief = 1
order by hoknr
") or die (mysqli_error($db));

$index = 0;
while ($hnr = mysqli_fetch_array($qryHokkeuze)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER  KEUZE ?>

<form action="LoslopersPlaatsen.php" method = "post"><?php 
// Opbouwen paginanummering 

$velden = " s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, s.geslacht, h.datum, date_format(h.datum,'%d-%m-%Y') dag, prnt.schaapId prnt ";

$tabel = " tblSchaap s
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId) 
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
	GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
	select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
 join (
	SELECT st.schaapId, max(hisId) hisId
	FROM tblStal st 
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best)
	GROUP BY st.schaapId
 ) hmax on (hmax.schaapId = s.schaapId)
 join tblHistorie h on (hmax.hisId = h.hisId)
 ";
$WHERE = " WHERE (isnull(b.hokId) or uit.hist is not null) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")"); 
// Einde Opbouwen paginanummering
if(!isset($sess_dag) && !isset($sess_bestm)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>

 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 	<td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen
  <input id="datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp
 <?php } else { ?> <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag) || isset($sess_bestm)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
 </td> 
 <td width = 150 align = center>
<?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
 </td>
 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
  <input type = submit name = "knpVervers_" value = "Verversen"> 
 </td>
 <td width = 200 align = 'right'>
  <input type = submit name = "knpSave_" value = "Plaatsen">&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr><td colspan = 7 align = left >
 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 Optioneel een verblijf voor alle schapen 
 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:<?php echo $w_hok; ?>;" name= 'kzlHokall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['kzlHokall_']) && $_POST['kzlHokall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->
 <?php } ?> 
</td><td></td></tr>
</table> <!-- einde tabel2 --> </td> </tr>
								<tr> <td>
<table border = 0 align = left > <!-- tabel3 -->
<?php if(isset($sess_dag) || isset($sess_bestm)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th>Plaatsen<br><b style = "font-size : 10px;">Ja/Nee</b><hr></th>
<th>Plaatsdatum<hr></th>
<th>Werknr<hr></th>
<th>naar verblijf<hr></th>
<th>Generatie<hr></th>
<th colspan = 2 ><hr></th>
</tr>
<?php
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$schaapId = $array['schaapId'];
		$werknr = $array['werknr'];
		$dmmax = $array['datum'];
		$maxdm = $array['dag'];
		$sekse = $array['geslacht'];
		$prnt = $array['prnt']; if(isset($prnt)) { if($sekse = 'ooi') { $fase = 'moeder'; } else if($sekse = 'ram') { $fase = 'vader'; } } else { $fase = 'lam'; }


if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) && !isset($_POST['kzlHokall_']) ) { $cbKies = $_POST["chbkies_$schaapId"]; $datum = $_POST["txtDatum_$schaapId"]; }
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlHokall_ . knpVervers_ bestaat als hidden veld. txtDatum_$schaapId en txtGewicht_$schaapId bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
	
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)													|| # Overplaatsdatum is leeg
		$day < $dmmax													|| # speendag is kleiner dan laatste registratiedatum
		($hokkeuze == 0 && !isset($_POST["kzlHok_$schaapId"])) 			|| # Hok is de eertse keer leeg
		(empty($_POST["kzlHok_$schaapId"])	&& !isset($_POST['kzlHokall_']))   # Hok is leeg bij verversen
	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_'])) { $cbKies = $_POST["chbkies_$schaapId"]; $txtOke = $_POST["txtOke_$schaapId"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:14px;">
 <td align = center> 
	<input type = hidden size = 1 name = <?php echo "txtOke_$schaapId"; ?>  value = <?php echo $oke; ?> ><!--hiddden Dit veld zorgt ervoor dat chbkies wordt aangevinkt als het ingebruk wordt gesteld -->
	<input type = hidden size = 1 name = <?php echo "chbkies_$schaapId"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$schaapId"; ?> value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else if ($txtOke == 0) {	echo 'checked';} /* else if ($txtOke == 0) wordt maar 1x gepasseerd nl. als onvolledige gegevens voor het eerst volledig zijn ingevuld. Anders is óf het eerst gedeeldte van het if-statement van toepassing of $txtOke == 1.  */ ?> >
 </td>
<!-- Overplaatsdatum -->
 <td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$schaapId"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
 </td>

 <td width = 110 align = center> <?php echo $werknr; ?>
 </td>

 <td width = 100 align = center>

<!-- KZLVERBLIJF -->
 <select style="width:<?php echo $w_hok; ?>;" name= <?php echo "kzlHok_$schaapId"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if (($hokkeuze == $hokRaak[$i]) || (isset($_POST["kzlHok_$schaapId"]) && $_POST["kzlHok_$schaapId"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>

 <!-- EINDE KZLVERBLIJF -->
	
<td align = center> <?php if(isset($fase)) { echo $fase; } ?> </td>
<td colspan = 3 style = "color : red"> 
<?php if($day < $dmmax) { echo 'De datum '.$datum.' mag niet voor '.$maxdm.' liggen.';}
?>
</td>	
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
		} // Einde if(isset($data))
	  } ?>
</table> <!-- Einde tabel3 --> </td> </tr>
</table> <!-- Einde tabel1 -->
</form> 


</TD>
<?php	
Include "menu1.php"; } ?>
</body>
</html>
