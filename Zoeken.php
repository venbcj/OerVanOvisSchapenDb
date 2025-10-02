<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt 
11-8-2014 : veld type gewijzigd in fase 
20-2-2015 : login toegevoegd 
19-11-2015 geboorte datum kan ook aankoopdatum zijn 
23-11-2015 : Berekening breedte kzlWerknr verplaatst naar login.php */
$versie = '2-12-2016'; /* Dubbele records verwijderd als schaap opnieuw wordt aangevoerd */
$versie = '5-12-2016'; /* In historie alleen meldingen die niet zijn verwijderd.  and m.skip = 0 toegvoegd dus */
$versie = '14-1-2017'; /* In query geschiedenis levnr vervangen door schaapId. Bij Overplaatsing = aanwas is schaap t.t.v. overplaatsing lam en geen moeder zoals tot voor 14-1-2017 */
$versie = '15-1-2017'; /* In query geschiedenis hisId toegevoegd bij eerste en laatste worp */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = '30-1-2017'; /* : Halsnummer toegevoegd  */
$versie = '16-2-2017'; /* hokken van volwassen dieren tonen (incl opnieuw lam ivm niet meer via tblPeriode)  LET OP : bij lam moet h1.actId = 2 worden uitgesloten en bij mdrs en vdrs h2.actId = 3 uitsluiten !!! */
$versie = '2-4-2017'; /* veld commentaar toegevoegd */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-12-2017';  /* In uit verblijf halen van moeder- en vaderdieren in Historie opgenomen */
$versie = '20-07-2018';  /* Index kzlRam_ gewijzigd van werknr_ram naar schaapId */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-12-2018'; /* Eerste en laatste worp mag alleen eigen lammeren zijn => sl.lidId = ...lidId toegevoegd */
$versie = '15-2-2020'; /* tabelnaam gewijzigd van HIS naar his en van TOEL naar toel */
$versie = '23-5-2020'; /* unset gem groei spenen en afvoer en stamboeknummer. Geadopteerd aan historie toegevoegd */
$versie = '27-9-2020'; /* Handmatig omnummeren toegevoegd */
$versie = '27-2-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '11-4-2021'; /* Adoptie losgekoppeld van verblijf */
$versie = '11-4-2021'; /* Union SELECT uit.hist hisId, concat(ho.hoknr,' verlaten ') toel   aangepast. ht.actId = 7 toegevoegd en niet alleen volwassen dieren kunnen nu de status 'verlaten' hebben. */
$versie = '16-4-2023'; /* Bij omnummeren oud nummmer getoond incl. de melding van omnunummeren. Na omnummeren werden eerdere meldingen aan RVO niet meer getoond. Dit was nl. gekoppeld aan het oude levensnummer. Dit is hersteld door het oude levensnummer te koppelen. Zie veld 'wanneer wel omgenummerd' */
$versie = '14-5-2023'; /* Voorouders toegevoegd */
$versie = '23-6-2023'; /* schaapId werd te laat gezet. Na de link Wijzigen. schaapId wordt nu eerder gezet. */
$versie = '01-01-2024'; /* h.skip = 0 aangevuld bij tblHistorie */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '30-11-2024'; /* In keuzelijst moeder- en vaderdieren  uitgeschaarde dieren wel tonen. zoek_afvoerstatus_mdr aangevuld met h.actId != 10 */
$versie = '14-12-2024'; /* 4 links t.b.v. jquery en ajax verplaatst naar header.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '16-08-2025'; /* ubn van gebruiker toegevoegd. Per deze versie kan een gebruiker meerdere ubn's hebben */

 Session::start();
  ?>
<!DOCTYPE html>
<html>
<head>
<title>Raadplegen</title>
</head>
<body>

