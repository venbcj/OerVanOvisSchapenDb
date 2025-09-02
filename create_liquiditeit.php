<?php
/* 30-10-2016 : $year vervangen door $new_jaar */
/*
$new_jaar = 2015;
$lidId = 4;
include "connect_db.php";*/

/* Toegepast in :  
	-	Deklijst.php
	-	Liquiditeit.php
*/


	$maanden = array($new_jaar.'-01-01',$new_jaar.'-02-01',$new_jaar.'-03-01',$new_jaar.'-04-01',$new_jaar.'-05-01',$new_jaar.'-06-01',$new_jaar.'-07-01',$new_jaar.'-08-01',$new_jaar.'-09-01',$new_jaar.'-10-01',$new_jaar.'-11-01',$new_jaar.'-12-01');

for ($i = 0; $i<12; $i++)
{
$maand = $maanden[$i];

//echo $maand.'<br>';
	
$ophalen_rubriekuser = mysqli_query($db,"SELECT '$maand' dag, rubuId FROM tblRubriekuser WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ") or die (mysqli_error($db));
	while ( $oph = mysqli_fetch_assoc($ophalen_rubriekuser)) { 
		
		$rub_user = $oph['rubuId']; 	
		$datum = $oph['dag']; 	
		
		//echo $rub_user.' - '.$datum.'<br>';
	
	$toevoegen_jaar = "INSERT INTO tblLiquiditeit SET rubuId = '$rub_user', datum = '$datum' ";
	
	/*echo $toevoegen_jaar.'<br>';*/
		mysqli_query($db,$toevoegen_jaar) or die (mysqli_error($db));
	
}
}

?>