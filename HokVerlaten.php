<?php 
$versie = '27-12-2019'; /* gekopieerd van HokOverpl.php */
$versie = '16-2-2020'; /* in variabele $tabel 'and h2.actId != 3' toegevoegd zodat moederdier wordt getoond */

 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'Uit verblijf halen';
$subtitel = '';
Include "header.php";
?>
		<TD width = 940 height = 400 valign = "top">
<?php 
$file = "Bezet.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

if(isset($_GET['pstId']))	{ $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */
if(isset($_POST['knpVerder_']) && isset($_POST['txtDatumall_']))	{
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum; } 
 $sess_dag = $_SESSION["DT1"]; 

$zoek_hok = mysqli_query ($db,"
SELECT hoknr FROM tblHok where hokId = ".mysqli_real_escape_string($db,$ID)."
") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; }
/*		
$zoek_nu_in_verblijf_parent = mysqli_query($db,"
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
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId)
") or die (mysqli_error($db));
		
	while($nu_p = mysqli_fetch_assoc($zoek_nu_in_verblijf_parent))
		{ $nu_prnt = $nu_p['aantin']; }		
		
	$nu = $nu_prnt;*/

if(isset($_POST['knpSave_'])) { include "save_verlaten.php"; } // staat hier omdat $doelId moet zijn gedeclareerd !
	?>

<form action="HokVerlaten.php" method = "post"><?php 
// Opbouwen paginanummering 
$velden = " schaapId, levensnummer, geslacht, datum, dag, prnt ";

$tabel = " (
SELECT s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b_prnt.hokId, uit.bezId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
	SELECT max(hisId) hisId, h.stalId
	FROM tblHistorie h
	 join tblStal st on (st.stalId = h.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	group by h.stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 
 join tblHistorie h on (st.stalId = h.stalId)
 join (
	SELECT b.hisId, b.hokId
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		SELECT st.schaapId, h.hisId, h.datum
		FROM tblStal st
		join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and h.datum >= prnt.datum
 ) b_prnt on (b_prnt.hisId = h.hisId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	where b.hokId = ".mysqli_real_escape_string($db,$ID)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b_prnt.hisId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b_prnt.hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(uit.bezId)
) tbl ";
$WHERE = " where hokId = ".mysqli_real_escape_string($db,$ID)." and isnull(bezId) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "order by levensnummer"); 
// Einde Opbouwen paginanummering
if(!isset($sess_dag)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>

 <?php if(!isset($sess_dag)) { ?>
 	<td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen
  <input id="datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp
 <?php } else { ?> <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
 </td> 
 <td width = 150 align = center>
<?php if(!isset($sess_dag)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
 </td>
 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
  <input type = submit name = "knpVervers_" value = "Verversen"> 
 </td>
 <td width = 200 align = 'right'>
  <input type = submit name = "knpSave_" value = "Verlaten">&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr>
 <td colspan = 7 align = left ></td>
 <td></td>
</tr>
</table> <!-- einde tabel2 --> </td> </tr>
								<tr> <td>
<table border = 0 align = left > <!-- tabel3 -->
<?php if(isset($sess_dag)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th>Verlaten<br><b style = "font-size : 10px;">Ja/Nee</b><hr></th>
<th>datum verlaten<hr></th>
<th>Levensnummer<hr></th>
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


if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) && !isset($_POST['txtDatumall_']) ) { $cbKies = $_POST["chbkies_$schaapId"]; $datum = $_POST["txtDatum_$schaapId"]; }
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld txtDatumall_ . knpVervers_ bestaat als hidden veld. txtDatum_$schaapId en txtGewicht_$schaapId bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['txtDatumall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
	
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)													|| # Overplaatsdatum is leeg
		$day < $dmmax													 # speendag is kleiner dan laatste registratiedatum
	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) && !isset($_POST['txtDatumall_'])) { $cbKies = $_POST["chbkies_$schaapId"]; $txtOke = $_POST["txtOke_$schaapId"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

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
