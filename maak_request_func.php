<!-- 15-8-2015 gemaakt 

Toegepast in :
- InsAfleveren.php 
- InsGeboortes.php 
- InsOmnummeren.php 
- InvSchaap.php 
- HokAfleveren -->
<?php

/* function maak_request maakt een nieuw request aan. In geval van : 1. Er staat geen melding open 2. Het aantal van 60 meldingen is bereikt. 
De function wordt toegepast in maak_request.php. Dat script zit in een loop. Een function kan maar 1x worden aangemaakt. Includen van dit script, en dus het declareren vam de function, kan daar dus niet plaats vinden.
De include vindt plaats in bovengenoemde toegepaste scripts. Via de gelijknamige naam post_reader____.php wordt maak_request.php bereikt.
Voor afleveren geldt dus : include in InsAfleveren.php => post_readerAflev.php  =>  maak_request.php */

function maak_request($datb,$lidid,$fldCode) {
	
		$insert_tblRequest = "INSERT INTO tblRequest SET lidId_new = ".mysqli_real_escape_string($datb,$lidid).", code = '".mysqli_real_escape_string($datb,$fldCode)."' "; 	
							mysqli_query($datb,$insert_tblRequest) or die (mysqli_error($datb));
		$req_open = mysqli_query($datb,"SELECT max(reqId) reqId
										FROM tblRequest 
										WHERE lidId_new = ".mysqli_real_escape_string($datb,$lidid)." and isnull(dmmeld) and code = '$fldCode' ") ;
		
	if($req_open)
		{	$open = mysqli_fetch_assoc($req_open);
				return $open['reqId'];
		}
		return FALSE;
}

?>