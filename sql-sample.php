<?php


$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db, $STALID)."',
    datum = '".mysqli_real_escape_string($db, $DATUM)."',
actId = '".mysqli_real_escape_string($db, $ACTID)."' ";
/*echo $insert_tblHistorie.'<br>';*/        mysqli_query($db, $insert_tblHistorie);

$kzl = mysqli_query($db,"
SELECT date_format(h.datum,'%Y') jaar 
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(datum,'%Y') >= '$jaarstart' and h.actId = 4 and h.skip = 0
GROUP BY date_format(datum,'%Y')
ORDER BY date_format(datum,'%Y') desc 
") or die (mysqli_error($db));
