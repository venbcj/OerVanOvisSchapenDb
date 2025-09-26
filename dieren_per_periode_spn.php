**** eerste periode doelgroep gespeend

SELECT pId.periId, s.hokId, s.doelId, vanaf, eind, aantSch, (nutat / aantSch) gemKgVoerSch, 'eerste_periode' wat
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
	 	WHERE doelId = 2 and hokId = 1
	 	GROUP BY hokId
	 ) p1 on (b.hokId = p1.hokId)
	WHERE spn.datum <= h.datum and (isnull(prnt.schaapId) or prnt.datum > h.datum) and (isnull(p1.hokId) or h.datum < p1.sluit1) and b.hokId = 1
	GROUP BY b.hokId, doelId
 ) s
 left join (
	SELECT p.hokId, p.doelId, min(p.dmafsluit) eind, sum((nutat * stdat)) nutat
	FROM tblPeriode p
	join (
	 	SELECT hokId, doelId, min(dmafsluit) sluit1
	 	FROM tblPeriode
	 	WHERE doelId = 2 and hokId = 1
	 	GROUP BY hokId, doelId
	 ) p1 on (p.dmafsluit = p1.sluit1 and p.hokId = p1.hokId and p.doelId = p1.doelId)
	 left join tblVoeding v on (p.periId = v.periId)
	GROUP BY p.hokId, p.doelId
 ) e on (s.hokId = e.hokId and s.doelId = e.doelId)
 join tblPeriode pId on (e.hokId = pId.hokId and e.doelId = pId.doelId and e.eind = pId.dmafsluit)

UNION

SELECT pId.periId, p.hokId, p.doelId, p.vanaf, p.eind, bez.aantSch, sum((v.nutat * v.stdat)) / bez.aantSch gemKgVoerSch, 'volgende_periodes' wat
FROM (
	SELECT s.hokId, s.doelId, s.dmafsluit vanaf, min(e.dmafsluit) eind
	FROM tblPeriode s
	 join (
	 	SELECT dmafsluit, hokId, doelId
		FROM tblPeriode s
	 ) e on (s.hokId = e.hokId and s.doelId = e.doelId and s.dmafsluit < e.dmafsluit)
	WHERE s.doelId = 2 and s.hokId = 1
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
		WHERE s.doelId = 2 and s.hokId = 1
		GROUP BY s.hokId, s.doelId, s.dmafsluit
	 ) p on (p.hokId = b.hokId)
	 join tblPeriode pId on (p.hokId = pId.hokId and p.doelId = pId.doelId and p.eind = pId.dmafsluit)
	WHERE h.datum >= spn.datum and (isnull(prnt.schaapId) or prnt.datum > h.datum) and b.hokId = 1 and h.datum >= p.vanaf and (h.datum < p.eind or isnull(p.eind))
	GROUP BY pId.periId
 ) bez on (bez.periId = pId.periId)
WHERE p.hokId = 1
GROUP BY pId.periId, p.hokId, p.doelId, p.vanaf, p.eind

ORDER BY hokId, doelId, vanaf