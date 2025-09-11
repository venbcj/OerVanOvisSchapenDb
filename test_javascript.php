<?php  

$versie = '17-2-14'; /*insInkat = ln['vrbat']*(_POST['txtBstat']); gewijzigd naar insInkat = _POST['txtBstat']; zodat de totale hoeveelheid kan worden ingevoerd bij inkoop ipv het totale aantal / verbruikeenheid in te voeren.*/
$versie = '27-11-2014'; /*chargenr toegevoegd.*/ 
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '20-12-2015'; /* Inkoop ook toegevoegd aan tblOpgaaf indien module financieel in gebruik */
$versie = '16-6-2018'; /* Bedrag bij ingekochte artikelen wijzigbaar. Bedrag bij inkoop niet verplicht. function verplicht() toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2018'; /* javascript toegevoegd tbv eenheid artikel wijzigen */
$versie = '7-4-2019'; /* Prijs in tblOpgaaf incl. btw gemaakt */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 1-8-2020 : kalender toegevoegd */
$versie = '28-11-2020'; /* 28-11-2020 velde chkDel toegevoegd */

session_start();
 ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	 <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> 
<title>Inkoop</title>

<style type="text/css">
        .selectt {
            color: #fff;
            padding: 30px;
            display: none;
            margin-top: 30px;
            width: 60%;
            background: green
        }
          
        label {
            margin-right: 20px;
        }
    </style>

</head>
<body>

<center>
<?php
$titel = 'Inkopen';
$subtitel = '';
include "header.tpl.php"; ?>

			<TD width = 960 height = 400 valign = "top">
