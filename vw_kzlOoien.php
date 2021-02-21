<?php 
/*9-8-2014 :  werknr verwijderd. Dit wordt binnen de programmatuur behaald.
27-2-2015 : Van moeders die afgelopen 2 maanden zijn overleden moet de fase wel 'moeder' zijn
17-11-2015 : lammeren kunnen geen moeders zijn alleen fase moeders
20-10-2016 : mdrId gewijzigd naar volwId
19-11-2016 : Afgevoerde moeders tot 2 maanden terug toegevoegd.
29-11-2016 : quey aangepast om opnieuw aanvoer moederdier zichtbaar te krijgen. => actId = 3 aangpast en 'not exist' gekoppeld met stalId i.p.v. schaapId
2-12-2016 : Opnieuw aangvoerde dieren ook in query opgenomen
12-2-2017 : halsnummer toegevoegd

Toegepast in : 
	- 	Dracht.php
	-	InsAanwas.php
	-	InsGeboortes.php
	-	InvSchaap.php
	x	MutSchaap.php
	x	UpdSchaap.php
	x	WijzigSchaap.php
*/


$vw_kzlOoien =
("
select st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, count(lam.schaapId) lamrn, concat(st.kleur,' ',st.halsnr) halsnr
from (
	select max(stalId) stalId, schaapId
	from tblStal
	where lidId = ".mysqli_real_escape_string($db,$lidId)."
	group by schaapId
 ) stm
 join tblStal st on (stm.stalId = st.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join (
	select schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = st.schaapId)
 left join tblVolwas v on (s.schaapId = v.mdrId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
where s.geslacht = 'ooi'
and not exists (
	select stal.stalId, stal.schaapId
	from tblStal stal
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	where stal.stalId = st.stalId and a.af = 1 and h.skip = 0 and lidId = ".mysqli_real_escape_string($db,$lidId)." and h.datum < date_add(curdate(), interval -2 month)
 )

group by st.stalId, st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk)
order by right(s.levensnummer,$Karwerk), count(lam.schaapId)
")
?>