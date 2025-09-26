<?php 
/*9-8-2014 :  werknr verwijderd. Dit wordt binnen de programmatuur behaald.
27-2-2015 : Van moeders die afgelopen 2 maanden zijn overleden moet de fase wel 'moeder' zijn
17-11-2015 : lammeren kunnen geen moeders zijn alleen fase moeders
20-10-2016 : mdrId gewijzigd naar volwId
19-11-2016 : Afgevoerde moeders tot 2 maanden terug toegevoegd.
29-11-2016 : quey aangepast om opnieuw aanvoer moederdier zichtbaar te krijgen. => actId = 3 aangpast en 'not exist' gekoppeld met stalId i.p.v. schaapId
2-12-2016 : Opnieuw aangvoerde dieren ook in query opgenomen
12-2-2017 : halsnummer toegevoegd
23-11-2024 : sql beveiligd met quotes en subquery 'afgevoerde dieren' herzien. Dit was not exist in where clause. Uitgeschaarden worden niet gezien als afvoer.
10-07-2025 : Where clause aangepast van afv.datum < date_add(curdate(), interval -2 month) naar afv.datum > date_add(curdate(), interval -2 month)

Toegepast in : 
	- 	Dekkingen.php
	-	InsAanwas.php
	-	InsGeboortes.php
	-	InvSchaap.php
	-	UpdSchaap.php
*/


$vw_kzlOoien =
("
SELECT st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, count(lam.schaapId) lamrn, concat(st.kleur,' ',st.halsnr) halsnr
FROM (
	SELECT max(stalId) stalId, schaapId
	FROM tblStal
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
	GROUP BY schaapId
 ) stm
 join tblStal st on (stm.stalId = st.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join (
	SELECT schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = st.schaapId)
 left join tblVolwas v on (s.schaapId = v.mdrId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join (
 	SELECT st.stalId, datum
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1 and h.actId <> 10 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
 	) afv on (afv.stalId = st.stalId)
WHERE s.geslacht = 'ooi' and (isnull(afv.stalId) or afv.datum > date_add(curdate(), interval -2 month) )

GROUP BY st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk), count(lam.schaapId)
")
?>