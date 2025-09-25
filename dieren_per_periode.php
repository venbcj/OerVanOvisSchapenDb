
**** eerste periode doelgroep geboren
SELECT pId.periId, s.hokId, s.doelId, vanaf, eind, nutat, aantSch, (nutat / aantSch) gemKgVoerSch, 'eerste_periode' wat
FROM (
	SELECT b.hokId, 1 doelId, min(h.datum) vanaf, count(distinct(st.schaapId)) aantSch
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
	 	SELECT hokId, min(dmafsluit) sluit1
	 	FROM tblPeriode
	 	WHERE doelId = 1 and hokId = '".mysqli_real_escape_string($db,$Id)."'
	 	GROUP BY hokId
	 ) p1 on (b.hokId = p1.hokId)
	WHERE (isnull(spn.schaapId) or spn.datum > h.datum) and (isnull(prnt.schaapId) or prnt.datum > h.datum) and (isnull(p1.hokId) or h.datum < p1.sluit1) and b.hokId = '".mysqli_real_escape_string($db,$Id)."'
	GROUP BY b.hokId, doelId
 ) s
 left join (
	SELECT p.hokId, p.doelId, min(p.dmafsluit) eind, sum((nutat * stdat)) nutat
	FROM tblPeriode p
	join (
	 	SELECT hokId, doelId, min(dmafsluit) sluit1
	 	FROM tblPeriode
	 	WHERE doelId = 1 and hokId = '".mysqli_real_escape_string($db,$Id)."'
	 	GROUP BY hokId, doelId
	 ) p1 on (p.dmafsluit = p1.sluit1 and p.hokId = p1.hokId and p.doelId = p1.doelId)
	 left join tblVoeding v on (p.periId = v.periId)
	GROUP BY p.hokId, p.doelId
 ) e on (s.hokId = e.hokId and s.doelId = e.doelId)
 join tblPeriode pId on (e.hokId = pId.hokId and e.doelId = pId.doelId and e.eind = pId.dmafsluit)

UNION

**** periodes na eerste periode doelgroep geboren

