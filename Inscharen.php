<?php 
$versie = '03-11-2024'; /* Kopie gemaakt van InsAanvoer.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '09-08-2025'; /* Veld Ubn toegevoegd. Betreft eigen ubn van gebruiker. Per deze versie kan een gebruiker meerdere ubn's hebben */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inscharen';
$file = "Inscharen.php";
Include "login.php"; 
include "kalender.php"; ?>

			<TD valign = "top">
<?php
if (Auth::is_logged_in()) {
	$schaap_gateway = new SchaapGateway();

include "vw_kzlOoien.php";
if ($modmeld == 1 ) { include "maak_request_func.php"; }

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_Inscharen.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."Inscharen.php"); 
	}

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

$velden = "s.schaapId Id, s.levensnummer levnr ";

$tabel = $schaap_gateway->getInscharenFrom();
$WHERE = $schaap_gateway->getInscharenWhere($lidId);

include "paginas.php";

$data = $paginator->fetch_data($velden, "ORDER BY right(s.levensnummer,$Karwerk)"); 

if (isset($_POST['knpVervers_']) || isset($_POST['knpKeuzeAll_']))
{
	$datum_all = $_POST["txtDatumAll_"];
	$kzlUbn_all = $_POST["kzlUbnAll_"];
	$kzlHok_all = $_POST["kzlHokAll_"];
}

// Declaratie Ubn
$ubn_gateway = new UbnGateway();

$aantal_ubn = $ubn_gateway->countPerLid($lidId);

if($aantal_ubn > 1) {
	$ubns = $ubn_gateway->lijstKV($lidId);
} else {
	$ubn = $ubn_gateway->get_ubns_user($lidId);
}
// Einde Declaratie Ubn 

// Declaratie kzlVERBLIJF
$hok_gateway = new HokGateway();
$qryHoknummer = $hok_gateway->kzlHok($lidId);

$index = 0; 
while ($hknr = mysqli_fetch_assoc($qryHoknummer)) 
{
   $hoknId[$index] = $hknr['hokId']; 
   $hoknum[$index] = $hknr['hoknr'];
   $index++; 
}
unset($index);
// EINDE Declaratie kzlVERBLIJF
?>
<form action="Inscharen.php" method = "post">
<table border = 0>
<tr>
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td colspan = 3 align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 3 style = "font-size : 12px;"> 
<?php if($modtech == 1) { ?>* Alleen verplicht bij lammeren. <?php } ?> </td>
</tr>
<tr valign = bottom height = 50 style = "font-size : 12px;" >
 <td>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b></td>
 <td><input type = "text" size = 9 style = "font-size : 11px;" id="datepicker1" name = "txtDatumAll_" value = <?php echo $datum_all; ?> ></td>
 <td>
<?php if($aantal_ubn > 1) { ?>
 	<!-- KZLUBN -->
	 <select style="width:65;" name= "kzlUbnAll_" value = "" style = "font-size:10px;">
	  <option></option>
	<?php	$count = count($kzlUbnId);	
	for ($i = 0; $i < $count; $i++){

		$opties = array($kzlUbnId[$i]=>$ubnnm[$i]);
				foreach($opties as $key => $waarde)
				{
	  if (isset($_POST["kzlUbnAll_"]) && $_POST["kzlUbnAll_"] == $key){
	    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
	  } else { 
	    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
	  }		
				}
	}

	 ?> </select>
 	<!-- Einde KZLUBN -->
<?php } ?>
 </td>
 <td colspan="3" align="center"> <input type = "submit" name = "knpKeuzeAll_" value = "<= Alle velden deze waarden =>" style="font-size: 11px;"></td>
 <td>
	<!-- KZLHOKNR --> 
	 <select style="width:65;" name= "kzlHokAll_" value = "" style = "font-size:12px;">
	  <option></option>

	<?php	$count = count($hoknum);
	for ($i = 0; $i < $count; $i++){

		$opties = array($hoknId[$i]=>$hoknum[$i]);
				foreach($opties as $key => $waarde)
				{
	  if (isset($_POST["kzlHokAll_"]) && $_POST["kzlHokAll_"] == $key){
	    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
	  } else { 
	    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
	  }		
				}
	}
	?>	</select>
  <!-- EINDE KZLHOKNR -->
 </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Aanvoer<br>datum<hr></th>
 <th>Ubn<hr></th>
 <th>Levensnummer<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
