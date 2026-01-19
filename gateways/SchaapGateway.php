<?php

class SchaapGateway extends Gateway {

    public function zoek_schaapid($fldLevnr) {
        return $this->first_field(
            <<<SQL
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = :levensnummer
SQL
        ,
            [[':levensnummer', $fldLevnr]],
            0
        );
    }

    public function zoek_schaapid_transponder($levnr) {
        return $this->first_row(
            <<<SQL
SELECT schaapId, transponder
FROM tblSchaap
WHERE levensnummer = :levnr
SQL
        , [[':levnr', $levnr]]
            , [null, null]
        );
    }

    public function zoek_stalid($lidId) {
        return $this->first_field(
            <<<SQL
SELECT st.stalId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram'
 and isnull(st.rel_best)
 and u.lidId = :lidId
GROUP BY st.stalId
SQL
        ,
            [[':lidId', $lidId]]
        );
    }

    public function zoek_staldetails($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur, ' ', halsnr) halsnr, scan
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram'
 and isnull(st.rel_best)
 and lidId = :lidId
GROUP BY st.stalId, levensnummer, scan
ORDER BY right(levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId]]
        );
    }

    public function zoek_vaders($lidId, $Karwerk) {
        $vw = $this->run_query(
            <<<SQL
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur, ' ', halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram'
 and isnull(st.rel_best)
 and u.lidId = :lidId
GROUP BY st.stalId, levensnummer
ORDER BY right(levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId]]
        );
        return $vw->fetch_all(MYSQLI_ASSOC);
    }

    public function zoek_werknummer($mdrId, $Karwerk) {
        return $this->first_field(
            <<<SQL
SELECT right(levensnummer, $Karwerk) werknr
FROM tblSchaap
WHERE schaapId = :schaapId
SQL
        ,
            [[':schaapId', $mdrId]]
        );
    }

    public function levnr_exists_outside($fldLevnr, $schaapId): bool {
        return 0 < $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblSchaap
WHERE levensnummer = :levensnummer
 and schaapId <> :schaapId
SQL
        ,
            [
                [':levensnummer', $fldLevnr],
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    // deze handeling heet "change" omdat het sleutelveld verandert
    public function changeLevensnummer($old, $new) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap SET levensnummer = :new
        WHERE levensnummer = :old
SQL
        ,
            [
                [':new', $new],
                [':old', $old],
            ]
        );
    }

    public function updateGeslacht($levensnummer, $geslacht) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap SET geslacht = :geslacht
        WHERE levensnummer = :levensnummer
SQL
        ,
            [
                [':levensnummer', $levensnummer],
                [':geslacht', $geslacht]
            ]
        );
    }

    public function updateTransponder($schaapId, $transponder) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap SET transponder = :transponder
        WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId],
                [':transponder', $transponder]
            ]
        );
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

    private function countByStalFase($lidId, $Sekse, $Ouder) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and $Sekse
 and $Ouder
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function countByStal($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
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

    private function countByFaseUitgeschaard($lidId, $Sekse, $Ouder) {
        // TODO: #0004177 is de left join met prnt nodig?
        return $this->first_field(
            <<<SQL
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join (
    SELECT u.lidId, st.schaapId, max(st.stalId) stalId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
     GROUP BY u.lidId, st.schaapId
  ) mst on (mst.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = mst.ubnId)
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
WHERE u.lidId = :lidId
 and $Sekse
 and $Ouder
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    // Functie die het aantal lammeren, moederdieren of vaders telt
    public function med_aantal_fase($lidId, $M, $J, $V, $Sekse, $Ouder) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct s.levensnummer) werknrs
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
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
  AND month(h.datum) = :month
  AND date_format(h.datum, '%Y') = :year
  AND i.artId = :artId
  AND $Sekse
  AND $Ouder
  AND u.lidId = :lidId
  AND h.actId = 8
GROUP BY date_format(h.datum, '%Y%m')
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':month', $M],
                [':year', $J],
                [':artId', $V],
            ]
        );
    }

    // Functie die de hoeveelheid voer berekent per lammeren, moederdieren of vaders
    public function voer_fase($lidId, $M, $J, $V, $Sekse, $Ouder) {
        return $this->first_field(
            <<<SQL
        SELECT round(sum(n.nutat*n.stdat), 2) totats
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
         join tblNuttig n on (h.hisId = n.hisId)
         join tblInkoop i on (n.inkId = i.inkId)
         left join (
            SELECT st.schaapId, h.hisId
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3
 and h.skip = 0
         ) oudr on (s.schaapId = oudr.schaapId)
        WHERE true
  AND h.skip = 0
  AND month(h.datum) = :month
  AND date_format(h.datum, '%Y') = :year
  AND i.artId = :artId
  AND $Sekse
  AND $Ouder
  AND u.lidId = :lidId
        GROUP BY concat(date_format(h.datum, '%Y'), month(h.datum))
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':month', $M],
                [':year', $J],
                [':artId', $V],
            ]
        );
    }

    // zou dit in EenheidGateway horen?
    // Functie die de eenheid ophaalt per lammeren, moederdieren of vaders
    public function eenheid_fase($lidId, $M, $J, $V, $Sekse, $Ouder) {
        return $this->first_field(
            <<<SQL
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
  AND month(h.datum) = :month
  AND date_format(h.datum, '%Y') = :year
  AND i.artId = :artId
  AND $Sekse
  AND $Ouder
  AND eu.lidId = :lidId
GROUP BY e.eenheid
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':month', $M],
                [':year', $J],
                [':artId', $V],
            ]
        );
    }

    public function zoekStapel($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function countUitgeschaarden($lidId) {
        return $this->first_field(
            "SELECT count(*) aantal " . $this->FROMUitgeschaarden(),
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoekUitgeschaarden($lidId, $Karwerk) {
        $FROM = $this->FROMUitgeschaarden();
        return $this->run_query(
            <<<SQL
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder,
date_format(hg.datum, '%Y%m%d') gebdm_sort, date_format(hg.datum, '%d-%m-%Y') gebdm,
s.geslacht, prnt.datum aanw, best.naam, haf.actId
    $FROM
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    private function FROMUitgeschaarden() {
        return <<<SQL
FROM tblSchaap s
 join (
     SELECT st.schaapId, max(st.stalId) stalId
     FROM tblStal st
      join tblUbn u on (st.ubnId = u.ubnId)
     WHERE u.lidId = :lidId
     GROUP BY st.schaapId
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
 join tblUbn u on (st.ubnId = u.ubnId)
 join (
     SELECT relId, naam
     FROM tblPartij p
      join tblRelatie r on (p.partId = r.partId)
     WHERE p.lidId = :lidId
 ) best on (best.relId = st.rel_best)
 join (
     SELECT h.stalId, h.actId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE u.lidId = :lidId
 and haf.actId = 10
SQL
        ;
    }

    public function aanwezigen($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT u.ubn, s.transponder, right(s.levensnummer, $Karwerk) werknum, s.levensnummer,
 date_format(hg.datum, '%Y%m%d') gebdm_sort, date_format(hg.datum, '%d-%m-%Y') gebdm,
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
     SELECT contr_scan.schaapId, date_format(datum, '%Y%m%d') dag_sort, date_format(datum, '%d-%m-%Y') dag
     FROM tblHistorie h
      join (
         SELECT max(hisId) hismx, schaapId
         FROM tblHistorie h
          join tblStal st on (h.stalId = st.stalId)
         WHERE actId = 22 and h.skip = 0 and lidId = :lidId
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
WHERE u.lidId = :lidId
 and isnull(haf.actId)
ORDER BY u.ubn, right(s.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    // TODO rename: telt het aantal vijflingen bij deze ooi
    public function ooien_met_vijfling($lidId, $ooiId) {
        return $this->aantal_meerlingen_perOoi($lidId, $ooiId, 5);
    }

    // TODO spread: maak er zes functies van, verwijder Nr uit de publieke interface
    public function aantal_meerlingen_perOoi($lidId, $Ooiid, $Nr) {
        return $this->run_query(
            <<<SQL
SELECT v.volwId
FROM tblSchaap mdr
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE isnull(stm.rel_best)
 and u.lidId = :lidId
 and h.actId = 1
 and mdr.schaapId = :schaapId
 and h.skip = 0
GROUP BY v.volwId
HAVING count(st.schaapId) in (:nr)
ORDER BY date_format(h.datum, '%Y') desc, date_format(h.datum, '%m') desc
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $Ooiid, Type::INT],
                [':nr', $Nr],
            ]
        );
    }

    // deze query kijkt niet of stm.rel_best null is. Waarom niet?
    public function meerlingen_perOoi_perJaar($lidId, $Ooiid, $Jaar, $Maand) {
        return $this->first_row(
            <<<SQL
SELECT count(lam.schaapId) aant, v.volwId
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE u.lidId = :lidId
 and mdr.schaapId = :schaapId
 and h.actId = 1
 and date_format(h.datum, '%Y') = :year
 and date_format(h.datum, '%m') = :month
 and h.skip = 0
GROUP BY v.volwId
ORDER BY date_format(h.datum, '%Y%m') desc
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $Ooiid, Type::INT],
                [':year', $Jaar],
                [':month', $Maand],
            ],
            [null, null]
        );
    }

    public function periode($volwId) {
        # wordt een stuk makkelijker wanneer de afnemers gewoon naar ->maand en ->jaar kunnen vragen ...
        return array_merge([0], $this->first_row(
            <<<SQL
SELECT date_format(h.datum, '%Y') jaar, date_format(h.datum, '%m')*1 mndnr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = :volwId
 and h.actId = 1
 and h.skip = 0
GROUP BY date_format(h.datum, '%Y'), date_format(h.datum, '%m')
SQL
        ,
            [[':volwId', $volwId, Type::INT]],
            ['', '']
        ));
    }

    public function de_lammeren($Volwid, $KarWerk) {
        return $this->first_row(
            <<<SQL
SELECT coalesce(geslacht, '---') geslacht, coalesce(right(s.levensnummer, $KarWerk), '-------') werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = :volwId
 and h.actId = 1
 and h.skip = 0
ORDER BY coalesce(geslacht, 'zzz')
SQL
        ,
            [[':volwId', $Volwid]],
            [null, null]
        );
    }

    public function aantal_perGeslacht($Volwid, $Geslacht, $Jaar, $Maand) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE s.volwId = :volwId
 and s.geslacht = :geslacht
 and h.actId = 1
 and date_format(h.datum, '%m') = :month
 and date_format(h.datum, '%Y') = :year
 and h.skip = 0
SQL
        ,
            [
                [':volwId', $Volwid],
                [':geslacht', $Geslacht],
                [':month', $Maand],
                [':year', $Jaar],
            ]
        );
    }

    public function afleverdatum($lidId) {
        return $this->run_query(
            <<<SQL
SELECT min(h.hisId) hisId, count(h.hisId) aantal, date_format(h.datum, '%d-%m-%Y') datum, r.relId, p.naam
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblActie a on (a.actId = h.actId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE u.lidId = :lidId
 and a.af = 1
 and h.skip = 0
GROUP BY h.datum, r.relId, p.naam
ORDER BY r.uitval, h.datum desc
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_ooien_in_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) aant_mdr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3
 and skip = 0
 and date_format(datum, '%Y') <= :year
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
 and u.lidId = :lidId
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum), '%Y') <= :year)
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
 and u.lidId = :lidId
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum), '%Y') >= :year or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi'
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':year', $jaar]
            ]
        );
    }

    public function zoek_lammeren_in_jaar($lidId, $jaar, $jan1) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) aant_lam
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3
 and skip = 0
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
 and u.lidId = :lidId
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum), '%Y') <= :year)
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
 and u.lidId = :lidId
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum), '%Y') >= :year or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE (isnull(ouder.datum) or ouder.datum > :jan1)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':year', $jaar],
                [':jan1', $jan1]
            ]
        );
    }

    public function zoek_aantal_sterfte_lammeren_in_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) aant_lam
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 14
 and skip = 0
 ) dood on (st.stalId = dood.stalId)
 left join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3
 and skip = 0
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId, min(h.datum) tempmin
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
and u.lidId = :lidId
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum), '%Y') <= :jaar)
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
and u.lidId = :lidId
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum), '%Y') >= :jaar
or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)

WHERE isnull(ouder.datum)
 and date_format(dood.datum, '%Y') = :jaar
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':jaar', $jaar],
            ]
        );
    }

    public function zoek_aantal_sterfte_moeder_in_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) aant_mdr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 14
 and skip = 0
 ) dood on (st.stalId = dood.stalId)
 join (
    SELECT stalId, datum
    FROM tblHistorie
    WHERE actId = 3
 and skip = 0
 and date_format(datum, '%Y') <= :jaar
 ) ouder on (st.stalId = ouder.stalId)
 join (
    SELECT st.stalId, min(h.datum) tempmin
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0 and u.lidId = :lidId
    GROUP BY h.stalId
    HAVING (date_format(min(h.datum), '%Y') <= :jaar)
 ) mindm on (st.stalId = mindm.stalId)
 join (
    SELECT st.stalId, max(h.datum) tempmax, st.rel_best
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE skip = 0 and u.lidId = :lidId
    GROUP BY h.stalId, st.rel_best
    HAVING (date_format(max(h.datum), '%Y') >= :jaar or isnull(st.rel_best))
 ) maxdm on (st.stalId = maxdm.stalId)
