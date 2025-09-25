<?php 
include "connect_db.php";


//https://www.phphulp.nl/php/forum/topic/array-aanvullen-binnen-een-functie-die-zichzelf-herhaald/104705/last/

/*with recursive cte (id, rasId, volwId) as (
  SELECT     s.schaapId, s.rasId, s.volwId
  FROM       tblSchaap s
  WHERE      s.schaapId = 19
  union all
  SELECT     s.schaapId, s.rasId, s.volwId
  FROM       tblVolwas v
        join tblSchaap s on (s.svolwId = v.volwId)
  inner join cte on s.volwId = cte.schaapId
)
SELECT * FROM cte;*/ ?>


<form action = "temp_stamboom.php" method = "post">
<table>
<tr>
<th> werknr </th>
<th> geslacht </th>
<th> ras </th>
<th> moeder </th>
<th> vader </th>
<th> worp </th>
</tr>


<?php


$ouders = mysqli_query($db,"
with recursive sheep (schaapId, levnr, geslacht, ras, volwId_s, mdrId, levnr_ma, ras_ma, vdrId, levnr_pa, ras_pa) as (
   SELECT s.schaapId, right(s.levensnummer,5) levnr, s.geslacht, r.ras, s.volwId, v.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, v.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas v
     left join tblSchaap s on s.volwId = v.volwId
     left join tblRas r on s.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = v.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = v.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
    WHERE s.schaapId = 11933
    union all
   SELECT sm.schaapId, right(sm.levensnummer,5) levnr, sm.geslacht, r.ras, sm.volwId, vm.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, vm.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vm
     left join tblSchaap sm on sm.volwId = vm.volwId
     left join tblRas r on sm.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vm.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vm.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sm.schaapId = sheep.mdrId
    union all
   SELECT sv.schaapId, right(sv.levensnummer,5) levnr, sv.geslacht, r.ras, sv.volwId, vv.mdrId, right(ma.levensnummer,5) levnr_ma, rm.ras ras_ma, vv.vdrId, right(pa.levensnummer,5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vv
     left join tblSchaap sv on sv.volwId = vv.volwId
     left join tblRas r on sv.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vv.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vv.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sv.schaapId = sheep.vdrId
)


SELECT s.schaapId, levnr, s.geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa, count(worp.schaapId) grootte
  FROM sheep s
   join tblSchaap worp on (s.volwId_s = worp.volwId)
GROUP BY s.schaapId, levnr, geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa
ORDER BY s.schaapId
") or die (mysqli_error($db));

while($row = mysqli_fetch_assoc($ouders)) {

	$schaap = $row['levnr'];
	$geslacht = $row['geslacht'];
	$ras = $row['ras'];
	$moeder = $row['levnr_ma'];
	$vader = $row['levnr_pa']; 
	$worp = $row['grootte']; 
	?>

<tr>
	<td> <?php echo $schaap; ?> </td>
	<td> <?php echo $geslacht; ?> </td>
	<td> <?php echo $ras; ?> </td>
	<td> <?php echo $moeder; ?> </td>
	<td> <?php echo $vader; ?> </td>
	<td> <?php echo $worp; ?> </td>
</tr>



<?php	} ?>

</table>
</form>

<?php
 /* volwId 11020 => schaapId 21023
  volwId 11019 => schaapId 21022*/
?>