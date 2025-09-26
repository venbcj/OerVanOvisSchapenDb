SELECT st.stalId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE h.actId = 18 and st.schaapId = '22741' and h.datum = '2025-01-01'
GROUP BY st.stalId



SELECT max(h.datum) datum
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
WHERE h.stalId = '26944' and h.datum <= '2025-01-01'

SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
WHERE h.stalId = '26944' and h.datum = '2024-12-04'

SELECT b.bezId, uit.bezId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 left join
 (
	SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '22' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hisId = '123426' and (isnull(uit.bezId) or ht.datum > '2025-01-01')