<?php if($modtech == 1) { ?>
 <th>Verblijf*<hr></th>
<?php } ?>
 <th width="145">Herkomst<hr></th>
 <th><hr></th>

<?php
if($modtech == 1) {
// Declaratie kzlMOEDERDIER
$qryMoeder = ("SELECT ko.schaapId, right(ko.levensnummer,$Karwerk) Werknr, ko.lamrn
			FROM (".$vw_kzlOoien.") ko ORDER BY right(ko.levensnummer,$Karwerk) "); 
$moederdier = mysqli_query($db,$qryMoeder) or die (mysqli_error($db));

$index = 0; 
while ($mdr = mysqli_fetch_assoc($moederdier)) 
{ 
   $mdrId[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['Werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie kzlMOEDERDIER

}

if(isset($data))  {	

//echo count($data);

	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$delimiter = str_replace('/', '-', $var);
//$gebdatum = date('d-m-Y', strtotime($delimiter)-365*60*60*24);
$datum = date('d-m-Y', strtotime($delimiter));
if (!empty($array['uit_vmdm'])) {
		$varuitv = $array['uit_vmdm'];
$delimiter2 = str_replace('/', '-', $varuitv);
$uitvdm = date('d-m-Y', strtotime($delimiter2));
		} else { $uitvdm = '' ; } 
	
	$Id = $array['Id'];
	$levnr = $array['levnr'];


unset($schaapId);

unset($fase);
unset($sekse);

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr)."'
");

while ($zs = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId = $zs['schaapId']; }

#echo '$schaapId = '.$schaapId.'<br>';

$zoek_schaap_gegevens = mysqli_query($db,"
	SELECT geslacht
	FROM tblSchaap
	WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
");

while ($zsg = mysqli_fetch_assoc($zoek_schaap_gegevens)) { $sekse = $zsg['geslacht']; }

// Zoek historie van het schaap om te beoordelen dat het levensnummer van deze gebruiker (is geweest). Een levensnummer van een andere gebruiker wordt nl. gewoon geaccepteerd !!
unset($stalId_gebruiker);
$zoek_stalId = mysqli_query($db,"
	SELECT max(stalId) stalId
	FROM tblStal
	WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId_gebruiker = $zs['stalId']; }


unset($aanwas);
$zoek_aanwas = mysqli_query($db,"
SELECT hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE actId = 3 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
");

while ($za = mysqli_fetch_assoc($zoek_aanwas)) { $aanwas = $za['hisId']; }

If(isset($aanwas)) { if($sekse == 'ooi') { $fase = 'moeder'; } else { $fase = 'vader'; }
}
else { $fase = 'lam'; }

unset($max_his_af);
$zoek_laatste_keer_van_stallijst_af = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1 and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zlksa = mysqli_fetch_assoc($zoek_laatste_keer_van_stallijst_af)) { $max_his_af = $zlksa['hisId']; }


unset($stalId_uitsch);
unset($date_uitsch);
$zoek_uitscharen = mysqli_query($db,"
SELECT stalId, datum date, date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE actId = 10 and hisId = '".mysqli_real_escape_string($db,$max_his_af)."'
");

while ($zu = mysqli_fetch_assoc($zoek_uitscharen)) { 
	$stalId_uitsch = $zu['stalId']; 
	$date_uitsch = $zu['date']; }

#echo '$stalId_uitsch = '.$stalId_uitsch.'<br>';

unset($ubn_best);
unset($partij);
$zoek_ubn_bestemming = mysqli_query($db,"
SELECT p.ubn, concat(p.ubn, ' - ', p.naam) naam
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (r.partId = p.partId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_uitsch)."'
");

while ($zub = mysqli_fetch_assoc($zoek_ubn_bestemming)) { $ubn_best = $zub['ubn']; $partij = $zub['naam']; }


unset($relId_herk);
$zoek_crediteur_van_ubn = mysqli_query($db,"
SELECT relId
FROM tblRelatie r
 join tblPartij p on (r.partId = p.partId)
WHERE r.relatie = 'cred' and p.ubn = '".mysqli_real_escape_string($db,$ubn_best)."' and p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
");

while ($zcu = mysqli_fetch_assoc($zoek_crediteur_van_ubn)) { $relId_herk = $zcu['relId']; }

unset($opStal);
$opStal = zoek_stalId_in_stallijst($lidId,$schaapId);

#echo '$opStal = '.$opStal.'<br>';

unset($afv_status);
$zoek_afvoer /* excl. uitscharen */ = mysqli_query($db,"
SELECT lower(actie) actie
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.actId != 10 and h.hisId = '".mysqli_real_escape_string($db,$max_his_af)."'
");

while ($za = mysqli_fetch_assoc($zoek_afvoer)) { $afv_status = $za['actie']; }

$kzlUbn = $kzlUbn_all;

if (isset($_POST['knpVervers_'])) {
	$datum = $_POST["txtAanvdm_$Id"];
	$date = date('Y-m-d', strtotime($datum));
	$kzlUbn = $_POST["kzlUbn_$Id"];
 }

 if (isset($_POST['knpKeuzeAll_'])) {
	$datum_all = $_POST["txtDatumAll_"];
	$date = date('Y-m-d', strtotime($datum_all));
	$kzlUbn = $kzlUbn_all;
 }

// Controleren of ingelezen waardes correct zijn ingevuld.
unset($onjuist);
unset($color);

if (!isset($schaapId) ) 				{ $color = 'red'; $onjuist =  "Het levensnummer bestaat niet."; }
else if (empty($datum))					{ $color = 'red'; $onjuist = 'De aanvoerdatum is onbekend'; }
else if ($aantal_ubn > 1 && empty($kzlUbn))				{ $color = 'red'; $onjuist = 'Ubn is onbekend'; }
else if (isset($opStal))				{ $color = 'red'; $onjuist = "Dit dier staat op de stallijst."; }
else if ($date && $date < $date_uitsch)		{ $color = 'red'; $onjuist = "De datum ligt voor de datum van uitscharen."; }
else if (isset($afv_status))			{ $color = 'red'; $onjuist = "Dit dier is " . $afv_status; }
else if (isset($partij) && !isset($relId_herk)) { $color = 'red'; $onjuist = $partij ." is geen crediteur."; } 
else if (!isset($stalId_gebruiker)) { $color = 'red'; $onjuist = "Dit dier wordt niet herkend."; } 


if (isset($onjuist)) {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes correct zijn ingevuld.

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	  	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> 

	<input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbKies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtAanvdm_$Id"]; }
		if (isset($_POST['knpKeuzeAll_'])) { $datum = $_POST["txtDatumAll_"]; } ?>
	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAanvdm_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
   <td>
<?php if($aantal_ubn > 1) { ?>
<!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:10px;">
  <option></option>
<?php	$count = count($kzlUbnId);	
for ($i = 0; $i < $count; $i++){

	$opties = array($kzlUbnId[$i]=>$ubnnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['knpKeuzeAll_']) && $kzlUbn_all == $ubnRaak[$i]) || (!isset($_POST['knpKeuzeAll_']) && isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
 <!-- Einde KZLUBN -->
<?php } else { echo $ubn; } ?>
 </td>
<?php if (strlen($levnr) == 12 && numeriek($levnr) <> 1) { ?> 
 <td>
<?php echo $levnr; } else { ?> <td style = "color : red;" > <?php echo $levnr; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>


 <td align="center">
<?php echo $sekse; ?>
 </td>
 <td align="center">
<?php echo $fase; ?>
 </td>
<?php if($modtech == 1) { ?>

 <td style = "font-size : 9px;">
<!-- KZLHOKNR --> 
 <select style="width:65;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php	$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

	$opties = array($hoknId[$i]=>$hoknum[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['knpKeuzeAll_']) && $kzlHok_all == $hokRaak[$i]) || (!isset($_POST['knpKeuzeAll_']) && isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?>	</select>
 </td> <!-- EINDE KZLHOKNR -->
<?php } // Einde if($modtech == 1)?>

 <td>
<!-- HERKOMST -->
&nbsp &nbsp <?php echo $partij; ?>
 </td> <!-- EINDE HERKOMST -->	

 <td colspan = 3 style = "color : <?php echo $color; ?> ; font-size : 12px;"> <?php if(isset($onjuist)) { echo $onjuist; } ?>
<!-- EINDE Als levensnummer uniek is EN 12 karakters lang is EN geen letter bevat --> 
 </td> 
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
<?php
include "select-all.js.php";
?>

</body>
</html>