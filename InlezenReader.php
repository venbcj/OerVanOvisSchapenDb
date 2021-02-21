<?php 
$versie = '10-4-2014'; /*vw_Reader_sp wordt gebruikt in InsSpenen*/
$versie = '13-4-2014'; /*vw_Reader_ovpl wordt gebruikt in InsOverplaatsen */
$versie = '20-2-2015'; /*login toegevoegd*/ 
$versie = '18-11-2015'; /*gewijzigd inlezen aanwas naar inlezen aanvoer en inlezen locatie naar inlezen verblijf*/
$versie = '16-9-2016'; /*overschrijven van reader.txt gewijzigd in aanvullen*/
$versie = '22-6-2018'; /*Velden in impReader aangepast*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '2-2-2020'; /* De root naar alle bestanden op de FTP server variabel gemaakt */
$versie = '15-3-2020'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-6-2020'; /* Overleggen gewijzigd in adoptie */
$versie = '30-9-2020'; /* Halsnummers toegevoegd */
$versie = '14-11-2020'; /* Medicatie aangepast i.v.m. mogelijk vanuit reader Agrident */

session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php 
$titel = 'Inlezen reader';
$subtitel = '';
Include "header.php"; ?>
<TD width = 960 height = 400 valign = "top">

<?php
$file = "InlezenReader.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

$_SESSION["RPP"] = 30; $RPP = $_SESSION["RPP"];
$_SESSION["PA"] = 1; $pag = $_SESSION["PA"];

Include "responscheck.php";
	
$result = mysqli_query($db,"
SELECT lidId, root_reader
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db)); 

	while ($row = mysqli_fetch_assoc($result))
		{ $userId = $row['lidId']; // Nummer van de map die per gebruiker verschillend is en daardoor uniek maakt
		  $lokatie_reader = $row['root_reader'];
		   }

$dir = dirname(__FILE__); // Locatie bestanden op FTP server
  
$input_file = "reader.txt";
$end_dir_reader = $dir ."/". "user_" . $userId."/"; //Unieke mapnaam per klant. Bijv. user_1
$end_file_reader = "reader_".$userId.".txt"; //Unieke bestandsnaam per klant. Bijv. reader_1.txt
#$end_dir_reader = "G:/Databases/SchapenDb/usb_webserver/root/Schapendb/";
$file_exists = file_exists($end_dir_reader.$input_file); 

include "uploadReader.php"; ?>

       
        <div id="info">
            <!-- <p>PHP versie: &gt;= 4.1.0</p> -->
        </div>
                
       
            <p>
                
                
                
            </p>
        
        
        <?php
        // Weergeven van meldingen uit het phpscript.
        if(isset($errors))
        {
            echo '<ul>';
            foreach($errors as $error);
            {
                echo '<li>'.$error.'</li>';
            }
            echo '</ul>';
        }
        elseif(isset($content))
        {
            foreach($content as $line)
            {
                echo $line;
            }
        }
        ?>
 <form action="#" method="post" id="upload" enctype="multipart/form-data">
<table border = 0>
<tr><td><h3>Uploaden reader</h3></td></tr>
<tr><td><label class="field" for="bestand">Bestand:</label>
		<input type="file" name="bestand" id="bestand" />	</td>
		<td><input type="submit" name = "knpUpload" value="Uploaden" >  </td></tr>
<?php if(isset($lokatie_reader)) { ?>
<tr><td colspan = 2 style = "color : grey ";>Mijn reader lokatie </td></tr>
<tr><td colspan = 2 style = "color : grey ";><i><?php echo $lokatie_reader ?></i></td></tr>
<?php } else { ?> <tr height = 50 ><td> </td></tr> <?php } ?>
</table>
</form>

<?php 
if( $reader == 'Agrident') { Include("inlezenAgrident.php"); } // $reader is gedeclareerd in login.php
else if( $reader == 'Biocontrol') { Include("inlezenBiocontrol.php"); }

?>

<table border = 0>
<?php $leeg = "<a href=' ". $url . "InlezenReader.php' style = 'color : blue'>"; ?>

<tr><td> <?php if (!empty($aantdra)){ ?> <a href='<?php echo $url;?>InsDracht.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
inlezen dracht</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantdra)){	echo "&nbsp $aantdra dracht in te lezen.";	}?></td></tr>

