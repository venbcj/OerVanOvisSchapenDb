
<link rel="stylesheet" type="text/css" href="test2_style_header.css">




<?php include"url.php";
	  //$subtitel = "Optimalisering En Rendementverbetering van het Schaap";

$host = $_SERVER['HTTP_HOST'];
if($host == 'localhost:8080' )  	{ $tagid = 'balkOntw'; } 
if($host == 'test.oervanovis.nl') 	{ $tagid = 'balkProd'; }
if($host == 'demo.oervanovis.nl')  	{ $tagid = 'balkDemo'; }
if($host == 'ovis.oervanovis.nl') 	{ $tagid = 'balkProd'; }  ?>


	<div id = "rechts_uitlijnen" class = 'header_afbeelding'><section> <?php echo $titel; ?> </section><img src='OER_van_OVIS.jpg' /></div>

<ul class="header_groen" id = <?php echo $tagid; ?> >
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li class="dropdown"><span>Registratie</span>
		<div class="dropdown-content">
			<a href='<?php echo $url;?>InvSchaap.php' style = 'color : black'>Aanvoer schaap</a>
			<a href='<?php echo $url;?>Med_registratie.php' style = 'color : black' >Medicijn toediening</a>
			<a href='<?php echo $url;?>Dekkingen.php' style = 'color : black'>Dekkingen / Dracht</a>
		</div>
	</li>

	<li class="dropdown"><span>Reader</span>
	  <div class="dropdown-content" style="left:0;">
	  	<a href='<?php echo $url;?>InlezenReader.php' style = 'color : black'>Inlezen reader</a>
	  	<a href='<?php echo $url; ?>Alerts.php' style = 'color : black'>Raederalerts</a>
	  </div>
	</li>

	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
	<li id= "link"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>


	<li class="dropdown"><span>RVO</span>
	  <div class="dropdown-content" style="left:0;">
	  	<a href='<?php echo $url;?>Melden.php' style = 'color : red'>Melden RVO</a>
	  	<a href='<?php echo $url; ?>Meldingen.php' style = 'color : black'> Meldingen</a>
	  </div>
	</li>


	<li class="dropdown"><span>Raadplegen</span>
		<div class="dropdown-content">
<?php if($modtech == 0 && $modmeld == 1) { ?>
			<a href='<?php echo $url;?>Afvoerstal.php' style = 'color : black'>Afvoerlijst</a>
<?php } else { ?>
			<a href='<?php echo $url;?>Bezet.php' style = 'color : black'>Verblijven in gebruik</a>
<?php } ?>
			<a href='<?php echo $url;?>Zoeken.php' style = 'color : black'>Schaap opzoeken</a>
		<a href='<?php echo $url;?>Stallijst.php' style = 'color : black'>Stallijst</a>
	  	<a href='<?php echo $url;?>ZoekAfldm.php' style = 'color : black'>Afleverlijst</a>
		</div>
	</li>


	<li class="dropdown"><span>Rapporten</span>
	  <div class="dropdown-content">
	  	<a href='<?php echo $url;?>Mndoverz_fok.php' style = 'color : black'>Maandoverz. fokkerij</a>
	  	<a href='<?php echo $url;?>Mndoverz_vlees.php' style = 'color : black'>Maandoverz. vleeslam.</a>
	  	<a href='<?php echo $url;?>Med_rapportage.php' style = 'color : black'>Medicijn rapportage</a>
	  	<a href='<?php echo $url;?>Voer_rapportage.php' style = 'color : black'>Voer rapportage</a>
	  	<a href='<?php echo $url;?>Rapport1.php' style = 'color : black'>Ooi rapporten</a>
	  	<a href='<?php echo $url;?>MaandTotalen.php' style = 'color : black'>Maandtotalen</a>
	  </div>
	</li>


	
	

	<li id = "rechts_uitlijnen"><a href='<?php echo $url;?>index.php' style = 'color : black'>Uitloggen</a></li>
	<li><a></a></li>
	<li><a></a></li>



</ul>

<table id ="table1">
<tr height = 90><td></td>
<TR>
	