WHERE s.geslacht = 'ooi' and date_format(dood.datum, '%Y') = :jaar
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':jaar', $jaar],
            ]
        );
    }

    public function zoek_worpen_in_jaar($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct v.mdrId) aant_worp
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblVolwas v on (s.volwId = v.volwId)
 join tblHistorie hg on (hg.stalId = st.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hkoop on (hkoop.stalId = st.stalId and hkoop.actId = 2 and hkoop.skip = 0)
WHERE u.lidId = :lidId
 and date_format(hg.datum, '%Y') = :year
 and isnull(hkoop.hisId)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':year', $jaar],
            ]
        );
    }

    public function eigen_schapen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function werknummers($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, right(s.levensnummer, $Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and s.levensnummer is not null
GROUP BY s.schaapId, right(s.levensnummer, $Karwerk)
ORDER BY right(s.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function halsnummers($lidId) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, concat(st.kleur, ' ', st.halsnr) halsnr
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and st.kleur is not null
 and st.halsnr is not null
 and isnull(st.rel_best)
GROUP BY s.schaapId, concat(st.kleur, ' ', st.halsnr)
ORDER BY st.kleur, st.halsnr
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function ooien($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT mdr.schaapId, right(mdr.levensnummer, $Karwerk) werknr_ooi
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE u.lidId = :lidId
 and mdr.levensnummer is not null
GROUP BY mdr.schaapId, right(mdr.levensnummer, $Karwerk)
ORDER BY right(mdr.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function rammen($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT vdr.schaapId, right(vdr.levensnummer, $Karwerk) werknr_ram
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE u.lidId = :lidId
 and vdr.levensnummer is not null
GROUP BY vdr.schaapId, right(vdr.levensnummer, $Karwerk)
ORDER BY right(vdr.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
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

    public function zoekAankoop($lidId, $WHERE) {
        return $this->first_row(
            <<<SQL
SELECT date_format(hg.datum, '%d-%m-%Y') gebdm, koop.datum dmkoop
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
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
WHERE u.lidId = :lidId
 and $WHERE
SQL
        ,
            [[':lidId', $lidId, Type::INT]],
            ['gebdm' => null, 'dmkoop' => null]
        );
    }

    public function zoekSchaap($WHERE) {
        return $this->first_field(
            <<<SQL
SELECT s.schaapId
FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
WHERE $WHERE
SQL
        ,
            []
        );
    }

    public function zoekresultaat($lidId, $WHERE, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT s.transponder, concat(st.kleur, ' ', st.halsnr) halsnr, s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknr,
 s.fokkernr, right(mdr.levensnummer, $Karwerk) werknr_ooi, right(vdr.levensnummer, $Karwerk) werknr_ram, r.ras, s.geslacht,
 ouder.datum dmaanw, coalesce(lower(haf.actie), 'aanwezig') status, haf.af,
hs.datum dmspn, hs.kg spnkg, afl.datum dmafl, afl.kg aflkg, hg.datum dmgeb, date_format(hg.datum, '%d-%m-%Y') gebdm,
 hg.kg gebkg, date_format(aanv1.datum, '%d-%m-%Y') aanvdm, aanv1.datum dmaanv, aanv1.kg aankkg

FROM tblSchaap s
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 join (
    SELECT min(stalId) stalId, schaapId
    FROM tblStal st
    INNER JOIN tblUbn u USING (ubnId)
    WHERE u.lidId = :lidId
    GROUP BY schaapId
 ) st1 on (s.schaapId = st1.schaapId)
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal st
    INNER JOIN tblUbn u USING (ubnId)
    WHERE u.lidId = :lidId
    GROUP BY schaapId
 ) stm on (s.schaapId = stm.schaapId)
 join tblStal st on (stm.stalId = st.stalId)
 left join (
    SELECT st1.schaapId, h.datum, h.kg
    FROM tblStal st1
     join tblHistorie h on (st1.stalId = h.stalId)
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
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join tblVolwas v on (v.volwId = s.volwId)
     left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
     left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
    WHERE u.lidId = :lidId
 and $WHERE
 and a.af = 1
 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 left join (
    SELECT st.schaapId, h.datum, h.kg
    FROM tblHistorie h
     join
     (
        SELECT s.levensnummer, min(h.hisId) hisId
        FROM tblStal st
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblSchaap s on (st.schaapId = s.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
         left join tblVolwas v on (v.volwId = s.volwId)
         left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
         left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
        /* tblSchaap mdr en tblSchaap vdr is voor als er op moeder of vader wordt gezocht*/
    WHERE u.lidId = :lidId
 and h.actId = 12
 and h.skip = 0
        GROUP BY s.levensnummer
     ) afl on (afl.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.skip = 0
 ) afl on (afl.schaapId = s.schaapId)
 left join tblRas r on(s.rasId = r.rasId)
WHERE $WHERE
ORDER BY if(isnull(s.levensnummer), 'Geen', ''), dmgeb desc, status
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoekGeschiedenis($lidId, $schaapId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT his.hisId, his.ubn, his.levensnummer, his.geslacht, his.datum, his.date, his.actId, his.actie, his.actie_if,
 his.kg, date_format(his.dmaanw, '%Y-%m-%d 00:00:00') dmaanw, toel.toel, his.hisId hiscom, comment
FROM
(
    SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date,
 h.actId, a.actie, right(a.actie, 4) actie_if, h.kg, ouder.datum dmaanw, h.comment
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
        WHERE h.actId = 3
 and h.skip = 0
 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)
    WHERE s.schaapId = :schaapId
 and u.lidId = :lidId
 and h.skip = 0
     and not exists (
        SELECT datum
        FROM tblHistorie ha
         join tblStal st on (ha.stalId = st.stalId)
         join tblSchaap s on (st.schaapId = s.schaapId)
        WHERE actId = 2 and h.skip = 0 and h.datum = ha.datum and h.actId = ha.actId+1 and s.schaapId = :schaapId)

  union

    SELECT h.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(h.datum, '%d-%m-%Y') datum, h.datum date, h.actId, a.actie, right(a.actie, 4) actie_if, h.kg, ouder.datum, h.comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)
    WHERE s.schaapId = :schaapId and h.actId = 1 and h.skip = 0
) his
left join
(
    SELECT 'adoptie lammeren' qry, h.hisId, concat('Bij ooi ', right(mdr.levensnummer, $Karwerk)) toel
    FROM tblHistorie h
     join impAgrident vp on (h.datum = vp.datum)
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId and vp.levensnummer = s.levensnummer)
     left join (
        SELECT levensnummer
        FROM tblSchaap mdr
        join tblStal st on (mdr.schaapId = st.schaapId)
         join tblUbn u on (st.ubnId = u.ubnId)
        WHERE u.lidId = :lidId
     ) mdr on (vp.moeder = mdr.levensnummer)
    WHERE h.actId = 15 and h.skip = 0 and vp.actId = 15 and vp.lidId = :schaapId

Union

    SELECT 'lammeren in hok geplaatst excl. adoptie' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr), ' voor ', datediff(coalesce(ht.datum, curdate()), h.datum), If(datediff(coalesce(ht.datum, curdate()), h.datum) = 1, ' dag', ' dagen')) toel

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
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE u.lidId = :schaapId
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
    WHERE a.aan = 1 and h.skip = 0 and h.actId != 15 and ho.lidId = :schaapId
     and (isnull(prnt.schaapId) or (prnt.datum > h.datum))

Union

    SELECT 'Volwassenen in hok geplaatst' qry, h.hisId, concat('Geplaatst in ', lower(ho.hoknr), ' voor ', datediff(coalesce(ht.datum, curdate()), h.datum), If(datediff(coalesce(ht.datum, curdate()), h.datum) = 1, ' dag', ' dagen')) toel

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
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE u.lidId = :schaapId
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
    WHERE a.aan = 1 and h.skip = 0 and ho.lidId = :schaapId
     and prnt.datum <= h.datum

Union

    SELECT 'Volwassenen hok verlaten' qry, uit.hist hisId, concat(ho.hoknr, ' verlaten ') toel

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
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE u.lidId = :schaapId
        and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY h1.hisId
     ) uit on (uit.hisv = b.hisId)
     left join tblHistorie ht on (ht.hisId = uit.hist)
    WHERE a.aan = 1 and h.skip = 0 and ho.lidId = :schaapId
     and ht.actId = 7

Union

    SELECT 'toel_afvoer excl dood met een reden' qry, h.hisId, p.naam
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblRelatie r on (st.rel_best = r.relId)
     join tblPartij p on (r.partId = p.partId)
    WHERE u.lidId = :lidId and a.af = 1 and h.skip = 0
     and (h.actId != 14 or (h.actId = 14 and isnull(s.redId)))

Union

    SELECT 'toel_afvoer dood met een reden' qry, h.hisId, re.reden
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblReden re on (s.redId = re.redId)
     join tblRelatie r on (st.rel_best = r.relId)
     join tblPartij p on (r.partId = p.partId)
    WHERE u.lidId = :lidId and a.af = 1 and h.skip = 0
     and h.actId = 14 and s.redId is not null

Union

    SELECT 'medicatie' qry, n.hisId, concat(round(sum(n.nutat*n.stdat), 2), ' ', e.eenheid, '  ', a.naam, '  ', coalesce(i.charge, '')) toel
    FROM tblNuttig n
     join tblInkoop i on (n.inkId = i.inkId)
     join tblArtikel a on (a.artId = i.artId)
     join tblEenheiduser eu on (eu.enhuId = a.enhuId)
     join tblEenheid e on (e.eenhId = eu.eenhId)
    WHERE eu.lidId = :lidId
    GROUP BY n.hisId, e.eenheid, a.naam, i.charge

Union

    SELECT 'omnummeren' qry,  h.hisId, concat('Oud nummer ', h.oud_nummer) toel
    From tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    Where u.lidId = :lidId and h.actId = 17 and h.skip = 0

) toel
on (his.hisId = toel.hisId)

UNION

SELECT NULL hisId, u.ubn, s.levensnummer, s.geslacht, date_format(p.dmafsluit, '%d-%m-%Y') datum, p.dmafsluit date, NULL actId, 'Gevoerd' actie, NULL actie_if, NULL kg, NULL dmaanw, concat(coalesce(round(datediff(ht.datum, hv.datum) * vr.kg_st, 2), 0), ' kg ', lower(a.naam), ' t.b.v. ', lower(h.hoknr)) toel, NULL hiscom, NULL comment
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
         join tblUbn u on (st.ubnId = u.ubnId)
         join tblHistorie his on (st.stalId = his.stalId)
         join tblActie a on (a.actId = his.actId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and his.skip = 0 and u.lidId = :schaapId
         and (a.aan = 1 or a.uit = 1)
         and his.hisId > b.hisId
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
 join tblHistorie ht on (ht.hisId = uit.hist)
 join
(
    SELECT v.periId, v.inkId, v.nutat/sum(datediff(ht.datum, hv.datum)) kg_st
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
             join tblUbn u on (st.ubnId = u.ubnId)
             join tblSchaap s on (s.schaapId = st.schaapId)
            WHERE h.skip = 0 and u.lidId = :schaapId
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

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld, '%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Geboorte gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ', rs.foutmeld) else concat('meldnr : ', rs.meldnr) end toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

WHERE r.dmmeld is not null and r.code = 'GER' and u.lidId = :schaapId and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld, '%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Aanvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ', rs.foutmeld) else concat('meldnr : ', rs.meldnr) end toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

WHERE r.dmmeld is not null and r.code = 'AAN' and u.lidId = :schaapId and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, rs.levensnummer, s.geslacht, date_format(r.dmmeld, '%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Afvoer gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ', rs.foutmeld) else concat('meldnr : ', rs.meldnr) end toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

WHERE r.dmmeld is not null and r.code = 'AFV' and u.lidId = :schaapId and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld, '%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Uitval gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ', rs.foutmeld) else concat('meldnr : ', rs.meldnr) end toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

WHERE r.dmmeld is not null and r.code = 'DOO' and u.lidId = :schaapId and h.skip = 0 and m.skip = 0

UNION

SELECT m.hisId, u.ubn, s.levensnummer, s.geslacht, date_format(r.dmmeld, '%d-%m-%Y') datum, r.dmmeld date, NULL actId, 'Omnummeren gemeld' actie, NULL actie_if, NULL kg, ouder.datum dmaanw, case when isnull(rs.meldnr) then concat('RVO meldt : ', rs.foutmeld) else concat('meldnr : ', rs.meldnr) end toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

WHERE r.dmmeld is not null and r.code = 'VMD' and u.lidId = :schaapId and h.skip = 0 and m.skip = 0

UNION

SELECT hisId1 hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worp1, '%d-%m-%Y') datum, mdr.worp1 date, NULL actId,
 'Eerste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
FROM
 (
    SELECT u.ubn, s.levensnummer, s.geslacht, ouder.datum dmaanw, min(hl.datum) worp1, min(hl.hisId) hisId1
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
     join tblVolwas v on (v.mdrId = s.schaapId)
     join tblSchaap lam on (lam.volwId = v.volwId)
     join tblStal sl on (lam.schaapId = sl.schaapId)
     join tblUbn ul on (st.ubnId = u.ubnId)
     join tblHistorie hl on (sl.stalId = hl.stalId)
     left join (
        SELECT s.schaapId, h.datum
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

    WHERE u.lidId = :schaapId
    GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
    SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE u.lidId = :schaapId
    GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worp1 = lam.datum)

UNION

SELECT hisend hisId, mdr.ubn, mdr.levensnummer, mdr.geslacht, date_format(mdr.worpend, '%d-%m-%Y') datum, mdr.worpend date, NULL actId, 'Laatste worp' actie, 'worp' actie_if, NULL kg, mdr.dmaanw, concat(lam.lmrn) toel, NULL hiscom, NULL comment
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
        WHERE h.actId = 3 and h.skip = 0 and s.schaapId = :schaapId
     ) ouder on (ouder.schaapId = s.schaapId)

     left join (
        SELECT moe.levensnummer, moe.geslacht, min(hl.datum) worp1
        FROM tblStal st
         join tblSchaap moe on (moe.schaapId = st.schaapId)
         join tblVolwas v on (v.mdrId = moe.schaapId)
         join tblSchaap lam on (lam.volwId = v.volwId)
         join tblStal sl on (lam.schaapId = sl.schaapId)
         join tblUbn ul on (st.ubnId = ul.ubnId)
         join tblHistorie hl on (sl.stalId = hl.stalId)

        WHERE ul.lidId = :schaapId
        GROUP BY moe.levensnummer, moe.geslacht
     ) lam1 on (lam1.levensnummer = s.levensnummer and lam1.worp1 = hl.datum)

    WHERE u.lidId = :schaapId and isnull(lam1.worp1)
    GROUP BY s.levensnummer, s.geslacht, ouder.datum
 ) mdr
 join
 (
    SELECT mdr.levensnummer moeder, h.datum, count(lam.schaapId) lmrn
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE u.lidId = :schaapId
    GROUP BY mdr.levensnummer, h.datum
 ) lam on (mdr.levensnummer = lam.moeder and mdr.worpend = lam.datum)

ORDER BY date_format(date, '%Y-%m-%d 00:00:00') desc, hisId desc
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT]
            ]
        );
/*Toelichting Order by :
kg noodzakelijk eerst hok verlaten geboren en dan de(zelfde) datum van spenen
 Id noodzakelijk bij meerder overplaatsingen (recordes tblBezet) op dezelfde dag
 */
    }

    public function zoek_laatste_werpdatum($max_worp) {
        return $this->first_field(
            <<<SQL
SELECT date_add(max(h.datum), interval 60 day) werpdate
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1
 and h.skip = 0
 and s.volwId = :volwId
SQL
        ,
            [[':volwId', $max_worp]]
        );
    }

    public function resultvader($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.schaapId, right(s.levensnummer, $Karwerk) werknr
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
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
 join tblUbn u on (st.ubnId = u.ubnId)
     WHERE u.lidId = :lidId
     GROUP BY schaapId
 ) mst on (s.schaapId = mst.schaapId)
 left join (
     SELECT st.stalId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) afv on (afv.stalId = mst.stalId)
WHERE s.geslacht = 'ram'
 and u.lidId = :lidId
 and isnull(afv.stalId)
