<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content= "width=device-width, initial-scale=1.0">
<title>Fixed Header</title>

<style type="text/css">

.header_breed section {
    position: fixed;
    top: 0%;
    padding: 12px;
    left: .5%;
    width: 100%;
    height: 37px;
    font-size: 30px;
    background-color: grey;
}

.header_smal {
    position: fixed;
    top: 47px;
    padding: 0;
    left: .5%; right: 0;
    width: 99.5%;
    height: 25px;
    font-size: 14px;
    z-index: 1;
}

ul {
    list-style-type: none;
}

.header_smal li {
  float: left;
}

#rechts_uitlijnen {
    display: block;
    padding: 1px;
    float: right;
}

#table1 {
    width: 95%;
    border: 0px solid #A6C6EB;
    left: 20px;
    border-collapse: collapse; 
}

.dropdown {
  position: relative;
  padding: 5px 15px;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: yellow;
  min-width: 150px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 10px 10px;
  z-index: 1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown-content a:hover {
    background-color: #b5cc7a;
    padding: 5px 2px;
}

.dropdown2 {
  display: relative;
}

.dropdown-content2 {
  display: none;
  position: absolute;
  background-color: yellow;
  min-width: 150px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  padding: 10px 10px;
  z-index: 1;
}

.dropdown2:hover .dropdown-content2 {
  display: block;
}

.dropdown-content2 a:hover {
    background-color: grey;
    padding: 5px 2px;
}

table {
  border-collapse: collapse;
}

th, td {
  padding: 0.25rem;
}
tr.red th {
  background: red;
  color: white;
}
tr.green th {
  background: green;
  color: white;
}
tr.purple th {
  background: purple;
  color: white;
}
tr.StickyHeader_green th {
  background: green;
  position: sticky;
  top: 85px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

tr.StickyHeader_blue th {
  background: blue;
  position: sticky;
  top: 85px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}


</style>
</head>

<body>
<?php
$url = 'https://test.......nl/'; ?>

      <div id = "rechts_uitlijnen" class = 'header_breed'><section> </section></div>

<ul class="header_smal" style="background-color : yellow;" >
  <li class="dropdown"><a href= '<?php echo $url;?>Home.php' style = 'color : black'>Home</a></li>
  <li class="dropdown"><a href='<?php echo $url;?>Invoeren.php' style = 'color : black'>Registratie</a></li>

  <li class="dropdown"><span>Reader</span>
    <div class="dropdown-content">
      <a href='<?php echo $url;?>InlezenReader.php' style = 'color : black'>Inlezen reader</a></br></br>
      <a href='<?php echo $url; ?>Alerts.php' style = 'color : black'>Raederalerts</a></br></br>
    </div>
  </li>

  <li class="dropdown"><span style = 'color : black'>RVO</span>
    <div class="dropdown-content">
      <a href='<?php echo $url;?>Melden.php' style = 'color : black'>Melden RVO</a></br></br>
      <a href='<?php echo $url; ?>Meldingen.php' style = 'color : black'> Meldingen</a></br></br>
    </div>
  </li>

  <li class="dropdown"><span>RAADPLEGEN</span>

  </li>

  <li class="dropdown"><span>Rapporten</span>
    <div class="dropdown-content">
      <a href='<?php echo $url;?>Mndoverz_fok.php' style = 'color : black'>Maandoverz. fokkerij</a></br></br>
      <a href='<?php echo $url;?>Mndoverz_vlees.php' style = 'color : black'>Maandoverz. vleeslam.</a></br></br>
      <a href='<?php echo $url;?>Med_rapportage.php' style = 'color : black'>Medicijn rapportage</a></br></br>
      <a href='<?php echo $url;?>Voer_rapportage.php' style = 'color : black'>Voer rapportage</a></br></br>
      <a href='<?php echo $url;?>MaandTotalen.php' style = 'color : black'>Maandtotalen</a></br></br>
      <a href='<?php echo $url;?>GroeiresultaatSchaap.php' style = 'color : black'>Groeiresultaten per schaap</a></br></br>
      <a href='<?php echo $url;?>GroeiresultaatWeging.php' style = 'color : black'>Groeiresultaten per weging</a></br></br>
      <a href='<?php echo $url;?>ResultHok.php' style = 'color : black'>Periode resultaten</a></br></br>


      
      <ul class="nested-dropdown">
      <li class="dropdown2"><span>Ooi rapporten</span></br></br>
        <div class="dropdown-content2">
         <a href='<?php echo $url;?>Ooikaart.php' style = 'color : black'>Ooikaart detail</a></br></br>
         <a href='<?php echo $url;?>OoikaartAll.php' style = 'color : black'>Ooikaart moeders</a></br></br>
         <a href='<?php echo $url;?>Meerlingen5.php' style = 'color : black'>Meerling in periode</a></br></br>
         <a href='<?php echo $url;?>Meerlingen.php' style = 'color : black'>Meerling per geslacht</a></br></br>
         <a href='<?php echo $url;?>Meerlingen2.php' style = 'color : black'>Meerlingen per jaar</a></br></br>
         <a href='<?php echo $url;?>Meerlingen3.php' style = 'color : black'>Meerling oplopend</a></br></br>
         <a href='<?php echo $url;?>Meerlingen4.php' style = 'color : black'>Meerlingen aanwezig</a></br></br>
        </div>
      </li>
      </ul>

    </div>
  </li>
  
  <li class="dropdown"><span>Voorraadbeheer</span>
    <div class="dropdown-content">
      <a href='<?php echo $url;?>Medicijnen.php' style = 'color : black'>Medicijnenbestand</a></br></br>
      <a href='<?php echo $url;?>Voer.php' style = 'color : black'>Voerbestand</a></br></br>
      <a href='<?php echo $url;?>Inkopen.php' style = 'color : black'>Inkopen</a></br></br>
      <a href='<?php echo $url;?>Voorraad.php' style = 'color : black'>Voorraad</a></br></br>
    </div>
  </li>
  
  <li class="dropdown"><span>Financieel</span>
    <div class="dropdown-content">
      <a href='<?php echo $url;?>Kostenopgaaf.php' style = 'color : black'>Inboeken</a></br></br>
      <a href='<?php echo $url;?>Deklijst.php' style = 'color : black'>Deklijst</a></br></br>
      <a href='<?php echo $url;?>Liquiditeit.php' style = 'color : black'>Liquiditeit</a></br></br>
      <a href='<?php echo $url;?>Saldoberekening.php' style = 'color : black'>Saldoberekening</a></br></br>
      <a href='<?php echo $url;?>Rubrieken.php' style = 'color : black'>Rubrieken</a></br></br>
      <a href='<?php echo $url;?>Componenten.php' style = 'color : black'>Componenten</a></br></br>
      <a href='<?php echo $url;?>Kostenoverzicht.php' style = 'color : black'>Betaalde posten</a></br></br>
    </div>
  </li>

<li class="dropdown"><span style = 'color : black'>Beheer</span>
    <div class="dropdown-content">
    <a href='<?php echo $url;?>Hok.php' style = 'color : black'>
Verblijven</a><br/><br/>
      <a href='<?php echo $url; ?>Ras.php' style = 'color : black'>Rassen</a><br/><br/>
    <a href='<?php echo $url; ?>Uitval.php' style = 'color : black'>Redenen en momenten</a></br></br>
    <a href='<?php echo $url; ?>Combireden.php' style = 'color : black'>Combi redenen</a></br></br>
    <a href='<?php echo $url; ?>Vader.php' style = 'color : black'>Dekrammen</a><br/><br/>
    <a href='<?php echo $url;?>Eenheden.php' style = 'color : black'>
Eenheden</a></br></br>
    <a href='<?php echo $url; ?>Relaties.php' style = 'color : black'>Relaties</a></br></br>

    <a href='<?php echo $url; ?>Readerversies.php' style = 'color : black'>Readerversies</a></br></br>
    <a href='<?php echo $url; ?>Systeem.php' style = 'color : black'>Instellingen</a></br></br>
    </div>
  </li>

  <li id = "rechts_uitlijnen"><a href='<?php echo $url;?>index.php' style = 'color : black'>Uitloggen</a></li>
</ul>


<table id ="table1">

<tr height = 90> </tr>
<TR>
    <TD>
<table Border = 0 align = "center">


  <thead>
    <tr  class="StickyHeader_green">
      <th>Name</th>
      <th>Age</th>
      <th>Job</th>
      <th>Color</th>
      <th>URL</th>
    </tr>
  </thead>
  <tbody>
<?php for ($i=0; $i <6; $i++) { ?>
    <tr>
      <td>Lorem.</td>
      <td>Ullam.</td>
      <td>Vel.</td>
      <td>At.</td>
      <td>Quis.</td>
    </tr>
    <tr>
      <td>Quas!</td>
      <td>Velit.</td>
      <td>Quisquam?</td>
      <td>Rerum?</td>
      <td>Iusto?</td>
    </tr>
<?php } ?>
  
    <tr class="StickyHeader_blue">
      <th>Name</th>
      <th>Age</th>
      <th>Job</th>
      <th>Color</th>
      <th>URL</th>
    </tr>
<?php for ($i=0; $i <6; $i++) { ?>
    <tr>
      <td>Qui!</td>
      <td>Accusamus?</td>
      <td>Minima?</td>
      <td>Dolorum.</td>
      <td>Molestiae.</td>
    </tr>
    <tr>
      <td>Vero!</td>
      <td>Voluptatum?</td>
      <td>Ea?</td>
      <td>Odit!</td>
      <td>A.</td>
    </tr>
<?php } ?>

    <tr class="purple">
      <th>Name</th>
      <th>Age</th>
      <th>Job</th>
      <th>Color</th>
      <th>URL</th>
    </tr>
<?php for ($i=0; $i <16; $i++) { ?>
    <tr>
      <td>Atque!</td>
      <td>Tenetur.</td>
      <td>Optio.</td>
      <td>Iure.</td>
      <td>Porro.</td>
    </tr>
    <tr>
      <td>Atque.</td>
      <td>Alias.</td>
      <td>Doloremque.</td>
      <td>Velit.</td>
      <td>Culpa.</td>
    </tr>
<?php } ?>
  </tbody>
</table>

</TD>
</TR>
</table>


</body>

</html>