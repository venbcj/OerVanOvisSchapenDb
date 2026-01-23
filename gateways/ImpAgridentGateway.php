<?php

class ImpAgridentGateway extends Gateway {

    public function zoek_aantal_uit_reader($Id) {
        return $this->first_row(
            <<<SQL
SELECT toedat, toedat_upd
FROM impAgrident
WHERE Id = :id
SQL
        , [[':id', $Id]]
        );
    }

    public function update($id, $aantal) {
        $this->run_query(
            <<<SQL
UPDATE impAgrident set toedat_upd = :aantal WHERE Id = :id
SQL
        , [[':id', $id, Type::INT], [':aantal', $aantal]]
        );
    }

    public function set_verwerkt($recId) {
        $this->run_query(
            <<<SQL
UPDATE impAgrident set verwerkt = 1 WHERE Id = :recId
SQL
        , [[':recId', $recId, Type::INT]]
        );
    }

    public function update_hok($Id, $hokId) {
        $this->run_query(<<<SQL
UPDATE impAgrident SET hokId = :hokId WHERE Id = :Id
SQL
        , [[':Id', $Id, Type::INT], [':hokId', $hokId, Type::INT]]
        );
    }

    public function getInsAanvoerFrom() {
        return <<<SQL
impAgrident rd
 left join tblSchaap s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId and h.skip = 0
    GROUP BY s.schaapId, s.levensnummer
 ) st on (rd.levensnummer = st.levensnummer)
 left join (
    SELECT max(h.datum) datum, s.schaapId, s.levensnummer
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId and h.skip = 0
    GROUP BY s.schaapId, s.levensnummer
 ) lstDate on (rd.levensnummer = lstDate.levensnummer)
 left join (
     SELECT h.actId, h.datum, st.schaapId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblActie a on (a.actId = h.actId)
     WHERE u.lidId = :lidId and a.af = 1 and h.skip = 0
 ) afv on (afv.datum = lstDate.datum and afv.schaapId = lstDate.schaapId)
 left join tblPartij p on (rd.ubn = p.ubn and p.lidId = :lidId)
 left join (
    SELECT ru.lidId, r.rasId
    FROM tblRas r
     join tblRasuser ru on (r.rasId = ru.rasId)
    WHERE r.actief = 1 and ru.actief = 1
 ) r on (rd.rasId = r.rasId and r.lidId = rd.lidId)
 left join (
    SELECT ho.hokId
    FROM tblHok ho
    WHERE ho.lidId = :lidId
 ) ho on (rd.hokId = ho.hokId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
    WHERE rd.actId = 2 or rd.actId = 3
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
SQL;
    }

    public function getInsAanvoerWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and (rd.actId = 2 or rd.actId = 3) and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsAdoptieFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join tblSchaap mdr on (rd.moeder = mdr.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId and isnull(st.rel_best))
 join tblUbn u on (u.ubnId = st.ubnId and u.lidId = :lidId)
 left join (
    SELECT h.hisId, a.actie, a.af, h.datum
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 14 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
SQL;
    }

    public function getInsAdoptieWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 15 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsAfvoerFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 left join (
    SELECT st.schaapId, max(st.stalId) stalId
    FROM tblStal st
     join tblUbn u on (st.ubnId = u.ubnId)
    WHERE u.lidId = :lidId
    GROUP BY st.schaapId
 ) mst on (mst.schaapId = s.schaapId)
 left join (
    SELECT st.stalId, h.hisId, a.actie, a.af
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1 and h.skip = 0
 ) haf on (mst.stalId = haf.stalId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 4 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT levensnummer, max(datum) datum 
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE u.lidId = :lidId and h.actId = 2 and h.skip = 0
    GROUP BY levensnummer
 ) ak on (ak.levensnummer = rd.levensnummer)
 left join (
    SELECT schaapId, max(datum) datummax_afv, max(datum_kg) datummax_kg
    FROM (
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null
        Union
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2 and h.skip = 0 and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
         left join 
         (
            SELECT s.schaapId, h.actId, h.datum 
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join tblHistorie h on (h.stalId = st.stalId) 
            WHERE actId = 2 and h.skip = 0 and u.lidId = :lidId
         ) koop on (s.schaapId = koop.schaapId and koop.datum <= h.datum)
        WHERE a.actId = 3 and h.skip = 0 and (isnull(koop.datum) or koop.datum < h.datum) and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4 and h.skip = 0
        Union
        SELECT  mdr.schaapId, min(h.datum) datum, NULL datum_kg, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and u.lidId = :lidId
        GROUP BY mdr.schaapId
        Union
        SELECT mdr.schaapId, max(h.datum) datum, NULL datum_kg, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and u.lidId = :lidId
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))
        Union
        SELECT s.schaapId, p.dmafsluit datum, NULL datum_kg, 'Gevoerd' actie, NULL , h.skip
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and u.lidId = :lidId 
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
 left join (
    SELECT p.lidId, p.ubn
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.actief = 1 and r.relatie = 'deb' and r.actief = 1
 ) r on(r.ubn = rd.ubn and r.lidId = rd.lidId)
 left join (
    SELECT max(b.bezId) bezId, s.levensnummer
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE u.lidId = :lidId and h.skip = 0
    GROUP BY s.levensnummer
 ) b on (rd.levensnummer = b.levensnummer)
 left join tblRedenuser red on (rd.reden = red.redId and red.lidId = :lidId)
SQL;
    }

    public function getInsAfvoerWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 12 and isnull(rd.ubnId) and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsDekkenFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT s.schaapId, s.levensnummer
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
     WHERE lidId = :lidId
     ) mdr on (mdr.levensnummer = rd.moeder)
 left join (
     SELECT s.schaapId, s.levensnummer vader
     FROM tblSchaap s
     ) vdr on (vdr.schaapId = rd.vdrId)