ORDER BY right(s.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    // TODO: #0004183 hier gebruik je 5 ipv Karwerk, is dat expres?
    public function ouders($schaapId) {
        return $this->run_query(
            <<<SQL
with recursive sheep (schaapId, levnr, geslacht, ras, volwId_s, mdrId, levnr_ma, ras_ma, vdrId, levnr_pa, ras_pa) as (
   SELECT s.schaapId, right(s.levensnummer, 5) levnr, s.geslacht, r.ras, s.volwId, v.mdrId, right(ma.levensnummer, 5) levnr_ma, rm.ras ras_ma, v.vdrId, right(pa.levensnummer, 5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas v
     left join tblSchaap s on s.volwId = v.volwId
     left join tblRas r on s.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = v.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = v.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
    WHERE s.schaapId = :schaapId
    union all
   SELECT sm.schaapId, right(sm.levensnummer, 5) levnr, sm.geslacht, r.ras, sm.volwId, vm.mdrId, right(ma.levensnummer, 5) levnr_ma, rm.ras ras_ma, vm.vdrId, right(pa.levensnummer, 5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vm
     left join tblSchaap sm on sm.volwId = vm.volwId
     left join tblRas r on sm.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vm.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vm.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sm.schaapId = sheep.mdrId
    union all
   SELECT sv.schaapId, right(sv.levensnummer, 5) levnr, sv.geslacht, r.ras, sv.volwId, vv.mdrId, right(ma.levensnummer, 5) levnr_ma, rm.ras ras_ma, vv.vdrId, right(pa.levensnummer, 5) levnr_pa, rv.ras ras_pa
     FROM tblVolwas vv
     left join tblSchaap sv on sv.volwId = vv.volwId
     left join tblRas r on sv.rasId = r.rasId
     left join tblSchaap ma on ma.schaapId = vv.mdrId
     left join tblRas rm on ma.rasId = rm.rasId
     left join tblSchaap pa on pa.schaapId = vv.vdrId
     left join tblRas rv on pa.rasId = rv.rasId
     join sheep on sv.schaapId = sheep.vdrId
) SELECT s.schaapId, levnr, s.geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa, count(worp.schaapId) grootte
  FROM sheep s
   join tblSchaap worp on (s.volwId_s = worp.volwId)
GROUP BY s.schaapId, levnr, geslacht, ras, volwId_s, levnr_ma, ras_ma, levnr_pa, ras_pa
ORDER BY s.schaapId
SQL
        ,
            [[':schaapId', $schaapId, Type::INT]]
        );
    }

    # LET OP er is een weeg() in Historie en Schaap
    public function weeg($lidId, $schaapId) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function zoek_bestaand_levensnummer($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT levensnummer
FROM tblSchaap s
WHERE s.schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function zoek_op_levensnummer($levensnummer) {
        return $this->first_field(
            <<<SQL
SELECT schaapId
FROM tblSchaap s
WHERE s.levensnummer = :levensnummer
SQL
        ,
            [
                [':levensnummer', $levensnummer]
            ]
        );
    }

    public function updateLevensnummer($schaapId, $levensnummer) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap
set levensnummer = :levensnummer
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':levensnummer', $levensnummer]
            ]
        );
    }

    public function zoek_fokkernr($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT fokkernr
FROM tblSchaap
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function updateFokkernr($schaapId, $newfokrnr) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap set fokkernr = :fokkernr
 WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':fokkernr', $newfokrnr]
            ]
        );
    }

    public function update_geslacht($schaapId, $newsekse) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap set geslacht = :geslacht
 WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':geslacht', $newsekse]
            ]
        );
    }

    public function zoek_ras($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT rasId
FROM tblSchaap
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function update_ras($schaapId, $rasId) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap set rasId = :rasId
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':rasId', $rasId, Type::INT]
            ]
        );
    }

    public function zoek_moeder($schaapId) {
        return $this->first_row(
            <<<SQL
SELECT v.volwId, v.mdrId
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE s.schaapId = :schaapId
SQL
        ,
            [[':schaapId', $schaapId, Type::INT]],
            [null, null]
        );
    }

    public function zoek_moeder_ooikaart($kzlLevnr) {
        $sql = <<<SQL
    SELECT schaapId
    FROM tblSchaap
    WHERE schaapId = :kzlLevnr
SQL;
        $args = [[':kzlLevnr', $kzlLevnr]];
        return $this->first_field($sql, $args);
    }

    public function zoek_moeder_werknr($kzlWerknr) {
        $sql = <<<SQL
                SELECT schaapId
                FROM tblSchaap
                WHERE schaapId = :kzlWerknr
SQL;
        $args = [[':kzlWerknr', $kzlWerknr]];
        return $this->first_field($sql, $args);
    }

    public function zoek_moeder_halsnr($kzlHalsnr) {
        $sql = <<<SQL
                SELECT schaapId
                FROM tblSchaap
                WHERE schaapId = :kzlHalsnr
SQL;
        $args = [[':kzlHalsnr', $kzlHalsnr]];
        return $this->first_field($sql, $args);
    }

    public function update_moeder($schaapId, $mdrId) {
        $this->run_query(
            <<<SQL
UPDATE tblVolwas v
join tblSchaap s on (v.volwId = s.volwId)
set v.mdrId = :mdrId
WHERE s.schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':mdrId', $mdrId, Type::INT],
            ]
        );
    }

    public function update_vader($schaapId, $newvdrId) {
        $this->run_query(
            <<<SQL
    UPDATE tblVolwas v
     join tblSchaap s on (v.volwId = s.volwId)
    set v.vdrId = :vdrId
    WHERE s.schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':vdrId', $newvdrId, Type::INT],
            ]
        );
    }

    public function update_volw($schaapId, $volwId) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap set volwId = :volwId
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':volwId', $volwId, Type::INT],
            ]
        );
    }

    public function zoek_reden($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT redId
FROM tblSchaap
WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
            ]
        );
    }

    public function update_reden($schaapId, $redId) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap set redId = :redId
 WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
                [':redId', $redId, Type::INT],
            ]
        );
    }

    public function zoek_geslacht($schaapId) {
        return $this->first_field(
            <<<SQL
SELECT geslacht FROM tblSchaap WHERE schaapId = :schaapId
SQL
        ,
            [
                [':schaapId', $schaapId, Type::INT],
            ]
        );
    }

    public function zoek_halsnr_db($lidId, $kleur, $halsnr) {
        return $this->first_field(
            <<<SQL
SELECT schaapId
FROM tblStal
WHERE lidId = :lidId
 and kleur = :kleur
 and halsnr = :halsnr
 and isnull(rel_best)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':kleur', $kleur],
                [':halsnr', $halsnr],
            ]
        );
    }

    public function zoek_schapen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
WHERE u.lidId = :lidId
 and s.levensnummer is not null
GROUP BY s.schaapId, s.levensnummer
ORDER BY s.levensnummer
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
            ]
        );
    }

    public function zoek_moeders($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT mdr.schaapId, right(mdr.levensnummer, $Karwerk) werknr_ooi
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE u.lidId = :lidId
 and mdr.levensnummer is not null
GROUP BY mdr.schaapId, right(mdr.levensnummer, $Karwerk)
ORDER BY right(mdr.levensnummer, $Karwerk)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
            ]
        );
    }

    public function zoek_groeiresultaat_schaap($lidId, $Karwerk, $WHERE) {
        return $this->run_query(
            <<<SQL
SELECT right(mdr.levensnummer, $Karwerk) moeder, s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknum,
 s.geslacht, prnt.datum aanw, h.kg, h.datum date, date_format(h.datum, '%d-%m-%Y') datum, h.actId, a.actie
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and h.kg is not null
 and h.skip = 0
 $WHERE
ORDER BY right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
            ]
        );
    }

    public function zoek_groeiresultaat_weging($lidId, $Karwerk, $WHERE) {
        return $this->run_query(
            <<<SQL
SELECT date_format(h.datum, '%d-%m-%Y') datum, h.datum date, a.actie, right(mdr.levensnummer, $Karwerk) moeder,
 s.schaapId, right(s.levensnummer, $Karwerk) werknum, s.geslacht, prnt.datum aanw, h.kg
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 INNER JOIN tblUbn u USING(ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and h.kg is not null
 and h.skip = 0
$WHERE
ORDER BY h.datum desc, h.actId, right(mdr.levensnummer, $Karwerk), right(s.levensnummer, $Karwerk), h.hisId
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
            ]
        );
    }

    // gebruiker UpdSchaap doet niets met actid en datum .. ?
    public function query_historie($lidId, $schaapId) {
        return $this->run_query(
            <<<SQL
SELECT date_format(datum, '%d-%m-%Y') dag, h.actId, actie, datum
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u USING (ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
 and h.skip = 0
and not exists (
    SELECT datum
    FROM tblHistorie geenAanwas
     join tblStal st on (geenAanwas.stalId = st.stalId)
    WHERE actId = 2
 and h.datum = geenAanwas.datum
 and h.actId = geenAanwas.actId+1
 and st.schaapId = :schaapId)

union

SELECT date_format(datum, '%d-%m-%Y') dag, h.actId, actie, datum
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE h.actId = 1
 and h.skip = 0
 and s.schaapId = :schaapId

union

SELECT date_format(p.dmafsluit, '%d-%m-%Y') dag, h.actId, 'Gevoerd' actie, p.dmafsluit
FROM tblVoeding v
 join tblPeriode p on (p.periId = v.periId)
 join tblBezet b on (p.periId = b.periId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u USING (ubnId)
 join tblSchaap s on (s.schaapId =st.schaapId)
WHERE h.skip = 0
 and u.lidId = :lidId
 and s.schaapId = :schaapId

union

SELECT date_format(min(h.datum), '%d-%m-%Y') dag, h.actId, 'Eerste worp' actie, min(h.datum) datum
FROM tblSchaap s
 join tblVolwas v on (s.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u USING (ubnId)
 join tblHistorie h on (st.stalId = h.stalId
 and h.actId = 1
 and h.skip = 0)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
GROUP BY h.actId

union

SELECT date_format(max(h.datum), '%d-%m-%Y') dag, h.actId, 'Laatste worp' actie, max(h.datum) datum
FROM tblSchaap s
 join tblVolwas v on (s.schaapId = v.mdrId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u USING (ubnId)
 join tblHistorie h on (st.stalId = h.stalId
 and h.actId = 1
 and h.skip = 0)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
GROUP BY h.actId
HAVING (max(h.datum) > min(h.datum))

union

SELECT date_format(rs.dmcreate, '%d-%m-%Y') dag, h.actId, 'Geboorte gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u USING (ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'GER'
 and rs.meldnr is not null
 and h.skip = 0
 and u.lidId = :lidId
 and s.schaapId = :schaapId

union

SELECT date_format(rs.dmcreate, '%d-%m-%Y') dag, h.actId, 'Aanvoer gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u USING (ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'AAN'
 and rs.meldnr is not null
 and h.skip = 0
 and u.lidId = :lidId
 and s.schaapId = :schaapId

union

SELECT date_format(rs.dmcreate, '%d-%m-%Y') dag, h.actId, 'Afvoer gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u USING (ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'AFV'
 and rs.meldnr is not null
 and h.skip = 0
 and u.lidId = :lidId
 and s.schaapId = :schaapId

union

SELECT date_format(rs.dmcreate, '%d-%m-%Y') dag, h.actId, 'Uitval gemeld' actie, rs.dmcreate
FROM impRespons rs
 join tblMelding m on (rs.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u USING (ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId
 and s.levensnummer = rs.levensnummer)
WHERE rs.melding = 'DOO'
 and rs.meldnr is not null
 and h.skip = 0
 and u.lidId = :lidId
 and s.schaapId = :schaapId

ORDER BY datum desc, actId desc
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT],
            ]
        );
    }

    public function show_update($lidId, $schaapId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT stm.stalId, st.kleur, st.halsnr hnr, s.levensnummer, date_format(hg.datum, '%d-%m-%Y') gebdm, hg.kg gebkg, s.rasId, s.geslacht,
 date_format(hs.datum, '%d-%m-%Y') speendm, hs.kg speenkg, ouder.datum dmaanw, date_format(ouder.datum, '%d-%m-%Y') aanwdm,
mdr.schaapId mdrId, right(mdr.levensnummer, $Karwerk) werknr_ooi, vdr.schaapId vdrId, right(vdr.levensnummer, $Karwerk) werknr_ram,
s.momId, s.redId,
b.bezId, ho.hoknr hoknr_lst, h_in.datum dmHokIn, date_format(h_in.datum, '%d-%m-%Y') hokInDm, p.periId, p.dmafsluit,
st.rel_best

FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal st
INNER JOIN tblUbn u ON (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
    GROUP BY schaapId
 ) stm on (stm.schaapId = s.schaapId)
 join tblStal st on (stm.stalId = st.stalId)

 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)

 left join tblVolwas vm on (vm.volwId = s.volwId)
 left join tblSchaap mdr on (mdr.schaapId = vm.mdrId)
 left join tblVolwas vv on (vv.volwId = s.volwId)
 left join tblSchaap vdr on (vdr.schaapId = vv.vdrId)

 left join (
     SELECT st.schaapId, h.datum, h.kg
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 1
 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId)

 left join (
     SELECT st.schaapId, h.datum, h.kg
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 4
 and h.skip = 0
 ) hs on (s.schaapId = hs.schaapId)

 left join (
    SELECT h.stalId, h.hisId, h.datum, h.kg, a.actie
    FROM tblHistorie h
     join tblActie a on (a.actId = h.actId)
     join tblStal st on (h.stalId = st.stalId)
INNER JOIN tblUbn u ON (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
 and h.skip = 0
 and a.af = 1
 and st.schaapId = :schaapId
 ) haf on (haf.stalId = stm.stalId)

 left join (
    SELECT max(bezId) bezId, st.stalId
    FROM tblBezet b
     join tblHistorie h on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
    WHERE st.schaapId = :schaapId
 and h.skip = 0
    GROUP BY stalId
 ) bm on (bm.stalId = stm.stalId)
 left join tblBezet b on (bm.bezId = b.bezId)
 left join tblHistorie h_in on (h_in.hisId = b.hisId)
 left join tblHok ho on (ho.hokId = b.hokId)
 left join tblPeriode p on (b.periId = p.periId)


 left join (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
INNER JOIN tblUbn u ON (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and st.schaapId = :schaapId
    GROUP BY b.bezId
 ) uit on (uit.bezId = bm.bezId)

WHERE st.schaapId = :schaapId

ORDER BY right(s.levensnummer, $Karwerk)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT],
            ]
        );
    }

    public function noteer_overleden($schaapId) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap SET momId = NULL, redId = NULL WHERE schaapId = :schaapId
SQL
        ,
            [[':schaapId', $schaapId, Type::INT]]
        );
    }

    public function zoek_eerste_worp($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT min(datum) date
From (
    SELECT  min(h.datum) datum, 'Eerste worp' actie
    FROM tblSchaap mdr
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (st.schaapId = lam.schaapId)
     join tblUbn u USING (ubnId)
     join tblHistorie h on (st.stalId = h.stalId and h.actId = 1 and h.skip = 0)
    WHERE u.lidId = :lidId
 and mdr.schaapId = :schaapId
) datum
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT],
            ]
        );
    }

    public function tel_bij_lid_en_levensnummer($lidId, $levensnummers) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) schpat
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
INNER JOIN tblUbn u USING (ubnId)
WHERE u.lidId = :lidId
 and s.levensnummer IN (:levensnummers)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':levensnummers', $levensnummers],
            ]
        );
    }

    public function zoek_per_levensnummers($levensnummers) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer
FROM tblSchaap s
WHERE s.levensnummer IN (:levensnummers)
SQL
        ,
            [
                [':levensnummers', $levensnummers],
            ]
        );
    }

    // zoek_medicatie_lijst en zoek_medicatielijst_werknummer gebruiken dezelfde bron
    public function zoek_medicatie_lijst($lidId, $afvoer) {
        $part = $this->db_filter_afvoerdatum($afvoer);
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    INNER JOIN tblUbn u USING(ubnId)
    WHERE u.lidId = :lidId
    GROUP BY schaapId
 )st on (st.schaapId = s.schaapId)
 join (
    SELECT max(hisId) hisId, stalId
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY stalId
 ) hm on (hm.stalId = st.stalId)
 join tblHistorie h on (hm.hisId = h.hisId)
 left join (
    SELECT h.datum, h.stalId
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0 and a.af = 1 and h.actId != 10
    GROUP BY stalId
 ) afv on (afv.stalId = st.stalId)
WHERE $part
 AND h.skip = 0
