<?php
/* Aangemaakt : 21-8-2016  Onderstaande statements moeten worden uitgevoerd om voor een bestaande klant maandelijks een demo omgeving te creeëren.
	Wordt ook gebruikt om handmatig de test omgevng te voorzien van nieuwe basisgegevens
18-1-2017 : in tblPeriode doel gewijzigd naar doelId
22-1-2017 : tblBezetting gewijzigd naar tblBezet
5-7-2020 : in tblArtikel en tblArtikel_basis prijs gewijzigd naar perkg, wdgn naar wdgn_v en wdgn_m toegevoegd
7-5-2023 : De tabellen tblHistorie, tblInkoop, tblPeriode, tblLiquiditeit en tblSalber worden ingelezen met actuele datums
11-6-2023 : mdrId en vdrId uit tblSchaap gehaald
*/



// AANVULLEN TABELLEN
$plus_levnr = ($lidId-1)*100000000;


/*De datums worden geactualiseerd o.b.v. de datum van vandaag. De maximale datum uit de tabellen tblHistorie_basis, tblInkoop_basis en tblPeriode_basis wordt opgezocht. Het verschil in dagen met vandaag wordt bepaald met de variable $dagen. Elke datum uit de tabellen tblHistorie_basis, tblInkoop_basis en tblPeriode_basis wordt opgehoogd met het getal in de variabele $dagen. */

