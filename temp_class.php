<?php

include "connect_db.php";

$query = mysqli_query($db,"
SELECT schaapId, levensnummer, rasId
FROM tblSchaap
WHERE schaapId > 2335 and schaapId < 2340
") or die (mysqli_error($db));

while ($zs = mysqli_fetch_assoc($query)) {
	// code...
}

?>