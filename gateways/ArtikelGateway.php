<?php

class ArtikelGateway extends Gateway {

    public function pilForLid($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam
FROM tblEenheiduser eu
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
WHERE eu.lidId = :lidId
 and a.soort = 'pil'
GROUP BY a.naam
ORDER BY a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function new_medicijn($lidId) {
        return $this->run_query(<<<SQL
SELECT a.artId, a.naam
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = :lidId and a.soort = 'pil' and a.actief = 1
ORDER BY a.naam
SQL
        , [[':lidId', $lidId, Type::INT]]);
    }

    public function zoek_soort($artId): ?string {
        return $this->first_field(
            <<<SQL
SELECT a.soort
FROM tblArtikel a
WHERE a.artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function pilregels($artId) {
        return $this->run_query(
            <<<SQL
SELECT i.inkId, a.naam, date_format(i.dmink,'%d-%m-%Y') toedm, i.charge, round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) totat, e.eenheid
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblNuttig n on (n.inkId = i.inkId) 
WHERE a.artId = :artId
GROUP BY i.inkId, a.naam, i.dmink, i.charge, i.inkat, e.eenheid
ORDER BY i.dmink desc, i.inkId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function voerregels($artId) {
        return $this->run_query(
            <<<SQL
SELECT i.inkId, a.naam, date_format(i.dmink,'%d-%m-%Y') toedm, NULL charge, round(i.inkat - sum(coalesce(v.nutat*v.stdat,0)),0) totat, e.eenheid
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblVoeding v on (v.inkId = i.inkId) 
WHERE a.artId = :artId
GROUP BY i.inkId, a.naam, i.dmink, i.inkat, e.eenheid
ORDER BY i.dmink desc, i.inkId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function voer($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.inkat-coalesce(v.vbrat,0) vrdat, round((i.inkat - coalesce(v.vbrat,0))/a.stdat,2) toedat
FROM tblArtikel a
 join (
    SELECT i.artId, i.enhuId, sum(i.inkat) inkat
    FROM tblEenheiduser eu
     join tblInkoop i on (i.enhuId = eu.enhuId)
     join tblArtikel a on (a.artId = i.artId)
    WHERE eu.lidId = :lidId
 and a.soort = 'voer'
    GROUP BY a.artId, i.enhuId
 ) i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
    SELECT a.artId, sum(v.nutat*v.stdat) vbrat
    FROM tblEenheiduser eu
     join tblArtikel a on (a.enhuId = eu.enhuId)
     join tblInkoop i on (i.artId = a.artId)
     join tblVoeding v on (i.inkId = v.inkId)
    WHERE eu.lidId = :lidId
 and a.soort = 'voer'
    GROUP BY a.artId
 ) v on (i.artId = v.artId)
WHERE eu.lidId = :lidId
 and a.soort = 'voer'
 and i.inkat - coalesce(v.vbrat,0) > 0
ORDER BY a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function pil($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.charge, sum(i.inkat-coalesce(n.vbrat,0)) vrdat,
 round(sum((i.inkat-coalesce(n.vbrat,0))/a.stdat),2) toedat, artvrd.totvrd
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join (
    SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
    FROM tblEenheiduser eu
     join tblArtikel a on (a.enhuId = eu.enhuId)
     join tblInkoop i on (i.artId = a.artId)
     join tblNuttig n on (i.inkId = n.inkId)
    WHERE eu.lidId = :lidId
 and a.soort = 'pil'
    GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
 left join (
    SELECT artId, sum(totat) totvrd
    FROM (
        SELECT a.artId, round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) totat
        FROM tblEenheiduser eu
         join tblArtikel a on (eu.enhuId = a.enhuId)
         join tblInkoop i on (a.artId = i.artId)
         left join tblNuttig n on (n.inkId = i.inkId) 
        WHERE eu.lidId = :lidId
 and a.soort = 'pil'
        GROUP BY i.inkId
     ) vrd
    GROUP BY artId
 ) artvrd on (artvrd.artId = a.artId)
WHERE eu.lidId = :lidId
 and a.soort = 'pil'
 and i.inkat-coalesce(n.vbrat,0) > 0 
GROUP BY a.artId, a.naam, a.stdat, e.eenheid, i.charge, artvrd.totvrd
ORDER BY a.naam, i.inkId
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_voer($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
WHERE a.soort = 'voer'
 and eu.lidId = :lidId
GROUP BY a.artId, a.naam 
ORDER BY a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function countVoerByName($lidId, $naam): ?int {
        return $this->first_field(
            <<<SQL
SELECT count(naam) aantal
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
WHERE eu.lidId = :lidId
 and a.naam = :naam
 and a.soort = 'voer'
GROUP BY a.naam
SQL
        ,
            [[':lidId', $lidId, Type::INT], [':naam', $naam]]
        );
    }

    public function store($insNaam, $insStdat, $insNhd, $insBtw, $insRelatie, $insRubriek): void {
        $this->run_query(
            <<<SQL
INSERT INTO tblArtikel SET
    soort = 'voer',
    naam = :naam,
    stdat = :stdat,
    enhuId = :enhuId,
    btw = :btw,
    relId= :relId,
    rubuId= :rubuId
SQL
            ,
            [
                [':naam', $insNaam],
                [':stdat', $insStdat],
                [':enhuId', $insNhd],
                [':btw', $insBtw],
                [':relId', $insRelatie],
                [':rubuId', $insRubriek],
            ]
        );
    }

    public function findVoerByUser($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = :lidId
 and a.soort = 'voer'
 and a.actief = 1
ORDER BY a.actief desc, a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function details($artId) {
        return $this->run_query(
            <<<SQL
SELECT a.soort, a.naam, a.stdat, a.enhuId, e.eenheid, a.btw, a.relId, a.rubuId, a.actief
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE a.artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function details_met_partij($artId) {
        return $this->run_query(
            <<<SQL
SELECT a.soort, a.naam, a.stdat, a.enhuId, e.eenheid, a.btw, p.naam relatie, r.rubriek, a.actief
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 left join tblRelatie rl on (rl.relId = a.relId)
 left join tblPartij p on (p.partId = rl.partId)
 left join tblRubriekuser ru on (a.rubuId = ru.rubuId)
 left join tblRubriek r on (r.rubId = ru.rubId)
WHERE a.artId = :artId
ORDER BY a.naam 
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function tel_niet_in_gebruik($lidId): ?int {
        return $this->first_field(
            <<<SQL
SELECT count(artId) aant 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = :lidId
 and a.soort = 'voer'
 and a.actief = 0 
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_niet_in_gebruik($lidId) {
        return $this->run_query(
            <<<SQL
SELECT artId, naam 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = :lidId
 and a.soort = 'voer'
 and a.actief = 0
ORDER BY a.actief desc, a.naam  
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function activeer($artId): void {
        $this->run_query(
            <<<SQL
Update tblArtikel set actief = 1 WHERE artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function zoek_pil_op_voorraad($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
    SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
    FROM tblNuttig n
    GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE eu.lidId = :lidId
 and i.inkat-coalesce(n.vbrat,0) > 0
 and a.soort = 'pil'
GROUP BY a.artId, a.naam, a.stdat, e.eenheid
ORDER BY a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek($artId): array {
        return $this->first_row(
            <<<SQL
SELECT replace(a.stdat, '.00', '') stdrd, a.naam, e.eenheid, a.stdat
FROM tblArtikel a
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE a.artId = :artId
SQL
            ,
            [[':artId', $artId]],
            [0, 0, 0, 0]
        );
    }

    public function zoek_eenheid($artId) {
        return $this->run_query(
            <<<SQL
SELECT a.stdat, e.eenheid
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE a.artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function voorraad($artId): ?int {
        return $this->first_field(
            <<<SQL
SELECT sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblInkoop i
 left join (
    SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
    FROM tblInkoop i
     join tblNuttig n on (i.inkId = n.inkId)
    WHERE i.artId = :artId
    GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function periodes($lidId, $minjaar, $maxjaar, $artId) {
        return $this->run_query(
            <<<SQL
SELECT date_format(h.datum,'%Y%m') jrmnd, month(h.datum) mnd, date_format(h.datum,'%Y') jaar
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
WHERE h.skip = 0
 and eu.lidId = :lidId
 and date_format(h.datum,'%Y') >= :minjaar
 and date_format(h.datum,'%Y') <= :maxjaar
 and a.artId = :artId
GROUP BY date_format(h.datum,'%Y%m')
ORDER BY date_format(h.datum,'%Y%m') desc
SQL
            ,
            [
            [':lidId', $lidId, Type::INT],
            [':minjaar', $minjaar],
            [':maxjaar', $maxjaar],
            [':artId', $artId, Type::INT],
            ]
        );
    }

    // gebruikt dezelfde query als periodes(), alleen om het aantal rijen te tellen
    // @TODO @REFACTOR dat kun je ook aan de periodes-query zelf zien
    public function aantal_periodes($lidId, $minjaar, $maxjaar, $artId): int {
        $vw = $this->run_query(
            <<<SQL
SELECT jrmnd FROM (
SELECT date_format(h.datum,'%Y%m') jrmnd, month(h.datum) mnd, date_format(h.datum,'%Y') jaar
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
WHERE h.skip = 0
 and eu.lidId = :lidId
 and date_format(h.datum,'%Y') >= :minjaar
 and date_format(h.datum,'%Y') <= :maxjaar
 and a.artId = :artId
GROUP BY date_format(h.datum,'%Y%m')
ORDER BY date_format(h.datum,'%Y%m') desc
)
SQL
            ,
            [
            [':lidId', $lidId, Type::INT],
            [':minjaar', $minjaar],
            [':maxjaar', $maxjaar],
            [':artId', $artId, Type::INT],
            ]
        );
        if ($vw) {
            return $vw->num_rows;
        }
        return 0;
    }

    public function maandjaren($lidId, $minjaar, $maxjaar, $artId, $filter) {
        return $this->run_query(
            <<<SQL
SELECT month(h.datum) mnd, date_format(h.datum,'%Y') jaar 
FROM tblEenheiduser eu
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
 join tblHistorie h on (h.hisId = n.hisId)
WHERE h.skip = 0
 and eu.lidId = :lidId
 and date_format(h.datum,'%Y') >= :minjaar
 and date_format(h.datum,'%Y') <= :maxjaar
 and i.artId = :artId
 and $filter
GROUP BY month(h.datum), date_format(h.datum,'%Y')
ORDER BY date_format(h.datum,'%Y') desc, month(h.datum) desc 
SQL
            ,
            [
                [':lidId', $lidId, Type::INT],
                [':minjaar', $minjaar],
                [':maxjaar', $maxjaar],
                [':filter', $filter],
                [':artId', $artId, Type::INT],
            ]
        );
    }

    public function zoek_artid_op_voorraad($lidId) {
        return $this->run_query(
            <<<SQL
SELECT a.artId, a.naam
FROM tblEenheiduser eu
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
    SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
    FROM tblVoeding v
    GROUP BY v.inkId
 ) n on (i.inkId = n.inkId)
WHERE eu.lidId = :lidId
 and i.inkat-coalesce(n.vbrat,0) > 0
 and a.soort = 'voer'
GROUP BY a.artId, a.naam
ORDER BY a.naam
SQL
            ,
            [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_stdat($artId): ?int {
        return $this->first_field(
            <<<SQL
SELECT round(a.stdat) stdat
FROM tblArtikel a
WHERE a.artId = :artId
SQL
            ,
            [[':artId', $artId, Type::INT]]
        );
    }

    public function zoek_stdat_with_fraction($dbArtId) {
        $sql = <<<SQL
        SELECT stdat
        FROM tblArtikel
        WHERE artId = :dbArtId
SQL;
        $args = [[':dbArtId', $dbArtId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function kzlMedicijn_combi($lidId, $artId) {
        // Declaratie MEDICIJN  Met union all kan ik een niet actief/pil reden toch tonen en kan dit en andere inactieve artikelen niet worden gekozen !!
        return $this->run_query(<<<SQL
SELECT u.artId, u.naam
FROM (
    SELECT a.artId, a.naam
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (eu.enhuId = a.enhuId)
    Where eu.lidId = :lidId and a.soort = 'pil' and a.actief = 1
    Union all
    SELECT a.artId, a.naam
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblArtikel a on (eu.enhuId = a.enhuId)
    WHERE eu.lidId = :lidId and a.artId = :artId
    ) u
GROUP BY u.artId, u.naam
ORDER BY u.naam
SQL
        , [[':lidId', $lidId, Type::INT], [':artId', $artId, Type::INT]]);
    }

    public function medicijn_actief($lidId, $artId): array {
        return $this->first_row(
            <<<SQL
SELECT a.naam, a.actief 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = :lidId and a.artId = :artId
SQL
            ,
            [[':lidId', $lidId, Type::INT], [':artId', $artId, Type::INT]],
            [null, null]
        );
    }

    public function ophalen_waardes($insVoer) {
        $sql = <<<SQL
            SELECT a.stdat, a.enhuId, a.btw, a.relId, a.rubuId, p.naam
            FROM tblArtikel a
             left join tblRelatie r on (a.relId = r.relId)
             left join tblPartij p on (r.partId = p.partId)
            WHERE a.artId = :insVoer
SQL;
        $args = [[':insVoer', $insVoer]];
        return $this->run_query($sql, $args);
    }

    public function zoek_readernaam($lidId, $readernaam) {
        $sql = <<<SQL
        SELECT count(*) aant 
                    FROM tblArtikel a
                     join tblEenheiduser eu on (a.enhuId = eu.enhuId) 
                    WHERE lidId = :lidId and a.naamreader = :readernaam ;
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':readernaam', $readernaam]];
        return $this->first_field($sql, $args);
    }

    public function insert_tblArtikel($insNaam, $readernaam, $insRegnr, $insStdat, $insNhd, $insKg, $insBtw, $insRelatie, $inswdgn_v, $inswdgn_m, $insRubriek) {
        $sql = <<<SQL
                INSERT INTO tblArtikel SET soort = 'pil', naam = :insNaam, naamreader = :readernaam, :insRegnr, :insStdat, enhuId = :insNhd, perkg = :insKg, btw = :insBtw, relId= :insRelatie, wdgn_v = :inswdgn_v, wdgn_m = :inswdgn_m, rubuId= :insRubriek
SQL;
        $args = [
            [':insNaam', $insNaam], [':readernaam', $readernaam], [':insRegnr', $insRegnr], [':insStdat', $insStdat, Type::INT], [':insNhd', $insNhd], [':insKg', $insKg], [':insBtw', $insBtw], [':insRelatie', $insRelatie], [':inswdgn_v', $inswdgn_v], [':inswdgn_m', $inswdgn_m], [':insRubriek', $insRubriek]
        ];
        return $this->run_query($sql, $args);
    }

}
