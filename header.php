<!-- 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
vervangen door 
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>. Aanleiding is dat selectie veld 'Alles selecteren' selectall en selectall_del in alle inlees bestanden van de reader (ins*.php) maar 1 x werkte. Er kon dus maar 1 x alles aan en uit worden gezet. 
-->
<link rel="stylesheet" type="text/css" href="style.css">

<!-- BackToTop button javascript 
	bron : https://www.wpromotions.eu/nl/hoe-een-scroll-to-top-knop-toevoegen-aan-website-in-webnode/	-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Deze links komen uit Zoeken.php per 14-12-2024 -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<!-- Einde Deze links komen uit Zoeken.php per 14-12-2024 -->

<script type="text/javascript">
$(document).ready(function(){
    $(window).scroll(function(){
        if($(this).scrollTop() > 100){
            $('#scroll').fadeIn();
        }else{
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});
</script>

 
<a href="javascript;" id="scroll" title="Scroll to Top" style="display: none;">Top<span></span></a> 

<!-- Einde BackToTop button javascript	-->

<?php /*

echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '$_SESSION["U1"] = '.$_SESSION["U1"].'<br>';
echo '$_SESSION["W1"] = '.$_SESSION["W1"].'<br>';
echo '$_SESSION["I1"] = '.$_SESSION["I1"].'<br>';

echo '$modtech = '.$modtech.'<br>';
echo '$modmeld = '.$modmeld.'<br>';
echo '$modbeheer = '.$modbeheer.'<br>';
echo '$actuele_versie = '.$actuele_versie.'<br>';*/

$host = $_SERVER['HTTP_HOST'];
if($host == 'localhost:8080' )  	{ $tagid = 'balkOntw'; } 
if($host == 'test.oervanovis.nl') 	{ $tagid = 'balkTest'; }
if($host == 'demo.oervanovis.nl')  	{ $tagid = 'balkDemo'; }
if($host == 'ovis.oervanovis.nl') 	{ $tagid = 'balkProd'; }


if($modtech == 1) { $colorTech = 'black'; } else { $colorTech = 'grey'; }
if($modfin == 1)  { $colorFin = 'black'; }  else { $colorFin = 'grey'; }
if($modmeld == 0) { $color_RVO = 'grey'; }
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

if($num_rows == 0){  $color_RVO = 'black'; } else { $color_RVO = 'red'; }  
} ?>

	<div id = "rechts_uitlijnen" class = 'header_breed'><section style = "text-align : center"; > <?php echo $titel; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </section><img src='OER_van_OVIS.jpg' /></div>

<ul class="header_smal" id = <?php echo $tagid; ?> >
	<li class="dropdown"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li class="dropdown"><span>Registratie</span>
		<div class="dropdown-content">
			<a href='<?php echo $url;?>InvSchaap.php' style = 'color : black'>Aanvoer schaap</a></br></br>
			<a href='<?php echo $url;?>Med_registratie.php' style = "color : <?php echo $colorTech; ?> ;" >Medicijn toediening</a></br></br>
			<a href='<?php echo $url;?>Dekkingen.php' style = "color : <?php echo $colorTech; ?> ;" >Dekkingen / Dracht</a></br></br>
		</div>
	</li>

	<li class="dropdown"><span>Reader</span>
	  <div class="dropdown-content-smal">
	  	<a href='<?php echo $url;?>InlezenReader.php' style = 'color : black'>Inlezen reader</a></br></br>
	  	<a href='<?php echo $url; ?>Alerts.php' style = 'color : black'>Raederalerts</a></br></br>
	  </div>
	</li>

	<li class="dropdown"><span style = "color : <?php echo $color_RVO; ?> ;">RVO</span>
	  <div class="dropdown-content-smal">
	  	<a href='<?php echo $url;?>Melden.php' style = "color : <?php echo $color_RVO; ?> ;">Melden RVO</a></br></br>
	  	<a href='<?php echo $url; ?>Meldingen.php' style = 'color : black'> Meldingen</a></br></br>
	  </div>
	</li>

	<li class="dropdown"><span>RAADPLEGEN</span>
		<div class="dropdown-content">
<?php if($modtech == 0 && $modmeld == 1) { ?>
		<a href='<?php echo $url;?>Afvoerstal.php' style = 'color : black'>Afvoerlijst</a></br></br>
<?php } else { ?>
		<a href='<?php echo $url;?>Bezet.php' style = 'color : black'>Verblijven in gebruik</a></br></br>
<?php } ?>
		<a href='<?php echo $url;?>Zoeken.php' style = 'color : black'>Schaap opzoeken</a><br/><br/>
		<a href='<?php echo $url;?>Stallijst.php' style = 'color : black'>Stallijst</a></br></br>
	  	<a href='<?php echo $url;?>ZoekAfldm.php' style = 'color : black'>Afleverlijst</a></br></br>
		</div>
	</li>

	<li class="dropdown"><span>Rapporten</span>
	  <div class="dropdown-content-breed">
	  	<a href='<?php echo $url;?>Mndoverz_fok.php' style = "color : <?php echo $colorTech; ?> ;">Maandoverz. fokkerij</a></br></br>
	  	<a href='<?php echo $url;?>Mndoverz_vlees.php' style = "color : <?php echo $colorTech; ?> ;">Maandoverz. vleeslam.</a></br></br>
	  	<a href='<?php echo $url;?>Med_rapportage.php' style = "color : <?php echo $colorTech; ?> ;">Medicijn rapportage</a></br></br>
	  	<a href='<?php echo $url;?>Voer_rapportage.php' style = "color : <?php echo $colorTech; ?> ;">Voer rapportage</a></br></br>
	  	<a href='<?php echo $url;?>MaandTotalen.php' style = "color : <?php echo $colorTech; ?> ;">Maandtotalen</a></br></br>
	  	<a href='<?php echo $url;?>GroeiresultaatSchaap.php' style = "color : <?php echo $colorTech; ?> ;">Groeiresultaten per schaap</a></br></br>
	  	<a href='<?php echo $url;?>GroeiresultaatWeging.php' style = "color : <?php echo $colorTech; ?> ;">Groeiresultaten per weging</a></br></br>
	  	<a href='<?php echo $url;?>ResultHok.php' style = "color : <?php echo $colorTech; ?> ;">Periode resultaten</a></br></br>


	  	
	  	<ul class="nested-dropdown">
	  	<li class="dropdown2"><span>Ooi rapporten</span></br></br>
	  		<div class="dropdown-content2">
  			 <a href='<?php echo $url;?>Ooikaart.php' style = "color : <?php echo $colorTech; ?> ;">Ooikaart detail</a></br></br>
  			 <a href='<?php echo $url;?>OoikaartAll.php' style = "color : <?php echo $colorTech; ?> ;">Ooikaart moeders</a></br></br>
  			 <a href='<?php echo $url;?>Meerlingen5.php' style = "color : <?php echo $colorTech; ?> ;">Meerling in periode</a></br></br>
  			 <a href='<?php echo $url;?>Meerlingen.php' style = "color : <?php echo $colorTech; ?> ;">Meerling per geslacht</a></br></br>
  			 <a href='<?php echo $url;?>Meerlingen2.php' style = "color : <?php echo $colorTech; ?> ;">Meerlingen per jaar</a></br></br>
  			 <a href='<?php echo $url;?>Meerlingen3.php' style = "color : <?php echo $colorTech; ?> ;">Meerling oplopend</a></br></br>
  			 <a href='<?php echo $url;?>Meerlingen4.php' style = "color : <?php echo $colorTech; ?> ;">Meerlingen aanwezig</a></br></br>
	  		</div>
	  	</li>
	  	</ul>

	  </div>
	</li>
	
	<li class="dropdown"><span>Voorraadbeheer</span>
	  <div class="dropdown-content">
	  	<a href='<?php echo $url;?>Medicijnen.php' style = "color : <?php echo $colorTech; ?> ;">Medicijnenbestand</a></br></br>
	  	<a href='<?php echo $url;?>Voer.php' style = "color : <?php echo $colorTech; ?> ;">Voerbestand</a></br></br>
	  	<a href='<?php echo $url;?>Inkopen.php' style = "color : <?php echo $colorTech; ?> ;">Inkopen</a></br></br>
	  	<a href='<?php echo $url;?>Voorraad.php' style = "color : <?php echo $colorTech; ?> ;">Voorraad</a></br></br>
	  </div>
	</li>
	
	<li class="dropdown"><span>Financieel</span>
	  <div class="dropdown-content">
	  	<a href='<?php echo $url;?>Kostenopgaaf.php' style = "color : <?php echo $colorFin; ?> ;">Inboeken</a></br></br>
	  	<a href='<?php echo $url;?>Deklijst.php' style = "color : <?php echo $colorFin; ?> ;">Deklijst</a></br></br>
	  	<a href='<?php echo $url;?>Liquiditeit.php' style = "color : <?php echo $colorFin; ?> ;">Liquiditeit</a></br></br>
	  	<a href='<?php echo $url;?>Saldoberekening.php' style = "color : <?php echo $colorFin; ?> ;">Saldoberekening</a></br></br>
	  	<a href='<?php echo $url;?>Rubrieken.php' style = "color : <?php echo $colorFin; ?> ;">Rubrieken</a></br></br>
	  	<a href='<?php echo $url;?>Componenten.php' style = "color : <?php echo $colorFin; ?> ;">Componenten</a></br></br>
	  	<a href='<?php echo $url;?>Kostenoverzicht.php' style = "color : <?php echo $colorFin; ?> ;">Betaalde posten</a></br></br>
	  </div>
	</li>

<?php if(isset($actuele_versie))  { $color_Rversie = 'black'; } else  { $color_Rversie = 'red'; } ?>

<li class="dropdown"><span style = 'color : black'>Beheer</span>
	  <div class="dropdown-content">
		<a href='<?php echo $url;?>Hok.php' style = "color : <?php echo $colorTech; ?> ;">
Verblijven</a><br/><br/>
	  	<a href='<?php echo $url; ?>Ras.php' style = 'color : black'>Rassen</a><br/><br/>
		<a href='<?php echo $url; ?>Uitval.php' style = 'color : black'>Redenen en momenten</a></br></br>
		<a href='<?php echo $url; ?>Combireden.php' style = 'color : black'>Combi redenen</a></br></br>
		<a href='<?php echo $url; ?>Vader.php' style = 'color : black'>Dekrammen</a><br/><br/>
		<a href='<?php echo $url;?>Eenheden.php' style = "color : <?php echo $colorTech; ?> ;">
Eenheden</a></br></br>
		<a href='<?php echo $url; ?>Relaties.php' style = 'color : black'>Relaties</a></br></br>

		<a href='<?php echo $url; ?>Readerversies.php' style = "color : <?php echo $color_Rversie; ?> ;">Readerversies</a></br></br>
		<a href='<?php echo $url; ?>Readerbestanden.php' style = 'color : black'>Readerbestanden</a></br></br>
<?php if($modbeheer == 1 ) { ?>
		<a href='<?php echo $url; ?>Gebruikers.php' style = 'color : black'>
Gebruikers</a></br></br> <?php } ?>
		<a href='<?php echo $url; ?>Systeem.php' style = 'color : black'>Instellingen</a></br></br>
	  </div>
	</li>

	<li id = "rechts_uitlijnen"><a href='<?php echo $url;?>index.php' style = 'color : black'>Uitloggen</a></li>




</ul>

<!-- <script src="test2_script_header.js"></script> -->

<table id ="table1" align="center">
<tbody>
<tr height = 90> </tr>
<TR>
	
