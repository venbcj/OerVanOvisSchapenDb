<!-- 3-9-2017 : gemaakt -->

<?php
/* toegepast in :
	- Ras.php */
	
function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}

function getIdFromKey($string) {
    $split_Id = explode('_', $string); 
    return $split_Id[1];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  

//echo 'recId = '.$recId.'<br>';
 foreach($id as $key => $value) { 




	if ($key == 'txtDatum' && !empty($value) ) { $dag = date_create($value); $fldDay =  "'".date_format($dag, 'Y-m-d')."'"; /*echo $key.'='.$value."<br/>";*/}    

    if ($key == 'txtKilo' && !empty($value)) {  $fldKilo = $value; } else if ($key == 'txtKilo' && empty($value)) { $fldKilo = 'NULL'; }
    
	if ($key == 'chbDelVoer') {  $fldDelVoer = $value; /*echo $key.'='.$value."<br/>";*/  }  	 
	if ($key == 'chbDelPeri') {  $fldDelPeri = $value; /*echo $key.'='.$value."<br/>";*/  }  	 
    	 
    	 }
    

	 

	


if(isset($recId) and $recId > 0) {

 

$zoek_in_database = mysqli_query($db, "
select dmafsluit, i.artId, sum(nutat) nutat
from tblPeriode p
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (v.inkId = i.inkId)
where p.periId = ".mysqli_real_escape_string($db,$recId)."
group by dmafsluit, i.artId
") or die (mysqli_error($db));
	while( $co = mysqli_fetch_assoc($zoek_in_database)) { 

		$dbDate = $co['dmafsluit']; /*if(empty($dbNaam)) { $dbNaam = 'NULL'; } else {*/ $dbDate = "'".$dbDate."'"; //}
		$dbArtId = $co['artId'];
		$dbNutat = $co['nutat'];  	if(empty($dbNutat)) { $dbNutat = 'NULL'; }
		
if(isset($fldDay) && $fldDay <> $dbDate) {					
	$update_datum = "UPDATE tblPeriode SET dmafsluit = ".$fldDay." WHERE periId = ".mysqli_real_escape_string($db,$recId)." ";
		mysqli_query($db,$update_datum) or die (mysqli_error($db));  
		//echo $update_datum."<br/>";

 } 
 unset($fldDay); 

/*** WIJZIGEN VOER ***/
if(isset($fldKilo) && $fldKilo <> $dbNutat) {
// *** VOER toevoegen ***
	if($fldKilo <> 'NULL' && $fldKilo > $dbNutat) {

$verschil = $fldKilo - $dbNutat;
// Totale hoeveelheid voer op voorraad bepalen.
$queryStock = mysqli_query($db,"
select sum(i.inkat-coalesce(v.vbrat,0)) vrdat
from tblInkoop i
 left join (
	select i.inkId, sum(v.nutat*v.stdat) vbrat
	from tblVoeding v
	 join tblInkoop i on (v.inkId = i.inkId)
	where i.artId = ".mysqli_real_escape_string($db,$dbArtId)."
	group by i.inkId
 ) v on (i.inkId = v.inkId)
where i.artId = ".mysqli_real_escape_string($db,$dbArtId)."
") or die(mysqli_error($db));

while ($hoev = mysqli_fetch_assoc($queryStock)) { $instock = $hoev['vrdat']; }
// EINDE Totale hoeveelheid voer op voorraad bepalen.  
// Controle of voorraad toereikend is
if (isset($instock) && $instock < $verschil ) { $fout = "Er is onvoldoende voer op voorraad."; }
// Einde Controle of voorraad toereikend is

if(!isset($fout)) { // Voldoende voorraad

$zoek_inkId = mysqli_query($db,"
select min(i.inkId) inkId
from tblInkoop i
 left join (
	select v.inkId, sum(v.nutat*v.stdat) vbrat
	from tblVoeding v
	 join tblInkoop i on (i.inkId = v.inkId)
	where i.artId = ".mysqli_real_escape_string($db,$dbArtId)."
	group by v.inkId
 ) v on (i.inkId = v.inkId)
where i.artId = ".mysqli_real_escape_string($db,$dbArtId)." and i.inkat-coalesce(v.vbrat,0) > 0
") or die (mysqli_error($db));
	
	while($ink = mysqli_fetch_assoc($zoek_inkId))
		  {	$inkId_ingebruik = $ink['inkId']; }

// zoek het aantal inkIds dat nog kan worden aangesproken
$zoek_aantal_inkIds = mysqli_query($db,"
SELECT count(inkId) aant
from tblInkoop
where artId = ".mysqli_real_escape_string($db,$dbArtId)." and inkId >= ".mysqli_real_escape_string($db,$inkId_ingebruik)."
") or die (mysqli_error($db));
	while ($nr = mysqli_fetch_assoc($zoek_aantal_inkIds)) {
		$count = $nr['aant'];
	}

for($i=1; $i<=$count; $i++) { // for loop

if($verschil > 0) {




// STAP 1) Zoek oudste inkId met voorraad
$zoek_inkId = mysqli_query($db,"
select min(i.inkId) inkId
from tblInkoop i
 left join (
	select v.inkId, sum(v.nutat*v.stdat) vbrat
	from tblVoeding v
	 join tblInkoop i on (i.inkId = v.inkId)
	where i.artId = ".mysqli_real_escape_string($db,$dbArtId)."
	group by v.inkId
 ) v on (i.inkId = v.inkId)
where i.artId = ".mysqli_real_escape_string($db,$dbArtId)." and i.inkat-coalesce(v.vbrat,0) > 0
") or die (mysqli_error($db));
	
	while($ink = mysqli_fetch_assoc($zoek_inkId))
		  {	$inkId = $ink['inkId']; }

// STAP 2) Hoeveelheid voorraad ophalen van oudste inkId
$stock_van_ink = mysqli_query($db,"
select i.inkat - sum(coalesce(v.nutat,0)) stock
from tblInkoop i
 left join tblVoeding v on (i.inkId = v.inkId)
where i.inkId = ".mysqli_real_escape_string($db,$inkId)."
group by i.inkat
") or die(mysqli_error($db));
		while($istk = mysqli_fetch_assoc($stock_van_ink))
		  {	$inkvrd = $istk['stock']; } #echo '$inkvrd = '.$inkvrd.'<br>';

if($inkvrd >= $verschil) { // Inkoopvoorraad volstaat WEL

//STAP 3) Voer aan bestaand voedId/inkId toevoegen of nieuw voedId/inkId toevoegen
if($i == 1 ) { // Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding

$zoek_ink_tblVoeding = mysqli_query($db,"
SELECT voedId, nutat
from tblVoeding
where periId = ".mysqli_real_escape_string($db,$recId)." and inkId = ".mysqli_real_escape_string($db,$inkId)."
") or die(mysqli_error($db));
	while($vId = mysqli_fetch_assoc($zoek_ink_tblVoeding))
		  {	$voedId = $vId['voedId']; 
		  	$nutat = $vId['nutat']; }
} // Einde Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding

if(isset($voedId)) { // Aan bestaand voedId toevoegen
 $newNutat = $nutat+$verschil;

	$update_kilo = "Update tblVoeding SET nutat = ".mysqli_real_escape_string($db,$newNutat)." WHERE voedId = ".mysqli_real_escape_string($db,$voedId)." 	";
		mysqli_query($db,$update_kilo) or die (mysqli_error($db));
		#echo $update_kilo."<br/>";
}
else if(!isset($voedId)) {  // Nieuwe voedId toevoegen

$zoek_stdat = mysqli_query($db,"
select stdat
from tblArtikel
where artId = ".mysqli_real_escape_string($db,$dbArtId)."
") or die (mysqli_error($db));
	while($std = mysqli_fetch_assoc($zoek_stdat))
		  {	$stdat = $std['stdat'];	}

	$insert_tblVoeding = "insert into tblVoeding set periId = ".mysqli_real_escape_string($db,$recId).", inkId = ".mysqli_real_escape_string($db,$inkId).", nutat = ".mysqli_real_escape_string($db,$verschil).", stdat = ".mysqli_real_escape_string($db,$stdat)."
";
		mysqli_query($db,$insert_tblVoeding) or die(mysqli_error($db));
		#echo $insert_tblVoeding."<br/>"; 

} // Einde Nieuwe voedId toevoegen
unset($voedId);

	$verschil = 0; } // Einde Inkoopvoorraad volstaat WEL
// Inkoopvoorraad volstaat NIET
if($inkvrd < $verschil) { 

if($i == 1 ) { // Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding

$zoek_ink_tblVoeding = mysqli_query($db,"
SELECT voedId, nutat
from tblVoeding
where periId = ".mysqli_real_escape_string($db,$recId)." and inkId = ".mysqli_real_escape_string($db,$inkId)."
") or die(mysqli_error($db));
	while($vId = mysqli_fetch_assoc($zoek_ink_tblVoeding))
		  {	$voedId = $vId['voedId']; 
		  	$nutat = $vId['nutat']; }
} // Einde Als de eerst inkId wordt aangesproken kan deze reeds bestaan in tblVoeding

if(isset($voedId)) { // Aan bestaand voedId toevoegen
 $newNutat = $nutat+$inkvrd;

	$update_kilo = "Update tblVoeding SET nutat = ".mysqli_real_escape_string($db,$newNutat)." WHERE voedId = ".mysqli_real_escape_string($db,$voedId)." 	";
		mysqli_query($db,$update_kilo) or die (mysqli_error($db));
		#echo $update_kilo."<br/>";
}
else if(!isset($voedId)) {  // Nieuwe voedId toevoegen

$zoek_stdat = mysqli_query($db,"
select stdat
from tblArtikel
where artId = ".mysqli_real_escape_string($db,$dbArtId)."
") or die (mysqli_error($db));
	while($std = mysqli_fetch_assoc($zoek_stdat))
		  {	$stdat = $std['stdat'];	}

	$insert_tblVoeding = "insert into tblVoeding set periId = ".mysqli_real_escape_string($db,$recId).", inkId = ".mysqli_real_escape_string($db,$inkId).", nutat = ".mysqli_real_escape_string($db,$inkvrd).", stdat = ".mysqli_real_escape_string($db,$stdat)."
";
		mysqli_query($db,$insert_tblVoeding) or die(mysqli_error($db));
		#echo $insert_tblVoeding."<br/>"; 

} // Einde Nieuwe voedId toevoegen


unset($voedId);
$verschil = $verschil-$inkvrd;

} // Einde Inkoopvoorraad volstaat NIET

#echo '$verschil = '.$verschil.'<br>';

} // Einde $verschil > 0
} // Einde for loop


	} // Einde Voldoende voorraad
	}

// *** Einde VOER toevoegen ***
// *** VOER verminderen ***

	if($fldKilo <> 'NULL' && $fldKilo < $dbNutat) {
// Bij meerdere inkId's van 1 periId wordt per inkId gekeken hoeveel kg voer kan worden afgehaald of het inkId moet worden verwijderd.
$verschil = $dbNutat-$fldKilo;

$hoeveel_inkIds = mysqli_query($db,"
SELECT count(voedId) aant
from tblVoeding
where periId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while($aa = mysqli_fetch_assoc($hoeveel_inkIds)) { $count = $aa['aant']; }


if($count == 1) {

	$update_kilo = "Update tblVoeding SET nutat = $fldKilo WHERE periId = ".mysqli_real_escape_string($db,$recId)." 	";
		mysqli_query($db,$update_kilo) or die (mysqli_error($db)); //header("Location:".$url."Ras.php");
		#echo $update_kilo."<br/>";
}
else if($count > 1) { 

for($i=1; $i<=$count; $i++) {

	#echo $i.' - verschil = '.$verschil.'<br>';
if($verschil > 0) {

$zoek_kg_laatste_inkId = mysqli_query($db,"
SELECT v.voedId, v.nutat
from tblVoeding v
 join (
	SELECT max(voedId) voedId
	from tblVoeding
	where periId = ".mysqli_real_escape_string($db,$recId)."
 ) lv on (v.voedId = lv.voedId)
") or die (mysqli_error($db));
	while($kg = mysqli_fetch_assoc($zoek_kg_laatste_inkId)) { 
		$last_v = $kg['voedId'];
		$nutat = $kg['nutat'];
	}

if($nutat-$verschil >0 ) { $newNutat = $nutat-$verschil; $verschil = 0; 

	$update_kilo = "Update tblVoeding SET nutat = $newNutat WHERE voedId = ".mysqli_real_escape_string($db,$last_v)." 	";
		mysqli_query($db,$update_kilo) or die (mysqli_error($db)); //header("Location:".$url."Ras.php");
		#echo $update_kilo."<br/>";

}
else { $verschil = $verschil-$nutat;

	$delete_voedId = "Delete from tblVoeding where voedId = ".mysqli_real_escape_string($db,$last_v)." 	";
		mysqli_query($db,$delete_voedId) or die (mysqli_error($db));
		#echo $delete_voedId."<br/>";
}

} // Einde $verschil >0
} // Einde for loop



} // Einde als $count > 1



#echo '$verschil = '.$verschil.'<br>';


 
	}
// *** Einde VOER verminderen ***


	
 } 
 unset($fldKilo);
/*** EINDE  WIJZIGEN VOER  EINDE ***/

if(isset($fldDelVoer)) {
	$delete_voeding = "Delete from tblVoeding where periId = ".mysqli_real_escape_string($db,$recId)." 	";
		mysqli_query($db,$delete_voeding) or die (mysqli_error($db)); //header("Location:".$url."Ras.php");
		//echo $delete_voeding."<br/>";
		unset($fldDelVoer);
 }


if(isset($fldDelPeri)) {
	$delete_voeding = "Delete from tblVoeding where periId = ".mysqli_real_escape_string($db,$recId)." 	";
		mysqli_query($db,$delete_voeding) or die (mysqli_error($db));
		
	$delete_periode = "Delete from tblPeriode where periId = ".mysqli_real_escape_string($db,$recId)." 	";
		mysqli_query($db,$delete_periode) or die (mysqli_error($db)); //header("Location:".$url."Ras.php");
		//echo $delete_periode."<br/>";

		unset($fldDelPeri);
 }


}



	
	}
						
}
?>
					
	