SQL;
    }

    public function getInsDekkenWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 18 and isnull(verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsDrachtFrom() {
        return <<<SQL
impAgrident rd 
 left join (
     SELECT s.schaapId, s.levensnummer
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
     WHERE lidId = :lidId
     ) mdr on (mdr.levensnummer = rd.moeder)
SQL
        ;
    }

    public function getInsDrachtWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 19 and isnull(verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsGeboortesFrom() {
        return <<<SQL
impAgrident rd
 left join (
 SELECT levensnummer 
 FROM tblSchaap s
  join tblStal st on (st.schaapId = s.schaapId)
 WHERE lidId = :lidId
 and isnull(st.rel_best)
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT st.stalId, s.levensnummer, af.datum
    FROM tblSchaap s
     join (
          SELECT max(stalId) stalId, schaapId
          FROM tblStal st
           join tblUbn u on (st.ubnId = u.ubnId)
          WHERE u.lidId = :lidId
          GROUP BY schaapId
      ) st on (s.schaapId = st.schaapId)
     join (
        SELECT schaapId
        FROM tblStal st
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3
 and h.skip = 0
     ) prnt on (prnt.schaapId = s.schaapId)
     left join (
       SELECT st.stalId, datum, hisId
       FROM tblStal st
        join tblUbn u on (u.ubnId = st.ubnId)
        join tblHistorie h on (st.stalId = h.stalId)
        join tblActie a on (a.actId = h.actId)
       WHERE a.af = 1
 and h.actId != 10
 and h.skip = 0
 and u.lidId = :lidId
     ) af on (af.stalId = st.stalId)
    WHERE (isnull(af.datum) or af.datum > date_add(curdate(), interval -2 month) )
    GROUP BY s.schaapId, s.levensnummer, af.datum
 ) mdr on (rd.moeder = mdr.levensnummer)
 left join (
    SELECT ru.rasId
    FROM tblRas r
     join tblRasuser ru on (r.rasId = ru.rasId)
    WHERE ru.lidId = :lidId
 and r.actief = 1
 and ru.actief = 1
 ) r on (rd.rasId = r.rasId)
 left join (
    SELECT hokId
    FROM tblHok
    WHERE lidId = :lidId
 and actief = 1
 ) hb on (rd.hokId = hb.hokId)
 left join (
    SELECT r.redId
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = :lidId
 and r.actief = 1
 and ru.uitval = 1
 ) red on (rd.reden = red.redId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId
 and rd.levensnummer = dup.levensnummer
 and rd.Id <> dup.Id
 and rd.actId = 1
 and dup.actId = 1
 and isnull(dup.verwerkt))
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
SQL;
    }

    public function getInsGeboortesWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 1 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsGrWijzigingUbnFrom() {
        return <<<SQL
impAgrident rd
 join tblUbn u_best on (u_best.ubnId = rd.ubnId)
 left join (
     SELECT u.ubnId, p.ubn, p.naam, r.relId
     FROM tblUbn u
      left join tblPartij p on (p.ubn = u.ubn)
      left join tblRelatie r on (p.partId = r.partId)
     WHERE u.lidId = :lidId
 and p.lidId = :lidId
 and (r.relatie = 'deb' or isnull(r.relatie))
  ) rel_best on (rd.ubnId = rel_best.ubnId)
 left join (
    SELECT s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 left join (
     SELECT max(stalId) stalIdmax, schaapId
     FROM tblStal
    WHERE lidId = :lidId
     GROUP BY schaapId
 ) st_max on (st_max.schaapId = s.schaapId)
 left join tblStal st on (st.stalId = st_max.stalIdmax)
 left join tblUbn u_herk on (u_herk.ubnId = st.ubnId)
 left join (
 SELECT p.ubn, p.naam, r.relId
     FROM tblPartij p
      left join tblRelatie r on (p.partId = r.partId)
     WHERE p.lidId = :lidId
 and (r.relatie = 'cred' or isnull(r.relatie))
 ) rel_herk on (u_herk.ubn = rel_herk.ubn)
 left join (
    SELECT st.schaapId, h.hisId, a.actie, a.af
    FROM tblStal st
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = :lidId
 and a.af = 1
 and h.skip = 0
 ) haf on (s.schaapId = haf.schaapId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT ho.hokId
    FROM tblHok ho
    WHERE ho.lidId = :lidId
 ) ho on (rd.hokId = ho.hokId)
 left join (
    SELECT schaapId, max(datum) datummax_afv, max(datum_kg) datummax_kg
    FROM (
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1
 and h.skip = 0
 and s.levensnummer is not null
        Union
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2
 and h.skip = 0
 and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14)
 and h.skip = 0
 and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
         left join 
         (
            SELECT s.schaapId, h.actId, h.datum 
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join tblHistorie h on (h.stalId = st.stalId) 
            WHERE actId = 2
 and h.skip = 0
 and u.lidId = :lidId
         ) koop on (s.schaapId = koop.schaapId
 and koop.datum <= h.datum)
        WHERE a.actId = 3
 and h.skip = 0
 and (isnull(koop.datum) or koop.datum < h.datum)
 and u.lidId = :lidId
        Union
        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4
 and h.skip = 0
        Union
        SELECT  mdr.schaapId, min(h.datum) datum, NULL datum_kg, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId
        Union
        SELECT mdr.schaapId, max(h.datum) datum, NULL datum_kg, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))
        Union
        SELECT s.schaapId, p.dmafsluit datum, NULL datum_kg, 'Gevoerd' actie, NULL , h.skip
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0
 and u.lidId = :lidId 
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
SQL
        ;
    }

    public function getInsGrWijzigingUbnWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 12 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsHalsnummersFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId
 and isnull(st.rel_best))
 join tblUbn u on (u.ubnId = st.ubnId and u.lidId = :lidId)
 left join (
    SELECT h.hisId, a.actie, a.af, h.datum
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 14
 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
SQL;
    }

    public function getInsHalsnummersWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 1717 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsLambarFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT h.hisId, h.datum, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join tblHok kh on (rd.hokId = kh.hokId
 and kh.lidId = rd.lidId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId
 and rd.levensnummer = dup.levensnummer
 and rd.actId = dup.actId
 and rd.Id <> dup.Id)
    WHERE rd.actId = 16
 and rd.lidId = :lidId
 and ISNULL(rd.verwerkt)
 and ISNULL(dup.verwerkt)
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join (
    SELECT m.levensnummer, max(m.datum) datum
    FROM (
        SELECT s.levensnummer, h.datum
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE u.lidId = :lidId
 and s.levensnummer is not null
 and h.skip = 0
    ) m
    GROUP BY m.levensnummer 
 ) lstday on (lstday.levensnummer = rd.levensnummer )
SQL;
    }

    public function getInsLambarWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and actId = 16 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsMedicijnAgridentFrom() {
        return <<<SQL
impAgrident rd 
left join tblSchaap s on (rd.levensnummer = s.levensnummer)
left join tblStal st on (s.schaapId = st.schaapId)
left join tblUbn u on (u.ubnId = st.ubnId and u.lidId = rd.lidId)
left join 
(
    SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, a.actief, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblInkoop i on (i.enhuId = eu.enhuId)
     join tblArtikel a on (i.artId = a.artId)
     left join (
        SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
        FROM tblNuttig n
         join tblInkoop i on (n.inkId = i.inkId)
         join tblArtikel a on (a.artId = i.artId)
         join tblEenheiduser eu on (a.enhuId = eu.enhuId)
        WHERE eu.lidId = :lidId
        GROUP BY n.inkId
     ) n on (i.inkId = n.inkId)
    WHERE eu.lidId = :lidId and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
    GROUP BY a.artId, a.naam, a.stdat, e.eenheid
) i on (rd.artId = i.artId)
left join tblRedenuser ru on (rd.reden = ru.reduId)
SQL;
    }

    public function getInsMedicijnBiocontrolFrom() {
        return <<<SQL
impReader rd 
left join tblSchaap s on (rd.levnr_pil = s.levensnummer)
left join tblStal st on (s.schaapId = st.schaapId)
left join tblUbn u on (u.ubnId = st.ubnId and u.lidId = rd.lidId)
left join (
    SELECT c.scan, a.artId, c.stdat, a.actief, ru.pil, ru.reduId, r.reden
    FROM tblCombireden c 
     join tblArtikel a on (a.artId = c.artId)
     join tblRedenuser  ru on (ru.reduId = c.reduId)
     join tblReden r on (ru.redId = r.redId)
    WHERE ru.lidId = :lidId
 ) cr on (cr.scan = rd.reden_pil)
left join 
(
    SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
    FROM tblEenheid e
     join tblEenheiduser eu on (e.eenhId = eu.eenhId)
     join tblInkoop i on (i.enhuId = eu.enhuId)
     join tblArtikel a on (i.artId = a.artId)
     left join (
        SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
        FROM tblNuttig n
         join tblHistorie h on (n.hisId = h.hisId)
         join tblStal st on (h.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
        WHERE u.lidId = :lidId and h.skip = 0
        GROUP BY n.inkId
     ) n on (i.inkId = n.inkId)
    WHERE eu.lidId = :lidId and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
    GROUP BY a.artId, a.naam, a.stdat, e.eenheid
) i on (cr.artId = i.artId)
SQL;
    }

    public function getInsMedicijnAgridentWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 8 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsMedicijnBiocontrolWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.teller_pil is not null and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsOmnummerenFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join tblSchaap new on (rd.nieuw_nummer = new.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId
 left join tblUbn u on (u.ubnId = st.ubnId)
 and u.lidId = :lidId
 and isnull(st.rel_best))
 left join (
    SELECT h.hisId, a.actie, a.af, h.datum
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 14
 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
SQL;
    }

    public function getInsOmnummerenWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 17 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsOverplaatsAgridentFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT levensnummer, Id 
    FROM impAgrident 
    WHERE lidId = :lidId
 and actId = 4
 and isnull(verwerkt)
 ) rs on (rd.levensnummer = rs.levensnummer)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId
 left join tblUbn u on (u.ubnId = st.ubnId)
 and u.lidId = :lidId
 and isnull(st.rel_best))
 left join (
    SELECT h.hisId, a.actie, a.af, h.datum
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 14
 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
 left join (
    SELECT hokId
    FROM tblHok
    WHERE lidId = :lidId
 and actief = 1
 ) hb on (rd.hokId = hb.hokId)
SQL;
    }

    public function getInsOverplaatsAgridentWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 5 and isnull(rd.verwerkt) and isnull(rs.Id) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsOverplaatsBiocontrolFrom() {
        return <<<SQL
impReader rd
 left join (
    SELECT levnr_sp, readId 
    FROM impReader 
    WHERE lidId = :lidId
 and teller_sp is not null
 and isnull(verwerkt)
 ) rs on (rd.levnr_ovpl = rs.levnr_sp)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_ovpl = s.levensnummer)
 left join tblStal st on (st.schaapId = s.schaapId
 and isnull(st.rel_best))
 left join tblUbn u on (u.ubnId = st.ubnId and u.lidId = :lidId)
 left join (
    SELECT h.hisId, a.actie, a.af, h.datum
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) spn on (spn.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 14
 and h.skip = 0
 ) hu on (hu.schaapId = s.schaapId)
 left join (
    SELECT scan
    FROM tblHok
    WHERE lidId = :lidId
 and actief = 1
 ) hb on (rd.hok_ovpl = hb.scan)
