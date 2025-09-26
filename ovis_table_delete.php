<?php
/* 4-11-2023 gekopieerd van demo_table_delete.php om oude gebruikers te verwijderen
*/

// VERWIJDEREN RECORDS
/********************	Voorraadbeheer	*******************************************************************/

include"connect_db.php";

$delete_lidId = 2;

//tblNuttig
$zoek_NutId = mysqli_query($db,"select n.nutId
from tblNuttig n
 join tblHistorie h on (h.hisId = n.hisId)
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by n.nutId
") or die (mysqli_error($db));
//mysqli_query($db,$upd_tblNuttig) or die (mysqli_error($db));

//mysqli_query($db,"delete from tblNuttig where `delete` = 1 ") or die (mysqli_error($db));

$nutId = array();
while( $nut = mysqli_fetch_assoc($zoek_NutId)) { $nutId[] = $nut['nutId'];  
	
$nutIds = implode(',',$nutId);
	}
	if(isset($nutIds)) {
	$del_tblNuttig = "delete from `tblNuttig` where nutId IN (".mysqli_real_escape_string($db,$nutIds).") ; ";
	#echo $del_tblNuttig.'<br>';
	mysqli_query($db,$del_tblNuttig);
	}

//Einde tblNuttig


//tblInkoop
$zoek_inkId = mysqli_query($db,"select i.inkId
from tblInkoop i
 join tblArtikel a on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
where eu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by i.inkId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblInkoop) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblInkoop where `delete` = 1 ") or die (mysqli_error($db));

$inkId = array();
while( $ink = mysqli_fetch_assoc($zoek_inkId)) { $inkId[] = $ink['inkId'];  
	
$inkIds = implode(',',$inkId);
	}
	if(isset($inkIds)) {
	$del_tblInkoop = "delete from `tblInkoop` where inkId IN (".mysqli_real_escape_string($db,$inkIds).") ; ";
	#echo $del_tblInkoop.'<br>';
	mysqli_query($db,$del_tblInkoop);
	}
//Einde tblInkoop
//tblArtikel
$zoek_artId = mysqli_query($db,"select a.artId
from tblArtikel a
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
where eu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by a.artId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblArtikel) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblArtikel where `delete` = 1 ") or die (mysqli_error($db));

$artId = array();
while( $art = mysqli_fetch_assoc($zoek_artId)) { $artId[] = $art['artId'];  
	
$artIds = implode(',',$artId);
	}
	if(isset($artIds)) {
	$del_tblArtikel = "delete from `tblArtikel` where artId IN (".mysqli_real_escape_string($db,$artIds).") ; ";
	#echo $del_tblArtikel.'<br>';
	mysqli_query($db,$del_tblArtikel);
	}
//Einde tblArtikel
//tblEenheiduser
$zoek_enhuId = mysqli_query($db,"select eu.enhuId
from tblEenheiduser eu
where eu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblEenheiduser) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblEenheiduser where `delete` = 1 ") or die (mysqli_error($db));

$enhuId = array();
while( $enhu = mysqli_fetch_assoc($zoek_enhuId)) { $enhuId[] = $enhu['enhuId'];  
	
$enhuIds = implode(',',$enhuId);
	}
	if(isset($enhuIds)) {
	$del_tblEenheiduser = "delete from `tblEenheiduser` where enhuId IN (".mysqli_real_escape_string($db,$enhuIds).") ; ";
	#echo $del_tblEenheiduser.'<br>';
	mysqli_query($db,$del_tblEenheiduser);
	}
//Einde tblEenheiduser
/********************	Einde Voorraadbeheer	*******************************************************************/
/********************	Melden	*******************************************************************/

//tblRequest
$zoek_reqId = mysqli_query($db,"select r.reqId
from tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
group by r.reqId
order by r.reqId
") or die (mysqli_error($db));
//group by r.reqId
#mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblRequest where `delete` = 1 ") or die (mysqli_error($db));

$reqId = array();
while( $req = mysqli_fetch_assoc($zoek_reqId)) { $reqId[] = $req['reqId'];  

$reqIds = implode(',',$reqId);
	}
	if(isset($reqIds)) {
	$del_tblRequest = "delete from `tblRequest` where reqId IN (".mysqli_real_escape_string($db,$reqIds).") ; ";
	#echo $del_tblRequest.'<br>'.'<br>';
	mysqli_query($db,$del_tblRequest);
	}
//Einde tblRequest

//tblMelding
$zoek_meldId = mysqli_query($db,"select m.meldId
from tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by m.meldId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblMelding where `delete` = 1 ") or die (mysqli_error($db));

$meldId = array();
while( $meld = mysqli_fetch_assoc($zoek_meldId)) { $meldId[] = $meld['meldId'];  

$meldIds = implode(',',$meldId);
	}
	if(isset($meldIds)) {
	$del_tblMelding = "delete from `tblMelding` where meldId IN (".mysqli_real_escape_string($db,$meldIds).") ; ";
	#echo $del_tblMelding.'<br>';
	mysqli_query($db,$del_tblMelding);
	}

//Einde tblMelding
/********************	Einde Melden	*******************************************************************/
/********************	Het schaap		*******************************************************************/

//tblVolwas 

$zoek_volwId = mysqli_query($db,"select v.volwId
from tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
 join tblStal st on (s.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by v.volwId
") or die (mysqli_error($db));

//echo $upd_tblVolwas;
#mysqli_query($db,$upd_tblVolwas) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblVolwas where `delete` = 1 ") or die (mysqli_error($db));

$volwId = array();
while( $volwas = mysqli_fetch_assoc($zoek_volwId)) { $volwId[] = $volwas['volwId'];  

$volwIds = implode(',',$volwId);
	}
	if(isset($volwIds)) {
	$del_tblVolwas = "delete from `tblVolwas` where volwId IN (".mysqli_real_escape_string($db,$volwIds).") ; ";
	#echo $del_tblVolwas.'<br>';
	mysqli_query($db,$del_tblVolwas);
	}
//Einde tblVolwas


//tblSchaap
$zoek_schaapId = mysqli_query($db,"select s.schaapId
from tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by s.schaapId
") or die (mysqli_error($db));
#mysqli_query($db,$zoek_tblSchaap) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblSchaap where `delete` = 1 ") or die (mysqli_error($db));

$schaapId = array();
while( $schaap = mysqli_fetch_assoc($zoek_schaapId)) { $schaapId[] = $schaap['schaapId'];  
	
$schaapIds = implode(',',$schaapId);
	}
	if(isset($schaapIds)) {
	$del_tblSchaap = "delete from `tblSchaap` where schaapId IN (".mysqli_real_escape_string($db,$schaapIds).") ; ";
	#echo $del_tblSchaap.'<br>';
	mysqli_query($db,$del_tblSchaap);
	}
//Einde tblSchaap

//tblHistorie
$zoek_hisId = mysqli_query($db,"select h.hisId
from tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by h.hisId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblHistorie) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblHistorie where `delete` = 1 ") or die (mysqli_error($db));

$hisId = array();
while( $his = mysqli_fetch_assoc($zoek_hisId)) { $hisId[] = $his['hisId'];  
	
$hisIds = implode(',',$hisId);
	}
	if(isset($hisIds)) {
	$del_tblHistorie = "delete from `tblHistorie` where hisId IN (".mysqli_real_escape_string($db,$hisIds).") ; ";
	#echo $del_tblHistorie.'<br>';
	mysqli_query($db,$del_tblHistorie);
	}
//Einde tblHistorie

//tblStal
$zoek_stalId = mysqli_query($db,"select st.stalId
from tblStal st
where st.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by st.stalId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));

$stalId = array();
while( $stal = mysqli_fetch_assoc($zoek_stalId)) { $stalId[] = $stal['stalId'];  
	
$stalIds = implode(',',$stalId);
	}
	if(isset($stalIds)) {
	$del_tblStal = "delete from `tblStal` where stalId IN (".mysqli_real_escape_string($db,$stalIds).") ; ";
	#echo $del_tblStal.'<br>';
	mysqli_query($db,$del_tblStal);
	}
//Einde tblStal

// Dieren die niet zijn gekoppeld aan een stalId verwijderen. Dit kan bij inlezen dracht vaderdieren zijn. Wordt niet verwijderd als dit dier bij anderen ook voorkomt in impReader => dracht. Zie not exists 
//tblSchaap
$zoek_schaapId_dracht = mysqli_query($db,"
select s.schaapId
from tblSchaap s
 join impReader r on (r.levnr_ovpl = s.levensnummer)
 left join tblStal st on (s.schaapId = st.schaapId)
where r.lidId = ".mysqli_real_escape_string($db,$delete_lidId)." and isnull(st.stalId) and isnull(teller_ovpl)
 and not exists (
	select rd.levnr_ovpl
	from impReader rd
	where s.levensnummer = rd.levnr_ovpl and isnull(rd.teller_ovpl) and rd.lidId <> ".mysqli_real_escape_string($db,$delete_lidId)."
	)
group by s.schaapId
order by s.schaapId
") or die (mysqli_error($db));
#mysqli_query($db,$zoek_tblSchaap) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblSchaap where `delete` = 1 ") or die (mysqli_error($db));

$schaapId_dracht = array();
while( $sch = mysqli_fetch_assoc($zoek_schaapId_dracht)) { $schaapId_dracht[] = $sch['schaapId'];

$schaapIds_dracht = implode(',',$schaapId_dracht);
	}
	if(isset($schaapIds_dracht)) {
	$del_tblSchaap = "delete from `tblSchaap` where schaapId IN (".mysqli_real_escape_string($db,$schaapIds_dracht).") ; ";
	#echo $del_tblSchaap.'<br>';
	mysqli_query($db,$del_tblSchaap);
	}
//Einde tblSchaap
/********************	Einde Het schaap	*******************************************************************/
/********************	Reader		*******************************************************************/
// Dracht uit tabel tblVolwas halen. Dit is het restant uit tabel tblVolwas nadat de schapen zijn verwijderd hierboven.
//tblVolwas
$zoek_volwId = mysqli_query($db,"select v.volwId
from tblVolwas v
 join impReader r on (v.readId = r.readId)
where r.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by v.volwId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblVolwas) or die (mysqli_error($db));

$volwId = array();
while( $volw = mysqli_fetch_assoc($zoek_volwId)) { $volwId[] = $volw['volwId'];  
	
$volwIds = implode(',',$volwId);
	}
	if(isset($volwIds)) {
	$del_tblVolwas = "delete from `tblVolwas` where volwId IN (".mysqli_real_escape_string($db,$volwIds).") ; ";
	#echo $del_tblVolwas.'<br>';
	mysqli_query($db,$del_tblVolwas);
	}
//Einde tblVolwas

//impReader
$del_impReader = "delete from ".$dtb.".`impReader` where lidId = ".mysqli_real_escape_string($db,$delete_lidId)." ; ";
	mysqli_query($db,$del_impReader);
/********************	Einde Reader	*******************************************************************/
/********************	Relaties		*******************************************************************/
//tblPersoon
$zoek_persId = mysqli_query($db,"select ps.persId
from tblPersoon ps
 join tblPartij p on (p.partId = ps.partId)
where p.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by ps.persId 
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPersoon) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblPersoon where `delete` = 1 ") or die (mysqli_error($db));

$persId = array();
while( $pers = mysqli_fetch_assoc($zoek_persId)) { $persId[] = $pers['persId'];  
	
$persIds = implode(',',$persId);
	}
	if(isset($persIds)) {
	$del_tblPersoon = "delete from `tblPersoon` where persId IN (".mysqli_real_escape_string($db,$persIds).") ; ";
	#echo $del_tblPersoon.'<br>';
	mysqli_query($db,$del_tblPersoon);
	}
//Einde tblPersoon
//tblVervoer
$zoek_vervId = mysqli_query($db,"select v.vervId
from tblVervoer v
 join tblPartij p on (p.partId = v.partId)
where p.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by v.vervId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblVervoer) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblVervoer where `delete` = 1 ") or die (mysqli_error($db));

$vervId = array();
while( $verv = mysqli_fetch_assoc($zoek_vervId)) { $vervId[] = $verv['vervId'];  
	
$vervIds = implode(',',$vervId);
	}
	if(isset($vervIds)) {
	$del_tblVervoer = "delete from `tblVervoer` where vervId IN (".mysqli_real_escape_string($db,$vervIds).") ; ";
	#echo $del_tblVervoer.'<br>';
	mysqli_query($db,$del_tblVervoer);
	}
//Einde tblVervoer
//tblAdres
$zoek_adrId = mysqli_query($db,"select a.adrId
from tblAdres a
 join tblRelatie r on (a.relId = r.relId)
 join tblPartij p on (p.partId = r.partId)
where p.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by a.adrId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblAdres) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblAdres where `delete` = 1 ") or die (mysqli_error($db));

$adrId = array();
while( $adr = mysqli_fetch_assoc($zoek_adrId)) { $adrId[] = $adr['adrId'];  

$adrIds = implode(',',$adrId);
	}
	if(isset($adrIds)) {
	$del_tblAdres = "delete from `tblAdres` where adrId IN (".mysqli_real_escape_string($db,$adrIds).") ; ";
	#echo $del_tblAdres.'<br>';
	mysqli_query($db,$del_tblAdres);
	}
//Einde tblAdres
//tblRelatie
$zoek_relId = mysqli_query($db,"select r.relId
from tblRelatie r
 join tblPartij p on (p.partId = r.partId)
where p.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by r.relId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblRelatie) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblRelatie where `delete` = 1 ") or die (mysqli_error($db));

$relId = array();
while( $rel = mysqli_fetch_assoc($zoek_relId)) { $relId[] = $rel['relId'];
	
$relIds = implode(',',$relId);
	}
	if(isset($relIds)) {
	$del_tblRelatie = "delete from `tblRelatie` where relId IN (".mysqli_real_escape_string($db,$relIds).") ; ";
	#echo $del_tblRelatie.'<br>';
	mysqli_query($db,$del_tblRelatie);
	}
//Einde tblRelatie
//tblPartij
$zoek_partId = mysqli_query($db,"select p.partId
from tblPartij p
where p.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by p.partId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPartij) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblPartij where `delete` = 1 ") or die (mysqli_error($db));

$partId = array();
while( $part = mysqli_fetch_assoc($zoek_partId)) { $partId[] = $part['partId'];  
	
$partIds = implode(',',$partId);
	}
	if(isset($partIds)) {
	$del_tblPartij = "delete from `tblPartij` where partId IN (".mysqli_real_escape_string($db,$partIds).") ; ";
	#echo $del_tblPartij.'<br>';
	mysqli_query($db,$del_tblPartij);
	}
//Einde tblPartij
/********************	Einde Relaties	*******************************************************************/
/********************	Hokken		*******************************************************************/


//tblBezet
$zoek_bezId = mysqli_query($db,"select b.bezId
from tblBezet b
 join tblPeriode p on (b.periId = p.periId)
 join tblHok h on (p.hokId = h.hokId)
where h.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by b.bezId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblBezet) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblBezet where `delete` = 1 ") or die (mysqli_error($db));

$bezId = array();
while( $bez = mysqli_fetch_assoc($zoek_bezId)) { $bezId[] = $bez['bezId'];  
	
$bezIds = implode(',',$bezId);
	}
	if(isset($bezIds)) {
	$del_tblBezet = "delete from `tblBezet` where bezId IN (".mysqli_real_escape_string($db,$bezIds).") ; ";
	#echo $del_tblBezet.'<br>';
	mysqli_query($db,$del_tblBezet);
	}
//Einde tblBezet
//tblVoeding
$zoek_voedId = mysqli_query($db,"select v.voedId
from tblVoeding v
 join tblPeriode p on (v.periId = p.periId)
 join tblHok h on (p.hokId = h.hokId)
where h.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by v.voedId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblVoeding) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblVoeding where `delete` = 1 ") or die (mysqli_error($db));

$voedId = array();
while( $voed = mysqli_fetch_assoc($zoek_voedId)) { $voedId[] = $voed['voedId'];  
	
$voedIds = implode(',',$voedId);
	}
	if(isset($voedIds)) {
	$del_tblVoeding = "delete from `tblVoeding` where voedId IN (".mysqli_real_escape_string($db,$voedIds).") ; ";
	#echo $del_tblVoeding.'<br>'.'<br>';
	mysqli_query($db,$del_tblVoeding);
	}
//Einde tblVoeding
//tblPeriode
$zoek_periId = mysqli_query($db,"select p.periId
from tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
where h.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by p.periId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblPeriode) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblPeriode where `delete` = 1 ") or die (mysqli_error($db));

$periId = array();
while( $peri = mysqli_fetch_assoc($zoek_periId)) { $periId[] = $peri['periId'];  
	
$periIds = implode(',',$periId);
	}
	if(isset($periIds)) {
	$del_tblPeriode = "delete from `tblPeriode` where periId IN (".mysqli_real_escape_string($db,$periIds).") ; ";
	#echo $del_tblPeriode.'<br>';
	mysqli_query($db,$del_tblPeriode);
	}
//Einde tblPeriode
//tblHok
$zoek_hokId = mysqli_query($db,"select h.hokId
from tblHok h
where h.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by h.hokId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblHok) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblHok where `delete` = 1 ") or die (mysqli_error($db));

$hokId = array();
while( $hok = mysqli_fetch_assoc($zoek_hokId)) { $hokId[] = $hok['hokId'];  
	
$hokIds = implode(',',$hokId);
	}
	if(isset($hokIds)) {
	$del_tblHok = "delete from `tblHok` where hokId IN (".mysqli_real_escape_string($db,$hokIds).") ; ";
	#echo $del_tblHok.'<br>';
	mysqli_query($db,$del_tblHok);
	}
//Einde tblHok
/********************	Einde Hokken	*******************************************************************/
/********************	Financieel		*******************************************************************/

//tblLiquiditeit
$zoek_liqId = mysqli_query($db,"select l.liqId
from tblLiquiditeit l
 join tblRubriekuser ru on (ru.rubuId = l.rubuId)
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by l.liqId
") or die (mysqli_error($db));
//order by liqId
#mysqli_query($db,$upd_tblLiquiditeit) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblLiquiditeit where `delete` = 1 ") or die (mysqli_error($db));

$liqId = array();
while( $liq = mysqli_fetch_assoc($zoek_liqId)) { $liqId[] = $liq['liqId'];  
	
$liqIds = implode(',',$liqId);
	}
	if(isset($liqIds)) {
	$del_tblLiquiditeit = "delete from `tblLiquiditeit` where liqId IN (".mysqli_real_escape_string($db,$liqIds).") ; ";
	#echo $del_tblLiquiditeit.'<br>';
	mysqli_query($db,$del_tblLiquiditeit);//Einde tblLiquiditeit
	}
//tblOpgaaf
$zoek_opgId = mysqli_query($db,"select o.opgId
from tblOpgaaf o
 join tblRubriekuser ru on (ru.rubuId = o.rubuId)
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by o.opgId
") or die (mysqli_error($db));
//order by opgId
#mysqli_query($db,$upd_tblOpgaaf) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblOpgaaf where `delete` = 1 ") or die (mysqli_error($db));

$opgId = array();
while( $opg = mysqli_fetch_assoc($zoek_opgId)) { $opgId[] = $opg['opgId'];  

$opgIds = implode(',',$opgId);
	}
	if(isset($opgIds)) {
	$del_tblOpgaaf = "delete from `tblOpgaaf` where opgId IN (".mysqli_real_escape_string($db,$opgIds).") ; ";
	#echo $del_tblOpgaaf.'<br>';
	mysqli_query($db,$del_tblOpgaaf);
	}
//Einde tblOpgaaf
//tblSalber
$zoek_salbId = mysqli_query($db,"
select sb.salbId
from tblSalber sb
 join tblRubriekuser ru on (ru.rubuId = sb.tblId)
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)." and tbl = 'ru'

Union

select sb.salbId
from tblSalber sb
 join tblElementuser eu on (eu.elemuId = sb.tblId)
where eu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)." and tbl = 'eu'
order by salbId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblSalber) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblSalber where `delete` = 1 ") or die (mysqli_error($db));

$salbId = array();
while( $opg = mysqli_fetch_assoc($zoek_salbId)) { $salbId[] = $opg['salbId'];  

$salbIds = implode(',',$salbId);
	}
	if(isset($salbIds)) {
	$del_tblSalber = "delete from `tblSalber` where salbId IN (".mysqli_real_escape_string($db,$salbIds).") ; ";
	#echo $del_tblSalber.'<br>';
	mysqli_query($db,$del_tblSalber);
	}
//Einde tblSalber

//tblDeklijst
$zoek_dekId = mysqli_query($db,"select d.dekId
from tblDeklijst d
where d.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by d.dekId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblDeklijst) or die (mysqli_error($db));
#mysqli_query($db,"delete from tblDeklijst where `delete` = 1 ") or die (mysqli_error($db));

$dekId = array();
while( $dek = mysqli_fetch_assoc($zoek_dekId)) { $dekId[] = $dek['dekId'];  

$dekIds = implode(',', $dekId);
	}
	if(isset($dekIds)) {
	$del_tblDeklijst = "delete from `tblDeklijst` where dekId IN (".mysqli_real_escape_string($db,$dekIds).") ; ";
	#echo $del_tblDeklijst.'<br>'.'<br>';
	mysqli_query($db,$del_tblDeklijst);
	}
//echo '<br>';
//Einde tblDeklijst

//tblRubriekuser
$zoek_rubuId = mysqli_query($db,"select ru.rubuId
from tblRubriekuser ru
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by ru.rubuId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRubriekuser) or die (mysqli_error($db));
#mysqli_query($db,"delete from tblRubriekuser where `delete` = 1 ") or die (mysqli_error($db));

$rubuId = array();
while( $rubu = mysqli_fetch_assoc($zoek_rubuId)) { $rubuId[] = $rubu['rubuId'];  

$rubuIds = implode(',', $rubuId);
	}
	if(isset($rubuIds)) {
	$del_tblRubriekuser = "delete from `tblRubriekuser` where rubuId IN (".mysqli_real_escape_string($db,$rubuIds).") ; ";
	#echo $del_tblRubriekuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRubriekuser);
	}
//echo '<br>';
//Einde tblRubriekuser

//tblElementuser
$zoek_elemuId = mysqli_query($db,"select eu.elemuId
from tblElementuser eu
where eu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by eu.elemuId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblElementuser) or die (mysqli_error($db));

#mysqli_query($db,"delete from tblElementuser where `delete` = 1 ") or die (mysqli_error($db));

$elemuId = array();
while( $elemu = mysqli_fetch_assoc($zoek_elemuId)) { $elemuId[] = $elemu['elemuId'];  
	
$elemuIds = implode(',',$elemuId);
	}
	if(isset($elemuIds)) {
	$del_tblElementuser = "delete from `tblElementuser` where elemuId IN (".mysqli_real_escape_string($db,$elemuIds).") ; ";
	#echo $del_tblElementuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblElementuser);
	}
//Einde tblElementuser
/********************	Einde Financieel	*******************************************************************/
/********************	Stamtabellen	*******************************************************************/

//tblMomentuser
$zoek_momuId = mysqli_query($db,"select mu.momuId
from tblMomentuser mu
where mu.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by mu.momuId
") or die (mysqli_error($db));
#mysqli_query($db,$upd_tblMomentuser) or die (mysqli_error($db));
#mysqli_query($db,"delete from tblMomentuser where `delete` = 1 ") or die (mysqli_error($db));

$momuId = array();
while( $momu = mysqli_fetch_assoc($zoek_momuId)) { $momuId[] = $momu['momuId']; 

$momuIds = implode(',', $momuId); 
	}
	if(isset($momuIds)) {
	$del_tblMomentuser = "delete from `tblMomentuser` where momuId IN (".mysqli_real_escape_string($db,$momuIds).") ; ";
	#echo $del_tblMomentuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblMomentuser);
	}
//Einde tblMomentuser



//tblRasuser
$zoek_rasuId = mysqli_query($db,"select ru.rasuId
from tblRasuser ru
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by ru.rasuId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRasuser) or die (mysqli_error($db));
#mysqli_query($db,"delete from tblRasuser where `delete` = 1 ") or die (mysqli_error($db));

$rasuId = array();
while( $rasu = mysqli_fetch_assoc($zoek_rasuId)) { $rasuId[] = $rasu['rasuId'];  

$rasuIds = implode(',', $rasuId);
	
	}
	if(isset($rasuIds)) {
	$del_tblRasuser = "delete from `tblRasuser` where rasuId IN (".mysqli_real_escape_string($db,$rasuIds).") ; ";
	#echo $del_tblRasuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRasuser);
	}
//Einde tblRasuser



//tblRedenuser
$zoek_reduId = mysqli_query($db,"select ru.reduId
from tblRedenuser ru
where ru.lidId = ".mysqli_real_escape_string($db,$delete_lidId)."
order by ru.reduId
") or die (mysqli_error($db));

#mysqli_query($db,$upd_tblRedenuser) or die (mysqli_error($db));
#mysqli_query($db,"delete from tblRedenuser where `delete` = 1 ") or die (mysqli_error($db));

$reduId = array();
while( $redu = mysqli_fetch_assoc($zoek_reduId)) { $reduId[] = $redu['reduId']; /*$reuId = $redu['reduId'];*/

$reduIds = implode(',', $reduId);
	
	}
	if(isset($reduIds)) { 
	$del_tblRedenuser = "delete from `tblRedenuser` where reduId IN (".mysqli_real_escape_string($db,$reduIds).") ; ";
	#echo $del_tblRedenuser.'<br>'.'<br>';
	mysqli_query($db,$del_tblRedenuser);
	}
	

//Einde tblRedenuser	
/********************	Einde Stamtabellen	*******************************************************************/



?>