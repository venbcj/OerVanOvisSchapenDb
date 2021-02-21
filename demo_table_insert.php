<?php
/* Aangemaakt : 21-8-2016  Onderstaande statements moeten worden uitgevoerd om voor een bestaande klant maandelijks een demo omgeving te creeëren.
	Wordt ook gebruikt om handmatig de test omgevng te voorzien van nieuwe basisgegevens
18-1-2017 : in tblPeriode doel gewijzigd naar doelId
22-1-2017 : tblBezetting gewijzigd naar tblBezet
5-7-2020 : in tblArtikel en tblArtikel_basis prijs gewijzigd naar perkg, wdgn naar wdgn_v en wdgn_m toegevoegd
*/

echo '<br>'.'<br>';
// AANVULLEN TABELLEN
$plus_levnr = ($lidId-1)*100000000;

// Bepalen modules ja of nee
$module = mysqli_query($db,"select beheer, tech, fin, meld from tblLeden where lidId = ".mysqli_real_escape_string($db,$lidId)."; ") or die (mysqli_error($db));
	while ($mod = mysqli_fetch_assoc($module)) { $modbeheer = $mod['beheer']; $modtech = $mod['tech']; $modfin = $mod['fin']; $modmeld = $mod['meld']; }

/********************	Het schaap deel 1	*******************************************************************/
 //Aanvullen tblSchaap incl veld schaapId_basis als sleutelveld voor andere tabellen (foreign key)
if($modtech == 1 ) {
$ins_tblVolwas = "
INSERT INTO `tblVolwas` (readId, datum, mdrId, vdrId, volwId_basis, lidId_demo)
	select readId, datum, mdrId, vdrId, volwId, ".mysqli_real_escape_string($db,$lidId)."
	from tblVolwas_basis
	order by volwId
";
	mysqli_query($db,$ins_tblVolwas) or die(mysqli_error($db)); 
//echo $ins_tblVolwas.'<br>'.'<br>';

$ins_tblSchaap = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, volwId, mdrId, vdrId, indx, momId, redId, schaapId_basis, lidId_demo)
	select s.levensnummer+$plus_levnr, s.rasId, s.geslacht, v.volwId, s.mdrId, s.vdrId, s.indx,	s.momId, s.redId, s.schaapId, ".mysqli_real_escape_string($db,$lidId)."
	from tblSchaap_basis s
	 left join tblVolwas v on (s.volwId = v.volwId_basis and v.lidId_demo = ".mysqli_real_escape_string($db,$lidId).")
	order by s.schaapId
";
	mysqli_query($db,$ins_tblSchaap) or die(mysqli_error($db)); 
#echo $ins_tblSchaap.'<br>'.'<br>';

//mdrId in tblVolwas aanpassen
$upd_moeder = "
update tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId_basis and mdr.lidId_demo = ".mysqli_real_escape_string($db,$lidId).")
set v.mdrId = mdr.schaapId
";
	mysqli_query($db,$upd_moeder) or die(mysqli_error($db));
//echo $upd_moeder.'<br>'.'<br>';


//vdrId in tblVolwas aanpassen
$upd_vader = "
update tblVolwas v
 join tblSchaap vdr on (v.vdrId = vdr.schaapId_basis and vdr.lidId_demo = ".mysqli_real_escape_string($db,$lidId).")
set v.vdrId = vdr.schaapId
";
	mysqli_query($db,$upd_vader) or die(mysqli_error($db));
//echo $upd_vader.'<br>'.'<br>';

// volwId in tblSchaap aanpassen
$upd_volwId = "
update tblSchaap s 
 join tblVolwas v on (s.volwId = v.volwId_basis and s.lidId_demo = v.lidId_demo)
set s.volwId = v.volwId 
where v.lidId_demo = ".mysqli_real_escape_string($db,$lidId)."
";
	mysqli_query($db,$upd_volwId) or die(mysqli_error($db));