SQL;
    }

    public function getInsOverplaatsBiocontrolWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.teller_ovpl is not null and isnull(rd.verwerkt) and isnull(rs.readId) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsSpenenAgridentFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT lidId, levensnummer, hokId
    FROM impAgrident
    WHERE actId = 5
 and lidId = :lidId
 and isnull(verwerkt) 
 ) ro on (rd.levensnummer = ro.levensnummer)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join tblHok kh on (coalesce(rd.hokId,ro.hokId) = kh.hokId
 and kh.lidId = :lidId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId
 and rd.levensnummer = dup.levensnummer
 and rd.actId = dup.actId
 and rd.Id <> dup.Id)
    WHERE rd.actId = 4
 and rd.lidId = :lidId
 and ISNULL(rd.verwerkt)
 and ISNULL(dup.verwerkt)
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join (
    SELECT m.levensnummer, max(m.datum) datum
    FROM (
        SELECT s.levensnummer, h.datum
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE u.lidId = :lidId
 and s.levensnummer is not null
 and h.skip = 0
    ) m
    GROUP BY m.levensnummer 
 ) lstday on (lstday.levensnummer = rd.levensnummer )
SQL;
    }

    public function getInsSpenenAgridentWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and actId = 4 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsSpenenBiocontrolFrom() {
        return <<<SQL
impReader rd
 left join (
    SELECT r.lidId, r.levnr_ovpl, r.hok_ovpl
    FROM impReader r
    WHERE r.teller_ovpl is not null
 and isnull(r.verwerkt) 
 ) ro on (rd.lidId = ro.lidId
 and rd.levnr_sp = ro.levnr_ovpl)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_sp = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
     WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join tblHok kh on (coalesce(rd.hok_sp,ro.hok_ovpl) = kh.scan
 and kh.lidId = :lidId)
 left join (
     SELECT rd.readId, count(dup.readId) dubbelen
    FROM impReader rd
     join impReader dup on (rd.lidId = dup.lidId
 and rd.levnr_sp = dup.levnr_sp
 and rd.readId <> dup.readId)
    WHERE rd.teller_sp is not null
 and rd.lidId = :lidId
 and ISNULL(rd.verwerkt)
 and ISNULL(dup.verwerkt)
    GROUP BY rd.readId
 ) dup on (rd.readId = dup.readId)
 left join (
    SELECT m.levensnummer, max(m.datum) datum
    FROM (
        SELECT s.levensnummer, h.datum
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE u.lidId = :lidId
 and s.levensnummer is not null
 and h.skip = 0
    ) m
    GROUP BY m.levensnummer 
 ) lstday on (lstday.levensnummer = rd.levnr_sp )
SQL;
    }

    public function getInsSpenenBiocontrolWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.teller_sp is not null and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsStallijstscanFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT max(h.hisId) hisId, st.stalId, u.ubn, s.schaapId, s.levensnummer, s.geslacht, s.rasId, u.lidId
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId
 and h.skip = 0
    GROUP BY st.stalId, s.schaapId, s.levensnummer, s.geslacht, s.rasId, u.lidId
 ) stal on (rd.levensnummer = stal.levensnummer)
 left join tblRas r on (stal.rasId = r.rasId)
 left join (
     SELECT schaapId ouder
     FROM tblStal st
      join tblHistorie h on (h.stalId = st.stalId)
     WHERE actId = 3
 and h.skip = 0
 ) ouder on (ouder.ouder = stal.schaapId)
 left join (
     SELECT st.stalId, actie
    FROM tblActie a
     join tblHistorie h on (a.actId = h.actId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE a.af = 1
 and u.lidId = :lidId
 and h.skip = 0
 ) af on (af.stalId = stal.stalId)
 left join (
         SELECT lsthk.hisId actueel_hisId_hok, lsthk.stalId
        FROM ( 
                 SELECT max(h.hisId) hisId, h.stalId
                 FROM tblBezet b
                  join tblHistorie h on (h.hisId = b.hisId)
                  join tblStal st on (h.stalId = st.stalId)
                  join tblUbn u on (u.ubnId = st.ubnId)
                 WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and h.skip = 0
                 GROUP BY stalId
             ) lsthk
              left join 
             (
                SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
                 join tblUbn u on (u.ubnId = st.ubnId)
                WHERE u.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
                GROUP BY b.bezId, st.schaapId, h1.hisId
             ) uit on (lsthk.hisId = uit.hisv)
             left join tblBezet b on (b.hisId = lsthk.hisId)
                WHERE lsthk.hisId is not null
 and isnull(hist)
    ) act_b on (act_b.stalId = stal.stalId)
 left join tblBezet b on (act_b.actueel_hisId_hok = b.hisId)
 left join tblHok hk on (hk.hokId = b.hokId)
 left join (
    SELECT date_format(h.datum,'%d-%m-%Y') gebdm, schaapId
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
    WHERE h.actId = 1
 and h.skip = 0
 ) hg on (stal.schaapId = hg.schaapId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId
 and rd.levensnummer = dup.levensnummer
 and rd.Id <> dup.Id
 and rd.actId = dup.actId
 and isnull(dup.verwerkt))
    WHERE rd.actId = 22
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
SQL;
    }

    public function getInsStallijstscanWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 22 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function aantal_niet_op_stallijst($lidId) {
        return $this->first_field(
            <<<SQL
SELECT count(Id) aant
FROM impAgrident rd
 left join (
     SELECT s.schaapId, levensnummer
     FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId
 ) s on (s.levensnummer = rd.levensnummer)
WHERE rd.lidId = :lidId and rd.actId = 22 and isnull(rd.verwerkt) and isnull(s.schaapId)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function getInsStallijstscanNieuweklantFrom() {
        return <<<SQL
impAgrident rd
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
    WHERE rd.actId = 21
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join tblHok ho on (rd.hokId = ho.hokId)
SQL;
    }

    public function getInsStallijstscanNieuweklantWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 21 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsTvUitscharenFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT ho.hokId
    FROM tblHok ho
    WHERE ho.lidId = :lidId
 ) ho on (rd.hokId = ho.hokId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
    WHERE rd.actId = 11
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
SQL;
    }

    public function getInsTvUitscharenWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 11 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsUitscharenFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT st.stalId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
     WHERE u.lidId = :lidId
     GROUP BY st.stalId, s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 join (
     SELECT max(stalId) stalId, schaapId
     FROM tblStal
     WHERE lidId = :lidId
     GROUP BY schaapId
 ) st on (s.stalId = st.stalId)
 left join (
    SELECT st.stalId, st.schaapId, h.hisId, a.actie, a.af
    FROM tblStal st
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = :lidId
 and a.af = 1
 and h.skip = 0
 ) haf on (s.stalId = haf.stalId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT levensnummer, max(datum) datum 
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE u.lidId = :lidId
 and h.actId = 2
 and h.skip = 0
    GROUP BY levensnummer
 ) ak on (ak.levensnummer = rd.levensnummer)
 left join (
    SELECT schaapId, max(datum) datummax_afv
    FROM (
        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1
 and h.skip = 0
 and s.levensnummer is not null

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2
 and h.skip = 0
 and u.lidId = :lidId

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14)
 and h.skip = 0
 and u.lidId = :lidId

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
         left join 
         (
            SELECT s.schaapId, h.actId, h.datum 
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblUbn u on (u.ubnId = st.ubnId)
             join tblHistorie h on (h.stalId = st.stalId) 
            WHERE actId = 2
 and h.skip = 0
 and u.lidId = :lidId
         ) koop on (s.schaapId = koop.schaapId
 and koop.datum <= h.datum)
        WHERE a.actId = 3
 and h.skip = 0
 and (isnull(koop.datum) or koop.datum < h.datum)
 and u.lidId = :lidId

        Union

        SELECT s.schaapId, h.datum, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4
 and h.skip = 0

        Union

        SELECT  mdr.schaapId, min(h.datum) datum, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum, 'Gevoerd' actie, NULL , h.skip
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0
 and u.lidId = :lidId 
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
 left join (
    SELECT p.lidId, p.ubn
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.actief = 1
 and r.relatie = 'deb'
 and r.actief = 1
 ) r on(r.ubn = rd.ubn
 and r.lidId = rd.lidId)
 left join (
    SELECT max(b.bezId) bezId, s.levensnummer
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE u.lidId = :lidId
 and h.skip = 0
    GROUP BY s.levensnummer
 ) b on (rd.levensnummer = b.levensnummer)
SQL;
    }

    public function getInsUitscharenWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 10 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsUitvalAgridentFrom() {
        return <<<SQL
impAgrident rd
 left join (
    SELECT r.reduId, r.lidId 
    FROM tblRedenuser r
    WHERE r.lidId = :lidId
 and r.uitval = 1
 ) ru on (ru.reduId = rd.reden
 and ru.lidId = rd.lidId)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT sd.schaapId, max(sd.datum) datummax 
    FROM (
        SELECT st.schaapId, max(h.datum) datum
        FROM tblStal st
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE u.lidId = :lidId
 and h.skip = 0
        GROUP BY st.schaapId         

        union

        SELECT  mdr.schaapId, min(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0
 and u.lidId = :lidId
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY sd.schaapId
 ) max on (s.schaapId = max.schaapId)
SQL
        ; 
    }

    public function getInsUitvalAgridentWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.actId = 14 and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsUitvalBiocontrolFrom() {
        return <<<SQL
impReader rd
 left join (
    SELECT r.reduId, r.lidId 
    FROM tblRedenuser r
    WHERE r.lidId = :lidId
 and r.uitval = 1
 ) ru on (ru.reduId = rd.reden_uitv
 and ru.lidId = rd.lidId)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_uitv = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT sd.schaapId, max(sd.datum) datummax 
    FROM (
        SELECT st.schaapId, max(h.datum) datum
        FROM tblStal st
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE u.lidId = :lidId
 and h.skip = 0
        GROUP BY st.schaapId         

        union

        SELECT  mdr.schaapId, min(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1
 and h.skip = 0
 and u.lidId = :lidId
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0
 and u.lidId = :lidId
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY sd.schaapId
 ) max on (s.schaapId = max.schaapId)
SQL
        ; 
    }

    public function getInsUitvalBiocontrolWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and rd.teller_uitv is not null and isnull(rd.verwerkt) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsVoerregistratieFrom() {
        return <<<SQL
(
    SELECT max(Id) Id, hokId, artId, doelId
    FROM impAgrident rd
    WHERE lidId = :lidId
 and actId = 8888
 and isnull(verwerkt)
    GROUP BY hokId, artId, doelId
) rd
 join (
    SELECT max(Id) Id, min(datum) dmeerst, max(datum) dmlaatst, hokId, artId, sum(coalesce(toedat_upd, toedat)) toedtot, doelId
    FROM impAgrident
    WHERE lidId = :lidId
 and actId = 8888
 and isnull(verwerkt)
    GROUP BY hokId, artId, doelId
 ) md on (md.Id = rd.Id)
 join tblHok hk on (rd.hokId = hk.hokId) 
 join tblArtikel a on (rd.artId = a.artId)
  join (
    SELECT artId, sum(coalesce(toedat_upd, toedat)) totat
    FROM impAgrident
    WHERE lidId = :lidId
 and actId = 8888
 and isnull(verwerkt)
    GROUP BY artId
) ntot on (ntot.artId = rd.artId)
 left join 
(
    SELECT min(i.inkId) inkId, a.artId, a.naam, a.stdat, a.actief, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
    FROM tblEenheiduser eu
     join tblInkoop i on (i.enhuId = eu.enhuId)
     join tblArtikel a on (i.artId = a.artId)
     left join (
        SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
        FROM tblVoeding v
         join tblInkoop i on (v.inkId = i.inkId)
         join tblArtikel a on (a.artId = i.artId)
         join tblEenheiduser eu on (a.enhuId = eu.enhuId)
        WHERE eu.lidId = :lidId
        GROUP BY v.inkId
     ) n on (i.inkId = n.inkId)
    WHERE eu.lidId = :lidId
 and i.inkat-coalesce(n.vbrat,0) > 0
 and a.soort = 'voer'
    GROUP BY a.artId, a.naam, a.stdat
) i on (rd.artId = i.artId)
SQL;
    }

    public function getInsVoerregistratieWhere($lidId) {
        return [
            // was in de pagina uitgeschakeld
            // "WHERE rd.lidId = :lidId and rd.actId = 8888 and isnull(rd.verwerkt) ",
            '',
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getInsWegenFrom() {
        return <<<SQL
impAgrident rd
 left join ( 
     SELECT levensnummer, max(stalId) stalId
     FROM tblSchaap s
      join tblStal st on (s.schaapId = st.schaapId)
     GROUP BY levensnummer
 ) lstst on (lstst.levensnummer = rd.levensnummer)
 left join (
     SELECT stalId, schaapId
     FROM tblStal
     WHERE lidId = :lidId
 ) st on (st.stalId = lstst.stalId)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblUbn u on (u.ubnId = st.ubnId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE u.lidId = :lidId
 and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (st.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, a.actie, a.af, h.datum
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1
 and h.skip = 0
 ) haf on (haf.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, max(h.datum) datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 9
 and h.skip = 0
    GROUP BY st.schaapId
 ) hlst on (hlst.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId
 and rd.levensnummer = dup.levensnummer
 and rd.actId = dup.actId
 and rd.Id <> dup.Id)
    WHERE rd.actId = 9
 and rd.lidId = :lidId
 and ISNULL(rd.verwerkt)
 and ISNULL(dup.verwerkt)
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join (
    SELECT st.schaapId, max(h.datum) datum
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId
 and h.skip = 0
    GROUP BY st.schaapId 
 ) lstday on (lstday.schaapId = st.schaapId)
SQL;
    }

    public function getInsWegenWhere($lidId) {
        return [
            "WHERE rd.lidId = :lidId and actId = 9 and isnull(rd.verwerkt)",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getLoslopersPlaatsenFrom() {
        return <<<SQL
tblSchaap s
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and a.aan = 1
 and h.skip = 0
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and h.skip = 0
    GROUP BY st.schaapId
 ) hmax on (hmax.schaapId = s.schaapId)
 join tblHistorie h on (hmax.hisId = h.hisId)
SQL;
    }

    public function getLoslopersPlaatsenWhere($lidId) {
        return [
            " WHERE (isnull(b.hokId) or uit.hist is not null) ",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function getLoslopersVerkopenFrom() {
        return <<<SQL
tblSchaap s
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and a.aan = 1
 and h.skip = 0
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId
 and ((h1.datum < h2.datum) or (h1.datum = h2.datum
 and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = :lidId
 and a1.aan = 1
 and a2.uit = 1
 and h1.skip = 0
 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 and h.skip = 0
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 and h.skip = 0
 ) prnt on (prnt.schaapId = hin.schaapId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE u.lidId = :lidId
 and isnull(st.rel_best)
 and h.skip = 0
    GROUP BY st.schaapId
 ) hmax on (hmax.schaapId = s.schaapId)
 join tblHistorie h on (hmax.hisId = h.hisId)
SQL
        ;
    }

    public function getLoslopersVerkopenWhere($lidId) {
        return [
            "WHERE (isnull(b.hokId) or uit.hist is not null) and prnt.schaapId is not null",
            [[':lidId', $lidId, Type::INT]]
        ];
    }

    public function zoek_voerregels_reader($hokId, $artId, $doelId) {
        return $this->run_query(
            <<<SQL
SELECT Id, date_format(datum,'%d-%m-%Y') dag, hokId, artId, coalesce(toedat_upd, toedat) toedat, doelId
FROM impAgrident
WHERE hokId = :hokId
 and artId = :artId
 and doelId = :doelId
 and isnull(verwerkt)
ORDER BY datum
SQL
        , [
            [':hokId', $hokId, Type::INT],
            [':doelId', $doelId, Type::INT],
            [':artId', $artId, Type::INT],
        ]);
    }

    public function zoek_readerRegel_verwerkt($recId) {
        $sql = <<<SQL
SELECT verwerkt
FROM impAgrident
WHERE Id = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function zoek_levnr_reader($recId) {
        return $this->first_row(
            <<<SQL
SELECT levensnummer levnr_aanv, transponder
FROM impAgrident
WHERE Id = :recId
SQL
        , [[':recId', $recId, Type::INT]]
            , [null, null]
        );
    }

    public function zoek_lambar_record($lidId) {
        return $this->first_field(<<<SQL
SELECT max(Id) Id
FROM impAgrident
WHERE actId = 16 and isnull(hokId) and lidId = :lidId and isnull(verwerkt)
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function delete_user($lidId) {
        $this->run_query(<<<SQL
DELETE FROM impAgrident WHERE lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function zoek_levensnummer_transponder($recId) {
        $sql = <<<SQL
        SELECT transponder tran, levensnummer lam, moeder, moedertransponder mdr_tran
        FROM impAgrident
        WHERE Id = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_row($sql, $args, [0, 0, 0, 0]);
    }

    public function zoek_worpverloop_reader($recId) {
        $sql = <<<SQL
    SELECT verloop
     FROM impAgrident
     WHERE Id = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function updateReaderAgrident($recId) {
        $sql = <<<SQL
        UPDATE impAgrident set verwerkt = 1 WHERE Id = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function updateReaderBiocontrol($recId) {
        $sql = <<<SQL
        UPDATE impReader set verwerkt = 1 WHERE readId = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        $this->run_query($sql, $args);
    }

    public function zoek_data_reader($recId) {
        $sql = <<<SQL
    SELECT rd.levensnummer, u.ubnId, u.ubn, s.schaapId, rd.hokId
    FROM impAgrident rd
     join tblUbn u on (rd.ubnId = u.ubnId)
     join tblSchaap s on (rd.levensnummer = s.levensnummer)
    WHERE rd.Id = :recId
SQL;
        $args = [[':recId', $recId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    // MMMM
    public function count_stallijstscan_new_lid($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 21 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_zoek_dekken($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident
     WHERE lidId = :lidId and actId = 18 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_zoek_dracht($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident
     WHERE lidId = :lidId and actId = 19 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_lammeren($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident
     WHERE lidId = :lidId and actId = 1 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_lambar($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident
     WHERE lidId = :lidId and actId = 16 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_gespeenden($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 4 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_afgeleverden($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 12 and isnull(ubnId) and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_uitgeschaarden($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 10 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_uitgevallen($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 14 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_aanvoer($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and (actId = 2 or actId = 3) and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_TvUitscharen($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 11 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_overplaatsen($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident
     WHERE lidId = :lidId and actId = 5 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_SpenenEnOverpl($lidId) {
        $sql = <<<SQL
     SELECT count(rs.datum) aantsp
     FROM impAgrident rs 
      join (
         SELECT lidId, levensnummer
         FROM impAgrident
         WHERE lidId = :lidId and actId = 5 and isnull(verwerkt)
      ) ro ON (rs.lidId = ro.lidId and rs.levensnummer = ro.levensnummer)
     WHERE rs.lidId = :lidId and actId = 4 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_adoptie($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 15 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_medicijn($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 8 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_wegingen($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 9 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_omnummer($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 17 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_halsnummer($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 1717 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_voerregistratie($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 8888 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_wijzigingen_ubn($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 12 and ubnId is not null and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function count_stallijstscan_controle($lidId) {
        $sql = <<<SQL
     SELECT count(Id) aant 
     FROM impAgrident 
     WHERE lidId = :lidId and actId = 22 and isnull(verwerkt)
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

}