<tr><td> <?php if (!empty($aantgeb)){ ?> <a href='<?php echo $url;?>InsGeboortes.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
inlezen geboortes</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantgeb)){	echo "&nbsp $aantgeb geboorte(s) in te lezen.";	}?></td></tr>

<?php if($reader == 'Agrident') { ?>
<tr><td> <?php if (!empty($aantLbar)){ ?> <a href='<?php echo $url;?>InsLambar.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
inlezen lambar</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantLbar)){ echo "&nbsp $aantLbar lambar in te lezen."; }?></td></tr>
<?php } ?>
<tr><td><?php if (!empty($aantspn)){ ?><a href='<?php echo $url;?>InsSpenen.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
inlezen gespeenden</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantspn)){	echo "&nbsp $aantspn gespeenden in te lezen.";	}?></td></tr>

<tr><td><?php if (!empty($aantafl)){ ?><a href='<?php echo $url;?>InsAfvoer.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
inlezen afvoer</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantafl)){	echo "&nbsp $aantafl afgeleverden in te lezen.";	}?></td></tr>

<tr><td> <?php if (!empty($aantuitv)){ ?><a href='<?php echo $url;?>InsUitval.php' style = 'color : blue'> <?php } else {echo "$leeg"; } ?>
inlezen uitval</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantuitv)){	echo "&nbsp $aantuitv uitval in te lezen.";	}?></td></tr>

<tr><td> <?php if (!empty($aantaanw)){ ?> <a href='<?php echo $url;?>InsAanvoer.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
inlezen aanvoer</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantaanw)){	echo "&nbsp $aantaanw aanwas in te lezen.";	}?></td></tr>

<tr><td> <?php if (!empty($aantovpl)){ ?> <a href='<?php echo $url;?>InsOverplaats.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
inlezen overplaatsen</a> </td><td style = "font-size : 12px;">
	<?php if (!empty($aantovpl) && empty($speen_ovpl)){	echo "&nbsp $aantovpl overplaatsingen in te lezen.";	}
	else if (!empty($aantovpl) && $speen_ovpl == 1)	{	echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moet worden gespeend. *";	}
	else if (!empty($aantovpl) && $speen_ovpl > 1)	{	echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moeten worden gespeend. *";	}
	?></td></tr>

<tr><td> <?php if (!empty($aantadop)){ ?> <a href='<?php echo $url;?>InsAdoptie.php' style = 'color : blue'>  <?php } else {echo "$leeg"; } ?>
inlezen adoptie</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantadop)){  echo "&nbsp $aantadop adoptie in te lezen."; }?></td></tr>

<tr><td> <?php if (!empty($aantpil)){ ?> <a  href='<?php echo $url;?>InsMedicijn.php' style = 'color : blue' >     <?php } else {echo "$leeg"; } ?>
inlezen medicatie </a>  </td> <td style = "font-size : 12px;"><?php if (!empty($aantpil)){	echo "&nbsp $aantpil medicatie in te lezen.";	}?></td></tr>

<tr><td><?php if (!empty($aantwg)){ ?><a href='<?php echo $url;?>InsWegen.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
inlezen wegingen</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantwg)){   echo "&nbsp $aantwg wegingen in te lezen."; }?></td></tr>

<tr><td><?php if (!empty($aantomn)){ ?><a href='<?php echo $url;?>InsOmnummeren.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
inlezen omnummeren</a> </td><td style = "font-size : 12px;"><?php if (!empty($aantomn)){   echo "&nbsp $aantomn omnummeren in te lezen."; }?></td></tr>

<tr><td><?php if (!empty($aanthals)){ ?><a href='<?php echo $url;?>InsHalsnummers.php' style = 'color : blue'><?php } else { echo "$leeg"; } ?>
inlezen halsnummers</a> </td><td style = "font-size : 12px;"><?php if (!empty($aanthals)){   echo "&nbsp $aanthals halsnummers in te lezen."; }?></td></tr>

</table>
<br><br><br>
<table>
<tr><td style = "font-size : 13px ;"> <?php if (!empty($aantovpl) && $speen_ovpl > 0) { ?> * Mogelijk moeten schapen worden herverdeeld na het spenen.<br> Deze herverdeling (= functie locatie in reader) gebeurt gelijktijdig met het inlezen van gespeende lammeren.<?php } ?>	</td>
</tr>
</table>

	</TD>
<?php
Include "menu1.php"; } ?>
</tr>
</table>
</center>

</body>
</html>
<!-- Aantal in te lezen medicatie niet zichtbaar maken. -->