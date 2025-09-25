<?php

require_once("autoload.php");

/* 20-2-2015 : login toegevoegd
14-11-2015 Eerste en tweede inenting verwijderd
18-11-2015 Hok gewijzigd naar verblijf
23-11-2015 Spenen afleveren mogelijk gemaakt en link 'periode afsluiten verplaatst naar achteren'
19-12-2015 : link 'hok overpl' gewijzigd naar overpl */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel Aantal nu in hok gewijzigd van count(distinct st.schaapId)-count(distinct uit.schaapId) naar count(b.bezId)-count(uit.bezId) zodat terugplaatsen ook zichtbaar is. */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "5-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = "12-2-2017"; /* Bij historie lammeren H1.ACTID != 2 toegevegd. Bij aankoop moederdieren bestaat act 2 en act 3 waardoor dit dier in het hok heeft gezeten van aankoop t/m aanwas als dier 'zonder' aanwas datum. Wordt ooit een lam aangekocht maak dan een nieuwe actie hiervoor aan in tblActie !!!!!!!!!!!!!!!! */
$versie = "29-12-2017"; /* Aantal aanwezige volwassen dieren toegevoegd */
$versie = "13-05-2018"; /* Session::set("DT1", NULL); Session::set("BST", NULL);  toegevoegd */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '18-05-2019'; /* Afleveren, spenen en Overplaatsen mogelijk gemaakt via Hoklijsten.php */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '28-6-2020'; /* datum in verblijf van volwassen dieren toegevoegd zodat link 'periode sluiten' zichtbaar wordt bij verblijven met enkel volwassen dieren */
$versie = '8-2-2021'; /* zoek_nu_in_verblijf_prnt herschreven i.v.m. dubbele records. Sql beveiligd met quotes */
$versie = '4-6-2021'; /* Verblijf ook zichtbaar als enkel volwassen dieren in het verblijf hebben gezeten */
$versie = '9-7-2021'; /* Schapen uit verblijf herzien. Join gewijzigd van h.hisId = uit.hisv naar b.bezId = uit.bezId */
$versie = '4-8-2021'; /* Schapen die 0 dagen in verblijf zitten ook meegeteld. Zie bijv (h.datum = spn.datum && h.hisId >= spn.hisId) */
$versie = '23-12-2023'; /* In query zoek_nu_in_verblijf_prnt skip = 0 toegevoegd. Vandaag is bij Folkert een herstel actie uitgevoerd n.a.v. toevoegen speendatum op 17-12 jl. Alle 116 overplaatsingen zijn verwijderd (skip = 1) 27-12-2023 and skip = 0 toegevoegd bij tblHistorie */
$versie = '05-01-2024'; /* Schapen die in het verblijf spenen de status aanwas kregen werden niet getoond. Dit is aangepast
7-1-2024 : Aanwas werd onterecht aan een verblijf gekoppeld waardoor volwassendieren dubbel werden geteld in de kolom Volwassen aanwezig.
Dit is voor de toekomst aangepast in save_aanwas.php. Met distinct in zoek_nu_in_verblijf_prnt is dit ook met bestaande registraties hersteld
14-01-2024 Doelgroep verlaten telden ook volwassen dieren die niet in het verblijf hadden gezeten. Dit is aangepast door bij zoek_verlaten_spn_excl_overpl_en_uitval or (isnull(uit.bezId) and prnt.schaapId is not null)) uit te breiden naar or (isnull(uit.bezId) and prnt.schaapId is not null and h.datum < spn.datum)) */
$versie = '19-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "10-03-2024"; /* De aantallen in kolom aanwezigen blauw gemaakt */
$versie = "11-03-2024"; /* Bij geneste query uit
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>
<?php
$titel = 'Verblijven in gebruik';
$file = "Bezet.php";
include "login.php";
?>
        <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $bezet_gateway = new BezetGateway($db);
    $aantal_zonder_speendatum = $bezet_gateway->zoek_verblijven_ingebruik_zonder_speendm($lidId);
    $aantal_met_speendatum = $bezet_gateway->zoek_verblijven_ingebruik_met_speendm($lidId);
    $aantal_zonder_verblijf = $bezet_gateway->zoek_schapen_zonder_verblijf($lidId);
    $zoek_verblijven_in_gebruik = $bezet_gateway->zoek_verblijven_in_gebruik($lidId);
    $periode_gateway = new PeriodeGateway($db);
?>
<form action="Bezet.php" method="post">
<table BORDER=0 width=960 align="center">
<tr>
  <td colspan=5> 
    <i style="font-size : 13px;" > Verblijflijsten per doelgroep : &nbsp  
<?php
if ($aantal_zonder_speendatum > 0) { ?>
<a href="<?php echo $url; ?>Hoklijst.php?pstgroep=1" style="color : blue"> Geboren </a>
<?php
}
if ($aantal_met_speendatum) { ?>
&nbsp &nbsp
<a href="<?php echo $url; ?>Hoklijst.php?pstgroep=2" style="color : blue"> Gespeend </a>
<?php    } ?>
    </i>
  </td>
  <td colspan=8 align="right">
<?php if ($aantal_zonder_verblijf > 0) { ?>
    <a href="<?php echo $url; ?>Loslopers.php?" style="color : blue"> Schapen zonder verblijf </a>     
<?php } ?>

</td>
<!--<td colspan=2> <a href= '< ?php echo $url;?>Bezet_pdf.php? ?>' style='color : blue'> print verblijven </a></td> -->
</tr>
<tr style="font-size:12px;">
<th colspan=4 ></th>
<th colspan=2 align =center valign=bottom style="text-align:center;" >Totaal</th>
<th colspan=6 ></th>
</tr>
<tr style="font-size:12px;">
 <th width=0 height=30></th>
 <th style="text-align:center;" valign="bottom" width= 150>Verblijf<hr></th>
 <th style="text-align:center;" valign="bottom" width= 110>Eerste in<hr></th>
 <th style="text-align:center;" valign="bottom" width= 110>Meest recente eruit<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>voor spenen<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>na spenen<hr></th>
 <th style="text-align:center;" valign="bottom" width= 80>Lam aanwezig<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Doelgroep verlaten<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Overge- plaatst<hr></th>
 <th style="text-align:center;" valign="bottom" width= 50>Uitval<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Moeders van lammeren<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Volwassen aanwezig<hr></th>
 <th style="text-align:center;" valign="bottom" width= 60>Volwassen<br> totaal geteld<hr></th>
 <th style="text-align:center;" valign="bottom"><hr></th>
 <th width=60></th>
</tr>
<?php
// TODO: (BCB) subqueries in closure verpakken en die doorsturen naar de view, zodat die een ->each_record() kan doen
// Alternatief: eerst alle rijen ophalen, en die set naar de view sturen
while ($row = mysqli_fetch_assoc($zoek_verblijven_in_gebruik)) {
    // BCB: da's 12 queries per rij. Dat kon wel eens langzaam zijn ja.
    // TODO: (BV) grote datasets opstellen tbv test, en dan kijken of indexen het sneller maken
    // Loop alle verblijven in gebruik
    $dmstopgeb = $periode_gateway->zoek_laatste_afsluitdm_geb($row['hokId']);
    if (!isset($dmstopgeb)) {
        $dmstopgeb = '1973-09-11';
    }
    $dmstopspn = $periode_gateway->zoek_laatste_afsluitdm_spn($row['hokId']);
    if (!isset($dmstopspn)) {
        $dmstopspn = '1973-09-11';
    }
    $aanwezig1 = $bezet_gateway->zoek_nu_in_verblijf_geb($row['hokId']);
    $aanwezig2 = $bezet_gateway->zoek_nu_in_verblijf_spn($row['hokId']);
    $aanwezig = $aanwezig1 + $aanwezig2;
    $aanwezig3 = $bezet_gateway->zoek_nu_in_verblijf_prnt($row['hokId']);
    $aanwezig_incl = $aanwezig + $aanwezig3; // wordt niet gebruikt
    $uit_geb = $bezet_gateway->zoek_verlaten_geb_excl_overpl_en_uitval($row['hokId'], $dmstopgeb);
    $uit_spn = $bezet_gateway->zoek_verlaten_spn_excl_overpl_en_uitval($row['hokId'], $dmstopspn);
    $uit = $uit_geb + $uit_spn;
    $overpl_geb = $bezet_gateway->zoek_overplaatsing_geb($row['hokId'], $dmstopgeb);
    $overpl_spn = $bezet_gateway->zoek_overplaatsing_spn($row['hokId'], $dmstopspn);
    $overpl = $overpl_geb + $overpl_spn;
    $uitval1 = $bezet_gateway->zoek_overleden_geb($row['hokId'], $dmstopgeb);
    $uitval2 = $bezet_gateway->zoek_overleden_spn($row['hokId'], $dmstopspn);
    $uitval = $uitval1 + $uitval2;
    $mdrs = $bezet_gateway->zoek_moeders_van_lam($row['hokId']);
    $dmvan = '';
    $van = '';
    $tot = '';
    if (isset($row['eerste_in'])) {
        $datum = date_create($row['eerste_in']);
        $van = date_format($datum, 'd-m-Y');
        $dmvan = date_format($datum, 'Y-m-d');
        $today = date('Y-m-d');
    }
    if (isset($row['laatste_uit'])) {
        $datum = date_create($row['laatste_uit']);
        $tot = date_format($datum, 'd-m-Y');
    }
?>
<tr align="center">    
    <td width=0> </td>            
    <td width=150 style="font-size:15px;">     
        <a href=" <?php echo $url; ?>Hoklijsten.php?pst=<?php echo $row['hokId']; ?>" style="color : blue"> <?php echo $row['hoknr']; ?>     </a> <br/>  
    </td>       
    <td width=110 style="font-size:13px;"> <?php echo $van; ?> </td>       
    <td width=110 style="font-size:13px;"> <?php echo $tot; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php if (!empty($row['maxgeb'])) { echo $row['maxgeb']; } ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php if (!empty($row['maxspn'])) { echo $row['maxspn']; } ?> </td>
    <td width=60 style="font-size:15px; color:blue; "> <?php echo $aanwezig; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $uit; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $overpl; ?> </td>
    <td width=50 style="font-size:15px; color:grey; "> <?php echo $uitval; ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php echo $mdrs; ?> </td>
    <td width=60 style="font-size:15px; color:blue; "> <?php if ($aanwezig3 >0) { echo $aanwezig3; } ?> </td>
    <td width=60 style="font-size:15px; color:grey; "> <?php if (!empty($row['maxprnt'])) { echo $row['maxprnt']; } ?> </td>
    <td width=200 style="font-size:13px;">
    <?php if ($dmvan && $dmvan < $today) { ?>
      <a href="<?php echo $url; ?>HokAfsluiten.php?pstId=<?php echo $row['hokId']; ?>" style="color : blue">   Periode sluiten  </a>    
    <?php } ?>
    </td>
</tr>
<?php
} // Einde Loop alle verblijven in gebruik ?>
</tr>            
</table>
</form>
</TD>
<?php
include "menu1.php";
}
?>
</tr>
</table>
</body>
</html>
