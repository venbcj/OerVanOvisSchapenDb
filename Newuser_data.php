<?php

/* 11-6-2020 Standaard Lambar toegevoegd bij nieuwe users 
8-4-2023 naamreader Rendac standaard vullen. Relatie Vermist standaard toevoegen en SQL beveiligd met quotes 
21-02-2025 Invoer Rendac in tblRelatie uitval = 1 gesplitst van Invoer Vermist in tblRelatie i.v.m. uitval = 0 */

$insert_tblHok = "INSERT INTO tblHok SET lidId = '".mysqli_real_escape_string($db,$newId)."', hoknr = 'Lambar', actief = 1 ";
		mysqli_query($db,$insert_tblHok) or die (mysqli_error($db));

$insert_tblMomentuser = "INSERT INTO tblMomentuser (lidId, momId)
	SELECT '".mysqli_real_escape_string($db,$newId)."', momId
	FROM tblMoment
	";
		mysqli_query($db,$insert_tblMomentuser) or die (mysqli_error($db));

$insert_tblEenheiduser = "INSERT INTO tblEenheiduser (lidId, eenhId)
	SELECT '".mysqli_real_escape_string($db,$newId)."', eenhId
	FROM tblEenheid
	";
		mysqli_query($db,$insert_tblEenheiduser) or die (mysqli_error($db));

$insert_tblElementuser = "INSERT INTO tblElementuser (elemId, lidId)
	SELECT elemId, '".mysqli_real_escape_string($db,$newId)."'
	FROM tblElement
	ORDER BY elemId
	";
		mysqli_query($db,$insert_tblElementuser) or die (mysqli_error($db));

//een aantal elementen m.b.t. de saldoberekening worden standaard uitgezet
$update_tblElementuser = "UPDATE tblElementuser set sal = 0
WHERE lidId = '".mysqli_real_escape_string($db,$newId)."' and (elemId = 2 or elemId = 3 or elemId = 4 or elemId = 5 or elemId = 6 or elemId = 7 or elemId = 8 or elemId = 10 or elemId = 11 or elemId = 14 or elemId = 15 or elemId = 17)
";

		mysqli_query($db,$update_tblElementuser) or die (mysqli_error($db));




$insert_tblPartij = "INSERT INTO tblPartij (lidId, ubn, naam, actief, naamreader ) VALUES
(	'".mysqli_real_escape_string($db,$newId)."', 123123, 'Rendac', 1, 'Rendac'),
(	'".mysqli_real_escape_string($db,$newId)."', 123456, 'Vermist', 1, 'Vermist');
";
		mysqli_query($db,$insert_tblPartij) or die (mysqli_error($db));


$insert_tblRelatie_Rendac = "INSERT INTO tblRelatie (partId, relatie, uitval, actief)
	SELECT p.partId, 'cred', 1, 1
	FROM tblPartij p
	WHERE p.ubn = '123123' and p.lidId = '".mysqli_real_escape_string($db,$newId)."' ;
	";
		mysqli_query($db,$insert_tblRelatie_Rendac) or die (mysqli_error($db));

$insert_tblRelatie_Vermist = "INSERT INTO tblRelatie (partId, relatie, actief)
	SELECT p.partId, 'cred', 0
	FROM tblPartij p
	WHERE p.ubn = '123456' and p.lidId = '".mysqli_real_escape_string($db,$newId)."' ;
	";
		mysqli_query($db,$insert_tblRelatie_Vermist) or die (mysqli_error($db));



$insert_tblRubriekuser = "INSERT INTO tblRubriekuser (rubId, lidId)
	SELECT rubId, '".mysqli_real_escape_string($db,$newId)."'
	FROM tblRubriek
	ORDER BY rubId;
	";
		mysqli_query($db,$insert_tblRubriekuser) or die (mysqli_error($db));
?>
