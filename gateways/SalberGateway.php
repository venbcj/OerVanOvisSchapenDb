<?php

class SalberGateway extends Gateway {

    public function zoek_jaar($lidId) {
        return mysqli_query($this->db,"
SELECT year(max(sb.datum)) jaar
FROM tblSalber sb
 join tblElementuser eu on (sb.tblId = eu.elemuId)
WHERE sb.tbl = 'eu' and eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'

Union

SELECT year(max(sb.datum)) jaar
FROM tblSalber sb
 join tblRubriekuser ru on (sb.tblId = ru.rubuId)
WHERE sb.tbl = 'ru' and ru.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
");
}

        public function insertJaar($lidId, $nextjaar) {
mysqli_query("
INSERT INTO tblSalber (datum, tbl, tblId, waarde)
    SELECT '".$nextjaar."-01-01', 'eu', elemuId, waarde
    FROM tblElementuser
    WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    
    union all
    
    SELECT '".$nextjaar."-01-01', 'ru', rubuId, NULL
    FROM tblRubriekuser
    WHERE lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
    
    ORDER BY elemuId;
");
        }

    public function countGeborenInJaar($lidId, $jaar) {
$vw = mysqli_query($this->db,"
SELECT count(s.schaapId) aant_geb
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie hg on (hg.stalId = st.stalId and hg.actId = 1 and hg.skip = 0)
 left join tblHistorie hkoop on (hkoop.stalId = st.stalId and hkoop.actId = 2 and hkoop.skip = 0)
WHERE st.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
 and date_format(hg.datum,'%Y') = '".mysqli_real_escape_string($this->db,$jaar)."'
 and isnull(hkoop.hisId)
");
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
    }

    public function jaren($lidId) {
        return mysqli_query($this->db, "
SELECT year(sb.datum) jaar
FROM tblSalber sb
 join tblElementuser eu on (sb.tblId = eu.elemuId)
WHERE sb.tbl = 'eu' and eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
GROUP BY year(sb.datum)

Union

SELECT year(sb.datum) jaar
FROM tblSalber sb
 join tblRubriekuser ru on (sb.tblId = ru.rubuId)
WHERE sb.tbl = 'ru' and ru.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
GROUP BY year(sb.datum)
ORDER BY  jaar desc
");
    }

    public function zoek_rekencomponenten($lidId, $jaar) {
       $vw = mysqli_query($this->db,"
SELECT max(elem1) ooital, max(elem12) dooperc, max(elem18) worptal, max(elem19) worpgr
FROM (
    SELECT sb.waarde elem1, 0 elem12, 0 elem18, 0 elem19
    FROM tblElement e
     join tblElementuser eu on (e.elemId = eu.elemId)
     join tblSalber sb on (eu.elemuId = sb.tblId)
    WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."' and sb.tbl = 'eu' and eu.sal = 1
    and e.elemId = 1
  union
    SELECT 0, sb.waarde/100 elem12, 0 elem18, 0 elem19
    FROM tblElement e
     join tblElementuser eu on (e.elemId = eu.elemId)
     join tblSalber sb on (eu.elemuId = sb.tblId)
    WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."' and sb.tbl = 'eu' and eu.sal = 1
    and e.elemId = 12
  union
    SELECT 0, 0 elem12, sb.waarde elem18, 0 elem19
    FROM tblElement e
     join tblElementuser eu on (e.elemId = eu.elemId)
     join tblSalber sb on (eu.elemuId = sb.tblId)
    WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."' and sb.tbl = 'eu' and eu.sal = 1
    and e.elemId = 18
  union
    SELECT 0, 0, 0, sb.waarde elem19
    FROM tblElement e
     join tblElementuser eu on (e.elemId = eu.elemId)
     join tblSalber sb on (eu.elemuId = sb.tblId)
    WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."' and sb.tbl = 'eu' and eu.sal = 1
    and e.elemId = 19
) reken
");
return $vw;
    }

    public function zoek_element_vervanging_ooi($lidId, $jaar) {
       $vw = mysqli_query($this->db,"
SELECT sb.waarde
FROM tblSalber sb
 join tblElementuser eu on (eu.elemuId = sb.tblId)
WHERE tbl = 'eu' and eu.elemId = 16 and eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(datum) = '".mysqli_real_escape_string($this->db,$jaar)."'
"); 
    if ($vw->num_rows > 0) {
        return $vw->fetch_row()[0];
    }
    return null;
    }

    public function zoek_element($lidId, $jaar) {
$vw= mysqli_query($this->db,"
SELECT sb.salbId, e.elemId, e.element, sb.waarde, e.eenheid, 1 sort
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
 join tblSalber sb on (eu.elemuId = sb.tblId)
WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."'
 and sb.tbl = 'eu' and eu.sal = 1
 and eenheid = 'getal'

Union 

SELECT sb.salbId, e.elemId, e.element, sb.waarde, e.eenheid, 2 sort
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
 join tblSalber sb on (eu.elemuId = sb.tblId)
WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."'
 and sb.tbl = 'eu' and eu.sal = 1
 and eenheid = 'procent'

Union

SELECT sb.salbId, e.elemId, e.element, sb.waarde, e.eenheid, 3 sort
FROM tblElement e
 join tblElementuser eu on (e.elemId = eu.elemId)
 join tblSalber sb on (eu.elemuId = sb.tblId)
WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and year(sb.datum) = '".mysqli_real_escape_string($this->db,$jaar)."'
 and sb.tbl = 'eu' and eu.sal = 1
 and eenheid = 'euro'
ORDER BY sort, element
");
return $vw;
}

/* jaarbasis()
 *
 * Deze query wordt gebruikt in Saldoberekening.php
Omdat hij zo groot is is er een apart bestand van gemaakt.

Totalen van de Saldoberekening, Prognose (liquiditeit) en realiteit worden naast elkaar gezet.
Eerst worden de Opbrengsten gesommeerd en vervolgens de Kosten
Binnen de Opbrengsten en de Kosten is onderscheid gemaakt in 7 mogelijkheden
 - 1 De sommatie, zonder veld 'aantal', houdt geen rekening met het aantal ooien, het aantal af te leveren lammeren en het aantal te vervangen ooien 
 - 2 De sommatie,   met   veld 'aantal', houdt geen rekening met het aantal ooien, het aantal af te leveren lammeren en het aantal te vervangen ooien             => N.v.t. bij opbrengsten
 - 3 De sommatie, zonder veld 'aantal', houdt rekening met het aantal ooien.                 Het aantal ooien is variable p_ooital in Saldoberekening.php
 - 4 De sommatie,   met   veld 'aantal', houdt rekening met het aantal ooien.                 Het aantal ooien is variable p_ooital in Saldoberekening.php     => N.v.t. bij opbrengsten
 - 5 De sommatie, zonder veld 'aantal', houdt rekening met het aantal af te leveren lammeren.    Het aantal lammeren is variable p_afv in Saldoberekening.php
 - 6 De sommatie,   met   veld 'aantal', houdt rekening met het aantal af te leveren lammeren.    Het aantal lammeren is variable p_afv in Saldoberekening.php     => N.v.t. bij opbrengsten
 - 7 De sommatie, zonder veld 'aantal', houdt rekening met het aantal te vervangen ooien         Het aantal te vervangen ooien is variable verv_ooi*p_ooital/100 in Saldoberekening.php 

 17-1-2021 : enkele quotes om variabele gezet */

    public function jaarbasis($lidId, $kzlJaar, $p_ooital, $p_afv, $verv_ooi) {
return mysqli_query($this->db, "
SELECT sum(bedrag_slb) bedrag_slb, sum(bedrag_liq) bedrag_liq, sum(bedrag_real) bedrag_real
FROM (
    -- opbrengst met dieren n.v.t. zonder aantallen
    SELECT r.credeb, sum(coalesce(sb.waarde,0)) bedrag_slb, sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
        FROM tblLiquiditeit l
        WHERE year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY l.rubuId, date_format(l.datum,'%Y')
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY o.rubuId, date_format(o.datum,'%Y')
     ) o on (o.rubuId = ru.rubuId)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and sb.tbl = 'ru'
and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
 and r.actief = 1
 and ru.sal = 1
      and r.rubhId = 5 and r.rubId != 39 and r.rubId != 40 and r.rubId != 46
    GROUP BY r.credeb

    union

    -- opbrengst o.b.v. moederdieren => $p_ooital zonder aantallen
    SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($this->db,$p_ooital) ."' *sb.waarde,0)) bedrag_slb,
 sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
        FROM tblLiquiditeit l
        WHERE year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY l.rubuId, date_format(l.datum,'%Y')
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY o.rubuId, date_format(o.datum,'%Y')
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
      and r.rubId = 46
    GROUP BY r.credeb

    union

    -- opbrengst o.b.v. lammeren => $p_afv zonder aantallen
    SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($this->db,$p_afv) ."' * sb.waarde,0)) bedrag_slb,
sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
        FROM tblLiquiditeit l
        WHERE year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY l.rubuId, date_format(l.datum,'%Y')
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY o.rubuId, date_format(o.datum,'%Y')
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
      and r.rubId = 39
    GROUP BY r.credeb

    union

    -- opbrengst o.b.v. vervanging moederdieren => $verv_ooi*$p_ooital/100 zonder aantallen
    SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($this->db,$verv_ooi*$p_ooital/100) ."' *sb.waarde,0)) bedrag_slb,
 sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
        FROM tblLiquiditeit l
        WHERE year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY l.rubuId, date_format(l.datum,'%Y')
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."'
        GROUP BY o.rubuId, date_format(o.datum,'%Y')
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
      and r.rubId = 40
    GROUP BY r.credeb


    union

    -- kosten met dieren n.v.t. zonder aantallen
    SELECT r.credeb, -sum(coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and (r.rubhId = 1 or r.rubhId = 3 or r.rubhId = 4 or r.rubId = 12)
    GROUP BY r.credeb

    union

    -- kosten met dieren n.v.t. met aantallen
    SELECT r.credeb, -sum(coalesce(sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb,
 -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and r.rubId = 51
    GROUP BY r.credeb

    union

    -- kosten o.b.v. moederdieren => $p_ooital zonder aantallen
    SELECT r.credeb, -sum( '". mysqli_real_escape_string($this->db,$p_ooital) ."' * coalesce(sb.waarde,0)) bedrag_slb,
 -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and (r.rubId = 10 or r.rubId = 11 or r.rubId = 18 or r.rubId = 25 or r.rubId = 32 or r.rubId = 49 or r.rubId = 50)
    GROUP BY r.credeb

    union

    -- kosten o.b.v. moederdieren => $p_ooital met aantallen
    SELECT r.credeb, -sum(coalesce( '". mysqli_real_escape_string($this->db,$p_ooital) ."' * sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb,
 -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and (r.rubId = 16 or r.rubId = 19 or r.rubId = 44)
    GROUP BY r.credeb

    union

    -- kosten o.b.v. lammeren => $p_afv zonder aantallen
    SELECT r.credeb, -sum( '". mysqli_real_escape_string($this->db,$p_afv) ."' * coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and (r.rubId = 13 or r.rubId = 36)
    GROUP BY r.credeb

    union

    -- kosten o.b.v. lammeren => $p_afv met aantallen
    SELECT r.credeb, -sum(coalesce( '". mysqli_real_escape_string($this->db,$p_afv) ."' * sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb,
 -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and (r.rubId = 15 or r.rubId = 17 or r.rubId = 48)
    GROUP BY r.credeb

    union

    -- kosten o.b.v. vervanging moederdieren => $verv_ooi*$p_ooital/100 zonder aantallen
    SELECT r.credeb, -sum( '". mysqli_real_escape_string($this->db,$verv_ooi*$p_ooital/100) ."' * coalesce(sb.waarde,0)) bedrag_slb,
 -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_real
    FROM tblRubriek r
     join tblRubriekuser ru on (r.rubId = ru.rubId)
     join tblSalber sb on (sb.tblId = ru.rubuId)
     left join (
        SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
        FROM tblLiquiditeit l
         join tblRubriekuser ru on (l.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(l.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(l.datum,'%Y'), rubuId
     ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
     left join (
        SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
        FROM tblOpgaaf o
         join tblRubriekuser ru on (o.rubuId = ru.rubuId)
        WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."'
 and year(o.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and ru.sal = 1
        GROUP BY date_format(o.datum,'%Y'), rubuId
     ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
    WHERE ru.lidId = '". mysqli_real_escape_string($this->db,$lidId) ."' and sb.tbl = 'ru'
 and year(sb.datum) = '". mysqli_real_escape_string($this->db,$kzlJaar) ."' and r.actief = 1 and ru.sal = 1
     and r.rubId = 1
    GROUP BY r.credeb
 ) som
");
    }

}
