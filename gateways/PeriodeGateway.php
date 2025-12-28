<?php

class PeriodeGateway extends Gateway {

    public function zoek_laatste_afsluitdm_geb($hokId) {
        return $this->first_field(<<<SQL
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = :hokId and doelId = 1 and dmafsluit is not null
SQL
        , [[':hokId', $hokId, self::INT]]
        );
    }

    public function zoek_laatste_afsluitdm_spn($hokId) {
        return $this->first_field(<<<SQL
SELECT max(dmafsluit) dmstop
FROM tblPeriode
WHERE hokId = :hokId and doelId = 2 and dmafsluit is not null
SQL
        , [[':hokId', $hokId, self::INT]]
        );
    }

    public function aantal_jaarmaanden($lidId, $artId, $doelId) {
        // $aantjaarmaanden zoekt het aantal jaarmaanden in tblPeriode o.b.v. lidId, al dan niet het voer en de doelgroep
        return $this->first_field(<<<SQL
SELECT count(date_format(p.dmafsluit,'%Y%m')) jrmnd
FROM tblPeriode p
 join tblHok h on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId) 
WHERE h.lidId = :lidId
 and i.artId = :artId
 and p.doelId = :doelId
SQL
        , [
            [':lidId', $lidId, self::INT],
            [':artId', $artId, self::INT],
            [':doelId', $doelId, self::INT],
        ]
        );
    }

    public function kzlJaarmaand($lidId, $fldVoer) {
        return $this->db->query("
SELECT date_format(p.dmafsluit,'%Y%m') jrmnd, month(p.dmafsluit) maand, date_format(p.dmafsluit,'%Y') jaar 
FROM tblPeriode p 
 left join tblVoeding v  on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 left join tblArtikel a on (a.artId = i.artId)
 left join tblEenheiduser eu on (a.enhuId = eu.enhuId)
 left join tblEenheid e on (e.eenhId = eu.eenhId)
WHERE eu.lidId = '".$this->db->real_escape_string($lidId)."' and ".db_null_filter('i.artId', $fldVoer)."
GROUP BY date_format(p.dmafsluit,'%Y%m')
"); 
}

public function maandjaren_hok_voer($lidId, $artId, $doelId, $resJrmnd, $resHok) {
    return $this->db->query("
SELECT month(p.dmafsluit) maand, date_format(p.dmafsluit,'%Y') jaar, date_format(p.dmafsluit,'%Y%m') jrmnd
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 left join tblVoeding v on (v.periId = p.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 left join tblArtikel a on (a.artId = i.artId) 
WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."'
 and p.doelId = $doelId
 and ".db_null_filter('i.artId', $artId)."
 and ".$resJrmnd."
 and ".$resHok."
GROUP BY month(p.dmafsluit), date_format(p.dmafsluit,'%Y')
ORDER BY jaar desc, month(p.dmafsluit) desc
");
    }

// subquery v binnen $allePeriodes_met_BeginEnEindDatum_inlc_Voer = Artikel met voeraantal per periode
private function allePeriodes_met_BeginEnEindDatum_inlc_Voer($lidId, $doelId) {
   return "
SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.artId, v.nutat
FROM tblPeriode p1
 join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
 join tblHok ho on (ho.hokId = p1.hokId) 
 left join ( 
         SELECT v.periId, i.artId, sum(v.nutat) nutat
        FROM tblVoeding v
         join tblPeriode p on (v.periId = p.periId)
         join tblHok ho on (ho.hokId = p.hokId)
         join tblInkoop i on (v.inkId = i.inkId)
        WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
        GROUP BY v.periId, i.artId
 ) v on (p2.periId = v.periId)
WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p1.doelId = $doelId
GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
";
}

public function begin_eind_periode($lidId, $doelId, $artId, $jrmnd) {
    // subquery p1 binnen $begin_eind_periode = Eerste periode met_fictieve startdatum
    // subquery v binnen $begin_eind_periode = Artikel met voeraantal per periode
    $allePeriodes_met_BeginEnEindDatum_inlc_Voer = $this->allePeriodes_met_BeginEnEindDatum_inlc_Voer($lidId, $doelId);
    return $this->db->query("
SELECT p.periId, ho.hokId, ho.hoknr, date_format(p.dmeind,'%Y%m') jrmnd, p.dmbegin, p.dmeind, p.artId, p.nutat
FROM (
    SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.artId, v.nutat 
    FROM (
         SELECT p.hokId, p.doelId, l.dmcreate dmbegin, min(p.dmafsluit) dmeind
        FROM tblPeriode p
         join tblHok ho on (p.hokId = ho.hokId)
         join tblLeden l on (ho.lidId = l.lidId)
        WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
        GROUP BY p.hokId, p.doelId
     ) p1
     join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
     left join (
         SELECT v.periId, i.artId, sum(v.nutat) nutat
        FROM tblVoeding v
         join tblPeriode p on (v.periId = p.periId)
         join tblHok ho on (ho.hokId = p.hokId)
         join tblInkoop i on (v.inkId = i.inkId)
        WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
        GROUP BY v.periId, i.artId
     ) v on (p.periId = v.periId)

    union

    $allePeriodes_met_BeginEnEindDatum_inlc_Voer
 ) p
 join tblHok ho on (ho.hokId = p.hokId)
 left join tblArtikel i on (i.artId = p.artId)
WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."'
 and p.doelId = $doelId
 and ".db_null_filter('i.artId', $artId)."
 and date_format(p.dmeind,'%Y%m') = '".$this->db->real_escape_string($jrmnd)."'
    ");
}

public function periode_totalen($lidId, $hokId, $fldVoer, $doelId, $filterDoel, $resHok, $dmstart, $dmbegin, $dmeind, $jrmnd) {
    return $this->db->query( "
SELECT p.periId, ho.hokId, ho.hoknr, p.dmbegin, date_format(p.dmbegin,'%d-%m-%Y') begindm, min(his_in.datum) dmschaap1, date_format(min(his_in.datum),'%d-%m-%Y') schaap1dm,
 p.dmeind, date_format(p.dmeind,'%d-%m-%Y') einddm, max(coalesce(his_uit.datum,p.dmeind)) dmschaapend, 
 date_format(max(coalesce(his_uit.datum,p.dmeind)),'%d-%m-%Y') schaapenddm,
 p.nutat, count(distinct st.schaapId) schpn, 
 sum(datediff(coalesce(his_uit.datum,p.dmeind),his_in.datum)) dagen,
 round(sum(datediff(coalesce(his_uit.datum,p.dmeind),his_in.datum))/count(st.schaapId),2) gemdgn,
 count(v.voedId) voedId
FROM tblHok ho
 join (
     SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.nutat 
     FROM (
         SELECT p.hokId, p.doelId, '".$this->db->real_escape_string($dmstart)."' dmbegin, min(p.dmafsluit) dmeind
        FROM tblPeriode p
         join tblHok ho on (p.hokId = ho.hokId)
        WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
        GROUP BY p.hokId, p.doelId
     ) p1
     join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
     left join (
         SELECT v.periId, i.artId, sum(v.nutat) nutat
         FROM tblVoeding v
          join tblPeriode p on (v.periId = p.periId)
          join tblHok ho on (ho.hokId = p.hokId)
          join tblInkoop i on (v.inkId = i.inkId)
         WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
         GROUP BY v.periId, i.artId
     ) v on (p.periId = v.periId)

    union

    SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.nutat 
    FROM tblPeriode p1
     join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
     join tblHok ho on (ho.hokId = p1.hokId)
     left join (
         SELECT v.periId, i.artId, sum(v.nutat) nutat
         FROM tblVoeding v
          join tblPeriode p on (v.periId = p.periId)
          join tblHok ho on (ho.hokId = p.hokId)
          join tblInkoop i on (v.inkId = i.inkId)
         WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
         GROUP BY v.periId, i.artId
     ) v on (p2.periId = v.periId)
    WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p1.doelId = $doelId
    GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
 ) p  on (p.hokId = ho.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)

 left join tblBezet b on (b.hokId = p.hokId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = '".$this->db->real_escape_string($lidId)."'
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
 and b.hokId = '".$this->db->real_escape_string($hokId)."'
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT hisId, '".$this->db->real_escape_string($dmbegin)."' datum, h.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0 and datum <= '".$this->db->real_escape_string($dmbegin)."' and u.lidId = '".$this->db->real_escape_string($lidId)."'
 
 union

    SELECT hisId, datum, h.stalId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0 and datum > '".$this->db->real_escape_string($dmbegin)."' and u.lidId = '".$this->db->real_escape_string($lidId)."'

 ) his_in on (b.hisId = his_in.hisId)
 left join (
    SELECT hisId, '".$this->db->real_escape_string($dmeind)."' datum
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0 and datum >= '".$this->db->real_escape_string($dmeind)."' and u.lidId = '".$this->db->real_escape_string($lidId)."'
 
 union

    SELECT hisId, datum
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE h.skip = 0
 and datum < '".$this->db->real_escape_string($dmeind)."'
 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 ) his_uit on (uit.hist = his_uit.hisId)
 left join tblStal st on (st.stalId = his_in.stalId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0 and u.lidId = '".$this->db->real_escape_string($lidId)."'
 ) prn on (prn.schaapId = st.schaapId)
WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."'
 and ho.hokId = '".$this->db->real_escape_string($hokId)."'
 and p.doelId = $doelId
 and ".db_null_filter('i.artId', $fldVoer)." 

 and date_format(p.dmeind,'%Y%m') = $jrmnd
 and his_in.datum < p.dmeind
 and coalesce(his_uit.datum,CURDATE()) > p.dmbegin ".$this->db->real_escape_string($filterDoel)."
 and ".$this->db->real_escape_string($resHok)."
 
GROUP BY p.periId, ho.hokId, ho.hoknr, p.dmbegin, p.dmeind, p.nutat
ORDER BY ho.hokId, p.dmeind
");
  }

public function periode_totalen_met_voer_zonder_schapen($lidId, $hokId, $artId, $doelId, $resHok, $dmstart, $jrmnd) {
    return $this->db->query("
SELECT p.periId, ho.hokId, ho.hoknr, p.dmbegin, date_format(p.dmbegin,'%d-%m-%Y') begindm, NULL dmschaap1, NULL schaap1dm,
 p.dmeind, date_format(p.dmeind,'%d-%m-%Y') einddm, NULL dmschaapend, NULL schaapenddm,
 p.nutat, NULL schpn, NULL dagen, NULL gemdgn, count(v.voedId) voedId
FROM tblHok ho
 join (
     SELECT p.periId, p1.hokId, p1.doelId, p1.dmbegin, p1.dmeind, v.nutat 
     FROM (
         SELECT p.hokId, p.doelId, '".$this->db->real_escape_string($dmstart)."' dmbegin, min(p.dmafsluit) dmeind
        FROM tblPeriode p
         join tblHok ho on (p.hokId = ho.hokId)
        WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
        GROUP BY p.hokId, p.doelId
     ) p1
     join tblPeriode p on (p1.hokId = p.hokId and p1.doelId = p.doelId and p1.dmeind = p.dmafsluit)
     left join (
         SELECT v.periId, i.artId, sum(v.nutat) nutat
         FROM tblVoeding v
          join tblPeriode p on (v.periId = p.periId)
          join tblHok ho on (ho.hokId = p.hokId)
          join tblInkoop i on (v.inkId = i.inkId)
         WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
         GROUP BY v.periId, i.artId
     ) v on (p.periId = v.periId)

    union

    SELECT p2.periId, p2.hokId, p1.doelId, max(p1.dmafsluit) dmbegin, p2.dmafsluit dmeind, v.nutat 
    FROM tblPeriode p1
     join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
     join tblHok ho on (ho.hokId = p1.hokId)
     left join (
         SELECT v.periId, i.artId, sum(v.nutat) nutat
         FROM tblVoeding v
          join tblPeriode p on (v.periId = p.periId)
          join tblHok ho on (ho.hokId = p.hokId)
          join tblInkoop i on (v.inkId = i.inkId)
         WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p.doelId = $doelId
         GROUP BY v.periId, i.artId
     ) v on (p2.periId = v.periId)
    WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."' and p1.doelId = $doelId
    GROUP BY p2.periId, p2.hokId, p1.doelId, p2.dmafsluit, v.nutat
 ) p  on (p.hokId = ho.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
 
WHERE ho.lidId = '".$this->db->real_escape_string($lidId)."'
 and ho.hokId = '".$this->db->real_escape_string($hokId)."'
 and p.doelId = $doelId
 and ".db_null_filter('i.artId', $artId)." 
 and date_format(p.dmeind,'%Y%m') = $jrmnd
 and '".$this->db->real_escape_string($resHok)."'
 
GROUP BY p.periId, ho.hokId, ho.hoknr, p.dmbegin, p.dmeind, p.nutat
ORDER BY ho.hokId, p.dmeind
");
}

public function findByHokAndDoel($hokId, $doelId, $datum) {
    return $this->first_field(
        <<<SQL
SELECT periId
FROM tblPeriode
WHERE hokId = :hokId
 and doelId= :doelId
 and dmafsluit = :datum
SQL
    , [
        [':hokId', $hokId, self::INT],
        [':doelId', $doelId, self::INT],
        [':datum', $datum],
    ]);
}

public function zoek_doelid($periId) {
    return $this->first_record(
        <<<SQL
SELECT p.hokId, ho.hoknr, p.doelId, d.doel, p.dmafsluit, date_format(p.dmafsluit,'%d-%m-%Y') afsluitdm
FROM tblPeriode p
 join tblHok ho on (p.hokId = ho.hokId)
 join tblDoel d on (p.doelId = d.doelId)
WHERE periId = :periId
SQL
    , [[':periId', $periId, self::INT]]
        , [
            'hokId' => null,
            'hoknr' => null,
            'doelId' => null,
            'doel' => null,
            'dmafsluit' => null,
            'afsluitdm' => null,
        ]
    );
}

public function zoek_start_periode($hokId, $doelId, $dmafsl) {
    return $this->first_row(
        <<<SQL
SELECT max(dmafsluit) dmStart, date_format(max(dmafsluit),'%d-%m-%Y') Startdm
FROM tblPeriode
WHERE hokId = :hokId
 and doelId = :doelId
 and dmafsluit < :dmafsluit
SQL
    , [
        [':hokId', $hokId, self::INT],
        [':doelId', $doelId, self::INT],
        [':dmafsluit', $dmafsl]
    ]
    , [null, null]
    );
}

public function insert($hokId, $doelId, $dmafsluit) {
    $this->run_query(<<<SQL
INSERT INTO tblPeriode set hokId = :hokId, doelId= :doelId, dmafsluit = :dmafsluit
SQL
    , [
        [':hokId', $hokId, self::INT],
        [':doelId', $doelId, self::INT],
        [':dmafsluit', $dmafsluit, self::DATE],
    ]);
    return $this->db->insert_id;
}

}
