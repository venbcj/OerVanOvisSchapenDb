
**** eerste periode doelgroep geboren
SELECT pId.periId, s.hokId, s.doelId, vanaf, eind, aantSch, (nutat / aantSch) gemKgVoerSch, 'eerste_periode' wat
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


stalId 	periId 	hokId 	doelId 	vanaf 	eind 	aantSch 	gemKgVoerSch 	wat 	
190 	5 	1 	1 	2017-02-03 	2017-04-22 	1 	4016.000000 	eerste_periode
189 	5 	1 	1 	2017-02-03 	2017-04-22 	1 	4016.000000 	eerste_periode
185 	5 	1 	1 	2017-01-29 	2017-04-22 	1 	4016.000000 	eerste_periode
184 	5 	1 	1 	2017-01-29 	2017-04-22 	1 	4016.000000 	eerste_periode
183 	5 	1 	1 	2017-01-29 	2017-04-22 	1 	4016.000000 	eerste_periode
182 	5 	1 	1 	2017-01-26 	2017-04-22 	1 	4016.000000 	eerste_periode
175 	5 	1 	1 	2017-01-26 	2017-04-22 	1 	4016.000000 	eerste_periode
174 	5 	1 	1 	2017-01-24 	2017-04-22 	1 	4016.000000 	eerste_periode
173 	5 	1 	1 	2017-01-24 	2017-04-22 	1 	4016.000000 	eerste_periode
172 	5 	1 	1 	2017-01-24 	2017-04-22 	1 	4016.000000 	eerste_periode
171 	5 	1 	1 	2017-01-24 	2017-04-22 	1 	4016.000000 	eerste_periode
170 	5 	1 	1 	2017-01-23 	2017-04-22 	1 	4016.000000 	eerste_periode
169 	5 	1 	1 	2017-01-23 	2017-04-22 	1 	4016.000000 	eerste_periode
167 	5 	1 	1 	2017-01-23 	2017-04-22 	1 	4016.000000 	eerste_periode
166 	5 	1 	1 	2017-01-20 	2017-04-22 	1 	4016.000000 	eerste_periode
165 	5 	1 	1 	2017-01-28 	2017-04-22 	1 	4016.000000 	eerste_periode


UNION

SELECT pId.periId, p.hokId, p.doelId, p.vanaf, p.eind, bez.aantSch, sum((v.nutat * v.stdat)) / bez.aantSch gemKgVoerSch, 'volgende_periodes' wat
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
WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."'
GROUP BY pId.periId, p.hokId, p.doelId, p.vanaf, p.eind

ORDER BY hokId, doelId, vanaf

periId 	hokId 	doelId 	vanaf 		eind 		aantSch 	gemKgVoerSch 	wat 	
67 		1 		1 		2017-04-22 	2018-06-27 	129 		NULL 	volgende_periodes
79 		1 		1 		2018-06-27 	2019-03-16 	18 			NULL 	volgende_periodes
194 	1 		1 		2019-03-16 	2021-06-27 	366 		NULL 	volgende_periodes
197 	1 		1 		2021-06-27 	2021-07-24 	NULL 		NULL 	volgende_periodes
289 	1 		1 		2021-07-24 	2021-09-11 	NULL 		NULL 	volgende_periodes


periId = 67
stalId 	periId 	hokId 	doelId 	vanaf 	eind 	aantSch 	gemKgVoerSch 	wat 	
103 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
104 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
107 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
113 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
119 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
120 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
122 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
127 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
129 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
131 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
135 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
136 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
139 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
141 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
165 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
166 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
167 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
170 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
171 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
172 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
173 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
174 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
178 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
182 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
186 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
187 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
188 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
189 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
191 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
192 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
196 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
203 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
211 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
212 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
216 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
217 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4677 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4678 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4679 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4680 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4681 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4683 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4684 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4685 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4686 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4687 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4688 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4689 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4690 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4691 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4692 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4693 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4694 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4695 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4696 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4697 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4703 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4704 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4705 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4706 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4707 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4708 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4709 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4710 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4711 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4712 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4713 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4714 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4715 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4716 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4717 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4718 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4719 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4720 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4721 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4722 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4723 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4725 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4726 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4727 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4728 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4730 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4731 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4732 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4733 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4734 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4735 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4736 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4737 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4738 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4739 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4740 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4741 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4742 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4743 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4744 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4745 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4749 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4750 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4751 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes 
4752 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4753 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4754 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4757 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4758 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4759 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4760 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4761 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4762 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4763 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4764 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4765 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4766 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4767 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4768 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4769 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4770 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4771 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4772 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4773 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4774 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4775 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4776 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4777 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
4778 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
5923 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
5925 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
5929 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes
6609 	67 	1 	1 	2017-04-22 	2018-06-27 	1 	NULL 	volgende_periodes

 *********************************
