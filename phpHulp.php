<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>


<?php
Include "connect_db.php";


function kzl_loop($ArrayKey, $ArrayName, $dbKey, $btnRefresh, $fldKey) {
$count = count($ArrayName);
for ($i = 0; $i < $count; $i++){

	$opties = array($ArrayKey[$i]=>$ArrayName[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($btnRefresh) && $dbKey == $key) || (isset($fldKey) && $fldKey == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
}


// Array tbv javascript om vader automatisch te tonen
$zoek_laatste_dekkingen = mysqli_query($db,"
SELECT v.mdrId, right(vdr.levensnummer,5) lev
FROM tblVolwas v
 join (
 	SELECT v.mdrId, max(v.volwId) volwId
	FROM tblVolwas v
	 left join tblHistorie hv on (hv.hisId = v.hisId)
	 left join tblDracht d on (v.volwId = d.volwId)
	 left join tblHistorie hd on (hd.hisId = d.hisId)
	 left join tblSchaap k on (k.volwId = v.volwId)
	 left join (
	    SELECT s.schaapId
	    FROM tblSchaap s
	     join tblStal st on (s.schaapId = st.schaapId)
	     join tblHistorie h on (st.stalId = h.stalId)
	    WHERE h.actId = 3
	 ) ha on (k.schaapId = ha.schaapId)
	WHERE (isnull(hv.hisId) or hv.skip = 0) and (isnull(hd.hisId) or hd.skip = 0) and isnull(ha.schaapId)
	GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap vdr on (vdr.schaapId = v.vdrId)
") or die (mysqli_error($db));

while ( $zld = mysqli_fetch_assoc($zoek_laatste_dekkingen)) { 

	$array_vader_uit_koppel[$zld['mdrId']] = $zld['lev']; 
}

// Einde Array tbv javascript om vader automatisch te tonen
?>

<script>

function toon_dracht(id) {

var ooi = 'moeder_' + id;
var moeder = document.getElementById(ooi);		var mr = moeder.value;


 if(mr.length > 0) toon_vader_uit_koppel(mr, id);

}

 var jArray_vdr = <?php echo json_encode($array_vader_uit_koppel); ?>;

function toon_vader_uit_koppel(m, i) {
	//document.getElementById('result_vader').innerHTML = jArray_vdr[m];

var ram = 'vader_' + i;
var resultVdr = 'result_vader_' + i;
 	if(jArray_vdr[m] != null)
 	{
	document.getElementById(ram).style.display = "none";
  	document.getElementById(ram).value = null; // veld leegmaken indien gevuld
  	document.getElementById(resultVdr).innerHTML = jArray_vdr[m];
	}
  	else 
  	{
  	//document.getElementById(ram).style.display = "block";
	document.getElementById(ram).style.display = "inline-block";
	document.getElementById(resultVdr).innerHTML = "";
  	}
}

</script>


<form action="phpHulp.php" method = "post">
<table border = 0>
<tr valign = bottom style = "font-size : 12px;">
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th><hr></th>

</tr>

<?php
// Declaratie MOEDERDIEREN
$zoek_moederdieren = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,5) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblVolwas v on (s.schaapId = v.mdrId)
WHERE st.lidId = 13 and v.vdrId is not null
ORDER BY right(s.levensnummer,5)
") or die (mysqli_error($db));


$index = 0; 
while ($mdr = mysqli_fetch_assoc($zoek_moederdieren)) 
{ 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIEREN
?>

<tr><td>
	<input type = "submit" name = "knpVervers" value = "Verversen">
</td></tr>

<?php
$velden = mysqli_query($db,"
SELECT rd.Id, moeder 
FROM impAgrident rd
WHERE rd.lidId = 13 and rd.actId = 19 and isnull(verwerkt)
ORDER BY str_to_date(rd.datum,'%d/%m/%Y'), rd.Id
") or die (mysqli_error($db));

while ($vldn = mysqli_fetch_assoc($velden)) {

	$Id = $vldn['Id']; ?>



<tr style = "font-size:14px;">

 <td style = "font-size : 11px;">
 	<?php echo $Id; ?>

 <select id= <?php echo "moeder_$Id"; ?> onchange = <?php echo "toon_dracht(".$Id.")"; ?> style= "width:65; font-size:12px;" name = 'kzlOoi' >
  <option></option>
<?php
	kzl_loop($mdrkey, $wnrOoi, $mdrId_rd, $_POST['knpVervers'], $_POST['kzlOoi'][$Id]); ?> 
</select> 

 </td>
 <td>

 <select style= "width:125; font-size:12px;" id= <?php echo "vader_$Id"; ?> name = 'kzlRam' >
 <option></option>	
<?php	
	kzl_loop($vdrkey, $lvnrRam, $ram_db, $_POST['knpVervers'], $_POST['kzlRam'][$Id]); ?>
 </select><p id= <?php echo "result_vader_$Id"; ?> ></p> 

 </td>

</tr>


<?php }  ?>
</table>
</form> 



</body>
</html>