<?php

class SchaapGateway extends Gateway {

    public function zoek_schaapid($fldLevnr) {
        $vw = mysqli_query($this->db, "
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($this->db, $fldLevnr)."'");
$rec = mysqli_fetch_assoc($vw);
return $rec['schaapId'] ?? 0;
            # TODO: #0004137 nullcheck. Als fldLevnr niet voorkomt, is zs geen array, en dat geeft een warning.
        # Dit wijst erop dat de code dingen doet die niet bij elkaar horen.
    }

    public function zoek_stalid($lidId) {
$vw = mysqli_query($this->db,"
SELECT st.stalId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".mysqli_real_escape_string($this->db,$lidId)."' 
GROUP BY st.stalId  
") or die (mysqli_error($this->db));
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_vaders($lidId, $Karwerk) {
$vw = mysqli_query($this->db, "
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur, ' ', halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".mysqli_real_escape_string($this->db, $lidId)."' 
GROUP BY st.stalId, levensnummer
ORDER BY right(levensnummer, $Karwerk)  
") or die (mysqli_error($db));
return $vw;
    }

    public function count_levnr($fldLevnr, $schaapId) {
        $vw = mysqli_query($this->db, "
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($this->db, $fldLevnr)."' and schaapId <> '".mysqli_real_escape_string($this->db, $schaapId)."'");
$rec = mysqli_fetch_assoc($vw);
return $rec['aant'];
    }

    // deze handeling heet "change" omdat het sleutelveld verandert
    public function changeLevensnummer($old, $new) {
        mysqli_query($this->db, "
UPDATE tblSchaap SET levensnummer = '".mysqli_real_escape_string($this->db, $new)."'
        WHERE levensnummer = '".mysqli_real_escape_string($this->db, $old)."' ");
    }

    public function updateGeslacht($levensnummer, $geslacht) {
        mysqli_query($this->db, "
UPDATE tblSchaap SET geslacht='".mysqli_real_escape_string($this->db, $geslacht)."'
        WHERE levensnummer = '".mysqli_real_escape_string($this->db, $levensnummer)."' ");
    }

    public function aantalLamOpStal($lidId) {
        $sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
        $ouder = 'isnull(prnt.schaapId)';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    public function aantalOoiOpStal($lidId) {
        $sekse = "s.geslacht = 'ooi'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    public function aantalRamOpStal($lidId) {
        $sekse = "s.geslacht = 'ram'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByStalFase($lidId, $sekse, $ouder);
    }

    private function countByStalFase($lidid, $Sekse, $Ouder) {
$vw = mysqli_query($this->db, "
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = '".mysqli_real_escape_string($this->db, $lidid)."' and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");
if ($vw) {
            $row = mysqli_fetch_assoc($vw);
            return $row['aant'];
}
return false; // Foutafhandeling
    }

    public function aantalLamUitschaar($lidId) {
        $sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
        $ouder = 'isnull(prnt.schaapId)';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    public function aantalOoiUitschaar($lidId) {
        $sekse = "s.geslacht = 'ooi'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    public function aantalRamUitschaar($lidId) {
        $sekse = "s.geslacht = 'ram'";
        $ouder = 'prnt.schaapId is not null';
        return $this->countByFaseUitgeschaard($lidId, $sekse, $ouder);
    }

    private function countByFaseUitgeschaard($lidid, $Sekse, $Ouder) {
        $vw = mysqli_query($this->db, "
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join (
     SELECT lidId, schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($this->db, $lidid)."'
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
WHERE mst.lidId = '".mysqli_real_escape_string($this->db, $lidid)."' and ".$Sekse." and ".$Ouder." 
");
if ($vw) {
    $row = mysqli_fetch_assoc($vw);
    return $row['aant'];
}
return false; // Foutafhandeling
    }

    // Functie die het aantal lammeren, moederdieren of vaders telt
    public function med_aantal_fase($lidid,$M,$J,$V,$Sekse,$Ouder) {
        $vw_totaalFase = mysqli_query($this->db,"
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
WHERE true
  AND h.skip = 0
  AND month(h.datum) = $M
  AND date_format(h.datum,'%Y') = $J
  AND i.artId = $V
  AND ".$Sekse."
  AND ".$Ouder."
  AND st.lidId = '".mysqli_real_escape_string($this->db,$lidid)."'
  AND h.actId = 8
GROUP BY date_format(h.datum,'%Y%m')
");
        if($vw_totaalFase) {
            $row = mysqli_fetch_assoc($vw_totaalFase);
            return $row['werknrs'];
        }
        return FALSE; // Foutafhandeling
    }

    // Functie die de hoeveelheid voer berekend per lammeren, moederdieren of vaders
    public function voer_fase($lidid,$M,$J,$V,$Sekse,$Ouder) { 
        $vw_totaalFase = mysqli_query($this->db,"
        SELECT round(sum(n.nutat*n.stdat),2) totats
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
        WHERE true
  AND h.skip = 0
  AND month(h.datum) = $M
  AND date_format(h.datum,'%Y') = $J
  AND i.artId = $V
  AND ".$Sekse."
  AND ".$Ouder."
  AND st.lidId = '".mysqli_real_escape_string($this->db,$lidid)."'
        GROUP BY concat(date_format(h.datum,'%Y'),month(h.datum))
        ");
        if($vw_totaalFase)
                {    $row = mysqli_fetch_assoc($vw_totaalFase);
                        return $row['totats'];
                }
                return FALSE; // Foutafhandeling
    }

    // zou dit in EenheidGateway horen?
    // Functie die de eenheid ophaalt per lammeren, moederdieren of vaders
    public function eenheid_fase($lidid,$M,$J,$V,$Sekse,$Ouder) {
        $vw_totaalFase = mysqli_query($this->db,"
SELECT e.eenheid 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) oudr on (s.schaapId = oudr.schaapId)
WHERE true
  AND h.skip = 0
  AND month(h.datum) = $M
  AND date_format(h.datum,'%Y') = $J
  AND i.artId = $V
  AND ".$Sekse."
  AND ".$Ouder."
  AND eu.lidId = '".mysqli_real_escape_string($this->db,$lidid)."'
GROUP BY e.eenheid
");
if($vw_totaalFase) {
    $row = mysqli_fetch_assoc($vw_totaalFase);
                return $row['eenheid'];
        }
        return FALSE; // Foutafhandeling
}

public function zoekStapel($lidId) {
    $zoek_stapel = mysqli_query($this->db,"
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and isnull(st.rel_best)
");
$stapel = null;
    while($zs = mysqli_fetch_array($zoek_stapel))
        { $stapel = $zs['aant']; }
    return $stapel;
}

// TODO: #0004136 dit is de count-query bij zoekUitgeschaarden. Maak dat duidelijk.
// Misschien de select parameteriseren?
public function countUitgeschaarden($lidId) {
    $vw = mysqli_query($this->db,"
SELECT count(*) aantal
FROM tblSchaap s
 join (
     SELECT schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
     GROUP BY schaapId
  ) mst on (mst.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId) 
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 join tblStal st on (st.stalId = mst.stalId)
 join (
     SELECT relId, naam
     FROM tblPartij p
      join tblRelatie r on (p.partId = r.partId)
     WHERE p.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 ) best on (best.relId = st.rel_best)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and haf.actId = 10
");
return mysqli_num_rows($vw);
}

public function zoekUitgeschaarden($lidId, $Karwerk) {
return mysqli_query($this->db,"
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder, date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, best.naam, haf.actId
FROM tblSchaap s
 join (
     SELECT schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
     GROUP BY schaapId
  ) mst on (mst.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId) 
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 join tblStal st on (st.stalId = mst.stalId)
 join (
     SELECT relId, naam
     FROM tblPartij p
      join tblRelatie r on (p.partId = r.partId)
     WHERE p.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 ) best on (best.relId = st.rel_best)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and haf.actId = 10
");
}

public function aanwezigen($lidId, $Karwerk) {
return mysqli_query($this->db,"
SELECT u.ubn, s.transponder, right(s.levensnummer, $Karwerk) werknum, s.levensnummer, date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, scan.dag_sort, scan.dag, haf.actId
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0) 
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join (
     SELECT contr_scan.schaapId, date_format(datum,'%Y%m%d') dag_sort, date_format(datum,'%d-%m-%Y') dag
     FROM tblHistorie h
      join (
         SELECT max(hisId) hismx, schaapId
         FROM tblHistorie h
          join tblStal st on (h.stalId = st.stalId)
         WHERE actId = 22 and h.skip = 0 and lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
         GROUP BY schaapId
    ) contr_scan on (contr_scan.hismx = h.hisId)
 ) scan on (scan.schaapId = s.schaapId)
 left join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and isnull(haf.actId)
ORDER BY u.ubn, right(s.levensnummer, $Karwerk)
");
}

public function aantal_meerlingen_perOoi($Lidid,$Ooiid,$Nr) {
$zoek_meerlingen = "
SELECT v.volwId
FROM tblSchaap mdr
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE isnull(stm.rel_best) and st.lidId = '".mysqli_real_escape_string($this->db,$Lidid)."' and h.actId = 1 and mdr.schaapId = '".mysqli_real_escape_string($this->db,$Ooiid)."' and h.skip = 0
GROUP BY v.volwId
HAVING count(st.schaapId) in ('".mysqli_real_escape_string($this->db,$Nr)."')
ORDER BY date_format(h.datum,'%Y') desc, date_format(h.datum,'%m') desc
";
return $zoek_meerlingen;
} 

public function periode($Volwid) {
    $zoek_periode = mysqli_query($this->db,"
SELECT date_format(h.datum,'%Y') jaar, date_format(h.datum,'%m')*1 mndnr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($this->db,$Volwid)."' and h.actId = 1 and h.skip = 0
GROUP BY date_format(h.datum,'%Y'), date_format(h.datum,'%m')
") or die(mysqli_error($this->db));
while($a = mysqli_fetch_assoc($zoek_periode)) { 
    return array(1=>$a['mndnr'], $a['jaar']); 
    }
}

public function de_lammeren($Volwid,$KarWerk) {
    $zoek_lammeren = mysqli_query($this->db,"
SELECT coalesce(geslacht,'---') geslacht, coalesce(right(s.levensnummer,$KarWerk),'-------') werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".mysqli_real_escape_string($this->db,$Volwid)."' and h.actId = 1 and h.skip = 0
ORDER BY coalesce(geslacht,'zzz')
") or die(mysqli_error($this->db));
while($a = mysqli_fetch_assoc($zoek_lammeren)) { $rr[] = array($a['geslacht'], $a['werknr']); 
 }
return $rr;
}

public function meerlingen_perOoi_perJaar($Lidid,$Ooiid,$Jaar,$Maand) {
$zoek_meerlingen = "
SELECT count(lam.schaapId) aant, v.volwId
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 
WHERE st.lidId = ".mysqli_real_escape_string($this->db,$Lidid)." and mdr.schaapId = ".mysqli_real_escape_string($this->db,$Ooiid)." and h.actId = 1 and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($this->db,$Jaar)."' and date_format(h.datum,'%m') = '".mysqli_real_escape_string($this->db,$Maand)."' and h.skip = 0
GROUP BY v.volwId
ORDER BY date_format(h.datum,'%Y%m') desc
";
//echo $zoek_meerlingen;
$zoek_meerlingen = mysqli_query($this->db,$zoek_meerlingen) or die (mysqli_error($this->db));    
    while($mrl = mysqli_fetch_assoc($zoek_meerlingen))
            {
                return array($mrl['aant'], $mrl['volwId']); 
            } 
} 

public function aantal_perGeslacht($Volwid,$Geslacht,$Jaar,$Maand) {
    $zoek_aantal_geslacht = mysqli_query($this->db,"
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = ".mysqli_real_escape_string($this->db,$Volwid)." and s.geslacht = '".mysqli_real_escape_string($this->db,$Geslacht)."' and h.actId = 1 and date_format(h.datum,'%m') = ".mysqli_real_escape_string($this->db,$Maand)." and date_format(h.datum,'%Y') = ".mysqli_real_escape_string($this->db,$Jaar)." and h.skip = 0        
") or die(mysqli_error($this->db));
while($a = mysqli_fetch_assoc($zoek_aantal_geslacht)) { return $a['aant']; }
}

public function afleverdatum($lidId) {
    return mysqli_query($this->db,"
SELECT min(h.hisId) hisId, count(h.hisId) aantal, date_format(h.datum,'%d-%m-%Y') datum, r.relId, p.naam 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and a.af = 1 and h.skip = 0
GROUP BY h.datum, r.relId, p.naam
ORDER BY r.uitval, h.datum desc
");
}

public function zoek_ooien_in_jaar($lidId, $jaar) {
    $vw = mysqli_query($this->db,"
SELECT count(s.schaapId) aant_mdr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3 and skip = 0 and date_format(datum,'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."'
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".mysqli_real_escape_string($this->db,$jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi'
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
    }

public function zoek_lammeren_in_jaar($lidId, $jaar, $jan1) {
   $vw = mysqli_query($this->db,"
SELECT count(s.schaapId) aant_lam
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3 and skip = 0
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".mysqli_real_escape_string($this->db,$jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE (isnull(ouder.datum) or ouder.datum > '".mysqli_real_escape_string($this->db,$jan1)."')
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
}

public function zoek_aantal_sterfte_lammeren_in_jaar($lidId, $jaar) {
$vw = mysqli_query($this->db,"
SELECT count(s.schaapId) aant_lam
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 14 and skip = 0
 ) dood on (st.stalId = dood.stalId)
 left join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3 and skip = 0
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId, min(h.datum) tempmin
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".mysqli_real_escape_string($this->db,$jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE isnull(ouder.datum) and date_format(dood.datum,'%Y') = '".mysqli_real_escape_string($this->db,$jaar)."'
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
}

public function zoek_aantal_sterfte_moeder_in_jaar($lidId, $jaar) {
   $vw = mysqli_query($this->db,"
SELECT count(s.schaapId) aant_mdr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 14 and skip = 0
 ) dood on (st.stalId = dood.stalId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3 and skip = 0 and date_format(datum,'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."'
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId, min(h.datum) tempmin
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".mysqli_real_escape_string($this->db,$jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE skip = 0 and st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".mysqli_real_escape_string($this->db,$jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi' and date_format(dood.datum,'%Y') = '".mysqli_real_escape_string($this->db,$jaar)."'
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
    }

public function zoek_worpen_in_jaar($lidId, $jaar) {
   $vw = mysqli_query($this->db,"
SELECT count(distinct v.mdrId) aant_worp
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (s.volwId = v.volwId)
 join tblHistorie hg on (hg.stalId = st.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hkoop on (hkoop.stalId = st.stalId and hkoop.actId = 2 and hkoop.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and date_format(hg.datum,'%Y') = '".mysqli_real_escape_string($this->db,$jaar)."' and isnull(hkoop.hisId)
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
}

}
