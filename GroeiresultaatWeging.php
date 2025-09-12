<?php

require_once("autoload.php");


$versie = '29-09-2024'; /* Gekopieerd van GroeiresultaatSchaap.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Groeiresultaat wegingen</title>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

</head>
<body>

<?php

$titel = 'Groeiresultaten per weging';
$file = "GroeiresultaatWeging.php";
include "login.php"; ?>

            <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {

include "kalender.php";

if(isset($_POST['knpZoek_'])) { 
    $kzlActie = $_POST['kzlActie_'];

    $wegingvan = $_POST['txtWegingVan_']; $dmWegingvan = date_format(date_create($wegingvan), 'Y-m-d');
    $wegingtot = $_POST['txtWegingTot_']; $dmWegingtot = date_format(date_create($wegingtot), 'Y-m-d');
}


/* Declaratie keuzelijst Acties */
$zoek_acties = mysqli_query($db,"
SELECT h.actId, a.actie
FROM tblHistorie h
 join tblActie a on (h.actId = a.actId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.kg is not null
GROUP BY h.actId, a.actie
ORDER BY h.actId
") or die (mysqli_error($db));
/* Einde Declaratie keuzelijst Acties */

$zoek_groei = mysqli_query($db,"SELECT groei FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' ") or die (mysqli_error($db));
    while ( $gr = mysqli_fetch_assoc($zoek_groei)) { $groei = $gr['groei']; }
?> 

<form action="GroeiresultaatWeging.php" method="post">
<table border = 0>
<tr>
 <td></td>
 <td></td>
 <td></td>
 <td align="center"><i>Actie</i></td>
 <td></td>
 <td></td>
 <td></td>
 <td colspan="4" align="right"><i>Wegingen vanaf &nbsp</i></td>
 <td colspan="4" align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
 <td> <input type = radio name = 'radGroei_' value = 0 
        <?php if(!isset($_POST['radGroei_']) && $groei == 0 ) { echo "checked"; } 
         else if(isset($_POST['radGroei_']) && $_POST['radGroei_'] == 0 ) { echo "checked"; } ?> title = "Toont totale groei tussen 2 weegmomenten"> Toon totaal groei
 </td>
 <td></td>
</tr>

<tr>
 <td></td>
  <td> </td>
  <td> </td>
  <td> 
    <select name= "kzlActie_" style= "width:130; height: 20px" class="search-select">
 <option></option>
<?php        while($row = mysqli_fetch_array($zoek_acties))
        {
        
            $opties= array($row['actId']=>$row['actie']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlActie_']) && $_POST['kzlActie_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        }
?> </select>
 </td>
 <td></td>

 <td></td>
 <td></td>
 <td colspan="4" align="right"><input id = "datepicker1" type= text name = "txtWegingVan_" size = "8" value = <?php if(isset($wegingvan)) { echo "$wegingvan"; } ?> ></td>
 <td colspan="4" align="left"><input id = "datepicker2" type= text name = "txtWegingTot_" size = "8" value = <?php if(isset($wegingtot)) { echo "$wegingtot"; } ?> >
 </td>
 <td><input type = radio name = 'radGroei_' value = 1
        <?php if(!isset($_POST['radGroei_']) && $groei == 1) { echo "checked"; } 
         else if(isset($_POST['radGroei_']) && $_POST['radGroei_'] == 1 ) { echo "checked"; } ?> > Toon gemiddelde groei per dag
 </td>
 <td></td>
 <td> <input type="submit" name="knpZoek_" value="Zoeken"> </td>
</tr>    

<tr height = 35 ></tr>

<?php
/*unset($where_actie);*/

if(isset($kzlActie) && !empty($kzlActie)) { $where_actie = " and h.actId = ". mysqli_real_escape_string($db,$kzlActie) . " "; }
if(!empty($wegingvan) && !empty($wegingtot)) { $where_weging = " and h.datum >= '". mysqli_real_escape_string($db,$dmWegingvan) . "' and h.datum <= '". mysqli_real_escape_string($db,$dmWegingtot) . "' "; }

$where = '';

if(isset($where_actie)) { $where .= $where_actie; }
if(isset($where_weging))   { $where .= $where_weging; }

$result = "
SELECT date_format(h.datum,'%d-%m-%Y') datum, h.datum date, a.actie, right(mdr.levensnummer, $Karwerk) moeder, s.schaapId, right(s.levensnummer, $Karwerk) werknum, s.geslacht, prnt.datum aanw, h.kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId) 
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best) and h.kg is not null and h.skip = 0 ".$where. "
ORDER BY h.datum desc, h.actId, right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
";

#echo $result;

$result = mysqli_query($db,$result) or die (mysqli_error($db));

if( (!isset($_POST['radGroei_']) && $groei == 0) || (isset($_POST['radGroei_']) && $_POST['radGroei_'] == 0) ) { $kolomkop = 'Totale groei'; $kolomkopxls = 'T'; }
else if(isset($_POST['radGroei_']) && $_POST['radGroei_'] == 1 ) { $kolomkop = 'Gem groei per dag'; $kolomkopxls = 'G';} 
else { $kolomkop = 'Gem groei per dag'; $kolomkopxls = 'G'; } ?>
 
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 150>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 150>Actie<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Moeder<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 100>Generatie<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 600> <?php echo $kolomkop; ?> <hr></th>

<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 600> <a href="exportGroeiWeging.php?pst=<?php echo $lidId; ?>&show=<?php echo $kolomkopxls; ?>&where=<?php echo $where; ?> "> Export-xlsx </a> </th>

    
<th width= 60 ></th>
 </tr>
<?php

//$levnr = $row['levensnummer']; if($levnr_record == $levnr) { $levnr_toon = ''; unset($moeder); } else { $levnr_toon = $levnr; unset($kg1); unset($date1); unset($actie1); }

        while($row = mysqli_fetch_array($result))
        { 
$datum_record = $datum;
$actie_record = $actie;
$moeder_record = $moeder;

    $schaapId = $row['schaapId'];            
    $date = $row['date'];            
    $datum = $row['datum'];        if($datum_record == $datum) { $datum_toon = ''; } else { $datum_toon = $datum; }
    $actie = $row['actie'];        if($actie_record == $actie && $datum_toon == '') { $actie_toon = ''; } else { $actie_toon = $actie; }
    $moeder = $row['moeder'];        if(!isset($moeder)) {$moeder = '--';} if($moeder_record == $moeder && $datum_toon == '') { $moeder_toon = ''; } else { $moeder_toon = $moeder; }
    $werknr = $row['werknum'];
    $geslacht = $row['geslacht']; 
    $aanw = $row['aanw'];
    $kg = $row['kg'];
   
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } 

$date_2 = strtotime($date); // Betreft $datum_toon

// Zoek vorige weging
unset($vorige_weging);

$zoek_vorige_weging = mysqli_query($db,"
SELECT max(hisId) vorige_weging
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.datum < '".mysqli_real_escape_string($db,$date)."' and h.kg is not null
") or die (mysqli_error($db));

while($zvw = mysqli_fetch_array($zoek_vorige_weging))
        { $vorige_weging = $zvw['vorige_weging']; }

if(isset($vorige_weging)) { 


$zoek_actie_vorige_weging = mysqli_query($db,"
SELECT h.actId, actie, h.datum, kg
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$vorige_weging)."'
") or die (mysqli_error($db));

while($zavw = mysqli_fetch_array($zoek_actie_vorige_weging))
        { $vorige_actId = $zavw['actId']; 
          $vorige_actie = $zavw['actie']; if($vorige_actId == 9) { $vorige_actie = 'vorige tussenweging'; }
          $vorige_date = $zavw['datum']; 
          $vorige_kg = $zavw['kg']; }
}

$date_1 = strtotime($vorige_date); //time(); // or your date as well. Betreft vorige weegdatum
$datediff = $date_2 - $date_1;

$dagen = round($datediff / (60 * 60 * 24));


if( (!isset($_POST['radGroei_']) && $groei == 0) || (isset($_POST['radGroei_']) && $_POST['radGroei_'] == 0) ) { $factor = $dagen/$dagen; }
else if( (!isset($_POST['radGroei_']) && $groei == 1) || (isset($_POST['radGroei_']) && $_POST['radGroei_'] == 1) ) { $factor = $dagen; }

if(isset($vorige_weging)) { $berekening = round((($kg - $vorige_kg) / $factor),2).' kg in '.$dagen.' dagen vanaf '.strtolower($vorige_actie); }

// Einde Zoek vorige weging

if(isset($datum_record) && $datum_toon != '') { ?>
<tr>
 <td colspan="18"><hr></td>
</tr>    
<?php } else if(isset($moeder_record) && $moeder_toon != '') { ?>
<tr>
 <td colspan="4"></td>
 <td colspan="13"><hr></td>
</tr>    
<?php } ?>


<tr align = "center">    
       <td width = 0> </td>            
       
       <td width = 150 style = "font-size:15px;"> <?php echo $datum_toon; ?> <br> </td>
       <td width = 1> </td>
       <td width = 150 style = "font-size:15px;"> <?php echo $actie_toon; ?> <br> </td>
       <td width = 1> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $moeder_toon; ?> <br> </td>
       <td width = 1> </td>                 
       <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
       <td width = 1> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
       <td width = 1> </td>    
       <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
       <td width = 1> </td>

       <td width = 80 style = "font-size:15px;"> <?php echo $kg; ?> <br> </td>

       <td width = 1> </td>
       
       <td width = 600 style = "font-size:15px;" align="left"> <?php echo $berekening;


//echo '$date_2 = '. $date_2.' $date_1 = '.$date_1;


    ?> <br> </td>

       
</tr>                
<?php 

        } ?>
</tr>                
</table>
</form>


        </TD>

<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; }
include "zoeken.js.php";
?>
</body>
</html>
