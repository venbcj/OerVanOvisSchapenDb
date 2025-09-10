<?php 
$versie = '17-5-2019'; /* gemaakt als kopie van HokAfleveren */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '14-08-2020'; /* geslacht toegevoegd */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd aan tblHistorie en sql beveiligd */
$versie = '07-01-2024'; /* Select_all toegevoegd en include kalender op een andere plek gezet omdat dit elkaar anders bijt. */
$versie = '14-01-2024'; /* Sortering op fase en werknr */
$versie = '20-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 940 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '20-02-2025'; /* <input type = hidden size = 1 name = <?php echo "chbkies_Id"; ?> value = 0 > verwijderd */
  session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	 <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Aanwas';
$file = "HokkenBezet.php";
include "login.php"; ?>

				<TD align = "center" valign = "top">
<?php 
if (is_logged_in()) {

if(isset($_GET['pstId'])) { $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */


if(isset($_POST['knpVerder_']) )	{ 
	$datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum; }
  $sess_dag = $_SESSION["DT1"];

if(isset($_POST['knpSave_'])) { include "save_aanwas.php"; }

$zoek_hok = mysqli_query ($db,"
SELECT hoknr FROM tblHok WHERE hokId = ".mysqli_real_escape_string($db,$ID)."
") or die (mysqli_error($db));
	while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; } ?>

<form action="HokAanwas.php" method = "post"><?php
// Opbouwen paginanummering 
$velden = "s.schaapId, s.levensnummer, s.geslacht, date_format(max(h.datum),'%Y-%m-%d') dmlst, date_format(max(h.datum),'%d-%m-%Y') lstdm";

$tabel = "tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
	SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = ".mysqli_real_escape_string($db,$ID)."
	GROUP BY b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)";

 $WHERE = "WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId) and h.skip = 0
 and isnull(prnt.schaapId)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "GROUP BY s.levensnummer ORDER BY right(s.levensnummer,'".mysqli_real_escape_string($db,$Karwerk)."') "); 
// Einde Opbouwen paginanummering 
if(!isset($sess_dag)) { $width = 100; } 
else { $width = 200; } ?>
<table border = 0 > <!-- tabel1 -->
<tr>
 <td>
	<table border = 0 > <!-- tabel2 -->
	<tr> 
 	 <td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
	 </td>

 <?php if(!isset($sess_dag)) {
 	include "kalender.php"; ?>
	 <td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen 
	  <input id = "datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp 
 <?php } else { ?>
	 <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
	 </td>
	 <td width = 150 align = center>
<?php if(!isset($sess_dag)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
	 </td>
	 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
	  <input type = submit name = "knpVervers_" value = "Verversen"> 
	 </td>
	 <td width = 200 align = 'right'>
	  <input type = submit name = "knpSave_" value = "Opslaan">&nbsp &nbsp
	 </td> <?php } ?>
	</tr>

	<tr>
	 <td colspan = 7 align = left > </td>
	 <td></td>
	</tr>
	</table> <!-- einde tabel2 -->
 </td>
</tr>
<tr>
 <td>
	<table border = 0 align = left > <!-- tabel3 --> 
<?php if(isset($sess_dag)) { ?>
	<tr valign = bottom style = "font-size : 12px;">
	 <th>Aanwas<br><b style = "font-size : 10px;">Ja/Nee</b><br><input type="checkbox" id="selectall" checked /><hr></th>
	 <th>Aanwasdatum<hr></th>
	 <th>Levensnummer<hr></th>
	 <th>Geslacht<hr></th>
	 <th>Gewicht<hr></th>
	 <th><hr></th>
	 <th colspan = 3 ><hr></th>
	</tr>
<?php  
if(isset($data)) {
	foreach($data as $key => $array)
	{
		$Id = $array['schaapId'];
		$levnr = $array['levensnummer'];
		$geslacht = $array['geslacht'];
		$dmmax = $array['dmlst'];
		$maxdm = $array['lstdm'];


if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) ) { 
	$cbKies = $_POST["chbkies_$Id"];
	$datum = $_POST["txtDatum_$Id"];
	$kg = $_POST["txtKg_$Id"];
	}
// Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlRelall_ . txtDatum_$levnr en txtGewicht_$levnr bestaan dan nog niet. Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlRelall_']))  !!!
	if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
	if(isset($datum)) /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */ { $makeday = date_create($datum); $day = date_format($makeday,'Y-m-d'); }
// Controleren of ingelezen waardes correct zijn.
	if( empty($datum)				|| # Aanwasdatum is leeg
		$day < $dmmax				#||  afleverdag is kleiner dan laatste registratiedatum
		/*empty($kg)				||*/ # Aanwasgweicht is leeg

	)
	{$oke = 0; } else { $oke = 1; }
	 
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) ) { $cbKies = $_POST["chbkies_$Id"]; $txtOke = $_POST["txtOke_$Id"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

	<tr style = "font-size:14px;">
	 <td align = center>
		<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> 	value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else {	?> class="checkall" <?php } ?> >
	 </td>
	<!-- Aanwasdatum -->
	 <td align = center>
		<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
	 </td>

	 <td width = 110 align = center> <?php echo $levnr; ?> </td>

	 <td width = 50 align = center> <?php echo $geslacht; ?> </td>
		
	 <td width = 80 align = center style = "font-size : 9px;"> 
		<input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php if(isset($kg)) { echo $kg; } ?> > </td>

	 <td width = 100 align = center> </td>

	 <td colspan = 3 style = "color : red"> 
	<?php if($day < $dmmax) { echo 'De datum mag niet voor '.$maxdm.' liggen.';}
	 else if(isset($uitvaldm)) { echo 'Dit schaap is reeds overleden.';}
	?>
	 </td>	
	</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php } 
		} // Einde if(isset($data))
	  } ?>
	</table> <!-- Einde tabel3 -->
 </td>
</tr>
</table> <!-- Einde tabel1 -->
</form> 


</TD>
<?php	
include "menu1.php"; } ?>
</body>
</html>
<SCRIPT language="javascript">
$(function(){

	 // add multiple select / deselect functionality
	 $("#selectall").click(function () {
		  	$('.checkall').attr('checked', this.checked);
	 });

	 // if all checkbox are selected, check the selectall checkbox
	 // and viceversa
	 $(".checkall").click(function(){

			if($(".checkall").length == $(".checkall:checked").length) {
				 $("#selectall").attr("checked", "checked");
			} else {
				 $("#selectall").removeAttr("checked");
			}

	 });
});
</SCRIPT>
