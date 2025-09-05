<?php /* 6-11-2014 Melden RVO toegevoegd 
26-2-2015 url aangepast 
14-11-2015 naamwijziging van Inkoop naar Voorraadbeheer en Medicijn registratie naar Medicijn toediening
18-11-2015 Hok gewijzigd naar verblijf 
6-12-2015 :  $versie toegveoged 
19-12-2015 : query $moduleFinancieel verplaatst naar login.php 
20-12-2020 : Alerts toegevoegd 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.php 
25-12-2021 : Dracht.php hernoemd naar Dekkingen.php 11-1-2022 kleur link variabel gemaakt 
22-10-2023 : Menu optie Beheer kleur rood als er nog een nieuwe readerversie moet worden gedownload
23-10-2024 : Invoer nieuwe schapen gewijzigd naar Aanvoer schaap */


include "javascriptsAfhandeling.php";
include "url.php"; 

if($modtech == 0) { $color = 'grey'; } else { $color = 'blue'; }
?>

<td width = '150' height = '100' valign='top'>
Menu : <br>
<hr style ='color : #A6C6EB'>
<a href= '<?php echo $url;?>Home.php' style = 'color : blue'>
Home</a> <br>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>InvSchaap.php' style = 'color : blue'>
Aanvoer schaap</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>InlezenReader.php' style = 'color : blue'>
Inlezen reader</a> <br>
<hr style ='color : #E2E2E2'>
<?php if($modmeld == 0) { ?> <a href='<?php echo $url;?>Melden.php' style = 'color : grey'> <?php }
else {
// Kijken of er nog meldingen openstaan
$req_open = mysqli_query($db,"
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0 and isnull(r.dmmeld) and m.skip <> 1 ") or die (mysqli_error($db));
        $row = mysqli_fetch_assoc($req_open);
            $num_rows = $row['aant'];
        if($num_rows == 0){  ?>
<a href='<?php echo $url;?>Melden.php' style = 'color : blue'> <?php } else { ?>
<a href='<?php echo $url;?>Melden.php' style = 'color : red'> <?php }  
} ?>
RVO</a> <br>
<hr style ='color : #E2E2E2'>

    <?php if($modtech == 0 && $modmeld == 1) { ?>
<a href='<?php echo $url;?>Afvoerstal.php' style = 'color : blue'>
Afvoerlijst</a>
    <?php } else { ?>
<a href='<?php echo $url;?>Bezet.php' style = 'color : blue'>
Verblijven in gebruik</a>
    <?php } ?>
    
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Zoeken.php' style = 'color : blue'>
Schaap opzoeken</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Med_registratie.php' style = "color : <?php echo $color; ?> ;" > 
Medicijn toediening</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Dekkingen.php' style = 'color : blue'>
Dekkingen / Dracht</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Alerts.php' style = "color : <?php echo $color; ?> ;">
Raederalerts</a>
<hr style ='color : #E2E2E2'>

<a href='<?php echo $url;?>Rapport.php' style = 'color : blue'>
Rapporten</a>
<hr style ='color : #E2E2E2'>
<?php  if(isset($actuele_versie) || $reader != 'Agrident')   { ?> <a href='<?php echo $url; ?>Beheer.php' style = 'color : blue'> <?php }
    else { ?> <a href='<?php echo $url; ?>Beheer.php' style = 'color : red'> <?php  } ?>
Beheer</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Inkoop.php' style = "color : <?php echo $color; ?> ;">
Voorraadbeheer</a>
<hr style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Finance.php' style = "color : <?php echo $color; ?> ;">
Financieel</a>
<hr style ='color : #E2E2E2'>


<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?> </i> <br> <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>