SELECT pId.periId, p.hokId, p.doelId, p.vanaf, p.eind, sum((v.nutat * v.stdat)), bez.aantSch, sum((v.nutat * v.stdat)) / bez.aantSch gemKgVoerSch, 'volgende_periodes' wat
FROM (
	SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
	FROM tblPeriode s
	 join (
	 	SELECT dmafsluit, hokId, doelId
		FROM tblPeriode s
	 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
	WHERE s.doelId = 1 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
	GROUP BY s.hokId, s.doelId, s.dmafsluit
) p
 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
 left join tblVoeding v on (pId.periId = v.periId)
 left join (
	SELECT pId.periId, count(distinct(st.schaapId)) aantSch
	FROM tblBezet b
	 left join tblHistorie h on (b.hisId = h.hisId)
	 left join tblStal st on (h.stalId = st.stalId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 join (
		SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
		FROM tblPeriode s
		 join (
		 	SELECT dmafsluit, hokId, doelId
			FROM tblPeriode s
		 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
		WHERE s.doelId = 1 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
		GROUP BY s.hokId, s.doelId, s.dmafsluit
	 ) p on (p.hokId = b.hokId)
	 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and h.datum >= p.vanaf and h.datum < p.eind
	GROUP BY pId.periId
 ) bez on (bez.periId = pId.periId)
GROUP BY pId.periId, p.hokId, p.doelId, p.vanaf, p.eind

ORDER BY hokId, doelId, vanaf



 *********************************

**** eerste periode doelgroep gespeend

SELECT pId.periId, s.hokId, s.doelId, vanaf, eind, nutat, aantSch, (nutat / aantSch) gemKgVoerSch, 'eerste_periode' wat
FROM (
	SELECT b.hokId, 2 doelId, min(h.datum) vanaf, count(distinct(st.schaapId)) aantSch
	FROM tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (h.stalId = st.stalId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
	 	SELECT hokId, min(dmafsluit) sluit1
	 	FROM tblPeriode
	 	WHERE doelId = 2 and hokId = '".mysqli_real_escape_string($db,$Id)."'
	 	GROUP BY hokId
	 ) p1 on (b.hokId = p1.hokId)
	WHERE spn.datum <= h.datum and (isnull(prnt.schaapId) or prnt.datum > h.datum) and (isnull(p1.hokId) or h.datum < p1.sluit1) and b.hokId = '".mysqli_real_escape_string($db,$Id)."'
	GROUP BY b.hokId, doelId
 ) s
 left join (
	SELECT p.hokId, p.doelId, min(p.dmafsluit) eind, sum((nutat * stdat)) nutat
	FROM tblPeriode p
	join (
	 	SELECT hokId, doelId, min(dmafsluit) sluit1
	 	FROM tblPeriode
	 	WHERE doelId = 2 and hokId = '".mysqli_real_escape_string($db,$Id)."'
	 	GROUP BY hokId, doelId
	 ) p1 on (p.dmafsluit = p1.sluit1 and p.hokId = p1.hokId and p.doelId = p1.doelId)
	 left join tblVoeding v on (p.periId = v.periId)
	GROUP BY p.hokId, p.doelId
 ) e on (s.hokId = e.hokId and s.doelId = e.doelId)
 join tblPeriode pId on (e.hokId = pId.hokId and e.doelId = pId.doelId and e.eind = pId.dmafsluit)

UNION

**** periodes na eerste periode doelgroep gespeend

SELECT pId.periId, p.hokId, p.doelId, p.vanaf, p.eind, sum((v.nutat * v.stdat)), bez.aantSch, sum((v.nutat * v.stdat)) / bez.aantSch gemKgVoerSch, 'volgende_periodes' wat
FROM (
	SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
	FROM tblPeriode s
	 join (
	 	SELECT dmafsluit, hokId, doelId
		FROM tblPeriode s
	 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
	WHERE s.doelId = 2 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
	GROUP BY s.hokId, s.doelId, s.dmafsluit
) p
 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
 left join tblVoeding v on (pId.periId = v.periId)
 left join (
	SELECT pId.periId, count(distinct(st.schaapId)) aantSch
	FROM tblBezet b
	 left join tblHistorie h on (b.hisId = h.hisId)
	 left join tblStal st on (h.stalId = st.stalId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 join (
		SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
		FROM tblPeriode s
		 join (
		 	SELECT dmafsluit, hokId, doelId
			FROM tblPeriode s
		 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
		WHERE s.doelId = 2 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
		GROUP BY s.hokId, s.doelId, s.dmafsluit
	 ) p on (p.hokId = b.hokId)
	 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
	WHERE h.datum >= spn.datum and (isnull(prnt.schaapId) or prnt.datum > h.datum) and b.hokId = '".mysqli_real_escape_string($db,$Id)."' and h.datum >= p.vanaf and (h.datum < p.eind or isnull(p.eind))
	GROUP BY pId.periId
 ) bez on (bez.periId = pId.periId)
GROUP BY pId.periId, p.hokId, p.doelId, p.vanaf, p.eind

ORDER BY hokId, doelId, vanaf

periId 	hokId 	doelId 	vanaf 		eind 		gemKgVoerSch 	wat 	
9 		1 		2 		2017-03-16 	2017-05-26 	8.732673 	eerste_periode
15 		1 		2 		2017-05-26 	2017-06-23 	NULL 		volgende_periodes
66 		1 		2 		2017-06-23 	2018-06-27 	NULL 		volgende_periodes
80 		1 		2 		2018-06-27 	2019-03-16 	NULL 		volgende_periodes



 *********************************

**** eerste periode doelgroep aanwas

SELECT hokId, min(startperiode) startperiode
FROM (

	SELECT hokId, min(aanwas) startperiode
	FROM (
		SELECT b.hokId, h.stalId, h.datum datum_in, ht.datum datum_uit, prnt.datum aanwas, p1.sluit1
		FROM tblBezet b
		 left join (
		 	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblActie a1 on (a1.actId = h1.actId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
			 join tblActie a2 on (a2.actId = h2.actId)
			 join tblStal st on (h1.stalId = st.stalId)
			 join (
				SELECT st.schaapId, h.datum dmprnt
				FROM tblStal st
				 join tblHistorie h on (st.stalId = h.stalId)
				WHERE h.actId = 3
			 ) prnt on (prnt.schaapId = st.schaapId)
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = '".mysqli_real_escape_string($db,$Id)."'
			GROUP BY b.bezId, st.schaapId, h1.hisId
		 ) uit on (b.hisId = uit.hisv)
		 join tblHistorie h on (b.hisId = h.hisId)
		 left join tblHistorie ht on (uit.hist = ht.hisId)
		 join tblStal st on (h.stalId = st.stalId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3
		 ) prnt on (prnt.schaapId = st.schaapId)
		 join (
			 	SELECT hokId, min(dmafsluit) sluit1
			 	FROM tblPeriode
			 	WHERE doelId = 3 and hokId = '".mysqli_real_escape_string($db,$Id)."'
			 	GROUP BY hokId
			 ) p1 on (b.hokId = p1.hokId)
		WHERE h.datum < p1.sluit1 and h.datum <= prnt.datum and (isnull(ht.datum) or ht.datum > prnt.datum)
	 ) sp
	 GROUP BY hokId

UNION
    
	SELECT hokId, min(datum1_in) startperiode
	FROM (
		SELECT b.hokId, h.stalId, min(h.datum) datum1_in, prnt.datum aanwas, p1.sluit1
		FROM tblBezet b
		 join tblHistorie h on (b.hisId = h.hisId)
		 join tblStal st on (h.stalId = st.stalId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3
		 ) prnt on (prnt.schaapId = st.schaapId)
		 join (
		 	SELECT hokId, min(dmafsluit) sluit1
		 	FROM tblPeriode
		 	WHERE doelId = 3 and hokId = '".mysqli_real_escape_string($db,$Id)."'
		 	GROUP BY hokId
		 ) p1 on (b.hokId = p1.hokId)
		GROUP BY b.hokId, h.stalId, prnt.datum, p1.sluit1
		HAVING prnt.datum < min(h.datum) and min(h.datum) < p1.sluit1
	 ) sp
	 GROUP BY hokId

UNION
    
	SELECT hokId, min(datum_in) startperiode
	FROM (
		SELECT b.hokId, hu.datum datum_uit, hi.datum datum_in, prnt.datum aanwas, p1.sluit1
		FROM (
			SELECT st.schaapId, h1.hisId hisv, max(h2.hisId) hist
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblStal st on (st.stalId = h1.stalId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId > h2.hisId) and b.hokId = '".mysqli_real_escape_string($db,$Id)."'
			GROUP BY st.schaapId, h1.hisId
		 ) bez
		 join tblHistorie hi on (hi.hisId = bez.hisv)
		 join tblBezet b on (hi.hisId = b.hisId)
		 join tblHistorie hu on (hu.hisId = bez.hist)
		 join tblActie au on (hu.actId = au.actId)
		 join (
			SELECT st.schaapId, h.datum
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3
		 ) prnt on (prnt.schaapId = bez.schaapId)
		 join (
		 	SELECT hokId, min(dmafsluit) sluit1
		 	FROM tblPeriode
		 	WHERE doelId = 3 and hokId = '".mysqli_real_escape_string($db,$Id)."'
		 	GROUP BY hokId
		 ) p1 on (b.hokId = p1.hokId)
		WHERE au.aan = 0 and au.uit = 1 and hu.datum < hi.datum and hi.skip = 0 and hu.skip = 0 and hu.datum <= prnt.datum and hi.datum > prnt.datum and hi.datum < p1.sluit1
	 ) sp
	 GROUP BY hokId

) R
GROUP BY hokId 

UNION

**** periodes na eerste periode doelgroep aanwas

SELECT pId.periId, p.hokId, p.doelId, p.vanaf, p.eind, sum((v.nutat * v.stdat)), bez.aantSch, sum((v.nutat * v.stdat)) / bez.aantSch gemKgVoerSch, 'volgende_periodes' wat
FROM (
	SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
	FROM tblPeriode s
	 join (
	 	SELECT dmafsluit, hokId, doelId
		FROM tblPeriode s
	 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
	WHERE s.doelId = 3 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
	GROUP BY s.hokId, s.doelId, s.dmafsluit
 ) p
 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
 left join tblVoeding v on (pId.periId = v.periId)
 left join (
	SELECT pId.periId, count(distinct(st.schaapId)) aantSch
	FROM tblBezet b
	 left join (
		SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		FROM tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		 join (
			SELECT st.schaapId, h.datum dmprnt
			FROM tblStal st
			 join tblHistorie h on (st.stalId = h.stalId)
			WHERE h.actId = 3
		 ) prnt on (prnt.schaapId = st.schaapId)
		WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = '".mysqli_real_escape_string($db,$Id)."'
		GROUP BY b.bezId, st.schaapId, h1.hisId
	 ) uit on (b.hisId = uit.hisv)
	 left join tblHistorie h on (b.hisId = h.hisId)
	 left join tblHistorie ht on (uit.hist = ht.hisId)
	 left join tblStal st on (h.stalId = st.stalId)
	 join (
		SELECT st.schaapId, h.datum
		FROM tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		WHERE h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 join (
		SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
		FROM tblPeriode s
		 join (
		 	SELECT dmafsluit, hokId, doelId
			FROM tblPeriode s
		 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
		WHERE s.doelId = 3 and s.hokId = '".mysqli_real_escape_string($db,$Id)."'
		GROUP BY s.hokId, s.doelId, s.dmafsluit
	 ) p on (p.hokId = b.hokId)
	 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
	WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and (isnull(ht.datum) or ht.datum > p.vanaf) and h.datum < p.eind and 
	((ht.datum <= p.eind and prnt.datum < ht.datum) or ((ht.datum > p.eind or isnull(ht.datum)) and prnt.datum < p.eind) )
	GROUP BY pId.periId
 ) bez on (bez.periId = pId.periId)
GROUP BY pId.periId, p.hokId, p.doelId, p.vanaf, p.eind

ORDER BY hokId, doelId, vanaf
