<?php /* 
11-12-2014 gemaakt 
8-3-2015 : Login toegevoegd */
$versie = '24-2-2017'; /* Aangpast na.v. Release 2 of wel nieuwe databasestructuur */
$versie = '12-3-2017'; /* Verwijderen mogelijk gemaakt */
$versie = '29-7-2017'; /* toedienen bij afgevoerden mogelijk gemaakt */
$versie = '25-2-2018'; /* standaard hoeveelheid gebasserd op combireden */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '14-11-2020'; /* Onderschied gemaakt tussen reader Agrident en Biocontrol */
$versie = '15-01-2021'; /* Toedien aantal uit tabel impAgrident gehaald */
$versie = '07-09-2021'; /* In query's $zoek_afvoerdatum en $zoek_fase h.skip = 0 in where clause toegevoegd */
$versie = '22-09-2021'; /* func_artikelnuttigen.php toegevoegd */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-01-2025'; /*  and isnull(st.rel_best) toegevoegd aan opvragen van gegevens uit tabel impAgrident zodat stalId's van uitgeschaarden niet worden getoond */
 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Medicatie';
$file = "InsMedicijn.php";
include "login.php"; ?>

			<TD valign = "top">
<?php 
if (is_logged_in()) {

include "func_artikelnuttigen.php";

If (isset ($_POST['knpInsert_'])) {
	include "post_readerMed.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsMedicijn.php"); 
	} 

