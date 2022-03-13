
<?php
/* Toelichting : Deze query laat alle datums uit het verleden zien die bij een schaap horen. 
28-10-2016 : tblVowas toegevoegd
22-01-2017 : tblBezetting gewijzigd naar tblBezet
12-12-2020 : Alias actId bij NULL velden toegevoegd. Dit ging fout in MeldUitval.php
19-02-2022 : SQL beveiligd d.m.v. quotes
	
Toegepast in : 
	x	Afleveren.php
	x	Hokspenen.php
	-	InsAfvoer.php
	x	InsUitval.php
	x	InvSchaap.php
	x	MeldAfleveren.php
	x	MeldUitval.php
	x	UpdSchaap.php
	x	WijzigSchaap.php

	x	svwSchapenDmMax.doc

*/

$vw_HistorieDm =
("
SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 1 and s.levensnummer is not null

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 2 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join 
 (
    SELECT s.schaapId, h.actId, h.datum 
    FROM tblSchaap s
	join tblStal st on (st.schaapId = s.schaapId)
	join tblHistorie h on (h.stalId = st.stalId) 
    WHERE actId = 2 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
) koop on (s.schaapId = koop.schaapId and koop.datum <= h.datum)
WHERE a.actId = 3 and (isnull(koop.datum) or koop.datum < h.datum) and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 4

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 5 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 8 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 9 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 12 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 13 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.actId = 14 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

Union

SELECT  mdr.schaapId, min(h.datum) datum, 'Eerste worp' actie, NULL actId, 0 skip
FROM tblSchaap mdr
 join tblVolwas v on (mdr.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY mdr.schaapId

Union

SELECT mdr.schaapId, max(h.datum) datum, 'Laatste worp' actie, NULL actId, 0 skip
FROM tblSchaap mdr
 join tblVolwas v on (mdr.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY mdr.schaapId, h.actId
HAVING (max(h.datum) > min(h.datum))

Union

SELECT s.schaapId, p.dmafsluit datum, 'Gevoerd' actie, NULL actId, h.skip
FROM tblVoeding vd
 join tblPeriode p on (p.periId = vd.periId)
 join tblBezet b on (b.periId = p.periId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE  st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId, p.dmafsluit


Union

SELECT s.schaapId, max(r.dmmeld) dmmeld, 'Geboorte gemeld' actie, h.actId, h.skip
FROM tblMelding m 
 join tblRequest r on (m.reqId = r.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE r.code = 'GER' and r.dmmeld IS NOT NULL and m.skip <> 1 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId

Union

SELECT s.schaapId, max(r.dmmeld) dmmeld, 'Aanvoer gemeld' actie, h.actId, h.skip
FROM tblMelding m 
 join tblRequest r on (m.reqId = r.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE r.code = 'AAN' and r.dmmeld IS NOT NULL and m.skip <> 1 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId

Union

SELECT s.schaapId, max(r.dmmeld) dmmeld, 'Afvoer gemeld' actie, h.actId, h.skip
FROM tblMelding m 
 join tblRequest r on (m.reqId = r.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE r.code = 'AFV' and r.dmmeld IS NOT NULL and m.skip <> 1 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId

Union

SELECT s.schaapId, max(r.dmmeld) dmmeld, 'Uitval gemeld' actie, h.actId, h.skip
FROM tblMelding m 
 join tblRequest r on (m.reqId = r.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE r.code = 'DOO' and r.dmmeld IS NOT NULL and m.skip <> 1 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId


")
?>