and s.levensnummer is not null
ORDER BY s.levensnummer
SQL
        ,
            [
                [':lidId', $lidId, Type::INT]
            ]
        );
    }

    // zoek_medicatie_lijst en zoek_medicatielijst_werknummer gebruiken dezelfde bron
    public function zoek_medicatielijst_werknummer($lidId, $Karwerk, $afvoer) {
        $part = $this->db_filter_afvoerdatum($afvoer);
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, right(s.levensnummer, $Karwerk) werknr
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    INNER JOIN tblUbn u USING(ubnId)
    WHERE u.lidId = :lidId
    GROUP BY schaapId
 )st on (st.schaapId = s.schaapId)
 join (
    SELECT max(hisId) hisId, stalId
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY stalId
 ) hm on (hm.stalId = st.stalId)
 join tblHistorie h on (hm.hisId = h.hisId)
 left join (
    SELECT h.datum, h.stalId
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0 and a.af = 1 and h.actId != 10
    GROUP BY stalId
 ) afv on (afv.stalId = st.stalId)
WHERE $part
AND h.skip = 0
and s.levensnummer is not null
GROUP BY s.schaapId, right(s.levensnummer, $Karwerk)
ORDER BY right(s.levensnummer, $Karwerk)
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    private function db_filter_afvoerdatum($afvoer) {
        if ($afvoer) {
            return <<<SQL
(isnull(afv.datum) or (afv.datum > date_add(curdate(), interval -666 month) ))
SQL;
        }
        return <<<SQL
isnull(afv.stalId)
SQL;
    }

    public function getMedicatieWhere($post) {
        $filt_mdr = null;
        $parts = [];
        if (!empty($post['kzlLevnr'])) {
            $parts[] = "schaapId = '$post[kzlLevnr]' ";
        }
        if (!empty($post['kzlWerknr'])) {
            $parts[] = "schaapId = '$post[kzlWerknr]' ";
        }
        if (!empty($post['kzlHalsnr'])) {
            $parts[] = "schaapId = '$post[kzlHalsnr]' ";
        }
        if (!empty($post['chbOoi'])) {
            $parts[] = "geslacht = 'ooi' and aanw is not null";
        }
        // Als hok is gekozen is ook een keuze lam, moeders of allebei gemaakt. Vandaar opslitsing in variable $filt_hok.
        if (!empty($post['kzlHok'])) {
            if ($post['radHok'] == 1) {
                $filt_hok = "hokId = '$post[kzlHok]' and generatie = 'lam' ";
            } elseif ($post['radHok'] == 2) {
                $filt_hok = "hokId = '$post[kzlHok]' and generatie = 'ouder' ";
            } elseif ($post['radHok'] == 3) {
                $filt_hok = "hokId = '$post[kzlHok]' ";
            }
        }
        if (isset($filt_hok)) {
            $parts[] = $filt_hok;
        }
        if (count($parts) == 2 && isset($filt_hok)) {
            $filt_mdr = implode(' and ', $parts);
        }
        //$filt_mdr alleen bij keuzes niet betrekking op verblijf
        if (!empty($post['txtGeb_van'])) {
            $vanGeb = date_format(date_create($post['txtGeb_van']), 'Y-m-d');
            $totGeb = date_format(date_create($post['txtGeb_tot'] ?? date('d-m-Y')), 'Y-m-d');
            $parts[] = " dmgeb >= '" . $vanGeb . "' and dmgeb <= '" . $totGeb . "'";
        }
        $filter = implode(' and ', $parts);
        return [$filter, $filt_mdr];
    }

    public function zoek_schaapgegevens($lidId, $Karwerk, $afvoer, $filter) {
        $part = $this->db_filter_afvoerdatum($afvoer);
        return $this->run_query(
            <<<SQL
SELECT schaapId, levensnummer, werknr, dmgeb, gebdm, geslacht, aanw, hoknr, lstgeblam, generatie, actId, af
FROM (
    SELECT s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknr,
        hg.datum dmgeb, date_format(hg.datum, '%d-%m-%Y') gebdm, s.geslacht,
        prnt.schaapId aanw, b.hokId, b.hoknr, NULL lstgeblam, 'lam' generatie, a.actId, a.af
    FROM tblSchaap s
     join (
        SELECT max(stalId) stalId, schaapId
        FROM tblStal
        INNER JOIN tblUbn u USING (ubnId)
        WHERE u.lidId = :lidId
        GROUP BY schaapId
     ) stm on (stm.schaapId = s.schaapId)
     join (
        SELECT max(h.hisId) hisId, h.stalId
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
        INNER JOIN tblUbn u USING (ubnId)
        WHERE u.lidId = :lidId and h.skip = 0
        GROUP BY stalId
     ) hm on (hm.stalId = stm.stalId)
     join tblHistorie h on (hm.hisId = h.hisId)
     join tblActie a on (h.actId = a.actId)

     left join (
        SELECT st.schaapId, datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0
     ) hg on (hg.schaapId = s.schaapId)

     left join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = s.schaapId)

     left join (
        SELECT h.datum, h.stalId
        FROM tblHistorie h
         join tblActie a on (h.actId = a.actId)
        WHERE h.skip = 0 and a.af = 1 and h.actId != 10
        GROUP BY stalId
     ) afv on (afv.stalId = stm.stalId)

     left join (
        SELECT st.schaapId, hk.hokId, hk.hoknr
        FROM tblBezet b
         join tblHok hk on (hk.hokId = b.hokId)
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         left join (
            SELECT h1.stalId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblHistorie h1
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
            GROUP BY h1.stalId, h1.hisId
         ) tot on (b.hisId = tot.hisv)
        WHERE hk.lidId = :lidId and isnull(tot.hist) and h.skip = 0
     ) b on (s.schaapId = b.schaapId)

     left join (
        SELECT mdr.schaapId, max(h.datum) lstgeblam
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (lam.schaapId = st.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId and h.actId = 1)
        WHERE u.lidId = :lidId and h.skip = 0
        GROUP BY mdr.schaapId
     ) lstlam on (lstlam.schaapId = s.schaapId)

    WHERE $part and h.skip = 0 and isnull(prnt.schaapId)

    Union

    SELECT s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknr,
        hg.datum dmgeb, date_format(hg.datum, '%d-%m-%Y') gebdm, s.geslacht,
        prnt.schaapId aanw, b.hokId, b.hoknr, date_format(lstlam.lstgeblam, '%d-%m-%Y') lstgeblam, 'ouder' generatie, a.actId, a.af
    FROM tblSchaap s
     join (
        SELECT max(stalId) stalId, schaapId
        FROM tblStal
        WHERE lidId = :lidId
        GROUP BY schaapId
     )stm on (stm.schaapId = s.schaapId)
     join (
        SELECT max(h.hisId) hisId, h.stalId
        FROM tblHistorie h
         join tblStal st on (h.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
        WHERE u.lidId = :lidId and h.skip = 0
        GROUP BY stalId
     ) hm on (hm.stalId = stm.stalId)
     join tblHistorie h on (hm.hisId = h.hisId)
     join tblActie a on (h.actId = a.actId)

     left join (
        SELECT st.schaapId, datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0
     ) hg on (hg.schaapId = s.schaapId)

     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = s.schaapId)

     left join (
        SELECT h.datum, h.stalId
        FROM tblHistorie h
         join tblActie a on (h.actId = a.actId)
        WHERE h.skip = 0 and a.af = 1 and h.actId != 10
        GROUP BY stalId
     ) afv on (afv.stalId = stm.stalId)

     left join (
        SELECT st.schaapId, hk.hokId, hk.hoknr
        FROM tblBezet b
         join tblHok hk on (hk.hokId = b.hokId)
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         left join (
            SELECT h1.stalId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblHistorie h1
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
            GROUP BY h1.stalId, h1.hisId
         ) tot on (b.hisId = tot.hisv)
        WHERE hk.lidId = :lidId and isnull(tot.hist) and h.skip = 0
     ) b on (s.schaapId = b.schaapId)

     left join (
        SELECT mdr.schaapId, max(h.datum) lstgeblam
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (lam.schaapId = st.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId and h.actId = 1)
        WHERE u.lidId = :lidId and h.skip = 0
        GROUP BY mdr.schaapId
     ) lstlam on (lstlam.schaapId = s.schaapId)

    WHERE $part AND h.skip = 0
) geg

WHERE $filter
ORDER BY generatie, werknr, lstgeblam desc
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
            ]
        );
    }

    public function zoek_aanwezig_moeder($lidId, $WHERE_mdr) {
        return $this->run_query(
            <<<SQL
SELECT s.levensnummer
FROM tblSchaap s
 join (
    SELECT max(h.hisId) hisId, st.schaapId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId and isnull(st.rel_best) and h.skip = 0
    GROUP BY st.stalId
 ) hm on (hm.schaapId = s.schaapId)
 join tblHistorie h on (hm.hisId = h.hisId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join tblBezet b on (h.hisId = b.hisId)
WHERE s.geslacht = 'ooi' and $WHERE_mdr
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function tel_medicijn_historie($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT count(s.levensnummer) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 INNER JOIN tblUbn u USING (ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblNuttig n on (h.hisId = n.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (i.artId = a.artId)
WHERE u.lidId = :lidId
and h.skip = 0
and s.schaapId = :schaapId
and a.soort = 'pil'
GROUP BY s.levensnummer
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function zoek_datum_bestemming($hisId) {
        return $this->first_row(
            <<<SQL
SELECT datum, rel_best
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE h.hisId = :hisId
SQL
        ,
            [[':hisId', $hisId, Type::INT]],
            [0, 0]
        );
    }

    public function zoek_aflevergegevens($bestm, $date) {
        return $this->first_row(
            <<<SQL
SELECT p.naam, date_format(h.datum, '%d-%m-%Y') datum, count(st.schaapId) tal
FROM tblStal st
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (p.partId = r.partId)
WHERE a.af = 1
 and st.rel_best = :bestm
 and h.datum = :date
 and h.skip = 0
GROUP BY p.naam, date_format(h.datum, '%d-%m-%Y')
SQL
        ,
            [
                [':bestm', $bestm],
                [':date', $date],
            ],
            ['', '', '']
        );
    }

    public function zoek_schaap_aanvoer($schaapId) {
        return $this->run_query(
            <<<SQL
SELECT r.ras, s.geslacht, ouder.schaapId aanw, date_format(geb.datum, '%d-%m-%Y') gebdm
FROM tblSchaap s
 left join tblRas r on (s.rasId = r.rasId)
 left join (
     SELECT st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 3
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 1
 ) geb on (geb.schaapId = s.schaapId)
 WHERE s.schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, Type::INT]]
        );
    }

    public function zoek_schaap_aflever($bestm, $date, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer, right(s.levensnummer, $Karwerk) werknr, h.kg
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1
 and st.rel_best = :bestm
 and h.datum = :date
 and h.skip = 0
ORDER BY right(s.levensnummer, $Karwerk)
SQL
        ,
            [
                [':bestm', $bestm],
                [':date', $date],
            ]
        );
    }

    public function zoek_pil_aflever($lidId, $schaapId) {
        return $this->run_query(
            <<<SQL
SELECT date_format(h.datum, '%d-%m-%Y') datum, art.naam, art.wdgn_v, (h.datum + interval art.wdgn_v day) toon
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
 and h.actId = 8
 and h.skip = 0
 and (h.datum + interval art.wdgn_v day) >= sysdate()
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT]
            ]
        );
    }

    public function zoek_pil($lidId, $date, $schaapId) {
        return $this->run_query(
            <<<SQL
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, DATEDIFF( (h.datum + interval art.wdgn_v day), :date) resterend
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
 and h.actId = 8
 and h.skip = 0
 and :date < (h.datum + interval art.wdgn_v day)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT],
                [':date', $date],
            ]
        );
    }

    public function zoek_pil_afvoer($lidId, $schaapId, $date) {
        return $this->first_row(
            <<<SQL
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, DATEDIFF( (h.datum + interval art.wdgn_v day), :date) resterend
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
 and h.actId = 8
 and h.skip = 0
 and :date < (h.datum + interval art.wdgn_v day)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $schaapId, Type::INT],
                [':date', $date],
            ]
        );
    }

    // @TODO: deze query neemt geen parameters. En wordt eigenlijk ook niet gebruikt. Weg?
    public function fase_bij_dier() {
        $vw = $this->run_query(
            <<<SQL
SELECT s.levensnummer, 'moeder' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and geslacht = 'ooi' and date_add(hg.datum, interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'moeder' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and h.skip = 0 and isnull(hg.stalId) and geslacht = 'ooi' and date_add(s.dmcreatie, interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'vader' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and h.skip = 0 and geslacht = 'ram' and date_add(hg.datum, interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'vader' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
WHERE h.actId = 3 and h.skip = 0 and isnull(hg.stalId) and geslacht = 'ram' and date_add(s.dmcreatie, interval 10 year) > CURRENT_DATE()

UNION

SELECT s.levensnummer, 'lam' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3 and skip = 0
 ) h ON (h.stalId = st.stalId)
 join (
     SELECT stalId, datum
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
 WHERE isnull(h.stalId) and s.levensnummer is not null and date_add(hg.datum, interval 10 year) > CURRENT_DATE()

 UNION

SELECT s.levensnummer, 'lam' fase
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3 and skip = 0
 ) h ON (h.stalId = st.stalId)
 left join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hg ON (hg.stalId = st.stalId)
 WHERE isnull(h.stalId) and s.levensnummer is not null and isnull(hg.stalId) and date_add(s.dmcreatie, interval 10 year) > CURRENT_DATE()
SQL
        );
        if ($vw->num_rows == 0) {
            return [];
        }
        $res = [];
        while ($row = $vw->fetch_assoc()) {
            $res[$row['levensnummer']] = $row['fase'];
        }
        return $res;
    }

    public function zoek_laatste_dekkingen($Karwerk) {
        $vw = $this->run_query(
            <<<SQL
SELECT v.mdrId, right(vdr.levensnummer, $Karwerk) lev
FROM tblVolwas v
 join (
     SELECT v.mdrId, max(v.volwId) volwId
    FROM tblVolwas v
     left join tblHistorie hv on (hv.hisId = v.hisId)
     left join tblDracht d on (v.volwId = d.volwId)
     left join tblHistorie hd on (hd.hisId = d.hisId)
     left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) ha on (k.schaapId = ha.schaapId)
    WHERE (isnull(hv.hisId) or hv.skip = 0) and (isnull(hd.hisId) or hd.skip = 0) and isnull(ha.schaapId)
    GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap vdr on (vdr.schaapId = v.vdrId)
SQL
        );
        if ($vw->num_rows == 0) {
            return [];
        }
        $res = [];
        while ($row = $vw->fetch_assoc()) {
            $res[$row['mdrId']] = $row['lev'];
        }
        return $res;
    }

    public function zoek_laatste_dekking_van_ooi($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join (
        SELECT hisId
        FROM tblHistorie h
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
        WHERE h.skip = 0
 and u.lidId = :lidId
 and st.schaapId = :schaapId
 ) hv on (hv.hisId = v.hisId)
 left join (
        SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum
        FROM tblDracht d 
     join tblHistorie h on (h.hisId = d.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE h.skip = 0
 and u.lidId = :lidId
 and st.schaapId = :schaapId
 ) d on (v.volwId = d.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId)
WHERE (hv.hisId is not null or d.volwId is not null)
 and isnull(ha.schaapId)
 and v.mdrId = :schaapId
GROUP BY v.mdrId
SQL
        , [[':lidId', $lidId, Type::INT], [':schaapId', $schaapId, Type::INT]]
        );
    }

    public function zoek_werpdatum_laatste_dekking() {
        $vw = $this->run_query(
            <<<SQL
SELECT v.mdrId, date_format(h.datum, '%d-%m-%Y') werpdm
FROM tblVolwas v
 join (
     SELECT v.mdrId, max(v.volwId) volwId
    FROM tblVolwas v
     left join tblHistorie hv on (hv.hisId = v.hisId)
     left join tblDracht d on (v.volwId = d.volwId)
     left join tblHistorie hd on (hd.hisId = d.hisId)
     left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) ha on (k.schaapId = ha.schaapId)
    WHERE (isnull(hv.hisId) or hv.skip = 0) and (isnull(hd.hisId) or hd.skip = 0) and isnull(ha.schaapId)
    GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal st on (st.schaapId = l.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE h.actId = 1 and h.skip = 0 and date_add(h.datum, interval 30 day) > CURRENT_DATE()
GROUP BY v.mdrId, h.datum
SQL
        );
        if ($vw->num_rows == 0) {
            return [];
        }
        $res = [];
        while ($row = $vw->fetch_assoc()) {
            $res[$row['mdrId']] = $row['werpdm'];
        }
        return $res;
    }

    // NOTE dit doet iets anders dan zoek_bestaand_levensnummer. Waarom is dit zoveel uitgebreider?
    public function zoek_eerder_levensnummer($levnr) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.geslacht, s.volwId, v.mdrId, hg.datum dmgeb, h1.datum dmeerste,
date_format(h1.datum,'%d-%m-%Y') eerstedm, ha.datum dmaanw, haf.datum dmafv, date_format(haf.datum,'%d-%m-%Y') afvdm
FROM tblSchaap s
 left join tblVolwas v on (s.volwId = v.volwId)
 left join (
    SELECT s.schaapId, h.datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1
 and h.skip = 0
 and s.levensnummer = :levnr
 ) hg on (s.schaapId = hg.schaapId)
 left join (
    SELECT his1.schaapId, h.datum
    FROM tblHistorie h
    join (
        SELECT st.schaapId, min(h.hisId) hisId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.skip = 0
 and s.levensnummer = :levnr
        GROUP BY st.schaapId
    ) his1 on (his1.hisId = h.hisId)
 ) h1 on (s.schaapId = h1.schaapId)
 left join (
    SELECT s.schaapId, h.datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 and s.levensnummer = :levnr
 ) ha on (s.schaapId = ha.schaapId)
 left join (
    SELECT afv.schaapId, h.datum
    FROM tblHistorie h
    join (
        SELECT st.schaapId, max(h.hisId) hisId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
         join tblActie a on (h.actId = a.actId)
        WHERE a.af = 1
 and h.skip = 0
 and s.levensnummer = :levnr
        GROUP BY st.schaapId
    ) afv on (afv.hisId = h.hisId)
    WHERE h.skip = 0
 ) haf on (s.schaapId = haf.schaapId)
WHERE levensnummer = :levnr
SQL
        ,
            [[':levnr', $levnr]]
        );
    }

    public function zoek_laatste_worp($mdrId) {
        return $this->first_field(
            <<<SQL
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
 left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and isnull(ha.schaapId)
SQL
        , [[':mdrId', $mdrId, Type::INT]]
        );
    }

    public function zoek_datum_laatste_worp($volwId) {
        $vw = $this->run_query(
            <<<SQL
SELECT h.datum, date_format(h.datum,'%d-%m-%Y') dag
FROM tblSchaap l
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1
 and h.skip = 0
 and l.volwId = :volwId
SQL
        ,
            [[':volwId', $volwId, Type::INT]]
        );
        if ($vw->num_rows) {
            return $vw->fetch_row();
        }
        return [null, null];
    }

    public function maak_schaap($levnr, $rasId, $geslacht, $volwId, $momId, $redId) {
        $this->run_query(
            <<<SQL
INSERT INTO tblSchaap SET
 levensnummer = :levnr,
 rasId = :rasId,
 geslacht = :geslacht,
 volwId = :volwId,
 momId = :momId,
 redId = :redId
SQL
        ,
            [
                [':levnr', $levnr],
                [':rasId', $rasId],
                [':geslacht', $geslacht],
                [':volwId', $volwId],
                [':momId', $momId],
                [':redId', $redId],
            ]
        );
        return $this->db->insert_id;
    }

    public function maak_minimaal_schaap($levnr, $ras, $sekse) {
        $this->run_query(
            <<<SQL
INSERT INTO tblSchaap set levensnummer = :levnr,
 rasId = :rasId,
 geslacht = :sekse
SQL
        , [
            [':levnr', $levnr],
            [':rasId', $ras, Type::INT],
            [':sekse', $sekse]
        ]
        );
        return $this->db->insert_id;
    }

    public function wis_levensnummer($ubn) {
        $this->run_query(
            <<<SQL
UPDATE tblSchaap SET levensnummer = NULL WHERE levensnummer = :ubn
SQL
        ,
            [[':ubn', $ubn, Type::INT]]
        );
    }

    public function groeiresultaat($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT schaapId, werknr, geslacht, gebkg, wg1, spkg, wg2, wg3, afvkg, round(groei/coalesce(dagen,1)*1000,2) gemgroei
FROM (

SELECT s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, s.geslacht, hg.gebkg, w1_voorsp.wg1,
 hs.spkg, w1_nasp.wg2, w2_nasp.wg3, haf.afvkg,
 coalesce(hg.datum,coalesce(w1_voorsp.datum,coalesce(hs.dmspeen,coalesce(w1_nasp.datum,coalesce(w2_nasp.datum,haf.datum))))) minkg,
 coalesce(haf.datum,coalesce(w2_nasp.datum,coalesce(w1_nasp.datum,coalesce(hs.dmspeen,coalesce(w1_voorsp.datum,hg.datum))))) maxkg,
 coalesce(haf.afvkg,coalesce(w2_nasp.wg3,coalesce(w1_nasp.wg2,coalesce(hs.spkg,coalesce(w1_voorsp.wg1,hg.gebkg)))))
 - coalesce(hg.gebkg,coalesce(w1_voorsp.wg1,coalesce(hs.spkg,coalesce(w1_nasp.wg2,coalesce(w2_nasp.wg3,haf.afvkg))))) groei,
 datediff(
     coalesce(haf.datum,coalesce(w2_nasp.datum,coalesce(w1_nasp.datum,coalesce(hs.dmspeen,coalesce(w1_voorsp.datum,hg.datum))))),
     coalesce(hg.datum,coalesce(w1_voorsp.datum,coalesce(hs.dmspeen,coalesce(w1_nasp.datum,coalesce(w2_nasp.datum,haf.datum)))))
 ) dagen
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join (
     SELECT schaapId, kg gebkg, h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
 and actId = 1
 and skip = 0
 and h.kg is not null
 ) hg on (hg.schaapId = s.schaapId)
 left join (
    SELECT wg1.schaapId, h.kg wg1, h.datum
    FROM (
        SELECT st.schaapId, min(wg1.hisId) hisId
        FROM tblHistorie wg1
         join tblStal st on (wg1.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         left join (
             SELECT stalId, kg spkg, datum dmaanw FROM tblHistorie WHERE actId = 3
 and skip = 0
        ) aw on (aw.stalId = st.stalId)
         left join (
             SELECT stalId, kg spkg, datum dmspeen FROM tblHistorie WHERE actId = 4
 and skip = 0
        ) sp on (sp.stalId = st.stalId)
        WHERE u.lidId = :lidId
 and wg1.actId = 9
 and skip = 0 

 and (wg1.datum < sp.dmspeen or isnull(sp.dmspeen))

 and isnull(aw.dmaanw)
        GROUP BY st.schaapId
     ) wg1
     join tblHistorie h on (wg1.hisId = h.hisId)
 ) w1_voorsp on (w1_voorsp.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.kg spkg, h.datum dmspeen
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE actId = 4
 and skip = 0
 and h.kg is not null
 ) hs on (hs.schaapId = hg.schaapId)
 left join (
    SELECT wg_nasp.schaapId, h.kg wg2, h.datum
    FROM tblHistorie h
     join (
        SELECT w1.schaapId, w1.hisId, count(w1.lidId) rank
        FROM (
            SELECT u.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
            FROM tblHistorie wg
             join tblStal st on (wg.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
             left join (
                 SELECT stalId, datum dmaanw
                FROM tblHistorie
                WHERE actId = 3
 and skip = 0
            ) aw on (aw.stalId = st.stalId)
            left join (
                 SELECT stalId, datum dmspeen
                FROM tblHistorie
                WHERE actId = 4
 and skip = 0
            ) sp on (sp.stalId = st.stalId)

            WHERE u.lidId = :lidId
 and wg.actId = 9
 and skip = 0
 and ( (aw.stalId is not null
 and wg.datum > aw.dmaanw) or (sp.stalId is not null
 and wg.datum > sp.dmspeen) )
         ) w1
         join (
            SELECT u.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
            FROM tblHistorie wg
             join tblStal st on (wg.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
             left join (
                 SELECT stalId, datum dmaanw
                FROM tblHistorie
                WHERE actId = 3
 and skip = 0
            ) aw on (aw.stalId = st.stalId)
            left join (
                 SELECT stalId, datum dmspeen
                FROM tblHistorie
                WHERE actId = 4
 and skip = 0
            ) sp on (sp.stalId = st.stalId)

            WHERE u.lidId = :lidId
 and wg.actId = 9
 and skip = 0
 and ( (aw.stalId is not null
 and wg.datum > aw.dmaanw) or (sp.stalId is not null
 and wg.datum > sp.dmspeen) )
            ) w2 on (w1.lidId = w2.lidId
 and w1.schaapId = w2.schaapId
 and w1.datum >= w2.datum)
        GROUP BY w1.lidId, w1.schaapId, w1.hisId, w1.datum, w1.dmaanw, w1.dmspeen
        HAVING (count(w1.lidId) = 1 )
     ) wg_nasp on (h.hisId = wg_nasp.hisId)
) w1_nasp on (w1_nasp.schaapId = s.schaapId)
left join (
    SELECT wg_nasp.schaapId, h.kg wg3, h.datum
    FROM tblHistorie h
     join (
        SELECT w1.schaapId, w1.hisId, count(w1.lidId) rank
        FROM (
            SELECT u.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
            FROM tblHistorie wg
             join tblStal st on (wg.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
             left join (
                 SELECT stalId, datum dmaanw
                FROM tblHistorie
                WHERE actId = 3
 and skip = 0
            ) aw on (aw.stalId = st.stalId)
            left join (
                 SELECT stalId, datum dmspeen
                FROM tblHistorie
                WHERE actId = 4
 and skip = 0
            ) sp on (sp.stalId = st.stalId)

            WHERE u.lidId = :lidId
 and wg.actId = 9
 and skip = 0
 and ( (aw.stalId is not null
 and wg.datum > aw.dmaanw) or (sp.stalId is not null
 and wg.datum > sp.dmspeen) )
         ) w1
         join (
            SELECT u.lidId, st.schaapId, wg.hisId, wg.datum, aw.dmaanw, sp.dmspeen
            FROM tblHistorie wg
             join tblStal st on (wg.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
             left join (
                 SELECT stalId, datum dmaanw
                FROM tblHistorie
                WHERE actId = 3
 and skip = 0
            ) aw on (aw.stalId = st.stalId)
            left join (
                 SELECT stalId, datum dmspeen
                FROM tblHistorie
                WHERE actId = 4
 and skip = 0
            ) sp on (sp.stalId = st.stalId)

            WHERE u.lidId = :lidId
 and wg.actId = 9
 and skip = 0
 and ( (aw.stalId is not null
 and wg.datum > aw.dmaanw) or (sp.stalId is not null
 and wg.datum > sp.dmspeen) )
            ) w2 on (w1.lidId = w2.lidId
 and w1.schaapId = w2.schaapId
 and w1.datum >= w2.datum)
        GROUP BY w1.lidId, w1.schaapId, w1.hisId, w1.datum, w1.dmaanw, w1.dmspeen
        HAVING (count(w1.lidId) = 2 )
     ) wg_nasp on (h.hisId = wg_nasp.hisId)
) w2_nasp on (w2_nasp.schaapId = s.schaapId)
left join (
     SELECT schaapId, kg afvkg, h.datum
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
 and actId = 12
 and skip = 0
 and h.kg is not null
 ) haf on (haf.schaapId = s.schaapId)

WHERE s.levensnummer is not null
 and u.lidId = :lidId
 and (hg.gebkg is not null or w1_voorsp.wg1 is not null or hs.spkg is not null)
) a
ORDER BY werknr
SQL
        ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_wegingen($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(datum) aant
FROM impAgrident
WHERE lidId = :lidId
 and actId = 9
 and isnull(verwerkt)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function pil_overzicht($lidId, $schaapId) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, s.levensnummer, date_format(h.datum,'%d-%m-%Y') toedm, a.naam, i.charge,
 round(sum(n.nutat),2) nutat, n.stdat, round(sum(n.nutat*n.stdat),2) totat, e.eenheid, r.reden
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblNuttig n on (n.hisId = h.hisId)
 join tblInkoop i on (n.inkId = i.inkId)
 join tblArtikel a on (a.artId = i.artId)
 join tblRedenuser ru on (ru.reduId = n.reduId)
 join tblReden r on (r.redId = ru.redId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE u.lidId = :lidId
 and s.schaapId = :schaapId
 and a.soort = 'pil'
 and h.skip = 0
GROUP BY s.schaapId, s.levensnummer, h.datum, a.naam, i.charge, n.stdat, e.eenheid, r.reden
ORDER BY h.datum desc, i.inkId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':schaapId', $schaapId, Type::INT],
        ]
        );
    }

    // bijna-duplicaat: het verschil tussen __0 en ___1 is de HAVING-grens
    public function ooien_met_meerlingworpen1($lidId, $Karwerk, $order) {
        $sql = <<<SQL
SELECT schaapId, ooi, sum(worp) totat
FROM (
    SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) ooi, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (stm.ubnId = um.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
    GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk), v.volwId
    HAVING count(v.volwId) > 1
     ) perWorp
GROUP BY schaapId, ooi
ORDER BY $order
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function alle_ooien_met_meerlingworpen($lidId, $Karwerk, $order, $jaar1, $jaar2, $jaar3, $jaar4) {
        return $this->run_query(
            <<<SQL
SELECT perWorp.schaapId, ooi, sum(perWorp.worp) totat,
 sum(perWorp_jr1.worp) jr1, sum(perWorp_jr2.worp) jr2, sum(perWorp_jr3.worp) jr3, sum(perWorp_jr4.worp) jr4
FROM (
    SELECT mdr.schaapId, right(mdr.levensnummer,$Karwerk) ooi, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (um.ubnId = stm.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
 and date_format(h.datum,'%Y') <= '$jaar1'
 and date_format(h.datum,'%Y') >= '$jaar4'
 and h.actId = 1
 and h.skip = 0
    GROUP BY mdr.schaapId, right(mdr.levensnummer,$Karwerk), v.volwId
    HAVING count(v.volwId) > 0
 ) perWorp
left join (
    SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (um.ubnId = stm.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
 and h.actId = 1
 and date_format(h.datum,'%Y') = '$jaar1'
 and h.skip = 0
    GROUP BY mdr.schaapId, v.volwId
    HAVING count(v.volwId) > 0
) perWorp_jr1  on (perWorp.volwId = perWorp_jr1.volwId)
left join (
    SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (um.ubnId = stm.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
 and h.actId = 1
 and date_format(h.datum,'%Y') = '$jaar2'
 and h.skip = 0
    GROUP BY mdr.schaapId, v.volwId
    HAVING count(v.volwId) > 0
) perWorp_jr2 on (perWorp.volwId = perWorp_jr2.volwId)
left join (
    SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (um.ubnId = stm.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
 and h.actId = 1
 and date_format(h.datum,'%Y') = '$jaar3'
 and h.skip = 0
    GROUP BY mdr.schaapId, v.volwId
    HAVING count(v.volwId) > 0
) perWorp_jr3 on (perWorp.volwId = perWorp_jr3.volwId)
left join (
    SELECT mdr.schaapId, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on (um.ubnId = stm.ubnId)
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
 and h.actId = 1
 and date_format(h.datum,'%Y') = '$jaar4'
 and h.skip = 0
    GROUP BY mdr.schaapId, v.volwId
    HAVING count(v.volwId) > 0
) perWorp_jr4 on (perWorp.volwId = perWorp_jr4.volwId)
GROUP BY schaapId, ooi
ORDER BY $order
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_maanden_per_ooi($lidId, $ooiId, $jaar1, $jaar4) {
        return $this->run_query(
            <<<SQL
SELECT date_format(h.datum,'%m') mndtxt, date_format(h.datum,'%m')*1 mndnr
FROM tblSchaap mdr
 join tblVolwas v on (v.mdrId = mdr.schaapId)
 join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE u.lidId = :lidId
 and mdr.schaapId = :schaapId
 and h.actId = 1
 and date_format(h.datum,'%Y') <= '$jaar1'
 and date_format(h.datum,'%Y') >= '$jaar4'
 and h.skip = 0
GROUP BY date_format(h.datum,'%m')
ORDER BY date_format(h.datum,'%m')
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':schaapId', $ooiId, Type::INT],
            ]
        );
    }

    public function zoek_meerlingen($lidId, $Karwerk, $van, $tot) {
        return $this->run_query(
            <<<SQL
SELECT right(lam.levensnummer,$Karwerk) lam, lam.geslacht, count(wrp.volwId) worp, h.datum date,
 date_format(h.datum,'%d-%m-%Y') datum, right(mdr.levensnummer,$Karwerk) ooi,
 round(((lstkg.kg - h.kg)*1000)/datediff(mx.mdm,h.datum),2) gemgroei, date_format(mx.mdm,'%d-%m-%Y') kgdag, st.stalId
FROM tblSchaap lam
 join tblVolwas v on (lam.volwId = v.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 join tblSchaap wrp on (lam.volwId = wrp.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join (
     SELECT stalId, max(datum) mdm
     FROM tblHistorie
    WHERE kg is not null and actId > 1 and skip = 0
    GROUP BY stalId
 ) mx on (mx.stalId = st.stalId)
 left join (
     SELECT stalId, datum, max(kg) kg
     FROM tblHistorie
    WHERE kg is not null and actId > 1 and skip = 0
    GROUP BY stalId, datum
 ) lstkg on (lstkg.stalId = st.stalId and lstkg.datum = mx.mdm)
WHERE lam.levensnummer is not null
 and isnull(st.rel_best)
 and h.actId = 1
 and u.lidId = :lidId
 and h.datum >= :van
 and h.datum <= :tot
 and h.skip = 0
GROUP BY lam.levensnummer, lam.geslacht, h.datum, mdr.levensnummer, mx.mdm, st.stalId
ORDER BY right(lam.levensnummer,$Karwerk)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':van', $van, Type::DATE],
                [':tot', $tot, Type::DATE],
            ]
        );
    }

    public function zoek_meerling($lidId, $Karwerk, $van, $tot) {
        return $this->run_query(
            <<<SQL
SELECT right(lam.levensnummer,$Karwerk) lam, lam.geslacht, count(wrp.volwId) worp, h.datum date, 
date_format(h.datum,'%d-%m-%Y') datum, right(mdr.levensnummer,$Karwerk) ooi,
 round(((lstkg.kg - h.kg)*1000)/datediff(mx.mdm,h.datum),2) gemgroei, date_format(mx.mdm,'%d-%m-%Y') kgdag, st.stalId
FROM tblSchaap lam
 join tblVolwas v on (lam.volwId = v.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 join tblSchaap wrp on (lam.volwId = wrp.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join (
     SELECT stalId, max(datum) mdm
     FROM tblHistorie
    WHERE kg is not null and actId > 1
    GROUP BY stalId
 ) mx on (mx.stalId = st.stalId)
 left join (
     SELECT stalId, datum, max(kg) kg
     FROM tblHistorie
    WHERE kg is not null and actId > 1
    GROUP BY stalId, datum
 ) lstkg on (lstkg.stalId = st.stalId and lstkg.datum = mx.mdm)
WHERE lam.levensnummer is not null
 and isnull(st.rel_best)
 and h.actId = 1
 and u.lidId = :lidId
 and h.datum >= :van
 and h.datum <= :tot
GROUP BY lam.levensnummer, lam.geslacht, h.datum, mdr.levensnummer, mx.mdm, st.stalId
ORDER BY right(lam.levensnummer,$Karwerk)
SQL
        ,
            [
                [':lidId', $lidId, Type::INT],
                [':van', $van, Type::DATE],
                [':tot', $tot, Type::DATE],
            ]
        );
    }

    public function ooikaart_all($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT mdr.schaapId, mdr.levensnummer, right(mdr.levensnummer,$Karwerk) werknr, r.ras, hg.datum dmgebrn,
 date_format(hg.datum,'%d-%m-%Y') geb_datum, date_format(haf.datum,'%d-%m-%Y') afleverdm,
 date_format(hdo.datum,'%d-%m-%Y') uitvaldm,  
 count(lam.schaapId) lammeren, count(lam.levensnummer) levend,
 round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven, count(ooi.schaapId) aantooi,
 count(ram.schaapId) aantram, round(avg(hg_lm.kg),2) gemgewicht, 
 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn,
 round(avg(hs_lm.kg),2) gemspnkg, round(avg(hs_lm.kg-hg_lm.kg),2) gemgr_spn,
 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg, round(avg(haf_lm.kg-hg_lm.kg),2) gemgr_afv 
FROM tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (mdr.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (mdr.schaapId = ouder.schaapId)
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (st.schaapId = hg.schaapId)
 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 13 and haf.skip = 0)
 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14 and hdo.skip = 0)
 left join tblRas r on (r.rasId = mdr.rasId)
 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1 and hg_lm.skip = 0)
 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4 and hs_lm.skip = 0)
 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12 and haf_lm.skip = 0)
WHERE u.lidId = :lidId
 and mdr.geslacht = 'ooi'
 and isnull(haf.datum)
 and isnull(hdo.datum)
GROUP BY mdr.levensnummer, r.ras, hg.datum, date_format(haf.datum,'%d-%m-%Y'), date_format(hdo.datum,'%d-%m-%Y')
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function toon_meerlingen($lidId, $van, $tot) {
        return $this->run_query(
            <<<SQL
SELECT count(s.schaapId) aantal, aant worp
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join (
    SELECT s.volwId, count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
    WHERE lidId = :lidId
    GROUP BY s.volwId
 ) w on (s.volwId = w.volwId)
WHERE s.geslacht = 'ooi'
 and isnull(st.rel_best)
 and h.actId = 1
 and h.skip = 0
 and h.datum >= :van
 and h.datum <= :tot
GROUP BY aant
ORDER BY aant desc
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':van', $van],
            [':tot', $tot],
        ]
        );
    }

    public function tel_niet_afgevoerd($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function afvoerlijst($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.stalId, s.levensnummer, s.geslacht, h.actId
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 left join (
    SELECT schaapId, h.actId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) h on (h.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
ORDER BY h.actId, s.geslacht, right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_info($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT concat(transponder,levensnummer) tran, coalesce(s.geslacht,'Onbekend') geslacht, coalesce(r.ras,'Onbekend') ras,
 coalesce(ldek.dekdm_max,'n.v.t.') lastdekdm, coalesce(dram.werknr,'n.v.t.') dekram, coalesce(dram.ras,'n.v.t.') ras_dekram,
 coalesce(lw.worp,'n.v.t.') lastworp, coalesce(lw.werpdm,'n.v.t.') lastwerpdm,
    coalesce(aant_d, 0) aant_d,
    coalesce(aant_w, 0) aant_w,
    coalesce(round(aant_lam/aant_w,2),0) gemWorp,
    coalesce(aant_lam, 0) aant_lam,
    coalesce(round((1-coalesce(aant_dood, 0)/coalesce(aant_lam,0))*100,2),0) PercLevend,
    coalesce(maxworp, 0) maxworp,
    coalesce(aantalmaxworp, 0) aantalmaxworp
FROM tblSchaap s
 left join tblRas r on (s.rasId = r.rasId)
 join tblStal st on (s.schaapId = st.schaapId)
 left join (
     SELECT st.schaapId, max(h.datum) datum, date_format(max(h.datum),'%d-%m-%Y') dekdm_max
     FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
      join tblVolwas v on (v.hisId = h.hisId)
     WHERE h.skip = 0 and lidId = :lidId
     GROUP BY st.schaapId
 ) ldek on (ldek.schaapId = s.schaapId)
 left join (
     SELECT max(volwId) mvolwId, st.schaapId, h.datum
     FROM tblVolwas v
      join tblHistorie h on (v.hisId = h.hisId)
      join tblStal st on (h.stalId = st.stalId)
     WHERE h.skip = 0 and lidId = :lidId
     GROUP BY st.schaapId, h.datum
 ) lstVId on (lstVId.schaapId = ldek.schaapId and lstVId.datum = ldek.datum)
 left join (
     SELECT v.volwId, right(levensnummer, $Karwerk) werknr, r.ras
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
      join tblVolwas v on (v.vdrId = s.schaapId)
      join tblRas r on (r.rasId = s.rasId)
     WHERE lidId = :lidId
 ) dram on (dram.volwId = lstVId.mvolwId)
 left join (
     SELECT v.mdrId, count(l.volwId) worp, date_format(max(h.datum),'%d-%m-%Y') werpdm
    FROM (
        SELECT max(volwId) volwId, mdrId
        FROM tblVolwas v
         join tblStal st on (st.schaapId = v.mdrId)
        WHERE isnull(rel_best) and lidId = :lidId
        GROUP BY mdrId
    ) v
     join tblSchaap l on (l.volwId = v.volwId)
     join tblStal st on (st.schaapId = l.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
    GROUP BY v.mdrId
     ) lw on (lw.mdrId = s.schaapId)
 left join (
    SELECT count(hisId) aant_d, mdrId
    FROM tblVolwas v
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = :lidId
    GROUP BY mdrId
 ) dekat on (dekat.mdrId = s.schaapId)
  left join (
    SELECT count(DISTINCT(v.volwId)) aant_w, mdrId
    FROM tblVolwas v
     join tblStal st on (st.schaapId = v.mdrId)
     join tblSchaap s on (s.volwId = v.volwId)
    WHERE isnull(rel_best) and lidId = :lidId
    GROUP BY mdrId
 ) w on (w.mdrId = s.schaapId)
 left join (
    SELECT count(s.schaapId) aant_lam, mdrId
    FROM tblSchaap s
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = :lidId
    GROUP BY mdrId
 ) lm on (w.mdrId = lm.mdrId)
 left join (
    SELECT count(s.schaapId) aant_levend_niet_in_gebruik, mdrId
    FROM tblSchaap s
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = :lidId
 and levensnummer is not null
    GROUP BY mdrId
 ) le on (w.mdrId = le.mdrId)
 left join (
    SELECT coalesce(count(s.schaapId),0) aant_dood, mdrId
    FROM tblSchaap s
     join tblVolwas v on (s.volwId = v.volwId)
     join tblStal st on (st.schaapId = v.mdrId)
    WHERE isnull(rel_best) and lidId = :lidId
 and isnull(levensnummer)
    GROUP BY mdrId
 ) d on (w.mdrId = d.mdrId)
 left join (
     SELECT mw.mdrId, maxworp, count(wgr.volwId) aantalmaxworp
    FROM (
        SELECT max(worp) maxworp, mdrId
        FROM (
            SELECT s.volwId, count(s.schaapId) worp, mdrId
            FROM tblSchaap s
             join tblVolwas v on (s.volwId = v.volwId)
             join tblStal st on (st.schaapId = v.mdrId)
            WHERE isnull(rel_best) and lidId = :lidId
            GROUP BY s.volwId, mdrId
         ) wgr
        GROUP BY mdrId
     ) mw
     join (
        SELECT s.volwId, count(s.schaapId) worp, mdrId
        FROM tblSchaap s
         join tblVolwas v on (s.volwId = v.volwId)
         join tblStal st on (st.schaapId = v.mdrId)
        WHERE isnull(rel_best) and lidId = :lidId
        GROUP BY s.volwId, mdrId
     ) wgr on (mw.mdrId = wgr.mdrId and mw.maxworp = wgr.worp)
    GROUP BY mw.mdrId, maxworp
 ) amw on (w.mdrId = amw.mdrId)
WHERE isnull(st.rel_best)
 and lidId = :lidId
 and s.transponder is not null
 and s.schaapId = 9590
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_moederdier($lidId, $Karwerk, $ooi) {
        return $this->run_query(
            <<<SQL
SELECT mdr.levensnummer, right(mdr.levensnummer,$Karwerk) werknr, r.ras, date_format(hg.datum,'%d-%m-%Y') geb_datum,
 date_format(hop.datum,'%d-%m-%Y') aanvoerdm, count(lam.schaapId) lammeren, datediff(current_date(),ouder.datum) dagen,
 count(ooi.schaapId) aantooi, count(ram.schaapId) aantram,
 count(lam.levensnummer) levend, round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven,
 round(avg(hg_lm.kg),2) gemgewicht,
 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn, min(hs_lm.kg) minspnkg,
 max(hs_lm.kg) maxspnkg, round(avg(hs_lm.kg),2) gemspnkg,
 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg
FROM tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join (
     SELECT s.schaapId, s.levensnummer, s.volwId
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
 ) lam on (v.volwId = lam.volwId)
 join (
    SELECT max(stalId) stalId, mdr.schaapId
    FROM tblStal st
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblSchaap mdr on (st.schaapId = mdr.schaapId)
    WHERE u.lidId = :lidId
 and mdr.schaapId = :ooi
    GROUP BY mdr.schaapId
 ) maxst on (maxst.schaapId = mdr.schaapId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip =0
 ) ouder on (mdr.schaapId = ouder.schaapId)
 left join tblHistorie hg on (maxst.stalId = hg.stalId and hg.actId = 1)
 left join tblHistorie hop on (maxst.stalId = hop.stalId and (hop.actId = 2 or hop.actId = 11) )
 left join tblRas r on (r.rasId = mdr.rasId)
 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1)
 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4)
 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12)

GROUP BY mdr.levensnummer, mdr.geslacht, r.ras, date_format(hg.datum,'%d-%m-%Y'), date_format(hop.datum,'%d-%m-%Y')
ORDER BY right(mdr.levensnummer,$Karwerk) desc
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':ooi', $ooi, Type::INT]
        ]
        );
    }

    public function zoek_lammeren($lidId, $ooi, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT s.levensnummer, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, ouder.datum dmaanw,
 date_format(hg.datum,'%d-%m-%Y') gebrndm, date_format(hg.datum,'%Y-%m-%d') dmgebrn, hg.kg gebrnkg,
 date_format(hs.datum,'%d-%m-%Y') speendm, hs.kg speenkg, 
case when hs.kg-hg.kg > 0 and datediff(hs.datum,hg.datum) > 0 then round(((hs.kg-hg.kg)/datediff(hs.datum,hg.datum)*1000),2) end gemgr_s,
date_format(haf.datum,'%d-%m-%Y') afvdm, haf.kg afvkg, date_format(hdo.datum,'%d-%m-%Y') uitvaldm, re.reden, 
case when haf.kg-hg.kg > 0 and datediff(haf.datum,hg.datum) > 0 then round(((haf.kg-hg.kg)/datediff(haf.datum,hg.datum)*1000),2) end gemgr_a
FROM tblSchaap s
 join tblVolwas v on (v.volwId = s.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId) 
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join tblRas r on (s.rasId = r.rasId)
 left join tblReden re on (s.redId = re.redId)
 join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1)
 left join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4)
 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12)
 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14)
 join tblStal st_all on (st_all.schaapId = s.schaapId)
 left join tblHistorie ouder on (st_all.stalId = ouder.stalId and ouder.actId = 3)
WHERE u.lidId = :lidId and v.mdrId = :ooi
ORDER BY hg.datum
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':ooi', $ooi, Type::INT]
        ]
        );
    }

    public function stallijstgegevens($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1) 
 left join (
    SELECT st.schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE u.lidId = :lidId
 and isnull(st.rel_best)
ORDER BY right(s.levensnummer, $Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function getHokAanwasFrom() {
        return <<<SQL
tblSchaap s
        join tblStal st on (st.schaapId = s.schaapId)
        join tblHistorie h on (h.stalId = st.stalId)
        join tblBezet b on (b.hisId = h.hisId)
        left join (
            SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and b.hokId = :hokId
            GROUP BY b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 join (
     SELECT st.schaapId, h.datum
     FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 left join (
     SELECT st.schaapId, h.datum
     FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
SQL;
    }

    public function getHokAanwasWhere($ID, $lidId) {
        return [
            "WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and isnull(prnt.schaapId)",
            [[':hokId', $ID, Type::INT], [':lidId', $lidId, Type::INT]]
        ];
    }

    public function getHokOverplFrom() {
        return <<<SQL
(
SELECT s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId, 'lam' sort
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT max(hisId) hisId, stalId
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = :hokId
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
UNION
SELECT s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId, s.geslacht sort
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
        SELECT max(hisId) hisId, stalId
        FROM tblHistorie
        WHERE skip = 0
        GROUP BY stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 join tblHistorie h on (st.stalId = h.stalId)
 join (
        SELECT b.hisId, b.hokId
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join (
                SELECT st.schaapId, h.hisId, h.datum
                FROM tblStal st
                join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
        ) prnt on (prnt.schaapId = st.schaapId)
        WHERE b.hokId = :hokId and h.skip = 0
 ) b on (b.hisId = h.hisId)
 left join (
        SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
 WHERE b.hokId = :hokId and h.skip = 0 and isnull(uit.bezId)
) tbl
SQL;
    }

    public function getHokOverplWhere($keuze, $hokId) {
        $filterResult = '';
        if ($keuze == 1) {
            $filterResult = ' and isnull(prnt)';
        } elseif ($keuze == 2) {
            $filterResult = ' and prnt is not null';
        }
        return [
            " WHERE hokId = :hokId and isnull(bezId) $filterResult",
            [[':hokId', $hokId, Type::INT]]
        ];
    }

    public function aantal_volwassen_dieren($hokId) {
        return $this->first_field(
            <<<SQL
SELECT count(*) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblBezet b on (b.hisId = h.hisId)
left join
(
        SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = :hokId and a1.aan = 1
         and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId, st.schaapId, h1.hisId
) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = st.schaapId)
 join (
    SELECT st.schaapId, h.datum, h.actId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = :hokId and isnull(uit.bezId) and h.skip = 0 and prnt.schaapId is not null
SQL
        , [[':hokId', $hokId, Type::INT]]
        );
    }

    public function getHokVerlatenFrom() {
        return <<<SQL
tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join (
            SELECT max(hisId) hisId, h.stalId
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId and h.skip = 0
            GROUP BY h.stalId
     ) hmax on (hmax.stalId = st.stalId)
     join tblHistorie hm on (hm.hisId = hmax.hisId)
     join tblHistorie h on (st.stalId = h.stalId)
     join (
            SELECT b.hisId, b.hokId
            FROM tblBezet b
             join tblHistorie h on (b.hisId = h.hisId)
             join tblStal st on (st.stalId = h.stalId)
             join (
                SELECT st.schaapId, h.hisId, h.datum
                FROM tblStal st
                join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
            ) prnt on (prnt.schaapId = st.schaapId)
            WHERE b.hokId = :hokId and h.skip = 0
      ) b_prnt on (b_prnt.hisId = h.hisId)
     left join (
            SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
            WHERE b.hokId = :hokId and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
            GROUP BY b.bezId, h1.hisId
     ) uit on (uit.hisv = b_prnt.hisId)
     left join (
            SELECT st.schaapId, h.datum
            FROM tblStal st
             join tblHistorie h on (st.stalId = h.stalId)
            WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
SQL;
    }

    public function getHokVerlatenWhere($lidId, $hokId) {
        return [
            " WHERE b_prnt.hokId = :hokId and isnull(uit.bezId) ",
            [
                [':lidId', $lidId, Type::INT],
                [':hokId', $hokId, Type::INT],
            ]
        ];
    }

// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 60 dagen
    public function zoek_moederdieren($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
     SELECT stalId, hisId
     FROM tblHistorie h
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId != 10 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and s.geslacht = 'ooi'
 and isnull(haf.hisId)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    // Declaratie VADERDIER  ALLEEN OP STAL tussen nu en de afgelopen 2 maanden
    public function zoek_vaderdieren($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
    SELECT stalId, hisId, datum
     FROM tblHistorie h
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId != 10 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and s.geslacht = 'ram'
 and ( isnull(haf.hisId) or date_add(haf.datum,interval 2 month) > CURRENT_DATE() )
ORDER BY right(levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_vader_laatste_dekkingen($volwId, $Karwerk) {
        return $this->first_field(
            <<<SQL
SELECT right(levensnummer,$Karwerk) werknr
FROM tblSchaap vdr
 join tblVolwas v on (v.vdrId = vdr.schaapId)
WHERE v.volwId = :volwId
SQL
        , [[':volwId', $volwId, Type::INT]]
        );
    }

// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 183 dagen
    public function zoek_moederdieren_183($lidId, $Karwerk) {
        return $this->run_query(
            <<<SQL
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join (
     SELECT stalId, hisId
     FROM tblHistorie h
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.actId != 10 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = :lidId and s.geslacht = 'ooi' and isnull(haf.hisId)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function start_moeder($lidId, $schaapId, $stalId) {
        /* bij aankoop incl. geboortedatum wordt geboortedatum niet getoond */
        return $this->first_field(
            <<<SQL
SELECT h.datum
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE stalId != :stalId
 and lidId = :lidId
 and schaapId = :schaapId
    GROUP BY schaapId
 ) mst on (mst.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = mst.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.op = 1
 and h.skip = 0
and not exists (
    SELECT datum 
    FROM tblHistorie ha 
     join tblStal st on (ha.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE actId = 2
 and h.skip = 0
 and mst.stalId = st.stalId
 and h.actId = ha.actId-1
 and s.schaapId = :schaapId )
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':schaapId', $schaapId, Type::INT],
            [':stalId', $stalId, Type::INT]
        ]
        );
    }

    public function einde_moeder($lidId, $schaapId) {
        return $this->first_field(
            <<<SQL
SELECT h.datum
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE lidId = :lidId
 and schaapId = :schaapId
    GROUP BY schaapId
 ) st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1
 and h.actId != 10
 and h.skip = 0        
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':schaapId', $schaapId, Type::INT],
        ]
        );
    }

    // zoek de vorige worp waarbij de werpdatum minimaal 30 dagen voor de geboortedatum moet liggen.
    // Dit voor het geval de huidige worp enkele dagen voor de geboortedatum ligt.
    public function zoek_vorige_worp($schaapId, $day) {
        return $this->first_field(
            <<<SQL
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
   SELECT s.schaapId
   FROM tblSchaap s
    join tblStal st on (s.schaapId = st.schaapId)
    join tblHistorie h on (st.stalId = h.stalId)
   WHERE h.actId = 3 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId) 
WHERE v.mdrId = :schaapId
 and h.actId = 1
 and h.skip = 0
 and date_add(h.datum,interval 30 day) < :day
 and isnull(ha.schaapId)
SQL
        , [
            [':schaapId', $schaapId, Type::INT],
            [':day', $day]
        ]
        );
    }

    public function zoek_huidige_worp($mdrId, $volwId) {
        return $this->first_row(
            <<<SQL
SELECT h.datum dmwerp, date_format(h.datum,'%d-%m-%Y') werpdm
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.skip = 0
 and v.volwId > :volwId
SQL
        , [
            [':mdrId', $mdrId, Type::INT],
            [':volwId', $volwId, Type::INT],
        ]
        , [null, null]
        );
    }

    public function zoek_huidige_worp_geb($mdrId, $fldDag) {
        $sql = <<<SQL
    SELECT l.volwId
       FROM tblSchaap l
        join tblVolwas v on (l.volwId = v.volwId)
        join tblStal st on (l.schaapId = st.schaapId)
        join tblHistorie h on (h.stalId = st.stalId)
       WHERE v.mdrId = :mdrId
 and h.actId = 1
 and h.datum = :fldDag
SQL;
        $args = [[':mdrId', $mdrId, Type::INT], [':fldDag', $fldDag]];
        return $this->first_field($sql, $args);
    }

    public function zoek_fase($lidId, $schaapId) {
        return $this->first_record(
            <<<SQL
SELECT s.schaapId, s.geslacht, af.stalId s_af, prnt.schaapId prnt
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join (
    SELECT max(stalId) stalId
    FROM tblStal
    WHERE lidId = :lidId
 and schaapId = :schaapId
 ) mst on (mst.stalId = st.stalId)
 left join (
    SELECT st.stalId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE a.af = 1
 and lidId = :lidId
 and schaapId = :schaapId
 and h.skip = 0
 ) af on (af.stalId = mst.stalId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE u.lidId = :lidId
 and st.schaapId = :schaapId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':schaapId', $schaapId, Type::INT],
        ]
        , ['schaapId' => null, 'geslacht' => null, 'prnt' => null, 's_af' => null]
        );
    }

    public function zoek_levnr($levensnummer) {
        return $this->run_query(
            <<<SQL
SELECT s.schaapId, gebdag, geslacht, his_aanw, r.ras
FROM tblSchaap s
 left join (
     SELECT schaapId, date_format(h.datum,'%d-%m-%Y') gebdag
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 1 and h.skip = 0
 ) geb on (geb.schaapId = s.schaapId)
 left join (
     SELECT schaapId, hisId his_aanw
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3 and h.skip = 0
 ) aanw on (aanw.schaapId = s.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
WHERE s.levensnummer = :levensnummer
SQL
        , [[':levensnummer', $levensnummer]]
        );
    }

    public function zoek_levnr_db($schaapId) {
        return $this->run_query(
            <<<SQL
SELECT gebdag, spndag_geb, geslacht, spndag, his_aanw, r.ras
FROM tblSchaap s
 left join (
     SELECT schaapId, date_format(h.datum,'%d-%m-%Y') gebdag, date_format(date_add(h.datum,interval 49 day),'%d-%m-%Y') spndag_geb
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 1 and h.skip = 0
     ) geb on (geb.schaapId = s.schaapId)
 left join (
     SELECT schaapId, date_format(h.datum,'%d-%m-%Y') spndag
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 4 and h.skip = 0
     ) spn on (spn.schaapId = s.schaapId)
  left join (
     SELECT schaapId, hisId his_aanw
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3 and h.skip = 0
     ) aanw on (aanw.schaapId = s.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
WHERE s.schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, Type::INT]]
        );
    }

    public function zoek_aantal_niet_gescand($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(s.schaapId) nietgescand
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join (
     SELECT levensnummer
    FROM impAgrident
    WHERE lidId = :lidId
 and actId = 22
 and isnull(verwerkt)
 ) rd on (rd.levensnummer = s.levensnummer)
WHERE isnull(rd.levensnummer)
 and isnull(st.rel_best)
 and u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_niet_gescande_schapen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT s.levensnummer, gebdm, s.geslacht, oudr.hisId aanwasId, lastdm
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join (
     SELECT levensnummer
    FROM impAgrident
    WHERE lidId = :lidId and actId = 22 and isnull(verwerkt)
 ) rd on (rd.levensnummer = s.levensnummer)
 left join (
     SELECT st.schaapId, date_format(h.datum,'%d-%m-%Y') gebdm
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE actId = 1 and h.skip = 0
 ) geb on (geb.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.hisId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
     WHERE actId = 3 and h.skip = 0
 ) oudr on (oudr.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, date_format(h.datum,'%d-%m-%Y') lastdm
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join (
              SELECT max(hisId) hismx, schaapId
              FROM tblHistorie h
              join tblStal st on (h.stalId = st.stalId)
             WHERE actId = 22 and h.skip = 0 and lidId = :lidId
             GROUP BY schaapId
         ) sc on (sc.hismx = h.hisId and sc.schaapId = st.schaapId)
 ) lstsc on (lstsc.schaapId = s.schaapId)
WHERE isnull(rd.levensnummer) and isnull(st.rel_best) and u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    // TODO: hier komt voor $aant iets binnen dat $recId heet. Klopt alles nog?
    public function zoek_dieren($lidId, $datumvan, $datumtot, $aant) {
        return $this->run_query(
            <<<SQL
SELECT levensnummer, transponder
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join (
    SELECT s.volwId, count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
    WHERE lidId = :lidId
    GROUP BY s.volwId
 ) w on (s.volwId = w.volwId)
WHERE s.geslacht = 'ooi'
 and isnull(st.rel_best)
 and h.actId = 1
 and h.skip = 0
 and h.datum >= :datumvan
 and h.datum <= :datumtot
 and w.aant = :aant
ORDER BY levensnummer
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':datumvan', $datumvan],
            [':datumtot', $datumtot],
            [':aant', $aant, Type::INT]
        ]
        );
    }

    public function jaarworp($lidId, $jaar) {
        return $this->first_field(
            <<<SQL
SELECT count(distinct s.volwId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1
 and h.skip = 0
 and date_format(h.datum,'%Y') = :jaar
 and u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT], [':jaar', $jaar]]
        );
    }

    public function zoek_gegevens_schaap($schaapId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT prnt.hisId aanwId, s.geslacht, r.ras, right(mdr.levensnummer,$Karwerk) werknr_ooi,
right(vdr.levensnummer,$Karwerk) werknr_ram, date_format(hgeb.datum,'%d-%m-%Y') gebdm
FROM tblSchaap s
 left join (
     SELECT h.hisId, st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 3 and schaapId = :schaapId
 ) prnt on (s.schaapId = prnt.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (s.volwId = v.volwId)
 left join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 left join tblSchaap vdr on (vdr.schaapId = v.mdrId)
 left join (
     SELECT h.datum, st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 1 and schaapId = :schaapId
 ) hgeb on (s.schaapId = hgeb.schaapId)
WHERE s.schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, Type::INT]]
        );
    }

    public function zoek_vandaag_ingevoerd_met_levnr($lidId) {
        return $this->first_field(<<<SQL
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE s.levensnummer is not null
 and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE()
 and u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_vandaag_ingevoerd_zonder_levnr($lidId) {
        return $this->first_field(<<<SQL
SELECT count(s.schaapId) aant
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE isnull(s.levensnummer)
 and date_format(s.dmcreatie,'%Y-%m-%d') = CURRENT_DATE()
 and u.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_mindag($schaapId) {
        return $this->first_field(<<<SQL
SELECT hm.datum
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT max(hisId) hisId, stalId
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
WHERE s.schaapId = :schaapId
SQL
        , [[':schaapId', $schaapId, Type::INT]]
        );
    }

    public function schapen_geboren($lidId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function schapen_speen($lidId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function schapen_vanaf_aanwas($lidId, $Karwerk) {
        return $this->run_query(<<<SQL
SELECT s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId and isnull(st.rel_best) and a.aan = 1 and h.skip = 0
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
ORDER BY right(s.levensnummer,$Karwerk)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function list_for($lidId) {
        return $this->collect_list(<<<SQL
SELECT s.schaapId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE u.lidId = :%lidId
GROUP BY s.schaapId
ORDER BY s.schaapId
SQL
        , ['lidId' => $lidId]
        );
    }

    public function delete_ids($ids) {
        $this->run_query(<<<SQL
DELETE FROM tblSchaap WHERE :%schaapId
SQL
        , ['schaapId' => $ids]
        );
    }

    public function zoek_laatste_worpdatum($ooiId) {
        $sql = <<<SQL
        SELECT max(h.datum) dmworp
        FROM tblSchaap s
         join tblVolwas v on (s.volwId = v.volwId)
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and v.mdrId = :ooiId and h.skip = 0
SQL;
        $args = [[':ooiId', $ooiId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function kzl_ooikaart($lidId) {
        $sql = <<<SQL
            SELECT mdr.schaapId, mdr.levensnummer
            FROM tblSchaap mdr
             left join tblVolwas v on (mdr.schaapId = v.mdrId)
             left join tblSchaap lam on (v.volwId = lam.volwId) 
             join tblStal st on (mdr.schaapId = st.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join (
                SELECT schaapId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
             ) h on (st.schaapId = h.schaapId)
            WHERE u.lidId = :lidId and isnull(st.rel_best) and mdr.geslacht = 'ooi'
            GROUP BY mdr.schaapId, mdr.levensnummer
            ORDER BY mdr.levensnummer
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function kzl_werknr($Karwerk, $lidId) {
        $sql = <<<SQL
            SELECT mdr.schaapId, right(mdr.levensnummer,:Karwerk) werknr
            FROM tblSchaap mdr 
             left join tblVolwas v on (mdr.schaapId = v.mdrId)
             left join tblSchaap lam on (v.volwId = lam.volwId) 
             join tblStal st on (mdr.schaapId = st.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join (
                SELECT schaapId
                FROM tblStal st
                 join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
             ) h on (st.schaapId = h.schaapId)
            WHERE u.lidId = :lidId and isnull(st.rel_best) and mdr.geslacht = 'ooi'
            GROUP BY mdr.schaapId, right(mdr.levensnummer,:Karwerk)
            ORDER BY right(mdr.levensnummer,:Karwerk)
SQL;
        $args = [[':Karwerk', $Karwerk], [':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function kzl_halsnr($lidId) {
        $sql = <<<SQL
            SELECT s.schaapId, concat(st.kleur,' ',st.halsnr) halsnr
            FROM tblSchaap s
             join tblStal st on (s.schaapId = st.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
            WHERE u.lidId = :lidId and st.kleur is not null and st.halsnr is not null and isnull(st.rel_best)
            GROUP BY s.schaapId, concat(st.kleur,' ',st.halsnr)
            ORDER BY st.kleur, st.halsnr
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function result_mdr($Karwerk, $lidId, $gekozen_ooi) {
        $sql = <<<SQL
                SELECT mdr.levensnummer, right(mdr.levensnummer,:Karwerk) werknr, r.ras, date_format(hg.datum,'%d-%m-%Y') geb_datum, date_format(hop.datum,'%d-%m-%Y') aanvoerdm, count(lam.schaapId) lammeren, datediff(current_date(),ouder.datum) dagen, count(ooi.schaapId) aantooi, count(ram.schaapId) aantram,
                 count(lam.levensnummer) levend, round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven, round(avg(hg_lm.kg),2) gemgewicht,
                 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn, min(hs_lm.kg) minspnkg, max(hs_lm.kg) maxspnkg, round(avg(hs_lm.kg),2) gemspnkg,
                 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg
                FROM tblSchaap mdr 
                 left join tblVolwas v on (mdr.schaapId = v.mdrId)
                 left join (
                     SELECT s.schaapId, s.levensnummer, s.volwId
                     FROM tblSchaap s
                      join tblStal st on (s.schaapId = st.schaapId)
                join tblUbn u USING(ubnId)
                      join tblHistorie h on (st.stalId = h.stalId)
                     WHERE u.lidId = :lidId and h.actId = 1 and h.skip = 0
                 ) lam on (v.volwId = lam.volwId)
                 join (
                    SELECT max(stalId) stalId, mdr.schaapId
                    FROM tblStal st
                join tblUbn u USING(ubnId)
                     join tblSchaap mdr on (st.schaapId = mdr.schaapId)
                    WHERE u.lidId = :lidId and mdr.schaapId = :gekozen_ooi
                    GROUP BY mdr.schaapId
                 ) maxst on (maxst.schaapId = mdr.schaapId)
                 join (
                    SELECT st.schaapId, h.datum
                    FROM tblStal st
                     join tblHistorie h on (st.stalId = h.stalId)
                    WHERE h.actId = 3 and h.skip = 0
                 ) ouder on (mdr.schaapId = ouder.schaapId)
                 left join tblHistorie hg on (maxst.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0)
                 left join tblHistorie hop on (maxst.stalId = hop.stalId and (hop.actId = 2 or hop.actId = 11) and hop.skip = 0 )
                 left join tblRas r on (r.rasId = mdr.rasId)
                 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
                 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
                 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
                 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1 and hg_lm.skip = 0)
                 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4 and hg_lm.skip = 0)
                 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12 and haf_lm.skip = 0)
                GROUP BY mdr.levensnummer, mdr.geslacht, r.ras, date_format(hg.datum,'%d-%m-%Y'), date_format(hop.datum,'%d-%m-%Y')
                ORDER BY right(mdr.levensnummer,:Karwerk) desc
SQL;
        $args = [[':Karwerk', $Karwerk], [':lidId', $lidId, Type::INT], [':gekozen_ooi', $gekozen_ooi]];
        return $this->run_query($sql, $args);
    }

    public function lammeren($Karwerk, $lidId, $gekozen_ooi) {
        $sql = <<<SQL
                SELECT s.levensnummer, right(s.levensnummer,:Karwerk) werknr, r.ras, s.geslacht, ouder.datum dmaanw, date_format(hg.datum,'%d-%m-%Y') gebrndm, date_format(hg.datum,'%Y-%m-%d') dmgebrn, hg.kg gebrnkg, date_format(hs.datum,'%d-%m-%Y') speendm, hs.kg speenkg, 
                case when hs.kg-hg.kg > 0 and datediff(hs.datum,hg.datum) > 0 then round(((hs.kg-hg.kg)/datediff(hs.datum,hg.datum)*1000),2) end gemgr_s,
                date_format(haf.datum,'%d-%m-%Y') afvdm, haf.kg afvkg, date_format(hdo.datum,'%d-%m-%Y') uitvaldm, re.reden, 
                case when haf.kg-hg.kg > 0 and datediff(haf.datum,hg.datum) > 0 then round(((haf.kg-hg.kg)/datediff(haf.datum,hg.datum)*1000),2) end gemgr_a
                FROM tblSchaap s
                 join tblVolwas v on (v.volwId = s.volwId)
                 join tblSchaap mdr on (mdr.schaapId = v.mdrId) 
                 join tblStal st on (s.schaapId = st.schaapId)
                 join tblUbn u on (u.ubnId = st.ubnId)
                 left join tblRas r on (s.rasId = r.rasId)
                 left join tblReden re on (s.redId = re.redId)
                 join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0)
                 left join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4 and hs.skip = 0)
                 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 12 and haf.skip = 0)
                 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14 and hdo.skip = 0)
                 join tblStal st_all on (st_all.schaapId = s.schaapId)
                 left join tblHistorie ouder on (st_all.stalId = ouder.stalId and ouder.actId = 3 and ouder.skip = 0)
                WHERE u.lidId = :lidId and v.mdrId = :gekozen_ooi
                ORDER BY hg.datum
SQL;
        $args = [[':Karwerk', $Karwerk], [':lidId', $lidId, Type::INT], [':gekozen_ooi', $gekozen_ooi]];
        return $this->run_query($sql, $args);
    }

    public function zoek_oud_levensnummer($pstnr) {
        $sql = <<<SQL
    SELECT levensnummer
    FROM tblSchaap
    WHERE levensnummer = :pstnr
SQL;
        $args = [[':pstnr', $pstnr]];
        return $this->first_field($sql, $args);
    }

    public function zoek_oud_levensnummer_obv_schaapId($pst) {
        $sql = <<<SQL
            SELECT levensnummer
            FROM tblSchaap
            WHERE schaapId = :pst
SQL;
        $args = [[':pst', $pst]];
        return $this->first_field($sql, $args);
    }

    public function zoek_op_bestaand_levensnummer($levnr_new) {
        $sql = <<<SQL
    SELECT schaapId
    FROM tblSchaap
    WHERE levensnummer = :levnr_new
SQL;
        $args = [[':levnr_new', $levnr_new]];
        return $this->first_field($sql, $args);
    }

    public function zoek_transp_moeder($moeder) {
        $sql = <<<SQL
    SELECT schaapId, transponder
    FROM tblSchaap
    WHERE levensnummer = :moeder
SQL;
        $args = [[':moeder', $moeder]];
        return $this->first_row($sql, $args, [0, 0]);
    }

    public function update_tblSchaap($mdrTran_rd, $moederId) {
        $sql = <<<SQL
        UPDATE tblSchaap set transponder = :mdrTran_rd WHERE schaapId = :moederId
SQL;
        $args = [[':mdrTran_rd', $mdrTran_rd], [':moederId', $moederId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function insert_tblSchaap($fldLevnr, $fldRas, $fldSekse, $volwId, $fldMom, $fldRed, $tran) {
        $sql = <<<SQL
    INSERT INTO tblSchaap set
     levensnummer = :fldLevnr,
     rasId = :fldRas,
     geslacht = :fldSekse,
     volwId = :volwId,
     momId = :fldMom,
     redId = :fldRed,
     transponder = :tran
SQL;
        $args = [[':fldLevnr', $fldLevnr], [':fldRas', $fldRas], [':fldSekse', $fldSekse], [':volwId', $volwId, Type::INT], [':fldMom', $fldMom], [':fldRed', $fldRed], [':tran', $tran]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function wis_levensnummer_by_id($schaapId) {
        $sql = <<<SQL
    UPDATE tblSchaap set levensnummer = NULL WHERE schaapId = :schaapId
SQL;
        $args = [[':schaapId', $schaapId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    // bijna-duplicaat: het verschil tussen __0 en ___1 is de HAVING-grens
    public function ooien_met_meerlingworpen0($lidId, $Karwerk, $order) {
        $sql = <<<SQL
SELECT schaapId, ooi, sum(worp) totat
FROM (
    SELECT mdr.schaapId, right(mdr.levensnummer,:Karwerk) ooi, v.volwId, count(lam.schaapId) worp
    FROM tblSchaap mdr
     join tblStal stm on (stm.schaapId = mdr.schaapId)
     join tblUbn um on stm.ubnId = um.ubnId
     join tblVolwas v on (mdr.schaapId = v.mdrId)
     join tblSchaap lam on (v.volwId = lam.volwId)
     join tblStal st on (lam.schaapId = st.schaapId)
     join tblUbn u on st.ubnId = u.ubnId
    WHERE isnull(stm.rel_best)
 and um.lidId = :lidId
 and u.lidId = :lidId
    GROUP BY mdr.schaapId, right(mdr.levensnummer,:Karwerk), v.volwId
    HAVING count(v.volwId) > 0
     ) perWorp
GROUP BY schaapId, ooi
ORDER BY :order
SQL;
        $args = [[':Karwerk', $Karwerk], [':lidId', $lidId, Type::INT], [':order', $order]];
        return $this->run_query($sql, $args);
    }

    public function zoek_aantal_geengeslacht_tbv_hoofding($ooiId, $lidId) {
        $sql = <<<SQL
    SELECT count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblVolwas v on (s.volwId = v.volwId)
    WHERE isnull(s.geslacht)
     and v.mdrId = :ooiId
     and u.lidId = :lidId
SQL;
        $args = [[':ooiId', $ooiId, Type::INT], [':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_meerlingen_ooi($lidId, $ooiId) {
        $sql = <<<SQL
     SELECT date_format(h.datum,'%m')*1 mnd, date_format(h.datum,'%Y') jaar, count(lam.schaapId) aant, v.volwId
     FROM tblSchaap mdr
      join tblVolwas v on (v.mdrId = mdr.schaapId)
      join tblSchaap lam on (v.volwId = lam.volwId)
      join tblStal st on (st.schaapId = lam.schaapId)
      join tblUbn u ON u.ubnId = st.ubnId
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
      and mdr.schaapId = :ooiId
      and h.actId = 1
      and h.skip = 0
     GROUP BY date_format(h.datum,'%Y%m'), date_format(h.datum,'%Y'), v.volwId
     ORDER BY date_format(h.datum,'%Y%m') desc
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':ooiId', $ooiId, Type::INT]];
        return $this->run_query($sql, $args);
    }    

    public function zoek_aantal_ooitjes($volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and s.geslacht = 'ooi'
     and h.actId = 1
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
SQL;
        $args = [[':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_werknr_ooitjes($Karwerk, $volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT coalesce(right(s.levensnummer,:Karwerk),' ------- ') werknr, kg
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and s.geslacht = 'ooi'
     and h.actId = 1
     and isnull(st.rel_best)
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
    GROUP BY s.schaapId
SQL;
        $args = [[':Karwerk', $Karwerk], [':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_aantal_ramtjes($volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and s.geslacht = 'ram'
     and h.actId = 1
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
SQL;
        $args = [[':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_aantal_geengeslacht($volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and isnull(s.geslacht)
     and h.actId = 1
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
SQL;
        $args = [[':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_werknr_ramtjes($Karwerk, $volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT coalesce(right(s.levensnummer,:Karwerk),' ------- ') werknr, kg
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and s.geslacht = 'ram'
     and h.actId = 1
     and isnull(st.rel_best)
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
    GROUP BY s.schaapId
SQL;
        $args = [[':Karwerk', $Karwerk], [':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

    public function zoek_werknr_geengeslacht($Karwerk, $volwId, $mnd, $jaar) {
        $sql = <<<SQL
    SELECT coalesce(right(s.levensnummer,:Karwerk),' ------- ') werknr, kg
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE s.volwId = :volwId
     and isnull(s.geslacht)
     and h.actId = 1
     and isnull(st.rel_best)
     and date_format(h.datum,'%m')*1 = :mnd
     and date_format(h.datum,'%Y') = :jaar
     and h.skip = 0
    GROUP BY s.schaapId
SQL;
        $args = [[':Karwerk', $Karwerk], [':volwId', $volwId, Type::INT], [':mnd', $mnd], [':jaar', $jaar]];
        return $this->run_query($sql, $args);
    }

}