<?php
$file = "test_javascript.php";
include "login.php"; 
if (Auth::is_logged_in()) { 

$newvoer = "
SELECT artId, stdat, naam, concat(' ', eenheid) heid, soort, eenheid
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and a.actief = 1
ORDER BY soort desc, naam
"; 

$q_newvoer2 = mysqli_query($db,$newvoer) or die (mysqli_error($db));
	while($lin = mysqli_fetch_array($q_newvoer2))
		{

$array_eenheid[$lin['artId']] = $lin['eenheid'];

//echo $array_eenheid[$lin['artId']].'<br>';

} ?>
<script>
function verplicht() {
var datum	 = document.getElementById("datum"); 		var datum_v = datum.value;
var artikel	 = document.getElementById("artikel");		var artikel_v = artikel.value;


	 if(datum_v.length == 0) datum.focus() + alert("Datum is onbekend.");
else if(artikel_v.length == 0) artikel.focus() + alert("Omschrijving is onbekend.");

}


function eenheid_artikel() {

var artikel	 = document.getElementById("artikel");		var artikel_v = artikel.value;


 if(artikel_v.length > 0) { toon_eenheid(artikel_v); }
 else { removeElement(); }

}

 var jArray= <?php echo json_encode($array_eenheid); ?>;

function toon_eenheid(e) {
	document.getElementById('aantal').innerHTML = jArray[e] + '&nbsp &nbsp ';
}

/*function value_checkbox_jaar() {

var all_year = document.querySelectorAll('input[id="chbJaar[]"]:checked');

var aYrs = [];

for(var x = 0, l = all_year.length; x < l; x++)
{
	var str = aYrs.push(all_year[x].value);
}

var str = aYrs.join(',');
toon_jaar(aYrs[0]); */

/*
var chbJaar = document.getElementById("chbJaar"); var chbJaar_v = chbJaar.value;

 if(chbJaar.checked == true) { toon_jaar(chbJaar_v); }
 else { removeElement('toonj'); }
 /*{ chbJaar.style.display = "block"; }
 else { chbJaar.style.display = "none"; } 

}*/

/*function toon_jaar(v) {
	document.getElementById('toonj').innerHTML = v;
}*/

function removeElement(e) {
	 document.getElementById('aantal').innerHTML = '';
}






            $(function() {
    $(":checkbox[name^='chbToonJaar']").on('change', function() {
        alert(this.value + ' --- ' + this.checked);        
    } );
});

function jaarnr() {
        alert(this.value + ' --- ' + this.checked);        
    }

</script>
<?php 


//*******************
// NIEUWE INVOER POSTEN
//*******************
 ?>

<table border= 0><tr><td>

<form action="test_javascript.php" method="post" >

<!--*********************************
		 NIEUWE INVOER VELDEN
	********************************* -->
<table border= 0>
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe inkoop : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom"> 
 <td>Inkoopdatum<hr></td>
 <td>Omschrijving<hr></td>
 <td>Chargenummer<hr></td>
 <td colspan = 2> Aantal <hr></td> 

 <td colspan = 2 width = 50 align = center>Totaalprijs excl. btw<hr></td> 
</tr>
<tr>
 <td><input type="text" name = "txtInkdm_" id = "datum" size = 8 value = <?php if(isset($inkdatum)) { echo $inkdatum; } ?> ></td>
 <td>

<?php
// kzlvoer bij nieuwe invoer
$q_newvoer = mysqli_query($db,$newvoer) or die (mysqli_error($db));	?>

 <select style= "width:280;" name = "txtArtikel_" id = "artikel" onchange = "eenheid_artikel()" >
 <option> </option>	
<?php		while($lijn = mysqli_fetch_array($q_newvoer))
		{

$name = $lijn['naam'];
if ($lijn['soort'] == 'pil') {$getal = "&nbsp per $lijn[stdat]"; $eenheid = $lijn['heid'];}
else {$getal = ''; $eenheid = '';}

$cijf = str_replace('.00', '', $getal); 
$wrde = "$name$cijf$eenheid";

		
			$opties= array($lijn['artId']=>$wrde);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['txtArtikel_']) && $_POST['txtArtikel_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
</td>
<td><input type= "text" name = "txtCharge_" size = 14 value = <?php if(isset($txtcharge)) { echo $txtcharge; } ?> ></td>
<td><input type= "text" name = "txtInkat_" size = 3 value = <?php if(!isset($inkwaarde)) { $inkwaarde = 1; } echo $inkwaarde; ?> title = "Totale hoeveelheid ingekocht"> 
</td>
<td>
<p  id="aantal" > </p>
</td>
<td>
&euro;

</td>
<td><input type= "text" name = "txtPrijs_" size = 3  title = "Prijs totale hoeveelheid" <?php echo "value = $inkprijs "; ?> ></td> 

<td colspan = 2><input type = "submit" name = "knpInsert_" onclick = "verplicht()" value = "Toevoegen" style = "font-size:10px;"></td></tr>

<tr><td colspan = 15><hr></td></tr>
</table>
<!--*********************************
		EINDE NIEUWE INVOER VELDEN
	********************************* -->
</td></tr><tr><td>
<!--*****************************
	 		WIJZIGEN VOER
	***************************** -->
 <table border= 0 align =  "left" >
 <tr> 
  <td colspan =  16 > <b>Inkopen :</b> 
  </td>
  <td align="center" ><input type = "submit" name = "knpSave_" value = "Opslaan" style = "font-size:14px" >
 </td>
</tr>



<?php		
// START LOOP
$group_jaar = mysqli_query($db,"
SELECT year(i.dmink) jaar
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
WHERE eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
GROUP BY year(i.dmink)
ORDER BY year(i.dmink) desc
") or die (mysqli_error($db));

	while($lus = mysqli_fetch_assoc($group_jaar))
	{
            $jaar = ($lus['jaar']);   ?>
<tr>
 <td colspan="8" >
 	<p  id="jaartal" >  </p>
 	<input type="checkbox" name="chbToonJaar" id="chbJaar" onchange="value_checkbox_jaar()" value= <?php echo $jaar;  	if($jaar == 2021) { ?> checked <?php } ?> > toon <?php echo $jaar; ?>
 </td>
 <td>
<p  id="toonj" > </p>
<script>
toon_jaar(str);
</script>


</td>
 <td>
 	Stuksprijs
 </td>
</tr>
 <tr style =  "font-size:12px;" valign =  "bottom"> 
		 <th id="h_datum[]">Inkoopdatum<hr></th>
		 <th></th> 
		 <div class="2020 selectt"><th>Omschrijving<hr></th> </div>
		 <th></th> 
		 <th>Chargenummer<hr></th>
		 <th></th> 
		 <th colspan = 2>Aantal<hr></th> 
		 <th></th> 
		 <th width = 50>(excl.)<hr></th>
		 <th></th> 
		 <th>Prijs (excl.)<hr></th> 
		 <th></th>
		 <th>Btw<hr></th>
		 <th></th>
		 <th>Leverancier<hr></th> 
		 <th>Verwijder<hr></th> 
		  


 </tr> 


<tr><td height="50"></td></tr>

<?php    } ?>

</td></tr>

</table>
<!--*****************************
	 	EINDE WIJZIGEN VOER
	***************************** -->



</form>

<td><tr></table>




	</TD>
<?php 
include "menuInkoop.php"; } ?>



<script>    
        $('.delete_class').click(function(){
            var tr = $(this).closest('tr'),
                del_id = $(this).attr('id');

            $.ajax({
                url: "delete_inkoop.php?delete_id="+ del_id,
                cache: false,
                success:function(result){
                    tr.fadeOut(1000, function(){
                        $(this).remove();
                    });
                }
            });
        });
</script>


</body>
</html>