//echo $upd_vader.'<br>'.'<br>';
}

/********************  Afbouw constructie tblSchaap ********************/
  //mdrId in tblSchaap aanpassen
  //select lam.mdrId, mdr.schaapId mdr_new from
$upd_moeder = "
update tblSchaap lam
 join tblSchaap mdr on (lam.mdrId = mdr.schaapId_basis and mdr.lidId_demo = ".mysqli_real_escape_string($db,$lidId).")
 join tblStal st on (lam.schaapId = st.schaapId)
set lam.mdrId = mdr.schaapId
where st.lidId = ".mysqli_real_escape_string($db,$lidId)."
";
	mysqli_query($db,$upd_moeder) or die(mysqli_error($db));
//echo $upd_moeder.'<br>'.'<br>';


  //vdrId in tblSchaap aanpassen
  //select lam.vdrId, vdr.schaapId vdr_new from 
$upd_vader = "
update tblSchaap lam
 join tblSchaap vdr on (lam.vdrId = vdr.schaapId_basis and vdr.lidId_demo = ".mysqli_real_escape_string($db,$lidId).")
 join tblStal st on (lam.schaapId = st.schaapId)
set lam.vdrId = vdr.schaapId
where st.lidId = ".mysqli_real_escape_string($db,$lidId)."
";
	mysqli_query($db,$upd_vader) or die(mysqli_error($db));
//echo $upd_vader.'<br>'.'<br>';
/******************** Einde Afbouw constructie tblSchaap ********************/

if($modtech == 0 ) { // Alleen aanwezige schapen inlezen
$ins_tblSchaap = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, indx, momId, redId, schaapId_basis, lidId_demo)
	select levensnummer+$plus_levnr, rasId, geslacht, indx,	momId, redId, s.schaapId, ".mysqli_real_escape_string($db,$lidId)."
	from tblSchaap_basis s
	 join tblSTal_basis st on (st.schaapId = s.schaapId)
	 join tblHistorie_basis h on (st.stalId = h.stalId)
	where isnull(st.rel_best)
	group by levensnummer+$plus_levnr, rasId, geslacht, indx, momId, redId, s.schaapId
	order by s.schaapId
";
	mysqli_query($db,$ins_tblSchaap) or die(mysqli_error($db)); 
//echo $ins_tblSchaap.'<br>'.'<br>';
}
/********************	Einde Het schaap deel 1	*******************************************************************/
/********************	Stamtabellen	*******************************************************************/

  //Aanvullen tblRasuser incl veld rasuId_basis
$ins_tblRasuser = "
INSERT INTO tblRasuser (lidId, rasId, scan, actief, rasuId_basis)
	select ".mysqli_real_escape_string($db,$lidId).", rasId, scan, actief, rasuId
	from tblRasuser_basis
	order by rasuId
";
	mysqli_query($db,$ins_tblRasuser) or die(mysqli_error($db));
//echo $ins_tblRasuser.'<br>'.'<br>';


  //Aanvullen tblMomentuser incl veld momuId_basis
$ins_tblMomentuser = "
INSERT INTO tblMomentuser (lidId, momId, scan, actief, momuId_basis)
	select ".mysqli_real_escape_string($db,$lidId).", momId, scan, actief, momuId
	from tblMomentuser_basis
	order by momuId
";
	mysqli_query($db,$ins_tblMomentuser) or die(mysqli_error($db));
//echo $ins_tblMomentuser.'<br>'.'<br>';


  //Aanvullen tblRedenuser incl veld reduId_basis
$ins_tblRedenuser = "
INSERT INTO tblRedenuser (redId, lidId, uitval, pil, reduId_basis)
	select redId, ".mysqli_real_escape_string($db,$lidId).", uitval, pil, reduId
	from tblRedenuser_basis
	order by reduId
";
	mysqli_query($db,$ins_tblRedenuser) or die(mysqli_error($db));
