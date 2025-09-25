
**** eerste periode doelgroep aanwas
**** Uitgangspunt is dat de periode start op moment van aanwasdatum bij de eerste periode

SELECT hokId, min(startperiode) startperiode
FROM (

**** In welk verblijf zat een schaap op moment van aanwasdatum
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
			WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and b.hokId = 16
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
		 join ( "/* geen left join omdat alleen in afgesloten periodes wordt gezocht */"
			 	SELECT hokId, min(dmafsluit) sluit1
			 	FROM tblPeriode
			 	WHERE doelId = 3 and hokId = 16
			 	GROUP BY hokId
			 ) p1 on (b.hokId = p1.hokId)
		WHERE h.datum < p1.sluit1 and h.datum <= prnt.datum and (isnull(ht.datum) or ht.datum > prnt.datum)
	 ) sp
	 GROUP BY hokId

**** Op moment van aanwasdatum zat schaap niet in een vebrlijf (3 opties) ****

*** Optie 1 Aanwasdatum ligt voor eerst datum in verblijf 
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
		 join ( "/* geen left join omdat alleen in afgesloten periodes wordt gezocht */"
		 	SELECT hokId, min(dmafsluit) sluit1
		 	FROM tblPeriode
		 	WHERE doelId = 3 and hokId = 16
		 	GROUP BY hokId
		 ) p1 on (b.hokId = p1.hokId)
		GROUP BY b.hokId, h.stalId, prnt.datum, p1.sluit1
		HAVING prnt.datum < min(h.datum) and min(h.datum) < p1.sluit1
	 ) sp
	 GROUP BY hokId


*** Optie 2 Aanwadatum ligt tussen uit verblijf en volgend in verblijf
	SELECT hokId, min(datum_in) startperiode
	FROM (
		SELECT b.hokId, hu.datum datum_uit, hi.datum datum_in, prnt.datum aanwas, p1.sluit1
		FROM (
			SELECT st.schaapId, h1.hisId hisv, max(h2.hisId) hist
			FROM tblBezet b
			 join tblHistorie h1 on (b.hisId = h1.hisId)
			 join tblStal st on (st.stalId = h1.stalId)
			 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId > h2.hisId) and b.hokId = 16
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
		 join ( "/* geen left join omdat alleen in afgesloten periodes wordt gezocht */"
		 	SELECT hokId, min(dmafsluit) sluit1
		 	FROM tblPeriode
		 	WHERE doelId = 3 and hokId = 16
		 	GROUP BY hokId
		 ) p1 on (b.hokId = p1.hokId)
		WHERE au.aan = 0 and au.uit = 1 and hu.datum < hi.datum and hi.skip = 0 and hu.skip = 0 and hu.datum <= prnt.datum and hi.datum > prnt.datum and hi.datum < p1.sluit1
	 ) sp
	 GROUP BY hokId

) r
GROUP BY hokId 


*** Optie 3 Laatste datum uit verblijf ligt op of voor aanwadatum 
*** Deze optie telt niet mee bij eerste periode aanwas !!!
SELECT b.hokId, d.stalId, datumMax_uit, d.aanwas, p1.sluit1
FROM (
	SELECT h.stalId, max(h.datum) datumMax_in, max(coalesce(ht.datum,curdate())) datumMax_uit, prnt.datum aanwas
	FROM tblBezet b
	 left join ( "/* Schaap kan nog steeds in een verblijf zitten dus left join */"
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
		WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
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
	GROUP BY h.stalId, prnt.datum
) d
 join ( "/* bij meerdere plaatsing in verblijf op 1 dag zoeken naar max(hisId) */"
 	SELECT max(h.hisId) hisIdMax, stalId, h.datum
 	FROM tblHistorie h
 	 join tblBezet b on (h.hisId = b.hisId)
 	GROUP BY stalId, h.datum 
 ) hv on (hv.stalId = d.stalId and hv.datum = d.datumMax_in)
 join tblBezet b on (hv.hisIdMax = b.hisId)
 join ( "/* geen left join omdat alleen in afgesloten periodes wordt gezocht */"
 	SELECT hokId, min(dmafsluit) sluit1
 	FROM tblPeriode
 	WHERE doelId = 3
 	GROUP BY hokId
 ) p1 on (b.hokId = p1.hokId)

WHERE d.aanwas >= d.datumMax_uit and d.datumMax_uit < p1.sluit1
**** EINDE Op moment van aanwasdatum zat schaap niet in een vebrlijf (3 opties) ****

**** EINDE eerste periode doelgroep aanwas