if($reader == 'Agrident') {
$velden = "rd.Id readId, date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr, NULL scan, 

	s.schaapId,
	rd.artId,
	rd.toedat,
	round(i.stdat) stdat,
	i.eenheid,
	rd.reden reduId,
	i.actief a_act, 
	ru.pil r_act,
	i.inkId, i.vrdat";

$tabel = "
impAgrident rd 
left join tblSchaap s on (rd.levensnummer = s.levensnummer)
left join tblStal st on (s.schaapId = st.schaapId and st.lidId = rd.lidId)
left join 
(
	SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, a.actief, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
	FROM tblEenheid e
	 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
	 join tblInkoop i on (i.enhuId = eu.enhuId)
	 join tblArtikel a on (i.artId = a.artId)
	 left join (
		SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
		FROM tblNuttig n
		 join tblInkoop i on (n.inkId = i.inkId)
		 join tblArtikel a on (a.artId = i.artId)
		 join tblEenheiduser eu on (a.enhuId = eu.enhuId)
		WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
		GROUP BY n.inkId
	 ) n on (i.inkId = n.inkId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)/* deze query betreft min_inkId_met_vrd */."' and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
	GROUP BY a.artId, a.naam, a.stdat, e.eenheid
) i on (rd.artId = i.artId)
left join tblRedenuser ru on (rd.reden = ru.reduId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 8 and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");
}

else {
$velden = "rd.readId, str_to_date(rd.datum,'%Y/%m/%d') sort, rd.datum, rd.levnr_pil levnr, rd.reden_pil scan, 

	s.schaapId,
	cr.artId,
	1 toedat,
	round(cr.stdat) stdat,
	i.eenheid,
	cr.reduId,
	cr.actief a_act, 
	cr.pil r_act,
	i.inkId, i.vrdat";

$tabel = "
impReader rd 
left join tblSchaap s on (rd.levnr_pil = s.levensnummer)
left join tblStal st on (s.schaapId = st.schaapId and st.lidId = rd.lidId)
left join (
	SELECT c.scan, a.artId, c.stdat, a.actief, ru.pil, ru.reduId, r.reden
	FROM tblCombiReden c 
	 join tblArtikel a on (a.artId = c.artId)
	 join tblRedenuser  ru on (ru.reduId = c.reduId)
	 join tblReden r on (ru.redId = r.redId)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) cr on (cr.scan = rd.reden_pil)
left join 
(
	SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
	FROM tblEenheid e
	 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
	 join tblInkoop i on (i.enhuId = eu.enhuId)
	 join tblArtikel a on (i.artId = a.artId)
	 left join (
		SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
		FROM tblNuttig n
		 join tblHistorie h on (n.hisId = h.hisId)
		 join tblStal st on (h.stalId = st.stalId)
		WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
		GROUP BY n.inkId
	 ) n on (i.inkId = n.inkId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)/* deze query betreft min_inkId_met_vrd */."' and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
	GROUP BY a.artId, a.naam, a.stdat, e.eenheid
) i on (cr.artId = i.artId)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.teller_pil is not null and isnull(rd.verwerkt) ";



$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.readId");

} ?>

<table border = 0>
<form action="InsMedicijn.php" method = "post">

<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Toedien<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Medicijn<hr></th>
 <th>Aantal<hr></th>
 <th>hoeveel<br> heid<hr></th>
 <th>Eenheid<hr></th>
 <th>Reden<hr></th>
 <th>Status<hr></th>
</tr>
<?php 

if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
$dm = str_replace('/', '-', $var);
$dag = date('d-m-Y', strtotime($dm));
$date  = date('Y-m-d', strtotime($dm));
	
	$Id = $array['readId'];
	$levnr = $array['levnr'];
	$scan = $array['scan']; # het scannummer uit het veld reden_pil in tabel impReader
	$schaapId = $array['schaapId']; 
	//$inkId = $array['inkId']; #InkId uit vw_Voorraad indien voorradig anders uit tblInkoop
	$artId_rd = $array['artId']; #Artikel uit impAgrident of uit tblCombiReden
	$aantal = $array['toedat']; #Toedien aantal uit impAgrident
	$stdat = $array['stdat']; #stdat aantal uit tblCombiReden
	$eenheid = $array['eenheid']; 
	$reduId = $array['reduId']; /*Reden uit tblCombiReden*/
	$p_act = $array['a_act'];
	$r_act = $array['r_act'];
	$vrrd = $array['vrdat'];

	$kzlArt = $artId_rd;
	$kzlRedu = $reduId;
	
if(isset($schaapId)) {
$zoek_fase = mysqli_query($db,"
SELECT s.schaapId, s.geslacht, af.stalId s_af, prnt.schaapId prnt
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join (
	SELECT max(stalId) stalId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 ) mst on (mst.stalId = st.stalId)
 left join (
	SELECT st.stalId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	 join tblActie a on (a.actId = h.actId)
	WHERE a.af = 1 and lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.skip = 0
 ) af on (af.stalId = mst.stalId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
 
") or die (mysqli_error($db));
	while ($fs = mysqli_fetch_assoc($zoek_fase)) {  
	$gevonden = $fs['schaapId'];	if(isset($gevonden)) { $fase = 'lam'; }
	$sekse = $fs['geslacht'];
	$prnt = $fs['prnt']; 	if(isset($prnt)) { if($sekse = 'ooi') { $fase = 'moederdier'; } else if($sekse = 'ram') { $fase = 'vaderdier'; } }
	$weg = $fs['s_af']; if(isset($weg)) { $fase = 'afgevoerd'; }
	

	 }

// Zoek op afvoerdatum ter controle op toedien datum
$zoek_laatste_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	while( $stl = mysqli_fetch_assoc($zoek_laatste_stalId)) { $stalId = $stl['stalId']; }

$zoek_afvoerdatum = mysqli_query($db,"
SELECT h.datum date, date_format(h.datum,'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE h.stalId = '".mysqli_real_escape_string($db,$stalId)."' and a.af = 1 and h.skip = 0
") or die (mysqli_error($db));
	while( $afv = mysqli_fetch_assoc($zoek_afvoerdatum)) { $dmafv = $afv['date']; $afvdm = $afv['datum']; }
// Einde Zoek op afvoerdatum ter controle op toedien datum

}	

// De voorwaarden om in te kunnen lezen. 
if (isset($_POST['knpVervers_'])) {

	$dag = $_POST["txtDatum_$Id"];
		$makedate = date_create($dag);
		$date =  date_format($makedate, 'Y-m-d');
	$kzlArt = $_POST["kzlPil_$Id"];
	$aantal = $_POST["txtAantal_$Id"];
	$reduId = $_POST["kzlReden_$Id"];
	
	if(empty($kzlArt)) {$vrrd = '';} else {
$zoek_voorraad = mysqli_query($db,"
SELECT inkId, vrdat, actief v_actief 
FROM (
	SELECT i.artId, ifnull(vrd.inkId, max(i.inkId)) inkId, vrd.vrdat, a.actief
	FROM tblInkoop i
	 join tblArtikel a on (i.artId = a.artId)
	 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
	 left join (
		SELECT a.artId, i.inkId, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
		FROM tblArtikel a
		 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
		 join tblEenheid e on (e.eenhId = eu.eenhId)
		 join tblInkoop i on (a.artId = i.artId)
		 left join (
			SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
			FROM tblEenheiduser eu
			 join tblArtikel a on (a.enhuId = eu.enhuId)
			 join tblInkoop i on (i.artId = a.artId)
			 join tblNuttig n on (i.inkId = n.inkId)
			WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.soort = 'pil'
			GROUP BY n.inkId
		 ) n on (i.inkId = n.inkId)
		 left join (
			SELECT a.artId, sum(i.inkat) - sum(coalesce(n.vbrat,0)) totvrd
			FROM tblEenheiduser eu
			 join tblArtikel a on (a.enhuId = eu.enhuId)
			 join tblInkoop i on (a.artId = i.artId)
			 left join (
				SELECT n.inkId, sum(n.stdat*n.nutat) vbrat
				FROM tblStal st
				 join tblHistorie h on (h.stalId = st.stalId)
				 join tblNuttig n on (n.hisId = h.hisId)
				WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
				GROUP BY n.inkId
			 ) n on (i.inkId = n.inkId)
			WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
			GROUP BY a.artId 
		 ) artvrd on (artvrd.artId = a.artId)
		WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.soort = 'pil' and (i.inkat-coalesce(n.vbrat,0) > 0 or (a.actief = 1 and totvrd = 0) )
		GROUP BY a.artId, a.naam, a.stdat, e.eenheid, i.inkId, i.charge, artvrd.totvrd
	 ) vrd on (i.artId = vrd.artId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY i.artId, vrd.vrdat, actief
) A
WHERE artId = '".mysqli_real_escape_string($db,$kzlArt)."'
") or die (mysqli_error($db));
			while ($qry_st = mysqli_fetch_assoc($zoek_voorraad)) {
	$vrrd = $qry_st['vrdat']; 
	$p_act = $qry_st['v_actief']; }
		}
		
	if(empty($reduId)) {$r_act = '';} else {
	$zoek_reden_actief = mysqli_query($db,"
SELECT ru.pil
FROM tblRedenuser ru
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.reduId = '".mysqli_real_escape_string($db,$reduId)."' ") or die (mysqli_error($db));
		while ($ra = mysqli_fetch_assoc($zoek_reden_actief)) { 
	
	$r_act = $ra['pil']; }
											}
	} 


// Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
if (!empty($kzlArt)) {
$qryPorties = mysqli_query($db,"
SELECT a.stdat, e.eenheid
FROM tblInkoop i
 join tblArtikel a on (i.artId = a.artId)
 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and i.artId = '".mysqli_real_escape_string($db,$kzlArt)."'
") or die (mysqli_error($db));
	While ($por = mysqli_fetch_assoc($qryPorties))
		{ $stdat = $por['stdat'];
		  $eenheid = $por['eenheid']; }
					}
// Einde Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
				
	
If	 ( empty($fase)   					|| /*levensnummer moet bestaan */	
		empty($dag)						|| # of datum is leeg
		empty($kzlArt) || $p_act <> 1	|| # medicijn bestaat niet in kezeuelijst of is niet actief
		empty($vrrd) || $vrrd == 0		|| # medcijn niet meer op voorraad
		empty($aantal)					|| # aantal is leeg
		empty($stdat)					|| # Standaard hoeveelheid is leeg
		(isset($dmafv) && $dmafv <= $date)	|| #Afvoerdatum is gelijk aan of ligt voor toedien datum
		($r_act <> 1 && !empty($reduId))	 # reden t.b.v. medicijn niet actief
	 )
	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE De voorwaarden om in te kunnen lezen.  

	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:13px;">
 <td align = "center"> 

	<input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbkies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
	<input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 

</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php unset($schaapId); unset($dmafv); }
} //einde if(isset($data))
 unset($fase); ?>
</table>
</form> 

	</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

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

$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});
</SCRIPT>