//echo $ins_tblRedenuser.'<br>'.'<br>';

  //Aanvullen tblRubriekuser incl veld rubuId_basis
$ins_tblRubriekuser = "
INSERT INTO tblRubriekuser (rubId, lidId, actief, rubuId_basis)
	select rubId, ".mysqli_real_escape_string($db,$lidId).", actief, rubuId
	from tblRubriekuser_basis
	order by rubuId
";
	mysqli_query($db,$ins_tblRubriekuser) or die(mysqli_error($db));
//echo $ins_tblRubriekuser.'<br>'.'<br>';

/********************	Einde Stamtabellen	*******************************************************************/
/********************	Relaties	*******************************************************************/

  //Aanvullen tblPartij incl veld partId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblPartij = "
INSERT INTO tblPartij (lidId, ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId_basis)
	select ".mysqli_real_escape_string($db,$lidId).", ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId
	from tblPartij_basis
	order by partId
";
	mysqli_query($db,$ins_tblPartij) or die(mysqli_error($db));
//echo $ins_tblPartij.'<br>'.'<br>';


  //Aanvullen tblRelatie incl veld relId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblRelatie = "
INSERT INTO tblRelatie (partId, relatie, uitval, actief, relId_basis)
	select p.partId, r.relatie, r.uitval, r.actief, r.relId
	from tblRelatie_basis r
	 join tblPartij p on (r.partId = p.partId_basis)
	where p.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by relId
";
	mysqli_query($db,$ins_tblRelatie) or die(mysqli_error($db));
//echo $ins_tblRelatie.'<br>'.'<br>';


  //Aanvullen tblAdres incl veld adrId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblAdres = "
INSERT INTO tblAdres (relId, straat, nr, pc, plaats, actief, adrId_basis)
	select r.relId, a.straat, a.nr, a.pc, a.plaats, a.actief, a.adrId
	from tblAdres_basis a
	 join tblRelatie r on (a.relId = r.relId_basis)
	 join tblPartij p on (r.partId = p.partId)
	where p.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by adrId
";
	mysqli_query($db,$ins_tblAdres) or die(mysqli_error($db));
//echo $ins_tblAdres.'<br>'.'<br>';
/********************	Einde	Relaties	*******************************************************************/
/********************	Het schaap deel 2	*******************************************************************/
  //Aanvullen tblStal incl veld stalId_basis als sleutelveld voor andere tabellen (foreign key)
$ins_tblStal = "
INSERT INTO tblStal (lidId, schaapId, kleur, halsnr, rel_herk, rel_best, stalId_basis)
	select ".mysqli_real_escape_string($db,$lidId).", s.schaapId, kleur, halsnr, rh.relId, rb.relId, st.stalId
	from tblStal_basis st
	 join tblSchaap s on (st.schaapId = s.schaapId_basis)
	 left join tblRelatie rh on (st.rel_herk = rh.relId_basis)
	 left join tblPartij ph on (ph.partId = rh.partId)
	 left join tblRelatie rb on (st.rel_best = rb.relId_basis)
	 left join tblPartij pb on (pb.partId = rb.partId)
	where s.lidId_demo = ".mysqli_real_escape_string($db,$lidId)." and (isnull(ph.lidId) or ph.lidId = ".mysqli_real_escape_string($db,$lidId).") and (isnull(pb.lidId) or pb.lidId = ".mysqli_real_escape_string($db,$lidId).")
	order by st.stalId
";
	mysqli_query($db,$ins_tblStal) or die(mysqli_error($db));
//echo $ins_tblStal.'<br>'.'<br>';

if($modtech == 0 ) {
  //Aanvullen tblHistorie incl veld hisId_basis
$ins_tblHistorie = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
	select st.stalId, h.datum, NULL, h.actId, h.skip, h.hisId
	from tblHistorie_basis h
	 join tblStal st on (h.stalId = st.stalId_basis)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and (h.actId = 1 or h.actId = 2 or h.actId = 3 or h.actId = 10 or h.actId = 11 or h.actId = 12 or h.actId = 13 or h.actId = 14)
	order by h.hisId	
";
	mysqli_query($db,$ins_tblHistorie) or die(mysqli_error($db));
//echo $ins_tblHistorie.'<br>'.'<br>';
}

