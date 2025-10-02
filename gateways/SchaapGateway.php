<?php

class SchaapGateway extends Gateway {

    public function zoek_schaapid($fldLevnr) {
        $vw = $this->db->query("
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".$this->db->real_escape_string($fldLevnr)."'");
$rec = $vw->fetch_assoc();
return $rec['schaapId'] ?? 0;
            # TODO: #0004137 nullcheck. Als fldLevnr niet voorkomt, is zs geen array, en dat geeft een warning.
        # Dit wijst erop dat de code dingen doet die niet bij elkaar horen.
    }

    public function zoek_stalid($lidId) {
$vw = $this->db->query("
SELECT st.stalId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".$this->db->real_escape_string($lidId)."' 
GROUP BY st.stalId  
");
if ($vw->num_rows == 0) {
    return null;
}
return $vw->fetch_row()[0];
    }

    public function zoek_vaders($lidId, $Karwerk) {
$vw = $this->db->query("
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur, ' ', halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = '".$this->db->real_escape_string($lidId)."' 
GROUP BY st.stalId, levensnummer
ORDER BY right(levensnummer, $Karwerk)  
");
        $records = [];
        while ($record = $vw->fetch_assoc()) {
            $records[] = $record;
        }
        return $records;
    }

    public function zoek_werknummer($mdrId, $Karwerk) {
        $vw = $this->db->query("
SELECT right(levensnummer, $Karwerk) werknr
FROM tblSchaap
WHERE schaapId = '" . $this->db->real_escape_string($mdrId) . "'
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
    }

    public function levnr_exists_outside($fldLevnr, $schaapId): bool {
        $vw = $this->db->query("
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".$this->db->real_escape_string($fldLevnr)."'
 and schaapId <> '".$this->db->real_escape_string($schaapId)."'");
$rec = $vw->fetch_assoc();
return $rec['aant'] > 0;
    }

    // deze handeling heet "change" omdat het sleutelveld verandert
    public function changeLevensnummer($old, $new) {
        $this->db->query("
UPDATE tblSchaap SET levensnummer = '".$this->db->real_escape_string($new)."'
        WHERE levensnummer = '".$this->db->real_escape_string($old)."' ");
    }

    public function updateGeslacht($levensnummer, $geslacht) {
        $this->db->query("
UPDATE tblSchaap SET geslacht='".$this->db->real_escape_string($geslacht)."'
        WHERE levensnummer = '".$this->db->real_escape_string($levensnummer)."' ");
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
$vw = $this->db->query("
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = '".$this->db->real_escape_string($lidid)."' and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");
$row = $vw->fetch_assoc();
return $row['aant'];
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
        // TODO: #0004177 is de left join met prnt nodig?
        $vw = $this->db->query("
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join (
     SELECT lidId, schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".$this->db->real_escape_string($lidid)."'
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
WHERE mst.lidId = '".$this->db->real_escape_string($lidid)."' and ".$Sekse." and ".$Ouder." 
");
    $row = $vw->fetch_assoc();
    return $row['aant'];
    }

    // Functie die het aantal lammeren, moederdieren of vaders telt
    public function med_aantal_fase($lidid,$M,$J,$V,$Sekse,$Ouder) {
        $vw = $this->db->query("
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
  AND st.lidId = '".$this->db->real_escape_string($lidid)."'
  AND h.actId = 8
GROUP BY date_format(h.datum,'%Y%m')
");
            $row = $vw->fetch_assoc();
            return $row['werknrs'];
    }

    // Functie die de hoeveelheid voer berekend per lammeren, moederdieren of vaders
    public function voer_fase($lidid,$M,$J,$V,$Sekse,$Ouder) { 
        $vw = $this->db->query("
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
  AND st.lidId = '".$this->db->real_escape_string($lidid)."'
        GROUP BY concat(date_format(h.datum,'%Y'),month(h.datum))
        ");
            $row = $vw->fetch_assoc();
                        return $row['totats'];
    }

    // zou dit in EenheidGateway horen?
    // Functie die de eenheid ophaalt per lammeren, moederdieren of vaders
    public function eenheid_fase($lidid,$M,$J,$V,$Sekse,$Ouder) {
        $vw = $this->db->query("
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
  AND eu.lidId = '".$this->db->real_escape_string($lidid)."'
GROUP BY e.eenheid
");
if($vw->num_rows) {
    $row = $vw->fetch_assoc();
                return $row['eenheid'];
        }
        return FALSE; // Foutafhandeling
}

public function zoekStapel($lidId) {
    $vw = $this->db->query("
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(st.rel_best)
");
$stapel = null;
    while($zs = $vw->fetch_assoc())
        { $stapel = $zs['aant']; }
    return $stapel;
}

public function countUitgeschaarden($lidId) {
    $vw = $this->db->query("SELECT count(*) aantal" . $this->fromUitgeschaarden($lidId));
    return $vw->fetch_row()[0];
}

public function zoekUitgeschaarden($lidId, $Karwerk) {
    return $this->db->query("
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder, date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, best.naam, haf.actId
" . $this->fromUitgeschaarden($lidId));
}

private function fromUitgeschaarden($lidId) {
    return "
FROM tblSchaap s
 join (
     SELECT schaapId, max(stalId) stalId
     FROM tblStal
     WHERE lidId = '".$this->db->real_escape_string($lidId)."'
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
     WHERE p.lidId = '".$this->db->real_escape_string($lidId)."'
 ) best on (best.relId = st.rel_best)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and haf.actId = 10
";
}

public function aanwezigen($lidId, $Karwerk) {
return $this->db->query("
SELECT u.ubn, s.transponder, right(s.levensnummer, $Karwerk) werknum, s.levensnummer,
 date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm,
 s.geslacht, prnt.datum aanw, scan.dag_sort, scan.dag, haf.actId
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
         WHERE actId = 22 and h.skip = 0 and lidId = '".$this->db->real_escape_string($lidId)."'
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
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(haf.actId)
ORDER BY u.ubn, right(s.levensnummer, $Karwerk)
");
}

public function aantal_meerlingen_perOoi($Lidid,$Ooiid,$Nr) {
return $this->db->query("
SELECT v.volwId
FROM tblSchaap mdr
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE isnull(stm.rel_best)
 and st.lidId = '".$this->db->real_escape_string($Lidid)."'
 and h.actId = 1
 and mdr.schaapId = '".$this->db->real_escape_string($Ooiid)."'
 and h.skip = 0
GROUP BY v.volwId
HAVING count(st.schaapId) in ('".$this->db->real_escape_string($Nr)."')
ORDER BY date_format(h.datum,'%Y') desc, date_format(h.datum,'%m') desc
");
} 

// deze query kijkt niet of stm.rel_best null is. Waarom niet?
public function meerlingen_perOoi_perJaar($Lidid,$Ooiid,$Jaar,$Maand) {
    $zoek_meerlingen = $this->db->query("
SELECT count(lam.schaapId) aant, v.volwId
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE st.lidId = ".$this->db->real_escape_string($Lidid)."
 and mdr.schaapId = ".$this->db->real_escape_string($Ooiid)."
 and h.actId = 1
 and date_format(h.datum,'%Y') = '".$this->db->real_escape_string($Jaar)."'
 and date_format(h.datum,'%m') = '".$this->db->real_escape_string($Maand)."'
 and h.skip = 0
GROUP BY v.volwId
ORDER BY date_format(h.datum,'%Y%m') desc
");    
if ($zoek_meerlingen->num_rows) {
    return $zoek_meerlingen->fetch_row();
}
return [null, null];
} 

public function periode($Volwid) {
    $vw = $this->db->query("
SELECT date_format(h.datum,'%Y') jaar, date_format(h.datum,'%m')*1 mndnr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".$this->db->real_escape_string($Volwid)."' and h.actId = 1 and h.skip = 0
GROUP BY date_format(h.datum,'%Y'), date_format(h.datum,'%m')
");
if ($vw->num_rows) {
    # een stuk makkelijker wanneer de afnemers gewoon naar ->maand en ->jaar kunnen vragen ...
    return array_merge([0], array_values($vw->fetch_assoc()));
}
return [0, '', ''];
}

public function de_lammeren($Volwid,$KarWerk) {
    $zoek_lammeren = $this->db->query("
SELECT coalesce(geslacht,'---') geslacht, coalesce(right(s.levensnummer,$KarWerk),'-------') werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = '".$this->db->real_escape_string($Volwid)."' and h.actId = 1 and h.skip = 0
ORDER BY coalesce(geslacht,'zzz')
");
if ($zoek_lammeren->num_rows) {
    return $zoek_lammeren->fetch_row();
}
return [null, null];
}

public function aantal_perGeslacht($Volwid,$Geslacht,$Jaar,$Maand) {
    $vw = $this->db->query("
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = ".$this->db->real_escape_string($Volwid)."
 and s.geslacht = '".$this->db->real_escape_string($Geslacht)."'
 and h.actId = 1
 and date_format(h.datum,'%m') = ".$this->db->real_escape_string($Maand)."
 and date_format(h.datum,'%Y') = ".$this->db->real_escape_string($Jaar)."
 and h.skip = 0        
");
    return $vw->fetch_row()[0];
}

public function afleverdatum($lidId) {
    return $this->db->query("
SELECT min(h.hisId) hisId, count(h.hisId) aantal, date_format(h.datum,'%d-%m-%Y') datum, r.relId, p.naam 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and a.af = 1 and h.skip = 0
GROUP BY h.datum, r.relId, p.naam
ORDER BY r.uitval, h.datum desc
");
}

public function zoek_ooien_in_jaar($lidId, $jaar) {
    $vw = $this->db->query("
SELECT count(s.schaapId) aant_mdr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3 and skip = 0 and date_format(datum,'%Y') <= '".$this->db->real_escape_string($jaar)."'
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".$this->db->real_escape_string($jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".$this->db->real_escape_string($jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi'
");
        return $vw->fetch_row()[0];
    }

public function zoek_lammeren_in_jaar($lidId, $jaar, $jan1) {
   $vw = $this->db->query("
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
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".$this->db->real_escape_string($jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".$this->db->real_escape_string($jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE (isnull(ouder.datum) or ouder.datum > '".$this->db->real_escape_string($jan1)."')
");
        return $vw->fetch_row()[0];
}

public function zoek_aantal_sterfte_lammeren_in_jaar($lidId, $jaar) {
$vw = $this->db->query("
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
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".$this->db->real_escape_string($jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".$this->db->real_escape_string($jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE isnull(ouder.datum) and date_format(dood.datum,'%Y') = '".$this->db->real_escape_string($jaar)."'
");
        return $vw->fetch_row()[0];
}

public function zoek_aantal_sterfte_moeder_in_jaar($lidId, $jaar) {
   $vw = $this->db->query("
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
    WHERE actId = 3 and skip = 0 and date_format(datum,'%Y') <= '".$this->db->real_escape_string($jaar)."'
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId, min(h.datum) tempmin
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum),'%Y') <= '".$this->db->real_escape_string($jaar)."')
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum),'%Y') >= '".$this->db->real_escape_string($jaar)."' or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi' and date_format(dood.datum,'%Y') = '".$this->db->real_escape_string($jaar)."'
");
        return $vw->fetch_row()[0];
    }

public function zoek_worpen_in_jaar($lidId, $jaar) {
   $vw = $this->db->query("
SELECT count(distinct v.mdrId) aant_worp
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (s.volwId = v.volwId)
 join tblHistorie hg on (hg.stalId = st.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hkoop on (hkoop.stalId = st.stalId and hkoop.actId = 2 and hkoop.skip = 0)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and date_format(hg.datum,'%Y') = '".$this->db->real_escape_string($jaar)."'
 and isnull(hkoop.hisId)
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
}

public function eigen_schapen($lidId) {
    return $this->db->query("
SELECT s.schaapId,  s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
");
}

public function werknummers($lidId, $Karwerk) {
    return $this->db->query("
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and s.levensnummer is not null
GROUP BY s.schaapId, right(s.levensnummer,$Karwerk)
ORDER BY right(s.levensnummer,$Karwerk)
"); 
}

public function halsnummers($lidId) {
    return $this->db->query("
SELECT s.schaapId, concat(st.kleur,' ',st.halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and st.kleur is not null
 and st.halsnr is not null
 and isnull(st.rel_best)
GROUP BY s.schaapId, concat(st.kleur,' ',st.halsnr)
ORDER BY st.kleur, st.halsnr
"); 
}

public function ooien($lidId, $Karwerk) {
    return $this->db->query("
SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) werknr_ooi
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and mdr.levensnummer is not null
GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk)
ORDER BY right(mdr.levensnummer,$Karwerk)
");
}

public function rammen($lidId, $Karwerk) {
    return $this->db->query("
SELECT vdr.schaapId, right(vdr.levensnummer,$Karwerk) werknr_ram
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and vdr.levensnummer is not null
GROUP BY vdr.schaapId, right(vdr.levensnummer,$Karwerk)
ORDER BY right(vdr.levensnummer,$Karwerk)
");
}

    public function getZoekWhere($postdata) {
        $parts = [];
        $levnr = $postdata['kzlLevnr_'] ?? '';
        $werknr = $postdata['kzlWerknr_'] ?? '';
        $halsnr = $postdata['kzlHalsnr_'] ?? '';
        // er waren geen locals voor ooi of ram.
        if ($levnr == 'Geen') {
            $parts[] = "isnull(s.levensnummer)";
        } elseif (!empty($levnr)) {
            $parts[] = "s.schaapId = $levnr ";
        }
        if ($werknr == 'Geen') {
            $parts[] = " isnull(s.levensnummer) ";
        } elseif (!empty($werknr)) {
            $parts[] = "s.schaapId = $postdata[kzlWerknr_] ";
        }
        if (!empty($postdata['kzlHalsnr_'])) {
            $parts[] = "s.schaapId = " . $halsnr;
        }
        if (!empty($postdata['kzlOoi_'])) {
            $parts[] = "mdr.schaapId = $postdata[kzlOoi_] ";
        }
        if (!empty($postdata['kzlRam_'])) {
            $parts[] = "vdr.schaapId = $postdata[kzlRam_] ";
        }
        return implode(' and ', $parts);
    }

public function zoekAankoop($lidId, $where) {
    $aankoop = $this->db->query("
SELECT date_format(hg.datum,'%d-%m-%Y') gebdm, koop.datum dmkoop
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
    SELECT st.schaapId, h.datum 
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum 
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.actId = 2 and h.skip = 0
 ) koop on (koop.schaapId = s.schaapId)
WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
 and $where
");
if ($aankoop->num_rows > 0) {
    return $aankoop->fetch_assoc();
}
return ['gebdm' => null, 'dmkoop' => null];
             }

public function zoekSchaap($where) {
    $vw = $this->db->query("
SELECT s.schaapId
FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE $where
");
if ($vw->num_rows > 0) {
    return $vw->fetch_row()[0];
}
return null;
 }

public function zoekresultaat($lidId, $where, $Karwerk) {
    return $this->run_query("
SELECT s.transponder, concat(st.kleur,' ',st.halsnr) halsnr, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr,
 s.fokkernr, right(mdr.levensnummer,$Karwerk) werknr_ooi, right(vdr.levensnummer,$Karwerk) werknr_ram, r.ras, s.geslacht,
 ouder.datum dmaanw, coalesce(lower(haf.actie),'aanwezig') status, haf.af,
hs.datum dmspn, hs.kg spnkg, afl.datum dmafl, afl.kg aflkg, hg.datum dmgeb, date_format(hg.datum,'%d-%m-%Y') gebdm,
 hg.kg gebkg, date_format(aanv1.datum,'%d-%m-%Y') aanvdm, aanv1.datum dmaanv, aanv1.kg aankkg

FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 join (
    SELECT min(stalId) stalId, schaapId
    FROM tblStal
    WHERE lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY schaapId
 ) st1 on (s.schaapId = st1.schaapId)
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY schaapId
 ) stm on (s.schaapId = stm.schaapId)
 join tblStal st on (stm.stalId = st.stalId)
 left join (
    SELECT st.schaapId, h.datum, h.kg
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId)
 left join (
    SELECT st.stalId, h.datum, h.kg
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 2 and h.skip = 0
 ) aanv1 on (st1.stalId = aanv1.stalId)
 left join (
    SELECT st.stalId, h.datum, h.kg
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) hs on (st.stalId = hs.stalId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId)
 left join (
    SELECT st.stalId, a.actie, a.af
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join tblVolwas v on (v.volwId = s.volwId)
     left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
     left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
    WHERE st.lidId = '".$this->db->real_escape_string($lidId) /* tblSchaap mdr en tblSchaap vdr is voor als er op moeder of vader wordt gezocht*/."' and $where and a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 left join (
    SELECT st.schaapId, h.datum, h.kg
    FROM tblHistorie h 
     join 
     (
        SELECT s.levensnummer, min(h.hisId) hisId 
        FROM tblStal st
         join tblSchaap s on (st.schaapId = s.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
         left join tblVolwas v on (v.volwId = s.volwId)
         left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
         left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
        WHERE st.lidId = '".$this->db->real_escape_string($lidId)
        /* tblSchaap mdr en tblSchaap vdr is voor als er op moeder of vader wordt gezocht*/
."' and $where and h.actId = 12 and h.skip = 0
        GROUP BY s.levensnummer
     ) afl on (afl.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0
 ) afl on (afl.schaapId = s.schaapId)
 left join tblRas r on(s.rasId = r.rasId)
WHERE $where
ORDER BY if(isnull(s.levensnummer),'Geen',''), dmgeb desc, status
"); 
}

public function zoekGeschiedenis($lidId, $schaapId, $Karwerk) {
    return $this->db->query("
SELECT his.hisId, his.ubn, his.levensnummer, his.geslacht, his.datum, his.date, his.actId, his.actie, his.actie_if, his.kg, date_format(his.dmaanw,'%Y-%m-%d 00:00:00') dmaanw, toel.toel, his.hisId hiscom, comment
FROM
(
    SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum dmaanw, h.comment
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
     left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
    WHERE s.schaapId = '".$this->db->real_escape_string($schaapId)."' and st.lidId = '".$this->db->real_escape_string($lidId)."' and h.skip = 0
     and not exists (
        SELECT datum 
        FROM tblHistorie ha 
         join tblStal st on (ha.stalId = st.stalId)
         join tblSchaap s on (st.schaapId = s.schaapId)
        WHERE actId = 2 and h.skip = 0 and h.datum = ha.datum and h.actId = ha.actId+1 and s.schaapId = '".$this->db->real_escape_string($schaapId)."')

  union

    SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie,4) actie_if, h.kg, ouder.datum, h.comment
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     join tblActie a on (a.actId = h.actId)
     left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
    WHERE s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.actId = 1 and h.skip = 0
) his
left join 
(
    SELECT 'adoptie lammeren' qry, h.hisId, concat('Bij ooi ', right(mdr.levensnummer,$Karwerk)) toel
    FROM tblHistorie h
     join impAgrident vp on (h.datum = vp.datum)
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId and vp.levensnummer = s.levensnummer)
     left join (
         SELECT levensnummer 
         FROM tblSchaap mdr 
          join tblStal st on (mdr.schaapId = st.schaapId)
         WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
     ) mdr on (vp.moeder = mdr.levensnummer)
    WHERE h.actId = 15 and h.skip = 0 and vp.actId = 15 and vp.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."'

Union

    SELECT 'lammeren in hok geplaatst excl. adoptie' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

    FROM tblHok ho
     join tblBezet b on (b.hokId = ho.hokId)
     join tblHistorie h on (h.hisId = b.hisId)
     join tblActie a on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     left join (
        SELECT h1.hisId hisv, min(h2.hisId) hist
        FROM tblHistorie h1
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblStal st on (st.stalId = h1.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
        and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
        GROUP BY h1.hisId
     ) uit on (uit.hisv = b.hisId)
     left join tblHistorie ht on (ht.hisId = uit.hist)
      left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE a.aan = 1 and h.skip = 0 and h.actId != 15 and ho.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
     and (isnull(prnt.schaapId) or (prnt.datum > h.datum))

Union

    SELECT 'Volwassenen in hok geplaatst' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr),' voor ',datediff(coalesce(ht.datum,curdate()), h.datum), If(datediff(coalesce(ht.datum,curdate()), h.datum) = 1, ' dag', ' dagen')) toel

    FROM tblHok ho
     join tblBezet b on (b.hokId = ho.hokId)
     join tblHistorie h on (h.hisId = b.hisId)
     join tblActie a on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     left join (
        SELECT h1.hisId hisv, min(h2.hisId) hist
        FROM tblHistorie h1
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblStal st on (st.stalId = h1.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
        and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY h1.hisId
     ) uit on (uit.hisv = b.hisId)
     left join tblHistorie ht on (ht.hisId = uit.hist)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE a.aan = 1 and h.skip = 0 and ho.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
     and prnt.datum <= h.datum

Union

    SELECT 'Volwassenen hok verlaten' qry, uit.hist hisId, concat(ho.hoknr,' verlaten ') toel

    FROM tblHok ho
     join tblBezet b on (b.hokId = ho.hokId)
     join tblHistorie h on (h.hisId = b.hisId)
     join tblActie a on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT h1.hisId hisv, min(h2.hisId) hist
        FROM tblHistorie h1
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblStal st on (st.stalId = h1.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
        and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY h1.hisId
     ) uit on (uit.hisv = b.hisId)
     left join tblHistorie ht on (ht.hisId = uit.hist)
    WHERE a.aan = 1 and h.skip = 0 and ho.lidId = '".$this->db->real_escape_string($lidId)."' and st.schaapId = '".$this->db->real_escape_string($schaapId)."'
     and ht.actId = 7

Union

    SELECT 'toel_afvoer excl dood met een reden' qry, h.hisId, p.naam
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblRelatie r on (st.rel_best = r.relId)
     join tblPartij p on (r.partId = p.partId)
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and a.af = 1 and h.skip = 0
     and (h.actId != 14 or (h.actId = 14 and isnull(s.redId)))

Union

    SELECT 'toel_afvoer dood met een reden' qry, h.hisId, re.reden
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblReden re on (s.redId = re.redId)
     join tblRelatie r on (st.rel_best = r.relId)
     join tblPartij p on (r.partId = p.partId)
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and a.af = 1 and h.skip = 0
     and h.actId = 14 and s.redId is not null

Union

    SELECT 'medicatie' qry, n.hisId, concat(round(sum(n.nutat*n.stdat),2),' ', e.eenheid,'  ', a.naam,'  ',coalesce(i.charge,'')) toel
    FROM tblNuttig n
     join tblInkoop i on (n.inkId = i.inkId)
     join tblArtikel a on (a.artId = i.artId)
     join tblEenheiduser eu on (eu.enhuId = a.enhuId)
     join tblEenheid e on (e.eenhId = eu.eenhId)
    WHERE eu.lidId = '".$this->db->real_escape_string($lidId)."'
    GROUP BY n.hisId, e.eenheid, a.naam, i.charge

Union

    SELECT 'omnummeren' qry,  h.hisId, concat('Oud nummer ', h.oud_nummer) toel
    From tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
    Where st.lidId = 13 and h.actId = 17 and h.skip = 0

) toel
on (his.hisId = toel.hisId)

UNION 

SELECT NULL hisId, u.ubn, s.levensnummer, s.geslacht, date_format(p.dmafsluit,'%d-%m-%Y') datum, p.dmafsluit date, NULL actId, 'Gevoerd' actie, NULL actie_if, NULL kg, NULL dmaanw, concat(coalesce(round(datediff(ht.datum,hv.datum) * vr.kg_st,2),0), ' kg ', lower(a.naam), ' t.b.v. ', lower(h.hoknr)) toel, NULL hiscom, NULL comment
FROM tblBezet b
 join tblPeriode p on (p.periId = b.periId)
 join tblHok h on (h.hokId = p.hokId)
 join tblHistorie hv on (hv.hisId = b.hisId)
 join tblStal st on (st.stalId = hv.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join
     (
        SELECT b.bezId, min(his.hisId) hist
        FROM tblPeriode p
         join tblBezet b on (p.periId = b.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblHistorie his on (st.stalId = his.stalId)
         join tblActie a on (a.actId = his.actId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and his.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
         and (a.aan = 1 or a.uit = 1)
         and his.hisId > b.hisId
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join 
(
    SELECT v.periId, v.inkId, v.nutat/sum(datediff(ht.datum,hv.datum)) kg_st
    FROM tblVoeding v
     join tblPeriode p on (v.periId = p.periId)
     join tblBezet b on (p.periId = b.periId)
     join tblHistorie hv on (hv.hisId = b.hisId)
     join
     (
        SELECT b.bezId, min(his.hisId) hist
        FROM tblBezet b
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblHistorie his on (st.stalId = his.stalId)
         join tblActie a on (a.actId = his.actId)
         join (
            SELECT b.periId
            FROM tblBezet b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (h.stalId = st.stalId)
             join tblSchaap s on (s.schaapId = st.schaapId)
            WHERE h.skip = 0 and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
         ) peri_obv_schaap on (peri_obv_schaap.periId = b.periId)
        WHERE (a.aan = 1 or a.uit = 1)
         and his.hisId > b.hisId and h.skip = 0 and his.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join tblHistorie ht on (ht.hisId = uit.hist)
    GROUP BY v.periId, v.inkId
) vr on (vr.periId = b.periId)
 join tblInkoop i on (i.inkId = vr.inkId)
 join tblArtikel a on (a.artId = i.artId)

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Geboorte gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
        SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
        FROM impRespons rsp
         join tblSchaap s on (rsp.levensnummer = s.levensnummer)
        GROUP BY rsp.reqId, rsp.levensnummer

        UNION

        SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
        FROM impRespons rsp
         join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
         join tblStal st on (h.stalId = st.stalId)
        GROUP BY rsp.reqId, rsp.levensnummer
    ) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'GER' and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Aanvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
        SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
        FROM impRespons rsp
         join tblSchaap s on (rsp.levensnummer = s.levensnummer)
        GROUP BY rsp.reqId, rsp.levensnummer

        UNION

        SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
        FROM impRespons rsp
         join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
         join tblStal st on (h.stalId = st.stalId)
        GROUP BY rsp.reqId, rsp.levensnummer
    ) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AAN' and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Afvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
        SELECT max(rsp.respId) respId, rsp.reqId, s.schaapId, 'wanneer niet omgenummerd'
        FROM impRespons rsp
         join tblSchaap s on (rsp.levensnummer = s.levensnummer)
        GROUP BY rsp.reqId, rsp.levensnummer

        UNION

        SELECT max(rsp.respId) respId, rsp.reqId, st.schaapId, 'wanneer wel omgenummerd'
        FROM impRespons rsp
         join tblHistorie h on (rsp.levensnummer = h.oud_nummer)
         join tblStal st on (h.stalId = st.stalId)
        GROUP BY rsp.reqId, rsp.levensnummer
    ) id on (id.schaapId = s.schaapId and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'AFV' and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.skip = 0 and m.skip = 0

UNION 

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Uitval gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
        SELECT max(respId) respId, reqId, levensnummer
        FROM impRespons
        GROUP BY reqId, levensnummer
    ) id on (id.levensnummer = s.levensnummer and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'DOO' and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld,'%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Omnummeren gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ',rs.foutmeld) else concat('meldnr : ',rs.meldnr) end toel, NULL hiscom, NULL comment
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join (
        SELECT max(respId) respId, reqId, levensnummer_new
        FROM impRespons
        GROUP BY reqId, levensnummer
    ) id on (id.levensnummer_new = s.levensnummer and id.reqId = r.reqId)
 join impRespons rs on (id.respId = rs.respId )
 left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)
 
WHERE r.dmmeld is not null and r.code = 'VMD' and st.lidId = '".$this->db->real_escape_string($lidId)."' and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and h.skip = 0 and m.skip = 0

UNION

SELECT hisId1 hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worp1,'%d-%m-%Y') datum, mdr.worp1 date, NULL actId, 'Eerste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
FROM
 (
    SELECT u.ubn, s.levensnummer, s.geslacht, ouder.datum dmaanw, min(hl.datum) worp1, min(hl.hisId) hisId1
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblVolwas v on (v.mdrId = s.schaapId)
     join tblSchaap lam on (lam.volwId = v.volwId)
     join tblStal sl on (lam.schaapId = sl.schaapId)
     join tblHistorie hl on (sl.stalId = hl.stalId)
     left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)

    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and sl.lidId = '".$this->db->real_escape_string($lidId)."' and hl.actId = 1 and hl.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
    GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
    SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn 
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and h.actId = 1 and h.skip = 0 and mdr.schaapId = '".$this->db->real_escape_string($schaapId)."'
    GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worp1 = lam.datum)

UNION

SELECT hisend hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worpend,'%d-%m-%Y') datum, mdr.worpend date, NULL actId, 'Laatste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
FROM
 (
    SELECT u.ubn, s.levensnummer, s.geslacht, ouder.datum dmaanw, max(hl.datum) worpend, max(hl.hisId) hisend
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblVolwas v on (v.mdrId = s.schaapId)
     join tblSchaap lam on (lam.volwId = v.volwId)
     join tblStal sl on (lam.schaapId = sl.schaapId)
     join tblHistorie hl on (sl.stalId = hl.stalId)
     left join (
        SELECT s.schaapId, h.datum 
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = '".$this->db->real_escape_string($schaapId)."'
     ) ouder on (ouder.schaapId = s.schaapId)

     left join (
        SELECT moe.levensnummer, moe.geslacht, min(hl.datum) worp1
        FROM tblStal st
         join tblSchaap moe on (moe.schaapId = st.schaapId)
         join tblVolwas v on (v.mdrId = moe.schaapId)
         join tblSchaap lam on (lam.volwId = v.volwId)
         join tblStal sl on (lam.schaapId = sl.schaapId)
         join tblHistorie hl on (sl.stalId = hl.stalId)

        WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and hl.actId = 1 and hl.skip = 0 and moe.schaapId = '".$this->db->real_escape_string($schaapId)."'
        GROUP BY moe.levensnummer, moe.geslacht
     ) lam1 on (lam1.levensnummer = s.levensnummer and lam1.worp1 = hl.datum)
    
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and sl.lidId = '".$this->db->real_escape_string($lidId)."' and hl.actId = 1 and s.schaapId = '".$this->db->real_escape_string($schaapId)."' and isnull(lam1.worp1)
    GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
    SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn 
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE st.lidId = '".$this->db->real_escape_string($lidId)."' and h.actId = 1 and h.skip = 0 and mdr.schaapId = '".$this->db->real_escape_string($schaapId)."'
    GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worpend = lam.datum)

ORDER BY date_format(date, '%Y-%m-%d 00:00:00') desc, hisId desc
");
/*Toelichting Order by :
kg noodzakelijk eerst hok verlaten geboren en dan de(zelfde) datum van spenen
 Id noodzakelijk bij meerder overplaatsingen (recordes tblBezet) op dezelfde dag
  */
}

public function zoek_laatste_werpdatum($max_worp) {
    $vw = $this->db->query("
SELECT date_add(max(h.datum),interval 60 day) werpdate
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and s.volwId = '" . $this->db->real_escape_string($max_worp) . "'
");
if ($vw->num_rows) {
    return $vw->fetch_row()[0];
}
return null;
    }

public function resultvader($lidId, $Karwerk) {
    return $this->db->query("
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (s.schaapId = prnt.schaapId)
 join (
     SELECT schaapId, max(stalId) stalId
     FROM tblStal st
     WHERE st.lidId = '".$this->db->real_escape_string($lidId)."'
     GROUP BY schaapId
 ) mst on (s.schaapId = mst.schaapId)
 left join (
     SELECT st.stalId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) afv on (afv.stalId = mst.stalId)
WHERE s.geslacht = 'ram' and st.lidId = '".$this->db->real_escape_string($lidId)."' and isnull(afv.stalId)
ORDER BY right(s.levensnummer,$Karwerk)
"); 
    }

}
