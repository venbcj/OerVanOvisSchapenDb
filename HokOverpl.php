<?php /* 21-11-2015 Individueel spenen gewijzigd naar heel hok spenen 
23-11-2015 breedte kzlHok flexibel gemaakt via login.php */
$versie = "20-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel en overbodige hidden velden verwijderd (txtLevnr en txtMindatum) */
$versie = "23-1-2017"; /* 22-1-2017 tblBezetting gewijzigd naar tblBezet 23-1-2017 kalender toegevoegd */
$versie = "6-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = "12-2-2017"; /* tblPeriode verwijderd en hok direct aan tblBezet gekoppeld */
$versie = '21-05-2018';  /* Meerdere pagina's gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */

 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'Overplaatsen';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "HokkenBezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

if(isset($_GET['pstId']))	{ $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */
if(isset($_POST['knpVerder_']) && isset($_POST['kzlHokall_']))	{
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum;
	$hokkeuze = $_POST['kzlHokall_']; $_SESSION["BST"] = $hokkeuze; } 
 else { $hokkeuze = $_SESSION["BST"];  } $sess_dag = $_SESSION["DT1"]; $sess_bestm = $_SESSION["BST"];

$zoek_hok = mysqli_query ($db,"
select hoknr from tblHok where hokId = ".mysqli_real_escape_string($db,$ID)."
") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; }

$zoek_nu_in_verblijf_geb_spn = mysqli_query($db,"
select count(b.bezId) aantin
from tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
		
	while($nu_l = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb_spn))
		{ $nu_lam = $nu_l['aantin']; }
		
$zoek_nu_in_verblijf_parent = mysqli_query($db,"
select count(b.hisId) aantin
from (
	select b.hisId, b.hokId
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		select st.schaapId, h.hisId, h.datum
		from tblStal st
		join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId)
") or die (mysqli_error($db));
		
	while($nu_p = mysqli_fetch_assoc($zoek_nu_in_verblijf_parent))
		{ $nu_prnt = $nu_p['aantin']; }		
		
	$nu = $nu_lam + $nu_prnt;

if(isset($_POST['knpSave_'])) { include "save_overpl.php"; } // staat hier omdat $doelId moet zijn gedeclareerd !
	
// Declaratie HOKNUMMER KEUZE

$qryHokkeuze = mysqli_query($db,"
select hokId, hoknr
from tblHok h
where lidId = ".mysqli_real_escape_string($db,$lidId)." and hokId != ".mysqli_real_escape_string($db,$ID)." and actief = 1
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

<form action="HokOverpl.php" method = "post"><?php 
// Opbouwen paginanummering 
$velden = " schaapId, levensnummer, geslacht, datum, dag, prnt ";

$tabel = " (
select s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
	select max(hisId) hisId, stalId
	from tblHistorie
	group by stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 
 join tblHistorie h on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId) and isnull(prnt.schaapId)

union

select s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
	select max(hisId) hisId, stalId
	from tblHistorie
	group by stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 
 join tblHistorie h on (st.stalId = h.stalId)
 join (
	select b.hisId, b.hokId
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		select st.schaapId, h.hisId, h.datum
		from tblStal st
		join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and h.datum >= prnt.datum
 ) b on (b.hisId = h.hisId)
 left join (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId) 
) tbl ";
$WHERE = " where hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(bezId) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "order by levensnummer"); 
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
  <input type = submit name = "knpSave_" value = "Overplaatsen">&nbsp &nbsp
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
<th>Overplaatsen<br><b style = "font-size : 10px;">Ja/Nee</b><hr></th>
<th>Overplaatsdatum<hr></th>
<th>Levensnummer<hr></th>
<th>naar verblijf<hr></th>
<th>Generatie<hr></th>
<th colspan = 2 ><hr></th>
</tr>
<?php
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$schaapId = $array['schaapId'];
		$levnr = $array['levensnummer'];
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
	<input type = checkbox 		  name = <?php echo "chbkies_$schaapId"; ?> value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else if ($txtOke == 0) {	echo 'checked';} /* else if ($txtOke == 0) wordt maar 1x gepasseerd nl. als onvolledige gegevens voor het eerst volledig zijn ingevuld. Anders is �f het eerst gedeeldte van het if-statement van toepassing of $txtOke == 1.  */ ?> >
 </td>
<!-- Overplaatsdatum -->
 <td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$schaapId"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
 </td>

 <td width = 110 align = center> <?php echo $levnr; ?>
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