$zoek_max_datum = mysqli_query($db,"
SELECT max(datum) maximaal
FROM (
	SELECT datum FROM `tblHistorie_basis` 
	UNION
	SELECT dmink FROM `tblInkoop_basis` 
	UNION
	SELECT dmafsluit FROM `tblPeriode_basis` 
) t
") or die (mysqli_error($db));

while ($mx = mysqli_fetch_assoc($zoek_max_datum)) { $dmmax = $mx['maximaal']; }

$now = time(); // or your date as well
$your_date = strtotime($dmmax);
$datediff = $now - $your_date;
$dagen = round($datediff / (60 * 60 * 24));

/*De datums worden geactualiseerd o.b.v. de datum van vandaag. De minimale datum uit de tabellen tblLiquiditeit_basis en tblSalber_basis wordt opgezocht. Het verschil in jaren met vandaag wordt bepaald met de variable $jaren. Elke datum uit de tabellen tblLiquiditeit_basis en tblSalber_basis wordt opgehoogd met het getal in de variabele $jaren. */

$zoek_min_jaar = mysqli_query($db,"
SELECT year(max(datum)) jaar
FROM (
	SELECT datum FROM `tblHistorie_basis` 
	UNION
	SELECT dmink FROM `tblInkoop_basis` 
	UNION
	SELECT dmafsluit FROM `tblPeriode_basis` 
) t
") or die (mysqli_error($db));

while ($mn = mysqli_fetch_assoc($zoek_min_jaar)) { $jaar = $mn['jaar']; }

$now = DateTime::createFromFormat('U.u', microtime(true));
$ditjaar = $now->format("Y");
$jaren = $ditjaar - $jaar;	

// Bepalen modules ja of nee
$module = mysqli_query($db,"SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'; ") or die (mysqli_error($db));
	while ($mod = mysqli_fetch_assoc($module)) { $modbeheer = $mod['beheer']; $modtech = $mod['tech']; $modfin = $mod['fin']; $modmeld = $mod['meld']; }

/********************	Het schaap deel 1	*******************************************************************/
 //Aanvullen tblSchaap incl veld schaapId_basis als sleutelveld voor andere tabellen (foreign key)
if($modtech == 1 ) {
$ins_tblVolwas = "
INSERT INTO `tblVolwas` (readId, datum, mdrId, vdrId, volwId_basis, lidId_demo)
	SELECT readId, datum, mdrId, vdrId, volwId, '".mysqli_real_escape_string($db,$lidId)."'
	FROM tblVolwas_basis
	ORDER BY volwId
";
	mysqli_query($db,$ins_tblVolwas) or die(mysqli_error($db)); 
//echo $ins_tblVolwas.'<br>'.'<br>';

$ins_tblSchaap = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, volwId, indx, momId, redId, schaapId_basis, lidId_demo)
	SELECT s.levensnummer+$plus_levnr, s.rasId, s.geslacht, v.volwId, s.indx,	s.momId, s.redId, s.schaapId, '".mysqli_real_escape_string($db,$lidId)."'
	FROM tblSchaap_basis s
	 left join tblVolwas v on (s.volwId = v.volwId_basis and v.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."')
	ORDER BY s.schaapId
";
	mysqli_query($db,$ins_tblSchaap) or die(mysqli_error($db)); 
#echo $ins_tblSchaap.'<br>'.'<br>';

//mdrId in tblVolwas aanpassen
$upd_moeder = "
UPDATE tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId_basis and mdr.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."')
set v.mdrId = mdr.schaapId
";
	mysqli_query($db,$upd_moeder) or die(mysqli_error($db));
//echo $upd_moeder.'<br>'.'<br>';


//vdrId in tblVolwas aanpassen
$upd_vader = "
UPDATE tblVolwas v
 join tblSchaap vdr on (v.vdrId = vdr.schaapId_basis and vdr.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."')
set v.vdrId = vdr.schaapId
";
	mysqli_query($db,$upd_vader) or die(mysqli_error($db));
//echo $upd_vader.'<br>'.'<br>';

// volwId in tblSchaap aanpassen
$upd_volwId = "
UPDATE tblSchaap s 
 join tblVolwas v on (s.volwId = v.volwId_basis and s.lidId_demo = v.lidId_demo)
set s.volwId = v.volwId 
WHERE v.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."'
";
	mysqli_query($db,$upd_volwId) or die(mysqli_error($db));
//echo $upd_vader.'<br>'.'<br>';
}



if($modtech == 0 ) { // Alleen aanwezige schapen inlezen
$ins_tblSchaap = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, indx, momId, redId, schaapId_basis, lidId_demo)
	SELECT levensnummer+$plus_levnr, rasId, geslacht, indx,	momId, redId, s.schaapId, '".mysqli_real_escape_string($db,$lidId)."'
	FROM tblSchaap_basis s
	 join tblStal_basis st on (st.schaapId = s.schaapId)
	 join tblHistorie_basis h on (st.stalId = h.stalId)
	WHERE isnull(st.rel_best)
	GROUP BY levensnummer+$plus_levnr, rasId, geslacht, indx, momId, redId, s.schaapId
	ORDER BY s.schaapId
";
	mysqli_query($db,$ins_tblSchaap) or die(mysqli_error($db)); 
//echo $ins_tblSchaap.'<br>'.'<br>';
}
/********************	Einde Het schaap deel 1	*******************************************************************/
/********************	Stamtabellen	*******************************************************************/

  //Aanvullen tblRasuser incl veld rasuId_basis
$ins_tblRasuser = "
INSERT INTO tblRasuser (lidId, rasId, scan, actief, rasuId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', rasId, scan, actief, rasuId
	FROM tblRasuser_basis
	ORDER BY rasuId
";
	mysqli_query($db,$ins_tblRasuser) or die(mysqli_error($db));
//echo $ins_tblRasuser.'<br>'.'<br>';


  //Aanvullen tblMomentuser incl veld momuId_basis
$ins_tblMomentuser = "
INSERT INTO tblMomentuser (lidId, momId, scan, actief, momuId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', momId, scan, actief, momuId
	FROM tblMomentuser_basis
	ORDER BY momuId
";
	mysqli_query($db,$ins_tblMomentuser) or die(mysqli_error($db));
//echo $ins_tblMomentuser.'<br>'.'<br>';


  //Aanvullen tblRedenuser incl veld reduId_basis
$ins_tblRedenuser = "
INSERT INTO tblRedenuser (redId, lidId, uitval, pil, reduId_basis)
	SELECT redId, '".mysqli_real_escape_string($db,$lidId)."', uitval, pil, reduId
	FROM tblRedenuser_basis
	ORDER BY reduId
";
	mysqli_query($db,$ins_tblRedenuser) or die(mysqli_error($db));
//echo $ins_tblRedenuser.'<br>'.'<br>';

  //Aanvullen tblRubriekuser incl veld rubuId_basis
$ins_tblRubriekuser = "
INSERT INTO tblRubriekuser (rubId, lidId, actief, rubuId_basis)
	SELECT rubId, '".mysqli_real_escape_string($db,$lidId)."', actief, rubuId
	FROM tblRubriekuser_basis
	ORDER BY rubuId
";
	mysqli_query($db,$ins_tblRubriekuser) or die(mysqli_error($db));
//echo $ins_tblRubriekuser.'<br>'.'<br>';

/********************	Einde Stamtabellen	*******************************************************************/
/********************	Relaties	*******************************************************************/

  //Aanvullen tblPartij incl veld partId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblPartij = "
INSERT INTO tblPartij (lidId, ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId
	FROM tblPartij_basis
	ORDER BY partId
";
	mysqli_query($db,$ins_tblPartij) or die(mysqli_error($db));
//echo $ins_tblPartij.'<br>'.'<br>';


  //Aanvullen tblRelatie incl veld relId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblRelatie = "
INSERT INTO tblRelatie (partId, relatie, uitval, actief, relId_basis)
	SELECT p.partId, r.relatie, r.uitval, r.actief, r.relId
	FROM tblRelatie_basis r
	 join tblPartij p on (r.partId = p.partId_basis)
	WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY relId
";
	mysqli_query($db,$ins_tblRelatie) or die(mysqli_error($db));
//echo $ins_tblRelatie.'<br>'.'<br>';


  //Aanvullen tblAdres incl veld adrId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblAdres = "
INSERT INTO tblAdres (relId, straat, nr, pc, plaats, actief, adrId_basis)
	SELECT r.relId, a.straat, a.nr, a.pc, a.plaats, a.actief, a.adrId
	FROM tblAdres_basis a
	 join tblRelatie r on (a.relId = r.relId_basis)
	 join tblPartij p on (r.partId = p.partId)
	WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY adrId
";
	mysqli_query($db,$ins_tblAdres) or die(mysqli_error($db));
//echo $ins_tblAdres.'<br>'.'<br>';
/********************	Einde	Relaties	*******************************************************************/
/********************	Het schaap deel 2	*******************************************************************/
  //Aanvullen tblStal incl veld stalId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblStal = "
INSERT INTO tblStal (lidId, schaapId, kleur, halsnr, rel_herk, rel_best, stalId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', s.schaapId, kleur, halsnr, rh.relId, rb.relId, st.stalId
	FROM tblStal_basis st
	 join tblSchaap s on (st.schaapId = s.schaapId_basis)
	 left join tblRelatie rh on (st.rel_herk = rh.relId_basis)
	 left join tblPartij ph on (ph.partId = rh.partId)
	 left join tblRelatie rb on (st.rel_best = rb.relId_basis)
	 left join tblPartij pb on (pb.partId = rb.partId)
	WHERE s.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."' and (isnull(ph.lidId) or ph.lidId = '".mysqli_real_escape_string($db,$lidId)."') and (isnull(pb.lidId) or pb.lidId = '".mysqli_real_escape_string($db,$lidId)."')
	ORDER BY st.stalId
";
	mysqli_query($db,$ins_tblStal) or die(mysqli_error($db));
//echo $ins_tblStal.'<br>'.'<br>';

if($modtech == 0 ) {
  //Aanvullen tblHistorie incl veld hisId_basis
$ins_tblHistorie = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
	SELECT st.stalId, DATE_ADD(h.datum, INTERVAL $dagen DAY), NULL, h.actId, h.skip, h.hisId
	FROM tblHistorie_basis h
	 join tblStal st on (h.stalId = st.stalId_basis)
	 join tblUbn u on (st.ubnId = u.ubnId)
	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (h.actId = 1 or h.actId = 2 or h.actId = 3 or h.actId = 10 or h.actId = 11 or h.actId = 12 or h.actId = 13 or h.actId = 14)
	ORDER BY h.hisId	
";
	mysqli_query($db,$ins_tblHistorie) or die(mysqli_error($db));
echo '$ins_tblHistorie = '.$ins_tblHistorie.'<br>'.'<br>';
}

if($modtech == 1 ) {
  //Aanvullen tblHistorie incl veld hisId_basis
$ins_tblHistorie = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
	SELECT st.stalId, DATE_ADD(h.datum, INTERVAL $dagen DAY), h.kg, h.actId, h.skip, h.hisId
	FROM tblHistorie_basis h
	 join tblStal st on (h.stalId = st.stalId_basis)
	 join tblUbn u on (st.ubnId = u.ubnId)
	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY h.hisId	
";
	mysqli_query($db,$ins_tblHistorie) or die(mysqli_error($db));
//echo $ins_tblHistorie.'<br>'.'<br>';
}
/********************	Einde Het schaap deel 2	*******************************************************************/
/********************	Voorraadbeheer		*******************************************************************/
  //Aanvullen tblEenheiduser incl veld enhuId_basis
$ins_tblEenheiduser = "
INSERT INTO tblEenheiduser (lidId, eenhId, actief, enhuId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', eenhId, actief, enhuId
	FROM tblEenheiduser_basis
	ORDER BY enhuId
";
	mysqli_query($db,$ins_tblEenheiduser) or die(mysqli_error($db));
//echo $ins_tblEenheiduser.'<br>'.'<br>';

if($modtech == 1 ) {

  //Aanvullen tblArtikel incl veld artId_basis
$ins_tblArtikel = "
INSERT INTO tblArtikel (soort, naam, stdat, enhuId, perkg, btw, regnr, relId, wdgn_v, wdgn_m, rubuId, actief, artId_basis)
	SELECT art.soort, art.naam, art.stdat, eu.enhuId, art.perkg, art.btw, art.regnr, r.relId, art.wdgn_v, art.wdgn_m, ru.rubuId, art.actief, art.artId
	FROM tblArtikel_basis art
	 join tblEenheiduser eu on (art.enhuId = eu.enhuId_basis)
	 join tblRelatie r on (art.relId = r.relId_basis)
	 join tblPartij p on (p.partId = r.partId)
	 left join tblRubriekuser ru on (art.rubuId = ru.rubuId_basis)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 and (isnull(ru.lidId) or ru.lidId = '".mysqli_real_escape_string($db,$lidId)."')
	ORDER BY artId
";
	mysqli_query($db,$ins_tblArtikel) or die(mysqli_error($db));
//echo $ins_tblArtikel.'<br>'.'<br>';



  //Aanvullen tblInkoop incl veld inkId_basis
$ins_tblInkoop = " 
INSERT INTO tblInkoop (dmink, artId, charge, dmvval, inkat, enhuId, prijs, btw, relId, inkId_basis)
	SELECT DATE_ADD(ink.dmink, INTERVAL $dagen DAY), art.artId, ink.charge, ink.dmvval, ink.inkat, eu.enhuId, ink.prijs, ink.btw, r.relId, ink.inkId
	FROM tblInkoop_basis ink
	 join tblArtikel art on (ink.artId = art.artId_basis)
	 join tblEenheiduser eu_art on (art.enhuId = eu_art.enhuId)
	 join tblEenheiduser eu on (ink.enhuId = eu.enhuId_basis)
	 join tblRelatie r on (ink.relId = r.relId_basis)
	 join tblPartij p on (p.partId = r.partId)
	WHERE eu_art.lidId = '".mysqli_real_escape_string($db,$lidId)."' and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY inkId
";
	mysqli_query($db,$ins_tblInkoop) or die(mysqli_error($db));
//echo $ins_tblInkoop.'<br>'.'<br>';

  //Aanvullen tblNuttig veld nutId_basis n.v.t. 
$ins_tblNuttig = "
INSERT INTO tblNuttig (hisId, inkId, nutat, stdat, reduId)
	SELECT h.hisId, i.inkId, n.nutat, n.stdat, ru.reduId
	FROM tblNuttig_basis n
	join tblHistorie h on (n.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblUbn u on (st.ubnId = u.ubnId)
	 join tblInkoop i on (n.inkId = i.inkId_basis)
	 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
	 join tblRedenuser ru on (n.reduId = ru.reduId_basis)
	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY nutId
";
	mysqli_query($db,$ins_tblNuttig) or die(mysqli_error($db));
//echo $ins_tblNuttig.'<br>'.'<br>';
}
/********************	Einde Voorraadbeheer	*******************************************************************/
/********************	Hokken	*******************************************************************/

if($modtech == 1) {
  //Aanvullen tblHok incl veld hokId_basis
$ins_tblHok = "
INSERT INTO tblHok (lidId, hoknr, scan, actief, hokId_basis)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', h.hoknr, h.scan, h.actief, h.hokId
	FROM tblHok_basis h
	ORDER BY h.hokId
";
	mysqli_query($db,$ins_tblHok) or die(mysqli_error($db));
//echo $ins_tblHok.'<br>'.'<br>';


  //Aanvullen tblPeriode incl veld periId_basis
$ins_tblPeriode = "
INSERT INTO tblPeriode (hokId, doelId, dmafsluit, periId_basis)
	SELECT h.hokId, p.doelId, DATE_ADD(p.dmafsluit, INTERVAL $dagen DAY), p.periId
	FROM tblPeriode_basis p
	 join tblHok h on (h.hokId_basis = p.hokId)
	WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY p.periId
";
	mysqli_query($db,$ins_tblPeriode) or die(mysqli_error($db));
//echo $ins_tblPeriode.'<br>'.'<br>';


  //Aanvullen tblBezet  bezId_basis n.v.t. i.v.m. geen foreign key
$ins_tblBezet = "
INSERT INTO tblBezet (periId, hisId, hokId)
	SELECT p.periId, h.hisId, ho.hokId
	FROM tblBezet_basis b
	 left join tblHok ho on (ho.hokId_basis = b.hokId)
	 left join tblPeriode p on (b.periId = p.periId_basis and p.hokId = ho.hokId)
	 join tblHistorie h on (b.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)	
	 join tblUbn u on (st.ubnId = u.ubnId)
	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY b.bezId
";
	mysqli_query($db,$ins_tblBezet) or die(mysqli_error($db));
//echo $ins_tblBezet.'<br>'.'<br>';

  //Aanvullen tblVoeding veld voedId_basis n.v.t. 
$ins_tblVoeding = "
INSERT INTO tblVoeding (periId, inkId, nutat, stdat)
	SELECT p.periId, i.inkId, nutat, stdat
	FROM tblVoeding_basis v
	join tblPeriode p on (v.periId = p.periId_basis)
	 join tblHok ho on (ho.hokId = p.hokId)
	 join tblInkoop i on (v.inkId = i.inkId_basis)
	 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
	WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY voedId
";
	mysqli_query($db,$ins_tblVoeding) or die(mysqli_error($db));
//echo $ins_tblVoeding.'<br>'.'<br>';
}
/********************	Einde Hokken	*******************************************************************/
/********************	Melden		*******************************************************************/

if($modtech == 1 ) {

  //Aanvullen tblRequest incl veld reqId_basis
$ins_tblRequest = "
INSERT INTO tblRequest (code, def, dmcreate, dmmeld, reqId_basis, lidId_demo)
	SELECT code, def, dmcreate, dmmeld, reqId, '".mysqli_real_escape_string($db,$lidId)."'
	FROM tblRequest_basis
	ORDER BY reqId;
";
	mysqli_query($db,$ins_tblRequest) or die(mysqli_error($db));
//echo $ins_tblRequest.'<br>'.'<br>';



  //Aanvullen tblMelding meldId_basis n.v.t. i.v.m. geen foreign key
$ins_tblMelding = "
INSERT INTO tblMelding (reqId, hisId, meldnr, skip, fout)
	SELECT r.reqId, h.hisId, m.meldnr, m.skip, m.fout
	FROM tblMelding_basis m
	 join tblRequest r on (m.reqId = r.reqId_basis)
	 join tblHistorie h on (m.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblUbn u on (st.ubnId = u.ubnId)
	WHERE r.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."' and u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY meldId
";
	mysqli_query($db,$ins_tblMelding) or die(mysqli_error($db));
//echo $ins_tblMelding.'<br>'.'<br>';

}
/********************	Einde Melden	*******************************************************************/
/********************	Reader		*******************************************************************/

  //Aanvullen impReader readId_basis n.v.t. i.v.m. geen foreign key
$ins_impReader = "
INSERT INTO impReader (datum, tijd, levnr_geb, teller, rascode, geslacht, moeder, hokcode, gewicht, col10, col11, moment1, col13, moment2, levnr_uitv, teller_uitv, reden_uitv, levnr_afv, teller_afv, ubn_afv, afvoerkg, levnr_aanv, teller_aanv, ubn_aanv, levnr_sp, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl, teller_ovpl, hok_ovpl, reden_pil, levnr_pil, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, readId, lidId, dmcreate)

	Select datum, tijd, levnr_geb+$plus_levnr, teller, rascode, geslacht, moeder+$plus_levnr, hokcode, gewicht, col10, col11, ru_vm.reduId, col13, ru_vm_t.reduId, levnr_uitv+$plus_levnr, teller_uitv, ru_ui.reduId, levnr_afv+$plus_levnr, teller_afv, ubn_afv, afvoerkg, levnr_aanv+$plus_levnr, teller_aanv, ubn_aanv, levnr_sp+$plus_levnr, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl+$plus_levnr, teller_ovpl, hok_ovpl, ru_pi.reduId, levnr_pil+$plus_levnr, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, null, '".mysqli_real_escape_string($db,$lidId)."', dmcreate
	FROM impReader_basis rd
	 left join tblRedenuser ru_vm on (rd.moment1 = ru_vm.reduId_basis)
	 left join tblRedenuser ru_vm_t on (rd.moment2 = ru_vm_t.reduId_basis)
	 left join tblRedenuser ru_ui on (rd.reden_uitv = ru_ui.reduId_basis)
	 left join tblRedenuser ru_pi on (rd.reden_pil = ru_pi.reduId_basis)
	WHERE (isnull(ru_vm.lidId) or ru_vm.lidId = '".mysqli_real_escape_string($db,$lidId)."')
	 and (isnull(ru_vm_t.lidId) or ru_vm_t.lidId = '".mysqli_real_escape_string($db,$lidId)."')
	 and (isnull(ru_ui.lidId) or ru_ui.lidId = '".mysqli_real_escape_string($db,$lidId)."')
	 and (isnull(ru_pi.lidId) or ru_pi.lidId = '".mysqli_real_escape_string($db,$lidId)."')
";
	mysqli_query($db,$ins_impReader) or die(mysqli_error($db));
//echo $ins_impReader.'<br>'.'<br>';

if($modtech == 0) { // geboren lammeren zonder levensnummer mogen niet voorkomen als de module technisch niet wordt gebruikt
$upd_impReader = "UPDATE impReader SET verwerkt = 1 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(levnr_geb) and teller is not null and isnull(verwerkt) ";
mysqli_query($db,$upd_impReader) or die (mysqli_error($db));
// gespeende lammeren, overplaatsing en medicatie mogen niet voorkomen als de module technisch niet wordt gebruikt

$upd_impReader = "UPDATE impReader SET verwerkt = 1 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and (teller_sp is not null or teller_ovpl is not null or teller_pil is not null) and isnull(verwerkt) ";
mysqli_query($db,$upd_impReader) or die (mysqli_error($db));

}
/********************	Einde Reader	*******************************************************************/
/********************	Financieel		*******************************************************************/

  //Aanvullen tblElementuser incl. veld elemuId_basis
if($modtech == 1 ) { 
$ins_tblElementuser = "
INSERT INTO tblElementuser (elemId, lidId, waarde, actief, elemuId_basis)
	SELECT elemId, '".mysqli_real_escape_string($db,$lidId)."', waarde, actief, elemuId
	FROM tblElementuser_basis
	ORDER BY elemuId
";
	mysqli_query($db,$ins_tblElementuser) or die(mysqli_error($db));
//echo $ins_tblElementuser.'<br>'.'<br>';

$now = DateTime::createFromFormat('U.u', microtime(true));
$jaartal = $now->format("Y");
$maandag52 = date( "j", strtotime($jaartal."W"."52"."1") );
$dag1 = date("d", strtotime("first monday of january $jaartal"));
$monday1 = date( "Y-m-d", strtotime($jaartal."W"."01"."1") );
$day = strtotime($monday1)-(86400*7);

if($maandag52 > 24) { $weken_jaar = 52; } else { $weken_jaar = 53; }
if($dag1 < 05) { $startweek = 1; } else { $startweek = 2; }



for ($i = $startweek ; $i <= $weken_jaar ; $i++){
    
    if($startweek == 2){ $dId = $i-1; } else { $dId = $i; }
        $datum = date('Y-m-d',$day+($i*86400*7)); /*echo '$datum week '.$i.' = '.$datum.'<br>';*/

if($dId < 53) {        
$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', dekat, '".mysqli_real_escape_string($db,$datum)."'
	FROM tblDeklijst_basis
	WHERE dekId = '".mysqli_real_escape_string($db,$dId)."'
";
} else {
	$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dmdek) VALUES ('".mysqli_real_escape_string($db,$lidId)."', '".mysqli_real_escape_string($db,$datum)."')
";
}
       mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
      //  echo 'query inlezen dekId '.$dId.' = '.$ins_tblDeklijst.'<br>';
       }

/*
$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', dekat, dmdek
	FROM tblDeklijst_basis
	ORDER BY dekId
";
	mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
//echo $ins_tblDeklijst.'<br>'.'<br>';
*/

  //Aanvullen tblLiquiditeit veld liqId_basis n.v.t.
$ins_tblLiquiditeit = "
INSERT INTO tblLiquiditeit (rubuId, datum, bedrag)
	SELECT ru.rubuId, DATE_ADD(l.datum, INTERVAL $jaren-1 YEAR), l.bedrag
	FROM tblLiquiditeit_basis l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId_basis)
	WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY liqId
";
	mysqli_query($db,$ins_tblLiquiditeit) or die(mysqli_error($db));
//echo $ins_tblLiquiditeit.'<br>'.'<br>';


  //Aanvullen tblSalber veld salbId_basis n.v.t.
$ins_tblSalber = "
INSERT INTO tblSalber (datum, tbl, tblId, aantal, waarde)
	SELECT DATE_ADD(sb.datum, INTERVAL $jaren YEAR), 'ru' tbl, ru.rubuId tblId, sb.aantal, sb.waarde
	FROM tblSalber_basis sb
	 join tblRubriekuser ru on (sb.tblId = ru.rubuId_basis)
	WHERE sb.tbl = 'ru' and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	
	union
	
	SELECT DATE_ADD(sb.datum, INTERVAL $jaren YEAR), 'eu' tbl, eu.elemuId tblId, sb.aantal, sb.waarde
	FROM tblSalber_basis sb
	 join tblElementuser eu on (sb.tblId = eu.elemuId_basis)
	WHERE sb.tbl = 'eu' and eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY tbl, tblId
";
	mysqli_query($db,$ins_tblSalber) or die(mysqli_error($db));
//echo $ins_tblSalber.'<br>'.'<br>';
}
/********************	Einde Financieel	*******************************************************************/
/************* verwijderd ****************************************************/
  //Aanvullen impRespons respId_basis n.v.t. i.v.m. geen foreign key
/*$ins_impRespons = "
INSERT INTO impRespons (reqId, prod, def, urvo, prvo, melding, relnr, ubn, schaapdm, land, levensnummer, soort, ubn_herk, ubn_best, land_herk, gebdm, sucind, foutind, foutcode, foutmeld, meldnr, respId, dmcreate)	
	SELECT rq.reqId, rs.prod, rs.def, rs.urvo, rs.prvo, rs.melding, rs.relnr, rs.ubn, rs.schaapdm, rs.land, s.levensnummer, rs.soort, rs.ubn_herk, rs.ubn_best, rs.land_herk, rs.gebdm, rs.sucind, rs.foutind, rs.foutcode, rs.foutmeld, rs.meldnr, null, rs.dmcreate
	FROM imprespons_basis rs
	 join tblRequest rq on (rs.reqId = rq.reqId_basis)
	 join tblSchaap_basis sb on (sb.levensnummer = rs.levensnummer)
	 join tblSchaap s on (sb.schaapId = s.schaapId_basis)
	 join tblStal st on (st.schaapId = s.schaapId)
	WHERE rq.lidId_demo = '".mysqli_real_escape_string($db,$lidId)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY rs.respId
";
	//mysqli_query($db,$ins_impRespons) or die(mysqli_error($db));
echo $ins_impRespons.'<br>'.'<br>';*/



  //Aanvullen tblDeklijst veld dekId_basis n.v.t.
/*$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', dekat, dmdek
	FROM tblDeklijst_basis
	ORDER BY dekId
";
	//mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
echo $ins_tblDeklijst.'<br>'.'<br>';*/






  //Aanvullen tblOpgaaf veld dekId_basis n.v.t.
/*$ins_tblOpgaaf = "
INSERT INTO tblOpgaaf (rubuId, datum, bedrag, toel, liq, his, dmcreate)
	SELECT ru.rubuId, datum, bedrag, toel, liq, his, dmcreate
	FROM tblOpgaaf_basis o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId_basis)
	 WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
	ORDER BY opgId
";
	//mysqli_query($db,$ins_tblOpgaaf) or die(mysqli_error($db));
echo $ins_tblOpgaaf.'<br>'.'<br>';*/

/************* Einde verwijderd ****************************************************/

?>