<?php /* 18-9-2016 aangemaakt t.b.v. afvoeren bij alleen module melden */
$versie = '22-11-2016'; /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '4-2-2017'; /* kalender toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Afvoerlijst';
$subtitel = '';
Include "header.php"; ?>
		<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Afvoerstal.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include"kalender.php";
if ($modmeld == 1 ) { include "maak_request_func.php"; }
/*
function aantal_fase($datb,$lidid,$Sekse,$Ouder) {
$vw_aantalFase = mysqli_query($datb,"
select count(*) aant 
from tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join tblHistorie ouder on (st.stalId = ouder.stalId and ouder.actId = 3) 
where st.lidId = ".mysqli_real_escape_string($datb,$lidid)." and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");

if($vw_aantalFase)
		{	$row = mysqli_fetch_assoc($vw_aantalFase);
				return $row['aant'];
		}
		return FALSE; // Foutafhandeling
} */

if(isset($_POST['knpAfvoer_'])) { include "save_afvoerstal.php"; } 
$verder = 0;
// Declaratie RELATIE
$qryRelatiekeuze = mysqli_query($db,"SELECT r.relId, p.naam
			FROM tblPartij p
			 join tblRelatie r on (r.partId = p.partId)
			WHERE p.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
			ORDER BY p.naam") or die (mysqli_error($db)); 

$index = 0; 
while ($rel = mysqli_fetch_array($qryRelatiekeuze)) 
{ 
   $relId[$index] = $rel['relId']; 
   $relnm[$index] = $rel['naam'];
   $relRaak[$index] = $rel['relId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie RELATIE
$stapel = mysqli_query($db,"
select count(*) aant
from tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best)
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_array($stapel))
		{ 	  $schapen = $rij['aant'];		} //echo "Aantal schapen {$rij['aant']}"; ?>

<form action = "Afvoerstal.php" method = "post" >
<table Border = 0 align = "center">

<?php if(!isset($_POST['knpNext_']) && !isset($_POST['knpAfvoer_'])) { ?>
<!-- optionele velden om datum en bestemming te bepalen voor afvoerlijst -->
<tr>
 <td> Optioneel een datum voor alle <?php if($schapen > 10) { echo $schapen; } ?> schapen </td>
 <td>
 <input id  = "datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($dagkeuze)) { echo $dagkeuze; } ?> > 
 </td> 
 <td> <input type = submit name = "knpNext_" value = "Verder" >
</tr>
<tr><td> Optioneel een bestemming voor alle <?php if($schapen > 10) { echo $schapen; } ?> schapen </td>
 <td>
 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:150;" name= 'kzlBestall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

	$opties = array($relId[$i]=>$relnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['kzlBestall_']) && $_POST['kzlBestall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->
</td><td></td></tr>
<!-- EINDE optionele velden om datum en bestemming te bepalen voor afvoerlijst -->
<?php }
if(isset($_POST['knpNext_']) || isset($_POST['knpAfvoer_'])) { 
 if(isset($_POST['knpNext_'])) { $txtDatum = $_POST['txtDatumall_']; $kzeBest = $_POST['kzlBestall_']; }?>
<!-- AFVOERLIJST -->
<tr>
 <td colspan = 9 ></td>
 <td>
 <input type = submit name = "knpAfvoer_" value = "Afvoeren" >
 </td>
</tr>

<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 100>Afvoeren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Generatie<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Bestemming<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Uitval<hr></th>
<th width = 1></th>
<th width=60></th>
 </tr>

<?php
$result = mysqli_query($db,"
select st.stalId, s.levensnummer, s.geslacht, h.actId
from tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	select schaapId, h.actId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) h on (h.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best)
order by h.actId, s.geslacht, right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

		while($row = mysqli_fetch_array($result))
		{
		$Id = $row['stalId']; 
		$levnr = $row['levensnummer'];
		$sekse = $row['geslacht'];
		$aanw = $row['actId']; if(isset($aanw)) { if($sekse == 'ooi') { $fase = 'moederdier'; } else if($sekse == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
	if(isset($_POST['knpAfvoer_'])) { if(isset($_POST["chbKies_$Id"])) { $cbAfv = $_POST["chbKies_$Id"]; } $txtDatum = $_POST["txtDatum_$Id"]; $kzlBest = $_POST["kzlBest_$Id"]; if(isset($_POST["chbDood_$Id"])) { $cbDood = $_POST["chbDood_$Id"]; } } ?>
<tr align = center>	
 <td width = 0> </td>
 <td width = 100 > <input type = checkbox name = <?php echo "chbKies_$Id"; ?> value = 1 <?php if(isset($cbAfv)) { echo $cbAfv == 1 ? 'checked' : ''; }  ?> > </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr ?> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase ?> </td>
 <td width = 1> </td>
 <td> <input type = text name = <?php echo "txtDatum_$Id"; ?> size = 8 value = <?php if(isset($txtDatum)) { echo $txtDatum; } ?> > </td>
 <td width = 1> </td>
 <td> 
<!-- KZLRelatie -->
 <select style="width:150;" name= <?php echo "kzlBest_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

	$opties = array($relId[$i]=>$relnm[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((isset($_POST['knpNext_']) && $kzeBest == $relRaak[$i]) || (isset($_POST["kzlBest_$Id"]) && $_POST["kzlBest_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select>

 <!-- EINDE KZLRelatie-->
 </td>	   
 <td width = 1> </td>
 <td width = 100 > <input type = checkbox name = <?php echo "chbDood_$Id"; ?> value = 1 <?php if(isset($cbDood)) { echo $cbDood == 1 ? 'checked' : ''; }  ?> > </td>
 <td width = 1> </td>

 <td width = 300 style = 'color : red;' > <?php
 if(isset($kzlBest) && !empty($kzlBest) && isset($cbDood)) { echo "Bestemming en uitval kan niet beiden."; } 
 else if(isset($fldDag) && isset($dmmax) && $fldDag < $dmmax) { echo "De datum mag niet voor ".$maxdm." liggen."; }
	unset($cbAfv); unset($cbDood); unset($fldDag); unset($dmmax); unset($maxdm);
 ?>  </td>

</tr>				
		
	<?php	} ?>

<!-- EINDE AFVOERLIJST -->
<?php } ?>			
</table>
</form>
		</TD>
<?php
Include "menu1.php"; } ?>

</body>
</html>
