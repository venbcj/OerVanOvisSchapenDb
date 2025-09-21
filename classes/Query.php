<?php

class Query {

public static function aantal_fase_stal($datb,$lidid,$Sekse,$Ouder) {
$zoeken_aantalFase = mysqli_query($datb,"
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");
if($zoeken_aantalFase) {
    $row = mysqli_fetch_assoc($zoeken_aantalFase);
    return $row['aant'];
}
return false; // Foutafhandeling
} 

public static function aantal_fase_uitgeschaard($datb,$lidid,$Sekse,$Ouder) {
$zoeken_aantalFase_uitgeschaard = mysqli_query($datb,"
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join (
     SELECT lidId, schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($datb,$lidid)."'
     GROUP BY lidId, schaapId
  ) mst on (mst.schaapId = s.schaapId)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE h.actId = 10
 ) haf on (haf.stalId = mst.stalId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE mst.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and ".$Sekse." and ".$Ouder." 
");
if($zoeken_aantalFase_uitgeschaard)
        {    $zau = mysqli_fetch_assoc($zoeken_aantalFase_uitgeschaard);
                return $zau['aant'];
        }
        return FALSE; // Foutafhandeling
} 

public static function med_aantal_fase($datb,$lidid,$M,$J,$V,$Sekse,$Ouder) { // Functie die het aantal lammeren, moederdieren of vaders telt
    $vw_totaalFase = mysqli_query($datb,"
SELECT count(distinct s.levensnummer) werknrs
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 left join (
    SELECT st.schaapId, h.hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE h.skip = 0 and month(h.datum) = $M and date_format(h.datum,'%Y') = $J and i.artId = $V and ".$Sekse." and ".$Ouder."
    and st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and h.actId = 8
GROUP BY date_format(h.datum,'%Y%m')
");
if($vw_totaalFase)
        {    $row = mysqli_fetch_assoc($vw_totaalFase);
                return $row['werknrs'];
        }
        return FALSE; // Foutafhandeling
}

}
