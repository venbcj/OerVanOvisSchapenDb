 <?php

require_once("autoload.php");


 include "connect_db.php";
 
 $lidId = 3;
 $date = $_GET['date'];
 $Gespe = 
mysqli_query($db, " select count(h.hisId) aant
 from tblStal st
  join tblHistorie h on (h.stalId = st.stalId)
  join tblBezetting b on (h.hisId = b.hisId)
  join tblPeriode p on (p.periId =b.periId)
  join tblHok hk on (hk.hokId =p.hokId)
  left join
		(
			select b.bezId, h1.hisId hisv, min(h2.hisId) hist
			from tblBezetting b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			 join tblPeriode p on (p.periId = b.periId)
			where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
			group by b.bezId, h1.hisId
		) uit
		on (uit.bezId = b.bezId)
	left join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) hs on (hs.schaapId = st.schaapId)
 where hk.hokId = 1324 and h.datum <= '$date' and (ht.datum > '$date' or isnull(ht.datum)) and hs.datum <= '$date'
 ") or die (mysqli_error($db));
 
 while ( $sp = mysqli_fetch_assoc($Gespe)) { $geef = $sp['aant']; } 
 
 echo json_encode($geef); ?>
