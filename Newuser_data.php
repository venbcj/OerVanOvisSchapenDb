<?php
/* 11-6-2020 Standaard Lambar toegevoegd bij nieuwe users */

$insert_tblHok = "INSERT INTO tblHok SET lidId = ".mysqli_real_escape_string($db,$newId).", hoknr = 'Lambar', actief = 1 ";
/*echo '<br>'.$insert_tblHok.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblHok) or die (mysqli_error($db));

$insert_tblMomentuser = "INSERT INTO tblMomentuser (lidId, momId)
	SELECT ".mysqli_real_escape_string($db,$newId).", momId
	FROM tblMoment
	";
/*echo '<br>'.$insert_tblMomentuser.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblMomentuser) or die (mysqli_error($db));

$insert_tblEenheiduser = "INSERT INTO tblEenheiduser (lidId, eenhId)
	SELECT ".mysqli_real_escape_string($db,$newId).", eenhId
	FROM tblEenheid
	";
/*echo $insert_		tblEenheiduser.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblEenheiduser) or die (mysqli_error($db));

$insert_tblElementuser = "INSERT INTO tblElementuser (elemId, lidId)
	SELECT elemId, ".mysqli_real_escape_string($db,$newId)."
	FROM tblElement
	ORDER BY elemId
	";
/*echo $insert_		tblElementuser.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblElementuser) or die (mysqli_error($db));

//een aantal elementen m.b.t. de saldoberekening worden standaard uitgezet
$update_tblElementuser = "UPDATE tblElementuser set sal = 0
WHERE lidId = ".mysqli_real_escape_string($db,$newId)." and (elemId = 2 or elemId = 3 or elemId = 4 or elemId = 5 or elemId = 6 or elemId = 7 or elemId = 8 or elemId = 10 or elemId = 11 or elemId = 14 or elemId = 15 or elemId = 17)
";

/*echo $update_tblElementuser.'<br>'.'<br>';*/		mysqli_query($db,$update_tblElementuser) or die (mysqli_error($db));




$insert_tblPartij = "INSERT INTO tblPartij (lidId, ubn, naam ) VALUES
(".mysqli_real_escape_string($db,$newId).", 123123, 'Rendac');
	";
/*echo $insert_		tblPartij.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblPartij) or die (mysqli_error($db));


$insert_tblRelatie = "INSERT INTO tblRelatie (partId, relatie, uitval)
	SELECT p.partId, 'cred', 1
	FROM tblPartij p
	WHERE p.lidId = ".mysqli_real_escape_string($db,$newId).";
	";
/*echo $insert_		tblRelatie.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblRelatie) or die (mysqli_error($db));



$insert_tblRubriekuser = "INSERT INTO tblRubriekuser (rubId, lidId)
	SELECT rubId, ".mysqli_real_escape_string($db,$newId)."
	FROM tblRubriek
	ORDER BY rubId;
	";
/*echo $insert_		tblRubriekuser.'<br>'.'<br>';*/		mysqli_query($db,$insert_tblRubriekuser) or die (mysqli_error($db));
?>