<?php
$titel = 'Schaap zoeken';
$file = "Zoeken.php";
include "login.php"; ?>

        <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {

if(isset($_POST['knpSave_'])) { include "save_commentzoeken.php"; }
//include vw_Bezetting
//include vw_Hoklijsten

If (empty($_POST['kzlLevnr_']) )     {    $levnr = '';    } else {    $levnr = $_POST['kzlLevnr_'];        }
If (empty($_POST['kzlWerknr_']))    {    $werknr = '';    } else {    $werknr = $_POST['kzlWerknr_'];    }
If (!empty($_POST['kzlHalsnr_'])) {    $halsnr = $_POST['kzlHalsnr_'];    };
// tbv het posten en terug posten met dezelfde zoekcriterium
If (empty($_POST['kzlLevnr_'])) {$pstlevnr = NULL;} else {$pstlevnr = $_POST['kzlLevnr_'];}
If (empty($_POST['kzlWerknr_'])) {$pstwerknr = NULL;} else {$pstwerknr = $_POST['kzlWerknr_'];}

?>
<form action="Zoeken.php" method="post"> 
<table border = 0> <!-- Zoekgedeelte -->

<tr>
 <td> </td>    
 <td> <i><sub> Levensnummer </sub></i> </td>
 <td> </td>    
 <td> <i><sub> Werknr </sub></i> </td>
 <td> <i><sub> Halsnr </sub></i> </td>
 <td> </td>
 <td> <i><sub> Moederdier </sub></i> </td>
 <td> </td>
 <td> <i><sub> Vaderdier </sub></i> </td>
</tr>
<tr>
 <td> </td>

<!-- kzlLevensnummer -->
<?php
$schaap_gateway = new SchaapGateway();
$kzlLam = $schaap_gateway->eigen_schapen($lidId);
?> 
 <td>
 <select name= "kzlLevnr_" style= "width:130; height: 20px" class="search-select">
 <option></option>
 <option>Geen</option>
<?php        while($row = mysqli_fetch_array($kzlLam))
        {
        
            $opties= array($row['schaapId']=>$row['levensnummer']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlLevnr_']) && $_POST['kzlLevnr_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        }
?> </select>
</td>

<td> </td>
<!-- kzlWerknr -->
<?php  
$kzlWerknr = $schaap_gateway->werknummers($lidId, $Karwerk);
?>

 <td>            
 <select name="kzlWerknr_" style= "width:<?php echo $w_werknr; ?>;" >
 <option></option>
 <option>Geen</option>
<?php        while($row = mysqli_fetch_array($kzlWerknr))
        {
        
            $opties= array($row['schaapId']=>$row['werknr']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlWerknr_']) && $_POST['kzlWerknr_'] == $key)
        {
            $keuze = ' selected ';
        }
        
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>
</td>

<!-- kzlHalsnr -->
<?php
            $zoek_halsnr = $schaap_gateway->halsnummers($lidId);
?>

 <td>            
 <select name="kzlHalsnr_" style= "width: 80;" >
 <option></option>
<?php        while($row = mysqli_fetch_array($zoek_halsnr))
        {
        
            $opties= array($row['schaapId']=>$row['halsnr']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlHalsnr_']) && $_POST['kzlHalsnr_'] == $key)
        {
            $keuze = ' selected ';
        }
        
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>
 </td>
<!-- Einde kzlHalsnr -->

 <td> </td>
<!-- kzlMoeder -->
<?php
        $kzlOoi = $schaap_gateway->ooien($lidId, $Karwerk);
?>

 <td>
 <select name= "kzlOoi_" style= "width:<?php echo $w_werknr;?> " >
 <option></option>
<?php        while($row = mysqli_fetch_array($kzlOoi))
        {
        
            $opties= array($row['schaapId']=>$row['werknr_ooi']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlOoi_']) && $_POST['kzlOoi_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
  </select> 
  </td>

<!-- kzlVader -->
 <?php
        $kzlRam = $schaap_gateway->rammen($lidId, $Karwerk);
?>

 <td>
 <select name="kzlRam_" style= "width:<?php echo $w_werknr;?>;" >
 <option></option>    
<?php        while($row = mysqli_fetch_array($kzlRam))
        {
        
            $opties= array($row['schaapId']=>$row['werknr_ram']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlRam_']) && $_POST['kzlRam_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select> </td>

<?php
        $toon_historie = mysqli_query($db,"SELECT histo FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' ") or die (mysqli_error($db));
    while ( $hi = mysqli_fetch_assoc($toon_historie)) { $histo = $hi['histo'];} ?>
    
 <td width = 50></td>
 <td> Historie tonen : <input type = radio name = 'radHis_' value = 0 
        <?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 0) { echo "checked"; } 
         else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 0 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Nee
     <input type = radio name = "radHis_" value = 1
        <?php if(!isset($_POST['knpZoek_']) && !isset($_POST['knpSave_']) && $histo == 1) { echo "checked"; }
         else if(isset($_POST['radHis_']) && $_POST['radHis_'] == 1 ) { echo "checked"; } ?> title = "Standaard tonen van historie te wijzigen in systeemgegevens"> Ja 
        
        <?php if(isset($_POST['knpZoek_']) || isset($_POST['knpSave_']) ) { $historie = $_POST['radHis_'];} else { $historie = 0; }  ?>    
 </td>
 <td width="15"></td>
 <td> Voorouders tonen : <input type = radio name = 'radOud_' value = 0 
        <?php if(isset($_POST['radOud_']) && $_POST['radOud_'] == 0) { echo "checked"; } ?> > Nee
     <input type = radio name = "radOud_" value = 1
        <?php  if(!isset($_POST['radOud_']) || (isset($_POST['radOud_']) && $_POST['radOud_'] == 1 ) ) { echo "checked"; } ?> > Ja 
        
        <?php if(isset($_POST['knpZoek_']) || isset($_POST['knpSave_']) ) { $voorOud = $_POST['radOud_'];} else { $voorOud = 0; }  ?>    
 </td> 
</tr>

<tr>
<td colspan = 9 align = "center">
<input type = "submit" name= "knpZoek_" value = "zoeken">
</td>
</tr>


</table> <!-- Einde Zoekgedeelte -->
<table border = 0> <!-- Gegevens van het schaap -->
<?php
// Om alle resultaten uit tblSchapen te voorkomen moet minimaal 1 keuze zijn gemakt
             if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_']))
                 && (!empty($levnr) || !empty($werknr) || !empty($halsnr) || !empty($_POST['kzlOoi_']) || !empty($_POST['kzlRam_'])) ) {

                 $where = $schaap_gateway->getZoekWhere($_POST);

// Zoeken naar eerste datum en een eventuele aankoopdatum
$temp = $schaap_gateway->zoekAankoop($lidId, $where);
extract($temp); // brengt $gebdm en $dmkoop in scope -- al wordt dmkoop niet gebruikt :#
// Einde Controleren op aankoop door zoeken in tblBezetting

// schapen met status onbekend
//where isnull(vb.bezetId) and s.fase = 'lam' and isnull(afleverdm) and isnull(uitvaldm)

$schaapId = $schaap_gateway->zoekSchaap($where);

$result = $schaap_gateway->zoekresultaat($lidId, $where, $Karwerk);
?>
            
                
                
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th width = 1 height = 30></th>
 <th width = 90 height = 30></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Transponder<br>bekend<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 100>Halsnr<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50> <?php if(isset($gebdm)) { echo 'Geboortedatum'; } else { echo 'Aanvoerdatum'; } ?><hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Generatie<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 200>Werknr ooi<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 200>Werknr ram<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Status<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Gem Groei speen<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Gem Groei aflev<hr></th>

 <th style = "text-align:center;"valign="bottom";width= 60>Stamboeknr<hr></th>
 <td width = 60 style = "font-size:15px;" align="center" > <a href=' <?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Wijzigen</a> </td>

</tr>

<?php
while($row = mysqli_fetch_assoc($result))
{
    $transponder = $row['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
    //$schaapId = $row['schaapId'];
    $levnr = $row['levensnummer'];
    $werknr = $row['werknr'];
    $fokkernr = $row['fokkernr']; if(isset($fokkernr)) { $stamb = $fokkernr.' - '.$werknr; }
    $halsnr = $row['halsnr'];
    $gebdm = $row['gebdm'];
    $ras = $row['ras'];
    $sekse = $row['geslacht'];
    if(isset($row['dmaanw'])) { if($sekse == 'ooi' ) { $fase = 'moeder'; } else { $fase = 'vader'; } } else { $fase = 'lam';}
    $mdr = $row['werknr_ooi'];
    $vdr = $row['werknr_ram'];
    $status = $row['status'];
    $opstal = $row['af'];
    $dmspn = $row['dmspn'];
    $spnkg = $row['spnkg'];
    $dmafl = $row['dmafl'];
    $aflkg = $row['aflkg'];
    $dmgeb = $row['dmgeb'];
    $gebkg = $row['gebkg'];
    $dmaanv = $row['dmaanv'];
    $aanvdm = $row['aanvdm'];
    $aankkg = $row['aankkg'];
    if(isset($dmgeb)) { $dmstart = $dmgeb; } else { $dmstart = $dmaanv;}
    if(isset($gebkg)) { $startkg = $gebkg; } else { $startkg = $aankkg;}

    $dagen_spn = strtotime($dmspn)-strtotime($dmstart); $dgn_s = floor($dagen_spn/3600/24);
    $dagen_afl = strtotime($dmafl)-strtotime($dmstart); $dgn_a = floor($dagen_afl/3600/24);

    if($dgn_s >0 && $startkg > 0) { $gemgr_s = round((($spnkg-$startkg)/($dgn_s)*1000),2) ; } 


    if($dgn_a >0 && $startkg > 0) { $gemgr_a = round((($aflkg-$startkg)/($dgn_a)*1000),2) ; } ?>
                
<tr align = "center">    
 <td width = 0> </td>
<td width = 1> </td>
 <td width = 90> </td>       
 <td width = 150 style = "font-size:15px;"> <?php echo $transp; ?> <br> </td>

 <td width = 150 style = "font-size:15px;"> <?php echo $halsnr; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
   
 <td width = 100 style = "font-size:15px;"> <?php if(isset($gebdm)) { echo $gebdm; } else { echo $aanvdm; } ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $sekse; ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $mdr; ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php echo $vdr; ?> </td>

<?php if($status == 'aanwezig' && $fase == 'moeder') { ?>       
 <td><a href=' <?php echo $url; ?>Ooikaart.php?pstId=<?php echo $schaapId; ?>' style = "color : blue">
       <?php echo $status; ?></a></td>
<?php } else { ?>       
 <td width = 160 style = "font-size:15px;"> <?php echo $status; ?> </td>
<?php } ?>       

 <td width = 100 style = "font-size:15px;"> <?php if(isset($gemgr_s) ) { echo $gemgr_s; unset($gemgr_s); } ?> </td>

 <td width = 100 style = "font-size:15px;"> <?php if(isset($gemgr_a) && $status == 'afgeleverd') { echo $gemgr_a; unset($gemgr_a); } ?> </td>

 <td width = 100 style = "font-size:12px;"> <?php if(isset($stamb) ) { echo $stamb; unset($stamb); } ?> </td>

       <?php 
if ($status == 'aanwezig' || $status == 'uitgeschaard')
{    ?>
 <td width="450">
    <a href=' <?php echo $url; ?>OmnSchaap.php?pstschaap=<?php echo $schaapId; ?>' style = "color : blue">Omnummeren</a>
   </td> <?php
   
   
} ?>
       

<?php    } // Einde while($row = mysqli_fetch_assoc($result)) ?>                 
       
<?php  
if (!isset($schaapId))
{ 
$fout = "Het zoek criterium heeft geen resultaten opgeleverd. Pas het zoekcriterum eventueel aan.";
} 
} ?>
       
</tr>
</table>  <!-- Einde Gegevens van het schaap -->

<?php if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $historie == 1 && (!empty($_POST['kzlLevnr_']) || !empty($_POST['kzlWerknr_']) || !empty($_POST['kzlHalsnr_'])) ) { ?>    
<table border = 0>  <!-- Historie van het schaap -->

<!-- Om een lege tabel te verbergen moet minimaal 1 keuze zijn gemaakt -->

<tr height = 50>
</tr>

<tr><td colspan = 7 ><hr></td></tr>
<tr><td colspan = 2 >Historie van het schaap : </td> </tr>
<tr style = "font-size : 13px;">
 <th>Ubn<hr></th>
 <th>Datum<hr></th>
 <th>Actie<hr></th>
 <th>Generatie<hr></th>
<!--<th>Id<hr></th>-->
 <th>Gewicht<hr></th>
 <th align = "left"> &nbsp &nbsp &nbsp Toelichting<hr></th>
 <th align = "left"> &nbsp &nbsp &nbsp Commentaar<hr></th>
 <th align = "left"> <input type="submit" name="knpSave_" style="font-size: 11px;" value="Opslaan"> </th>
 <th width = 1></th>
</tr>

<?php
if(isset($schaapId)) { // Zoekcriterium moet bestaan
$geschiedenis = $schaap_gateway->zoekGeschiedenis($lidId, $schaapId, $Karwerk);


while ($his = mysqli_fetch_assoc($geschiedenis)) {
    $hisId = $his['hisId'];
    $ubn = $his['ubn'];
    $datum = $his['datum'];
    $actId = $his['actId'];
    $actie = $his['actie'];
    $actie_if = $his['actie_if'];
    $sekse = $his['geslacht'];
    $date = $his['date'];
    $dmaanw = $his['dmaanw'];
    if( !isset($dmaanw) || $date < $dmaanw /*geen lam meer */ ||
    ($date == $dmaanw && ($actId == 5 || $actId == 6) ) ) /*zeldedatum en nog wel lam */ { $fase = 'lam';} 
    else { if($sekse == 'ooi') { $fase = 'moeder';} else if($sekse == 'ram') { $fase = 'vader'; } }
    $kg = $his['kg'];
    $toel = $his['toel']; if ($actie_if == 'worp' && $toel== 1) { $toel = $toel." lam"; } else if ($actie_if == 'worp' && $toel > 1) { $toel = $toel." lammeren"; }
    $Id = $his['hiscom']; /* hisId t.b.v. commentaar*/
    $comm = $his['comment'];
    
    $lev = $his['levensnummer'];
    ?>

<tr style = "font-size : 14px;">
 <td> <?php echo $ubn; ?> </td>
 <td> <?php echo $datum; ?> </td>
 <td> <?php echo $actie; ?> </td>
 <td align = "center"> <?php echo $fase; ?> </td>
 <td align = 'right'> <?php if(isset($kg)) { echo $kg." kg"; } ?> </td>
 <td> <?php echo "&nbsp &nbsp &nbsp". $toel."&nbsp &nbsp &nbsp"; ?> </td>
 <td> <?php if($Id > 0) { ?>
    <input type="text" name=<?php echo "txtComm_$Id"; ?> style="font-size: 11px"; size="50" value= <?php echo " \"$comm\" "; ?> > <?php } ?> 
 </td>
</tr>
<?php }


} // Einde Zoekcriterium moet bestaan
?>
</table> <!-- Einde Historie van het schaap -->
<?php } // Einde if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $histo .........

// VOOROUDERS
 if ((isset($_POST['knpZoek_']) || isset($_POST['knpSave_'])) && $voorOud == 1 && (!empty($_POST['kzlLevnr_']) || !empty($_POST['kzlWerknr_']) || !empty($_POST['kzlHalsnr_'])) ) { ?>
<table border = 0>
<tr><td height="50"></td> </tr>

<tr><td colspan = 11 ><hr></td></tr>
<tr><td colspan = 5 >Voorouders van het schaap : </td> </tr>



<?php //   '".mysqli_real_escape_string($db,$schaapId)."'


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
    WHERE s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
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

if(mysqli_num_rows($ouders) == 0)  { ?>
 <td style = "font-size:13px;"> Van dit dier zijn geen voorouders bekend. </td>


<?php }

else { ?>

<tr style = "font-size:13px;" >
<th> Werknr<hr></th>
<th> Geslacht <hr></th>
<th> Ras <hr></th>
<th width="10"></th>
<th> Moeder <hr></th>
<th> Ras moeder <hr></th>
<th width="10"></th>
<th> Vader <hr></th>
<th> Ras vader <hr></th>
<th width="10"></th>
<th> Worpgrootte <hr></th>
</tr>

    <?php
while($row = mysqli_fetch_assoc($ouders)) {

    $schaap = $row['levnr'];
    $geslacht = $row['geslacht'];
    $ras = $row['ras'];
    $moeder = $row['levnr_ma'];
    $ras_ma = $row['ras_ma'];
    $vader = $row['levnr_pa']; 
    $ras_pa = $row['ras_pa']; 
    $worp = $row['grootte']; 
    ?>

<tr style = "font-size:13px;">
 <td align="center"> <?php echo $schaap; ?> </td>
 <td> <?php echo $geslacht; ?> </td>
 <td> <?php echo $ras; ?> </td>
 <td></td>
 <td align="center"> <?php echo $moeder; ?> </td>
 <td> <?php echo $ras_ma; ?> </td>
 <td></td>
 <td align="center"> <?php echo $vader; ?> </td>
 <td> <?php echo $ras_pa; ?> </td>
 <td></td>
 <td align="center"> <?php echo $worp; ?> </td>
</tr>

<?php    }


 } // Einde if(isset($ouders)) ?>

</table>

<?php
} // Einde   if ((isset($_POST['knpZoek_']) || isset($_POST['knpSa .......
 // EINDE VOOROUDERS ?>

 
</form>    

        </TD>
<?php        
include "menu1.php"; }
include "zoeken.js.php";
?>

</body>
</html>
