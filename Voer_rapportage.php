<?php 

require_once("autoload.php");

$versie = "03-09-2017"; /* 16-7-2017 gemaakt     3-9-2017 kg voer te wijzigen*/
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2019'; /* Hoeveelheid opnieuw gebouwd i.v.m. andere manier van kg voer vastleggen. Incl. toevoegen van optie Volwassen dieren */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '06-03-2020'; /* Rapport ook zichtbaar gemaakt als voer niet wordt gebruikt */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld bij tblHistorie en sql verder beveiligd */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = center> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */
$versie = '19-02-2025'; /* Gegevens werden niet getoond omdat geneste query's als variabele in mysqli_real_escape_string(db,... stonden. 23-02-2025 Titel gewijzigd van Overzicht voertoediening naar Voer rapportage*/

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>        
</head>
<body>

<?php
$titel = 'Voer rapportage';
$file = "Voer_rapportage.php";
 include "login.php"; ?>

                <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
    if($modtech == 1) { 
 
        if(isset($_POST['knpSave_'])) {
            include "save_voerrapport.php";  
        //header("Location: ".Url::getWebroot()."Voer_rapportage.php"); 
    }
    ?>

<table border = 0 align = "center">

<form action = "Voer_rapportage.php" method = "post">
<tr>
<td width= 100 >
 <select name= "kzlDoel_" style= "font-size : 11px; width:110;" > 
<?php
$opties = array(1 => 'Foklammeren', 2 => 'Vleeslammeren', 3 => 'Volwassen dieren');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlDoel_']) && $_POST['kzlDoel_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select> 

</td>

<td width = 250 style="font-size : 13px; text-align: right" >
Voer
<?php
//kzlVoer
$name = "kzlVoer_"; ?>
<select name= <?php echo"$name";?> width = 60 >
 <option></option>
<?php        
$artikel_gateway = new ArtikelGateway();
$zoek_voer = $artikel_gateway->zoek_voer($lidId);
while($row = mysqli_fetch_array($zoek_voer)) {
    $kzlkey="$row[artId]";
    $kzlvalue="$row[naam]";
    $opties= array($kzlkey=>$kzlvalue);
    foreach ( $opties as $key => $waarde) {
        $keuze = '';
        if(isset($_POST[$name]) && $_POST[$name] == $key) {
            $keuze = ' selected ';
        }
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
    }
}
// EINDE kzlVoer
?>
</select> 
 </td>
<td width = 300 style = "font-size : 13px;">
<?php
If ((isset($_POST['knpToon_']) || isset($_POST['knpSave_'])) ) { 

/*** Opbouw keuzelijst maand of verblijf als er meerdere maanden of verblijven zijn ***/

    if(isset($_POST['radVoer_']) && $_POST['radVoer_']==1 ) {
        $metVoer = 'ja';
    } else {
        $metVoer = 'nee';
    }

$fldVoer = $_POST['kzlVoer_'];

// Controle meerdere maanden om keuzelijst kzlJaarMaand te laten zien bij meer dan 1 maand.
$periode_gateway = new PeriodeGateway();
$rows_jrmnd = $periode_gateway->aantal_jaarmaanden($lidId, $fldVoer, $_POST['kzlDoel_']);
if ($rows_jrmnd >1) {
?>
 Maand
 
<?php //kzlJaarMaand
$kzljrmnd = $periode_gateway->kzlJaarmaand($lidId, $fldVoer);

$name = 'kzlMdjr_'; ?>
<select name="<?php echo $name;?>" style="font-size : 13px" width= 108 >
 <option></option>    
<?php
while($row = mysqli_fetch_array($kzljrmnd)) {
    $maand = $row['maand']; 
    $mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
    $kzlkey="$row[jrmnd]";
    $kzlvalue="$mndname[$maand] $row[jaar]";

include "kzl.php";
        }
?>
</select>
<?php //Einde kzlJaarMaand
}
//Einde Controle meerdere maanden om keuzelijst kzlJaarMaand te laten zien bij meer dan 1 maand.
// Controle meerdere verblijven om keuzelijst kzlHok_ te laten zien bij meer dan 1 verblijf.
$hok_gateway = new HokGateway();
$rows_hok = $hok_gateway->countVerblijven($lidId, $fldVoer, $_POST['kzlDoel_']);

if($rows_hok > 1) {
    echo "&nbsp Verblijf ";
//kzlHok

$kzlHok = $hok_gateway->kzlHokVoer($lidId, $fldVoer);
$name = 'kzlHok_';?>
<select name = <?php echo"$name";?> style = "font-size : 13px" width = 100 >
 <option></option>
<?php
while($row = mysqli_fetch_assoc($kzlHok)) {
$kzlkey = "$row[hokId]";
$kzlvalue="$row[hoknr]";
include "kzl.php";
}
} // Einde Controle meerdere verblijven om keuzelijst kzlHok_ te laten zien bij meer dan 1 verblijf.
 
} /*** EINDE Opbouw keuzelijst maand of verblijf als er meerdere maanden of verblijven zijn ***/

?>
</td>
 <td> <input type = "submit" name ="knpToon_" value = "Toon"> </td>

 <td width = 160 style = "font-size:12px;" >
Alleen met voer 
  <input type = radio name = 'radVoer_' value = 1 
<?php if(!isset($_POST['radVoer_']) || (isset($_POST['radVoer_']) && $_POST['radVoer_'] == 1 )) { echo "checked"; } ?> > Ja
  <input type = radio name = 'radVoer_' value = 0
<?php if(isset($_POST['radVoer_']) && $_POST['radVoer_'] == 0 ) { echo "checked"; } ?> > Nee

</td>
 
 <td> </td>
<td width="1" align="right">
 <input type = submit name = 'knpSave_' value="opslaan" style = "font-size:11px;" > </td>
 </tr>    
 </table>

<table border = 0 >
<tr>
<td> </td>
<td>
<?php
If (isset($_POST['knpToon_']) || isset($_POST['knpSave_'])) {
// TODO: resjrmnd en reshok migreren naar gateway.
    if ($rows_jrmnd <= 1 || empty($_POST['kzlMdjr_'])) {
        $resJrmnd = "( date_format(p.dmafsluit,'%Y%m') is not null )";
    }
    else if ($rows_jrmnd > 1 && !empty($_POST['kzlMdjr_'])) {
        $resJrmnd = "( date_format(p.dmafsluit,'%Y%m') = {$_POST['kzlMdjr_']})";
    }
    if ($rows_hok <= 1 || empty($_POST['kzlHok_'])) {
        $resHok = "( ho.hokId is not null )";
    }
    else if ($rows_hok > 1 && !empty($_POST['kzlHok_'])) {
        $resHok = "( ho.hokId = {$_POST['kzlHok_']} )";
    }

//$maandjaren toont de maand(en) uit tblPeriode binnen het gekozen voer en eventueel gekozen hok. T.b.v. de loop maand jaar
$maandjaren = $periode_gateway->maandjaren_hok_voer($lidId, $fldVoer, $_POST['kzlDoel_'], $resJrmnd, $resHok);
while ($rij = mysqli_fetch_assoc($maandjaren)) {
    // START LOOP maandnaam jaartal
        $mndnr = $rij['maand'];
        $jaar = $rij['jaar'];
        $jrmnd = $rij['jrmnd'];
        $mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 
?>
<tr height = 30><td></td></tr>
<tr style = "font-size:18px;" ><td colspan = 3><b><?php echo "$mndnaam[$mndnr] &nbsp $jaar"; ?></b></td></tr>
<tr style = "font-size:12px;">

<tr style = "font-size:15px;" valign = top>
<th style = "text-align:center;"valign="bottom";width = 70>Verblijf<hr></th>
<th style = "text-align:center;"valign="bottom";width= 90>start<hr></th>
<th style = "text-align:center;"valign="bottom";width= 80>einddatum<hr></th>
<th style = "text-align:center;"valign="bottom";width= 80><?php if($metVoer == 'ja') { ?> voerdatum <?php } else { ?> afsluit- / voerdatum <?php } ?> <hr></th>
<th style = "text-align:center;"valign="bottom";         >Aantal schapen<hr></th>
<th style = "text-align:center;"valign="bottom";width= 100     >Gem dagen<br>per schaap <hr></th>
<th style = "text-align:center;"valign="bottom";>Kg voer<hr></th>
<th style = "text-align:left;"valign="bottom";><hr></th>
<td align = "center" valign = "bottom"> <input type = "submit" name = <?php echo "knpToon_"; ?> value = "Toon" style = "font-size:11px;" > <hr> </td>
<td colspan = 2 align="center"; valign="bottom";> Verwijder<br>&nbsp&nbspvoer&nbsp&nbsp periode<hr></td>
</tr>

<?php // $zoek_startdatum zoekt eerste van de maand en het jaar dat een gebruiker is begonnen met het programma
$lid_gateway = new LidGateway();
$dmstart = $lid_gateway->zoek_startdatum($lidId);

$begin_eind_periode = $periode_gateway->begin_eind_periode($lidId, $_POST['kzlDoel_'], $fldVoer, $jrmnd);
  while ($mld = mysqli_fetch_assoc($begin_eind_periode))
        {  // START LOOP $begin_eind_periode
            $hokId = $mld['hokId'];
            $hoknr = $mld['hoknr'];
            $jaarmnd = $mld['jrmnd'];

            $periId = $mld['periId']; 
            $dmbegin = $mld['dmbegin']; 
            $dmeind = $mld['dmeind']; 

if($_POST['kzlDoel_'] == 1) { $filterDoel = ' and (his_in.datum < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 2) { $filterDoel = ' and (his_in.datum >= spn.datum and (his_in.datum < prn.datum or isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 3) { $filterDoel = ' and (his_in.datum >= spn.datum)'; }

$periode_totalen = $periode_gateway->periode_totalen($lidId, $hokId, $fldVoer, $_POST['kzlDoel_'], $filterDoel, $resHok, $dmstart, $dmbegin, $dmeind, $jrmnd);

if (mysqli_num_rows($periode_totalen) == 0) { 
    $periode_totalen = $periode_gateway->periode_totalen_met_voer_zonder_schapen($lidId, $hokId, $fldVoer, $_POST['kzlDoel_'], $resHok, $dmstart, $jrmnd);
}

  while ($mld = mysqli_fetch_assoc($periode_totalen)) {
        $Id = $mld['periId'];
        $hokId = $mld['hokId'];  
        $hoknr = $mld['hoknr'];
        $dmbegin = $mld['dmbegin'];  // Begindatum periode (= dmafsluit uit tblPeriode)
        $begindm = $mld['begindm'];      // Begindatum periode (= dmafsluit uit tblPeriode)
        $dmschaap1 = $mld['dmschaap1'];
        $schaap1dm = $mld['schaap1dm']; if($dmschaap1 > $dmbegin) { $begindm = $schaap1dm; }
        $dmeind = $mld['dmeind'];    // Einddatum periode
        $einddm = $mld['einddm'];        // Einddatum periode
        $voerdm = $mld['einddm'];        // Einddatum periode
        $dmschaapend = $mld['dmschaapend'];
        $schaapenddm = $mld['schaapenddm']; if(!empty($dmschaapend) && $dmschaapend < $dmeind) { $einddm = $schaapenddm; }
        $kilo = $mld['nutat']; /*if(!isset($kilo)) { $kilo = 70; }*/
        $schpn = $mld['schpn']; if(empty($schpn)) { $schpn = 0; }
        $dgn = $mld['dagen'];
        $gemdgn = $mld['gemdgn'];
        $voedId = $mld['voedId'];
         
 ?>
<tr ><td colspan = 25></td></tr>
<?php
if($_POST['kzlDoel_'] == 1) { $filterDoel = ' and (his_in.datum < spn.datum or (isnull(spn.schaapId) and isnull(prn.schaapId)) )'; }
if($_POST['kzlDoel_'] == 2) { $filterDoel = ' and (his_in.datum >= spn.datum or (isnull(spn.schaapId) and prn.schaapId is not null))'; }
?>
<tr align = "center" style = "font-size:15px;">    
 <td><?php echo $hoknr; ?></td>
 <td width = 90 > <?php echo $begindm; ?>  </td>
 <td width = 80 > <?php echo $einddm; ?>  </td>
 <td width = 80 > <input type="text" name = <?php echo "txtDatum_$Id"; ?> style="font-size: 11px;" size =8 value = <?php echo $voerdm; ?> > <?php unset($voerdm); ?> 
 </td>
 <td width = 80 > <?php echo $schpn; ?> </td>       
 <td width= 100 > <?php echo $gemdgn; ?> </td>
 <td width= 50 > 
<?php if($voedId>0) { ?>
  <input type="text" name = <?php echo "txtKilo_$Id"; ?> style="font-size: 11px; text-align: right; " size =3 value = <?php echo $kilo; ?> > <?php } ?>
 </td>
 <td width = 280 > </td>
 <td width = 200 style = "font-size:12px;" >
<?php if($schpn > 0) { ?>
  <input type = radio name = <?php echo "radSchaap_$Id"; ?> value = 0 
<?php if(!isset($_POST["radSchaap_$Id"]) || (isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"] == 0 )) { echo "checked"; } ?> > Excl.
  <input type = radio name = <?php echo "radSchaap_$Id"; ?> value = 1
<?php if(isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"] == 1 ) { echo "checked"; } ?> > Incl. schapen
<?php } ?>
</td>
 <td width= 50 >
<?php if($voedId>0) { ?>
  <input type = checkbox name = <?php echo "chbDelVoer_$Id"; ?> value= 1 style = "font-size:11px;" > <?php } ?> 
 </td>
 <td width= 50 > <input type = checkbox name = <?php echo "chbDelPeri_$Id"; ?> value= 1 style = "font-size:11px;" > </td>
       
</tr>

<?php
// CODE M.B.T. DETAIL SCHAAPGEGEVENS
if(isset($_POST["radSchaap_$Id"]) && $_POST["radSchaap_$Id"]==1 ) { 
    if($gemdgn == 0) {
        $dagkg = 0;
    } else {
        $dagkg = $kilo/$dgn;
    }
        unset($kilo); 
    $bezet_gateway = new BezetGateway();
    $schaap_gegevens = $bezet_gateway->schaap_gegevens($lidId, $hokId, $dmbegin, $dmeind, $dagkg, $filterDoel);
?>
<tr height = 10><td>    </td></tr>
<?php
    while($sch = mysqli_fetch_array($schaap_gegevens)) { 
        $levnr = $sch['levensnummer']; 
        $dmin = $sch['dmin']; 
        $indm = $sch['indm'];     if($dmin < $dmbegin) { $indm = $begindm; }
        $dmuit = $sch['dmuit']; 
        $uitdm = $sch['uitdm']; if($dmuit > $dmeind) { $uitdm = $einddm; }
        $dgn = $sch['dgn']; 
        $kg = $sch['kg'];
?> 
<tr align = "center" style = "font-size:15px;"> 
<td></td>
<td width = 80 >  <?php echo $indm; ?> </td>
<td width = 80 >  <?php echo $uitdm; ?> </td>
<td width = 80 >   </td>
<td width = 80 align = right>  <?php echo $levnr; ?> </td>

<td width = 80 >  <?php echo $dgn; ?> </td>
<td> <?php echo $kg; ?> </td>
<td>  </td>
<td> </td>

</tr>
<?php 
    }
    unset($aant);
 unset($dagen);
  unset($begindm);
 unset($einddm);

    // EINDE CODE M.B.T. DETAIL SCHAAPGEGEVENS
 } ?>
<tr><td colspan = 25><hr></td></tr>

<?php
  }
unset($periode_totalen);
// Einde $periode_totalen
unset($begindm);
unset($einddm);
  } // Einde $begin_eind_periode
}  // EINDE LOOP maandnaam jaartal
} //  Einde knop toon ?>            
</table>
</form>
        </TD>
<?php 
    } else {
?>
    <img src="Voer_rapportage_php.jpg"  width="970" height="550"/>
<?php
    }
    include "menuRapport.php";
}
?>
</body>
</html>
