<?php 
/* 20-1-2017 : Query aangepast n.a.v. nieuwe tblDoel    22-1-2017 : tblBezetting gewijzigd naar tblBezet
20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam
11-03-2024 : Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 

Toegepast in : 
    X    HokOverpl.php    per 6-2-2017 weggehaald
    X    HokSpenen.php    per 6-2-2017 weggehaald
    X    InsAanvoer.php    per 8-2-2017 weggehaald
    X    InsGeboortes.php    per 15-2-2017 weggehaald
    X    InsOverplaats.php    per 12-2-2017 weggehaald
    X    InsSpenen.php    per 16-2-2017 weggehaald
    X     InvSchaap.php    per 8-2-2017 weggehaald
    
Verwijderd uit :
    -    Med_registratie.php per 15-1-2017

*/

$vw_kzlHok =
("
SELECT h.hokId, h.hoknr, coalesce(inhok.doel,'niet in gebruik') doel, inhok.nu, h.scan
FROM tblHok h
left join (
    Select p.periId, p.hokId, p.doelId, d.doel, count(b.bezId)-coalesce(uit.weg,0) nu
    From tblHok h
    join tblPeriode p on (h.hokId = p.hokId)
    join tblDoel d on (p.doelId = d.doelId)
    join tblBezet b on (p.periId = b.periId)
    left join (
        Select b.periId, count(uit.bezId) weg
        From tblBezet b
        join
        (
            select b.bezId, h1.hisId hisv, min(h2.hisId) hist
            from tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
             join tblPeriode p on (p.periId = b.periId)
            where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and isnull(p.dmafsluit)
            group by b.bezId, h1.hisId
        ) uit
        on (uit.bezId = b.bezId)
        Group by b.periId
    ) uit
    on (p.periId = uit.periId)
    Where h.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(p.dmafsluit)
    Group by p.periId, p.hokId, d.doel, uit.weg
) inhok
on (h.hokId = inhok.hokId)
WHERE h.lidId = ".mysqli_real_escape_string($db,$lidId)." and ( isnull(inhok.doelId) or inhok.doelId != ".$hok_uitgez.") and h.actief = 1
")
?>