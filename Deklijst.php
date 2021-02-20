<?php $versie = '19-10-2016'; 
$versie = '5-11-2016'; /* include "func_euro.php"; toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */ 
session_start();  ?> 
<html>
<head>
<title>Financieel</title>
</head>
<body>

<center>
<?php
$titel = 'Deklijst';
$subtitel = ''; 
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modfin == 1) {

include "func_euro.php";

$laatste_jaar = mysqli_query($db,"
select max(year(dmdek)) maxjaar
from tblDeklijst 
where lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db));
	
	while ($lst = mysqli_fetch_assoc($laatste_jaar)) { $lastjaar = $lst['maxjaar']; }
	
if(isset($_POST['kzlJaar_'])) { $toon_jaar = $_POST['kzlJaar_']; } else if(isset($lastjaar) && $lastjaar < date('Y')) { $toon_jaar = $lastjaar; } else { $toon_jaar = date('Y'); }

if (isset($_POST['knpCreate_']))	
	{		
		$year = $_POST['txtNewjaar_'];
		include "create_deklijst.php";
	}
	
if(isset($_POST['knpSave_'])) { include "save_deklijst.php"; 
	
	$prijs = $_POST['txtPrijs_']; 	$worp = $_POST['txtWorp_'];		$sterf = $_POST['txtSterf_'];	$jaar = $_POST['kzlJaar_'];

// Toevoegen jaar in tblLiquiditeit indien noodzakelijk
$maxDekjaar = mysqli_query($db,"
	SELECT max(year(dmdek + interval 9 month)) maxjaar
	FROM tblDeklijst 
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(dmdek) = '$jaar'") or die (mysqli_error($db));
	
	while ($maxDj = mysqli_fetch_assoc($maxDekjaar)) { $maxjaar = $maxDj['maxjaar']; }

$Zoek_jaar_Liqiuditeit = mysqli_query($db,"	
	SELECT count(*) aant
	FROM tblLiquiditeit li
	join tblRubriekuser ru
	 on (li.rubuId = ru.rubuId)
	WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and year(li.datum) = '$maxjaar' ") or die (mysqli_error($db));	 
	
		while ($zoek = mysqli_fetch_assoc($Zoek_jaar_Liqiuditeit)) { $exist_jaar = $zoek['aant']; }
		
if($exist_jaar == 0) { // Toevoegen elke eerste van de maand van het jaar $maxjaar per rubuId. Dus zo'n 600 records (50 x 12)

$zoek_rubuId = mysqli_query($db,"
select rubuId
from tblRubriekuser
where lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db)) ;

while ( $rub = mysqli_fetch_assoc($zoek_rubuId)) { $rubuId = $rub['rubuId'];

for ($i = 1 ; $i <= 12 ; $i++){
$fistMonth = $maxjaar.'-'.$i.'-01'; // loop van 12 strings jaar - maandnr - 01

$date = date_create($fistMonth);
		$day = date_format($date, 'Y-m-d'); // datum formaat
		
$toevoegen_jaar = "INSERT INTO tblLiquiditeit (rubuId, datum) values
($rubuId,'$day')"; 

/*echo $toevoegen_jaar.'<br>';*/	mysqli_query($db,$toevoegen_jaar) or die (mysqli_error($db));
}
}
	}
// Einde Toevoegen jaar in tblLiquiditeit indien noodzakelijk	
// Jaar-maand uit tblDeklijst ophalen incl. totaal aan dekaantallen
$query_maanden = mysqli_query($db,"
	SELECT date_format((dmdek + interval 9 month),'%Y-%m') afvmnd, sum(dekat) dektot
	FROM tblDeklijst 
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(dmdek) = '$jaar' 
	GROUP BY date_format((dmdek + interval 9 month),'%Y-%m')
	") or die (mysqli_error($db)); 
		
		while ($month = mysqli_fetch_assoc($query_maanden)) { 
			$jr_mnd = $month['afvmnd']; 
			$werptot = ($month['dektot']*$worp)-($month['dektot']*$sterf/100);
			$day = $jr_mnd.'-01'; 
			$bedrag = $werptot*$prijs;  if($bedrag == 0) { $bedrag = 'NULL'; }
// Jaar-maand uit tblDeklijst ophalen incl. totaal aan dekaantallen
		
		$Update_tblLiquiditeit = "UPDATE tblLiquiditeit li join tblRubriekuser ru on (li.rubuId = ru.rubuId) join tblRubriek r on (ru.rubId = r.rubId) SET bedrag = ".$bedrag." WHERE ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.rubId = 39 and datum = '$day' ";
			mysqli_query($db,$Update_tblLiquiditeit) or die (mysqli_error($db));
		}
		}


	
//  3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
$prijs_lam = mysqli_query($db,"SELECT e.element, eu.waarde FROM tblElement e join tblElementuser eu on (e.elemId = eu.elemId) WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and e.elemId = 10 ") or die (mysqli_error($db));
	while ($prl = mysqli_fetch_assoc($prijs_lam)) { $prijs_nm = $prl['element'];  $prijs_val = $prl['waarde'];}

$sterfte = mysqli_query($db,"SELECT e.element, eu.waarde FROM tblElement e join tblElementuser eu on (e.elemId = eu.elemId) WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and e.elemId = 12 ") or die (mysqli_error($db));
	while ($stf = mysqli_fetch_assoc($sterfte)) { $sterf_nm = $stf['element'];  $sterf_val = $stf['waarde'];}
	
$worpgrootte = mysqli_query($db,"SELECT e.element, eu.waarde FROM tblElement e join tblElementuser eu on (e.elemId = eu.elemId) WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and e.elemId = 19 ") or die (mysqli_error($db));
	while ($wrp = mysqli_fetch_assoc($worpgrootte)) { $worp_nm = $wrp['element'];  $worp_val = $wrp['waarde'];}
//  Einde 3 Componenten ophalen : Prijs per lam, worpgrootte en sterfte
	
// Comtrole of saldo deklijst gelijk is aan saldo Liquiditeit
$query_Dekjaar = mysqli_query($db,"
	SELECT sum(dekat) dektot, liq.bedrag 
	FROM tblDeklijst dek
	 join (select date_format(li.datum,'%Y%m') jrmnd, li.bedrag 
		   from tblLiquiditeit li 
		    join tblRubriekuser ru on (li.rubuId = ru.rubuId) 
		   where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.rubId = 39 and year(li.datum) >= '$toon_jaar'
		   ) liq 
	 on (liq.jrmnd = date_format((dek.dmdek + interval 9 month),'%Y%m') )
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(dmdek) = '$toon_jaar' 
	GROUP BY liq.jrmnd, liq.bedrag
	") or die (mysqli_error($db)); 
		
		while ($year = mysqli_fetch_assoc($query_Dekjaar)) { 
			$dektot = $year['dektot']; 
			$werptot = ($year['dektot']*$worp_val)-($year['dektot']*$sterf_val/100);
			$dek_bedrag = ($werptot*$prijs_val);
			$liq_bedrag = ($year['bedrag']);
			
// Einde Comtrole of saldo deklijst gelijk is aan saldo Liquiditeit			
	
	If(round(($dek_bedrag-$liq_bedrag),2) <> 0 && $toon_jaar >= Date('Y')) { $letop = "De Liquiditeit wijkt af van deze deklijst.".'<br>'."Klik op 'Opslaan' om liquiditeit bij te werken met deze deklijst.";}
	}
//laatste jaar zoeken t.b.v aanmaken nieuwe deklijst
$laatste_jaar = mysqli_query($db,"
select year(max(dmdek)) jaar
from tblDeklijst
where lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die (mysqli_error($db));
			while ( $lst = mysqli_fetch_assoc($laatste_jaar)) { $old_jaar = $lst['jaar']; }
		if(isset($old_jaar)) { $new_jaar = $old_jaar+1; } else { $new_jaar = date('Y'); }
		

// Declaratie kzlJaar
$kzl_jaar = mysqli_query($db,"
select year(dmdek) jaar
from tblDeklijst
where lidId = ".mysqli_real_escape_string($db,$lidId)."
group by year(dmdek)
order by  year(dmdek)
") or die (mysqli_error($db));

$index = 0;
	while ( $kzljr = mysqli_fetch_assoc($kzl_jaar)) 
	{
	   $jaarnr[$index] = $kzljr['jaar'];
	   $jaarRaak[$index] = $toon_jaar;
	   $index++; 
    }
// Einde Declaratie kzlJaar
?>
<form method="post">
<table border = 0 ><tr>
<td style = "text-align:center;" valign= "top"; width= 80 ><b style = "font-size:18px;" >Jaar</b> </td>
<td valign = "top" >
<!-- KZLJAAR -->
 <select style="width:65;" name= "kzlJaar_" >
<?php	$count = count($jaarnr);	
for ($i = 0; $i < $count; $i++){

	$opties = array($jaarnr[$i]=>$jaarnr[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpToon_']) && $jaarRaak[$i] == $key) || (isset($_POST["kzlJaar_$Id"]) && $_POST["kzlJaar_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}

 ?> </select>
	 <!-- EINDE KZLJAAR -->
</td>
<td height = 5 width = 300 valign = "top" ><input type = submit name = "knpToon_" value = 'Toon' ></td>
<td width = 300> </td>
<td rowspan = 2 width = 300> <?php 
echo $prijs_nm." &euro; ".$prijs_val."<br>"; ?> <input type = hidden name = 'txtPrijs_' size = 1 value = <?php echo $prijs_val; ?> > <!-- hiddden --> <?php
echo $worp_nm.'  '.$worp_val."<br>"; 	?> <input type = hidden name = 'txtWorp_' size = 1 value = <?php echo $worp_val; ?> > <!-- hiddden --> <?php
echo $sterf_nm.' '.$sterf_val.' % '."<br>";?> <input type = hidden name = 'txtSterf_' size = 1 value = <?php echo $sterf_val; ?> > <!-- hiddden --> </td>

<td rowspan = 2><?php if($toon_jaar >= Date('Y') || ( $dtb == "k36098_bvdvschapendbs" && $lidId == 1 )) { ?><input type="submit" name = "knpSave_" value= "Opslaan" ><?php }
 if( $dtb == "k36098_bvdvschapendbs" && $lidId == 1 && $toon_jaar < Date('Y') ){
echo "Voorgaande jaren zijn normaal gesproken niet te wijzigen. ";
 }
  ?></td>
</tr>
<tr><td colspan = 4 align = center valign = "top" style = "color : 'blue' " >
	<?php if(isset($letop)) { echo $letop.'<br>'; } ?></td></tr>
</table>

<table border = 0 valign = 'top'>
<tr style = "font-size:12px;">

<th style = "text-align:right;"valign= bottom ;width= 80> Dek week <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 80>Dekdatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= 'bottom' ;width= 10>Aantal<br>dekkingen<hr></th>
<th width = 1></th>
<th style = "text-align:right;"valign= bottom ;width= 80> Werp week <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 80>Werpdatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 80>Aantal<br>lamm.<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 60>Speendatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 80>Afleverdatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom ;width= 80>Prognose <?php echo $toon_jaar; ?> <hr></th>
<th width = 1></th>
<th width=60></th>
 </tr>

<?php
$maandnm = array('','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december');
// Gegevens per maand ophalen incl. uit tblLiquiditeit
$query_maanden = mysqli_query($db,"
	SELECT month(dmdek) mndnr, sum(dekat) dektot, liq.bedrag 
	FROM tblDeklijst dek
	 left join (select date_format(li.datum,'%Y%m') jrmnd, li.bedrag 
		   from tblLiquiditeit li 
		    join tblRubriekuser ru on (li.rubuId = ru.rubuId) 
		   where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.rubId = 39 and year(li.datum) >= '$toon_jaar'
		   ) liq 
	 on (liq.jrmnd = date_format((dek.dmdek + interval 9 month),'%Y%m') )
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(dmdek) = '$toon_jaar' 
	GROUP BY month(dmdek), liq.bedrag
	") or die (mysqli_error($db)); 
		
		while ($month = mysqli_fetch_assoc($query_maanden)) { 
			$mndnr = $month['mndnr']; 
			$dektot = $month['dektot']; 
			$werptot = ($month['dektot']*$worp_val)-($month['dektot']*$sterf_val/100); 
// Einde Gegevens per maand ophalen incl. uit tblLiquiditeit
// Gegevens per week ophalen en tonen
$query_weken = mysqli_query($db,"
	SELECT dekId, dmdek, dekat, dmdek + interval 145 day dmwerp, (dmdek + interval 194 day) dmspeen, (dmdek + interval 275 day) dmafvoer, month(((dmdek + interval 145 day) + interval 4 month)) afvmnd 
	FROM tblDeklijst 
	WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and year(dmdek) = '$toon_jaar' and month(dmdek) = '$mndnr'
 ") or die (mysqli_error($db));

	while($week = mysqli_fetch_assoc($query_weken))
	{	$Id = $week['dekId'];
		$dweek = date_format(date_create($week['dmdek']),'W');
		$dekdm = date_format(date_create($week['dmdek']),'d-m-Y');
		$dekat = $week['dekat'];
		$wweek = date_format(date_create($week['dmwerp']),'W');
		$werpdm = date_format(date_create($week['dmwerp']),'d-m-Y');
		$werpat = ($dekat*$worp_val)-($dekat*$sterf_val/100);
		$speendm = date_format(date_create($week['dmspeen']),'d-m-Y');
		$afvoerdm = date_format(date_create($week['dmafvoer']),'d-m-Y');
		$afvmnd = $week['afvmnd'];	 ?>
		
		
<tr><td align = center><input type = hidden name = <?php echo "txtId_$Id"; ?> size = 1 value = <?php echo $Id; ?> >  <!--hiddden-->
 <?php echo $dweek; ?></td>
<td></td>
<td><?php echo $dekdm; ?></td>
<td></td>
<td align = center> 
 <input type = text name = <?php echo "txtDekat_$Id"; ?> size = 1 style = "text-align : right;" value = <?php echo $dekat; ?> >
 <input type = hidden name = <?php echo "ctrDekat_$Id"; ?> size = 1 value = <?php echo $dekat; ?> ></td> <!--hiddden-->
<td></td>
<td align = center><?php echo $wweek; ?></td>
<td></td>
<td align = center><?php echo $werpdm; ?></td>
<td></td>
<td align = center><?php echo $werpat; ?> </td> <!--hiddden-->
<td></td>
<td align = center><?php echo $speendm; ?></td>
<td></td>
<td align = center><?php echo $afvoerdm; ?></td>
<td></td>
<td align = center></td>
</tr>
<?php } // Gegevens per week ophalen en tonen ?> 
 <tr><td><hr></td>
 <td colspan = 3 align = center><hr></td>
 <td ><hr></td>
 <td colspan = 5><hr></td>
 <td ><hr></td>
 <td colspan = 2><hr></td>
 <td colspan = 3 ><hr></td>
 <td ><hr></td></tr>
  <tr height = 50 valign = 'top'><td><b>Totaal </b></td>
 <td colspan = 3 align = center><b><?php echo $maandnm[$mndnr]; ?></b></td>
 <td align = center ><b><?php echo $dektot; ?> </b></td>
 <td colspan = 5></td>
 <td align = center><b><?php echo $werptot; ?></b></td>
 <td colspan = 2></td>
 <td colspan = 3 align = center><b><?php echo $maandnm[$afvmnd]; ?></b></td>
 <td align = 'right'><b> <?php echo euro_format($werptot*$prijs_val); ?> </b></td></tr>
<?php } ?>
<tr><td colspan = 17 align = right >
<td>deklijst aanmaken : </td>
<td><input type = hidden name = "txtNewjaar_" size = 2 value = <?php echo $new_jaar; ?> >
<input type = submit name = 'knpCreate_' value = <?php echo $new_jaar ; ?> ></td>
</td></tr></table>
</form>


	</TD>
<?php } else { ?> <img src='deklijst_php.jpg'  width='970' height='550'> <?php }
Include "menuFinance.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