if($modtech == 1 ) {
  //Aanvullen tblHistorie incl veld hisId_basis
$ins_tblHistorie = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
	select st.stalId, h.datum, h.kg, h.actId, h.skip, h.hisId
	from tblHistorie_basis h
	 join tblStal st on (h.stalId = st.stalId_basis)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by h.hisId	
";
	mysqli_query($db,$ins_tblHistorie) or die(mysqli_error($db));
//echo $ins_tblHistorie.'<br>'.'<br>';
}
/********************	Einde Het schaap deel 2	*******************************************************************/
/********************	Voorraadbeheer		*******************************************************************/
  //Aanvullen tblEenheiduser incl veld enhuId_basis
$ins_tblEenheiduser = "
INSERT INTO tblEenheiduser (lidId, eenhId, actief, enhuId_basis)
	select ".mysqli_real_escape_string($db,$lidId).", eenhId, actief, enhuId
	from tblEenheiduser_basis
	order by enhuId
";
	mysqli_query($db,$ins_tblEenheiduser) or die(mysqli_error($db));
//echo $ins_tblEenheiduser.'<br>'.'<br>';

if($modtech == 1 ) {

  //Aanvullen tblArtikel incl veld artId_basis
$ins_tblArtikel = "
INSERT INTO tblArtikel (soort, naam, stdat, enhuId, perkg, btw, regnr, relId, wdgn_v, wdgn_m, rubuId, actief, artId_basis)
	select art.soort, art.naam, art.stdat, eu.enhuId, art.perkg, art.btw, art.regnr, r.relId, art.wdgn_v, art.wdgn_m, ru.rubuId, art.actief, art.artId
	from tblArtikel_basis art
	 join tblEenheiduser eu on (art.enhuId = eu.enhuId_basis)
	 join tblRelatie r on (art.relId = r.relId_basis)
	 join tblPartij p on (p.partId = r.partId)
	 left join tblRubriekuser ru on (art.rubuId = ru.rubuId_basis)
	where eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 and p.lidId = ".mysqli_real_escape_string($db,$lidId)."
	 and (isnull(ru.lidId) or ru.lidId = ".mysqli_real_escape_string($db,$lidId).")
	order by artId
";
	mysqli_query($db,$ins_tblArtikel) or die(mysqli_error($db));
//echo $ins_tblArtikel.'<br>'.'<br>';



  //Aanvullen tblInkoop incl veld inkId_basis
$ins_tblInkoop = " 
INSERT INTO tblInkoop (dmink, artId, charge, dmvval, inkat, enhuId, prijs, btw, relId, inkId_basis)
	select ink.dmink, art.artId, ink.charge, ink.dmvval, ink.inkat, eu.enhuId, ink.prijs, ink.btw, r.relId, ink.inkId
	from tblInkoop_basis ink
	 join tblArtikel art on (ink.artId = art.artId_basis)
	 join tblEenheiduser eu_art on (art.enhuId = eu_art.enhuId)
	 join tblEenheiduser eu on (ink.enhuId = eu.enhuId_basis)
	 join tblRelatie r on (ink.relId = r.relId_basis)
	 join tblPartij p on (p.partId = r.partId)
	where eu_art.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by inkId
";
	mysqli_query($db,$ins_tblInkoop) or die(mysqli_error($db));
//echo $ins_tblInkoop.'<br>'.'<br>';

  //Aanvullen tblNuttig veld nutId_basis n.v.t. 
$ins_tblNuttig = "
INSERT INTO tblNuttig (hisId, inkId, nutat, stdat, reduId)
	select h.hisId, i.inkId, n.nutat, n.stdat, ru.reduId
	from tblNuttig_basis n
	join tblHistorie h on (n.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblInkoop i on (n.inkId = i.inkId_basis)
	 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
	 join tblRedenuser ru on (n.reduId = ru.reduId_basis)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by nutId
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
	select ".mysqli_real_escape_string($db,$lidId).", h.hoknr, h.scan, h.actief, h.hokId
	from tblHok_basis h
	order by h.hokId
";
	mysqli_query($db,$ins_tblHok) or die(mysqli_error($db));
//echo $ins_tblHok.'<br>'.'<br>';


  //Aanvullen tblPeriode incl veld periId_basis
$ins_tblPeriode = "
INSERT INTO tblPeriode (hokId, doelId, dmafsluit, periId_basis)
	select h.hokId, p.doelId, p.dmafsluit, p.periId
	from tblPeriode_basis p
	 join tblHok h on (h.hokId_basis = p.hokId)
	where h.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by p.periId
";
	mysqli_query($db,$ins_tblPeriode) or die(mysqli_error($db));
//echo $ins_tblPeriode.'<br>'.'<br>';


  //Aanvullen tblBezet  bezId_basis n.v.t. i.v.m. geen foreign key
$ins_tblBezet = "
INSERT INTO tblBezet (periId, hisId, hokId)
	select p.periId, h.hisId, ho.hokId
	from tblBezet_basis b
	 left join tblHok ho on (ho.hokId_basis = b.hokId)
	 left join tblPeriode p on (b.periId = p.periId_basis and p.hokId = ho.hokId)
	 join tblHistorie h on (b.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)	
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and ho.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by b.bezId
";
	mysqli_query($db,$ins_tblBezet) or die(mysqli_error($db));
//echo $ins_tblBezet.'<br>'.'<br>';

  //Aanvullen tblVoeding veld voedId_basis n.v.t. 
$ins_tblVoeding = "
INSERT INTO tblVoeding (periId, inkId, nutat, stdat)
	select p.periId, i.inkId, nutat, stdat
	from tblVoeding_basis v
	join tblPeriode p on (v.periId = p.periId_basis)
	 join tblHok ho on (ho.hokId = p.hokId)
	 join tblInkoop i on (v.inkId = i.inkId_basis)
	 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
	where ho.lidId = ".mysqli_real_escape_string($db,$lidId)." and eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by voedId
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
	select code, def, dmcreate, dmmeld, reqId, ".mysqli_real_escape_string($db,$lidId)."
	from tblRequest_basis
	order by reqId;
";
	mysqli_query($db,$ins_tblRequest) or die(mysqli_error($db));
//echo $ins_tblRequest.'<br>'.'<br>';



  //Aanvullen tblMelding meldId_basis n.v.t. i.v.m. geen foreign key
$ins_tblMelding = "
INSERT INTO tblMelding (reqId, hisId, meldnr, skip, fout)
	select r.reqId, h.hisId, m.meldnr, m.skip, m.fout
	from tblMelding_basis m
	 join tblRequest r on (m.reqId = r.reqId_basis)
	 join tblHistorie h on (m.hisId = h.hisId_basis)
	 join tblStal st on (st.stalId = h.stalId)
	where r.lidId_demo = ".mysqli_real_escape_string($db,$lidId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by meldId
";
	mysqli_query($db,$ins_tblMelding) or die(mysqli_error($db));
//echo $ins_tblMelding.'<br>'.'<br>';

}
/********************	Einde Melden	*******************************************************************/
/********************	Reader		*******************************************************************/

  //Aanvullen impReader readId_basis n.v.t. i.v.m. geen foreign key
$ins_impReader = "
INSERT INTO impReader (datum, tijd, levnr_geb, teller, rascode, geslacht, moeder, hokcode, gewicht, col10, col11, moment1, col13, moment2, levnr_uitv, teller_uitv, reden_uitv, levnr_afv, teller_afv, ubn_afv, afvoerkg, levnr_aanv, teller_aanv, ubn_aanv, levnr_sp, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl, teller_ovpl, hok_ovpl, reden_pil, levnr_pil, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, readId, lidId, dmcreate)

	Select datum, tijd, levnr_geb+$plus_levnr, teller, rascode, geslacht, moeder+$plus_levnr, hokcode, gewicht, col10, col11, ru_vm.reduId, col13, ru_vm_t.reduId, levnr_uitv+$plus_levnr, teller_uitv, ru_ui.reduId, levnr_afv+$plus_levnr, teller_afv, ubn_afv, afvoerkg, levnr_aanv+$plus_levnr, teller_aanv, ubn_aanv, levnr_sp+$plus_levnr, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl+$plus_levnr, teller_ovpl, hok_ovpl, ru_pi.reduId, levnr_pil+$plus_levnr, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, null, ".mysqli_real_escape_string($db,$lidId).", dmcreate
	from impReader_basis rd
	 left join tblRedenuser ru_vm on (rd.moment1 = ru_vm.reduId_basis)
	 left join tblRedenuser ru_vm_t on (rd.moment2 = ru_vm_t.reduId_basis)
	 left join tblRedenuser ru_ui on (rd.reden_uitv = ru_ui.reduId_basis)
	 left join tblRedenuser ru_pi on (rd.reden_pil = ru_pi.reduId_basis)
	where (isnull(ru_vm.lidId) or ru_vm.lidId = ".mysqli_real_escape_string($db,$lidId).")
	 and (isnull(ru_vm_t.lidId) or ru_vm_t.lidId = ".mysqli_real_escape_string($db,$lidId).")
	 and (isnull(ru_ui.lidId) or ru_ui.lidId = ".mysqli_real_escape_string($db,$lidId).")
	 and (isnull(ru_pi.lidId) or ru_pi.lidId = ".mysqli_real_escape_string($db,$lidId).")
";
	mysqli_query($db,$ins_impReader) or die(mysqli_error($db));
//echo $ins_impReader.'<br>'.'<br>';

if($modtech == 0) { // geboren lammeren zonder levensnummer mogen niet voorkomen als de module technisch niet wordt gebruikt
$upd_impReader = "UPDATE impReader SET verwerkt = 1 
where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(levnr_geb) and teller is not null and isnull(verwerkt) ";
mysqli_query($db,$upd_impReader) or die (mysqli_error($db));
// gespeende lammeren, overplaatsing en medicatie mogen niet voorkomen als de module technisch niet wordt gebruikt

$upd_impReader = "UPDATE impReader SET verwerkt = 1 
where lidId = ".mysqli_real_escape_string($db,$lidId)." and (teller_sp is not null or teller_ovpl is not null or teller_pil is not null) and isnull(verwerkt) ";
mysqli_query($db,$upd_impReader) or die (mysqli_error($db));

}
/********************	Einde Reader	*******************************************************************/
/********************	Financieel		*******************************************************************/

  //Aanvullen tblElementuser incl. veld elemuId_basis
if($modtech == 1 ) { 
$ins_tblElementuser = "
INSERT INTO tblElementuser (elemId, lidId, waarde, actief, elemuId_basis)
	select elemId, ".mysqli_real_escape_string($db,$lidId).", waarde, actief, elemuId
	from tblElementuser_basis
	order by elemuId
";
	mysqli_query($db,$ins_tblElementuser) or die(mysqli_error($db));
//echo $ins_tblElementuser.'<br>'.'<br>';

$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	select ".mysqli_real_escape_string($db,$lidId).", dekat, dmdek
	from tblDeklijst_basis
	order by dekId
";
	mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
//echo $ins_tblDeklijst.'<br>'.'<br>';

  //Aanvullen tblLiquiditeit veld liqId_basis n.v.t.
$ins_tblLiquiditeit = "
INSERT INTO tblLiquiditeit (rubuId, datum, bedrag)
	select ru.rubuId, l.datum, l.bedrag
	from tblLiquiditeit_basis l
	 join tblRubriekuser ru on (l.rubuId = ru.rubuId_basis)
	where ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by liqId
";
	mysqli_query($db,$ins_tblLiquiditeit) or die(mysqli_error($db));
//echo $ins_tblLiquiditeit.'<br>'.'<br>';


  //Aanvullen tblSalber veld salbId_basis n.v.t.
$ins_tblSalber = "
INSERT INTO tblSalber (datum, tbl, tblId, aantal, waarde)
	select sb.datum, 'ru' tbl, ru.rubuId tblId, sb.aantal, sb.waarde
	from tblSalber_basis sb
	 join tblRubriekuser ru on (sb.tblId = ru.rubuId_basis)
	where sb.tbl = 'ru' and ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
	
	union
	
	select sb.datum, 'eu' tbl, eu.elemuId tblId, sb.aantal, sb.waarde
	from tblSalber_basis sb
	 join tblElementuser eu on (sb.tblId = eu.elemuId_basis)
	where sb.tbl = 'eu' and eu.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by tbl, tblId
";
	mysqli_query($db,$ins_tblSalber) or die(mysqli_error($db));
//echo $ins_tblSalber.'<br>'.'<br>';
}
/********************	Einde Financieel	*******************************************************************/
/************* verwijderd ****************************************************/
  //Aanvullen impRespons respId_basis n.v.t. i.v.m. geen foreign key
/*$ins_impRespons = "
INSERT INTO impRespons (reqId, prod, def, urvo, prvo, melding, relnr, ubn, schaapdm, land, levensnummer, soort, ubn_herk, ubn_best, land_herk, gebdm, sucind, foutind, foutcode, foutmeld, meldnr, respId, dmcreate)	
	select rq.reqId, rs.prod, rs.def, rs.urvo, rs.prvo, rs.melding, rs.relnr, rs.ubn, rs.schaapdm, rs.land, s.levensnummer, rs.soort, rs.ubn_herk, rs.ubn_best, rs.land_herk, rs.gebdm, rs.sucind, rs.foutind, rs.foutcode, rs.foutmeld, rs.meldnr, null, rs.dmcreate
	from imprespons_basis rs
	 join tblRequest rq on (rs.reqId = rq.reqId_basis)
	 join tblSchaap_basis sb on (sb.levensnummer = rs.levensnummer)
	 join tblSchaap s on (sb.schaapId = s.schaapId_basis)
	 join tblStal st on (st.schaapId = s.schaapId)
	where rq.lidId_demo = ".mysqli_real_escape_string($db,$lidId)." and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
order by rs.respId
";
	//mysqli_query($db,$ins_impRespons) or die(mysqli_error($db));
echo $ins_impRespons.'<br>'.'<br>';*/



  //Aanvullen tblDeklijst veld dekId_basis n.v.t.
/*$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	select ".mysqli_real_escape_string($db,$lidId).", dekat, dmdek
	from tblDeklijst_basis
	order by dekId
";
	//mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
echo $ins_tblDeklijst.'<br>'.'<br>';*/






  //Aanvullen tblOpgaaf veld dekId_basis n.v.t.
/*$ins_tblOpgaaf = "
INSERT INTO tblOpgaaf (rubuId, datum, bedrag, toel, liq, his, dmcreate)
	select ru.rubuId, datum, bedrag, toel, liq, his, dmcreate
	from tblOpgaaf_basis o
	 join tblRubriekuser ru on (o.rubuId = ru.rubuId_basis)
	 where ru.lidId = ".mysqli_real_escape_string($db,$lidId)."
	order by opgId
";
	//mysqli_query($db,$ins_tblOpgaaf) or die(mysqli_error($db));
echo $ins_tblOpgaaf.'<br>'.'<br>';*/

/************* Einde verwijderd ****************************************************/

?>