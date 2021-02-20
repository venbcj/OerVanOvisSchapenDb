<?php
/*Deze query wordt gebruikt in Saldoberekening.php
Omdat hij zo groot is is er een apart bestand van gemaakt.

Totalen van de Saldoberekening, Prognose (liquiditeit) en realiteit worden naast elkaar gezet.
Eerst worden de Opbrengsten gesommeerd en vervolgens de Kosten
Binnen de Opbrengsten en de Kosten is onderscheid gemaakt in 7 mogelijkheden
 - 1 De sommatie, zonder veld 'aantal', houdt geen rekening met het aantal ooien, het aantal af te leveren lammeren en het aantal te vervangen ooien 
 - 2 De sommatie,   met   veld 'aantal', houdt geen rekening met het aantal ooien, het aantal af te leveren lammeren en het aantal te vervangen ooien 			=> N.v.t. bij opbrengsten
 - 3 De sommatie, zonder veld 'aantal', houdt rekening met het aantal ooien. 				Het aantal ooien is variable $p_ooital in Saldoberekening.php
 - 4 De sommatie,   met   veld 'aantal', houdt rekening met het aantal ooien. 				Het aantal ooien is variable $p_ooital in Saldoberekening.php 	=> N.v.t. bij opbrengsten
 - 5 De sommatie, zonder veld 'aantal', houdt rekening met het aantal af te leveren lammeren.	Het aantal lammeren is variable $p_afv in Saldoberekening.php
 - 6 De sommatie,   met   veld 'aantal', houdt rekening met het aantal af te leveren lammeren.	Het aantal lammeren is variable $p_afv in Saldoberekening.php 	=> N.v.t. bij opbrengsten
 - 7 De sommatie, zonder veld 'aantal', houdt rekening met het aantal te vervangen ooien 		Het aantal te vervangen ooien is variable $verv_ooi*$p_ooital/100 in Saldoberekening.php 

 17-1-2021 : enkele quotes om variabele gezet */

$vw_Saldober_Jaarbasis = "
SELECT sum(bedrag_slb) bedrag_slb, sum(bedrag_liq) bedrag_liq, sum(bedrag_opg) bedrag_opg
FROM (
	-- opbrengst met dieren n.v.t. zonder aantallen
	SELECT r.credeb, sum(coalesce(sb.waarde,0)) bedrag_slb, sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
		FROM tblLiquiditeit l
		GROUP BY l.rubuId, date_format(l.datum,'%Y')
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
		FROM tblOpgaaf o
		GROUP BY o.rubuId, date_format(o.datum,'%Y')
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	  and r.rubhId = 5 and r.rubId != 39 and r.rubId != 40 and r.rubId != 46
	GROUP BY r.credeb

	union

	-- opbrengst o.b.v. moederdieren => $p_ooital zonder aantallen
	SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($db,$p_ooital) ."' *sb.waarde,0)) bedrag_slb, sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
		FROM tblLiquiditeit l
		GROUP BY l.rubuId, date_format(l.datum,'%Y')
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
		FROM tblOpgaaf o
		GROUP BY o.rubuId, date_format(o.datum,'%Y')
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	  and r.rubId = 46
	GROUP BY r.credeb

	union

	-- opbrengst o.b.v. lammeren => $p_afv zonder aantallen
	SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($db,$p_afv) ."' * sb.waarde,0)) bedrag_slb, sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
		FROM tblLiquiditeit l
		GROUP BY l.rubuId, date_format(l.datum,'%Y')
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
		FROM tblOpgaaf o
		GROUP BY o.rubuId, date_format(o.datum,'%Y')
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	  and r.rubId = 39
	GROUP BY r.credeb

	union

	-- opbrengst o.b.v. vervanging moederdieren => $verv_ooi*$p_ooital/100 zonder aantallen
	SELECT r.credeb, sum(coalesce( '". mysqli_real_escape_string($db,$verv_ooi*$p_ooital/100) ."' *sb.waarde,0)) bedrag_slb, sum(l.bedrag) bedrag_liq, sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT l.rubuId, date_format(l.datum,'%Y') jaar, sum(coalesce(l.bedrag,0)) bedrag
		FROM tblLiquiditeit l
		GROUP BY l.rubuId, date_format(l.datum,'%Y')
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT o.rubuId, date_format(o.datum,'%Y') jaar, sum(coalesce(o.bedrag,0)) bedrag
		FROM tblOpgaaf o
		GROUP BY o.rubuId, date_format(o.datum,'%Y')
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	  and r.rubId = 40
	GROUP BY r.credeb


	union

	-- kosten met dieren n.v.t. zonder aantallen
	SELECT r.credeb, -sum(coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and (r.rubhId = 1 or r.rubhId = 3 or r.rubhId = 4 or r.rubId = 12)
	GROUP BY r.credeb

	union

	-- kosten met dieren n.v.t. met aantallen
	SELECT r.credeb, -sum(coalesce(sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and r.rubId = 51
	GROUP BY r.credeb

	union

	-- kosten o.b.v. moederdieren => $p_ooital zonder aantallen
	SELECT r.credeb, -sum( '". mysqli_real_escape_string($db,$p_ooital) ."' * coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and (r.rubId = 10 or r.rubId = 11 or r.rubId = 18 or r.rubId = 25 or r.rubId = 32 or r.rubId = 49 or r.rubId = 50)
	GROUP BY r.credeb

	union

	-- kosten o.b.v. moederdieren => $p_ooital met aantallen
	SELECT r.credeb, -sum(coalesce( '". mysqli_real_escape_string($db,$p_ooital) ."' * sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and (r.rubId = 16 or r.rubId = 19 or r.rubId = 44)
	GROUP BY r.credeb

	union

	-- kosten o.b.v. lammeren => $p_afv zonder aantallen
	SELECT r.credeb, -sum( '". mysqli_real_escape_string($db,$p_afv) ."' * coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and (r.rubId = 13 or r.rubId = 36)
	GROUP BY r.credeb

	union

	-- kosten o.b.v. lammeren => $p_afv met aantallen
	SELECT r.credeb, -sum(coalesce( '". mysqli_real_escape_string($db,$p_afv) ."' * sb.aantal,0)*coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and (r.rubId = 15 or r.rubId = 17 or r.rubId = 48)
	GROUP BY r.credeb

	union

	-- kosten o.b.v. vervanging moederdieren => $verv_ooi*$p_ooital/100 zonder aantallen
	SELECT r.credeb, -sum( '". mysqli_real_escape_string($db,$verv_ooi*$p_ooital/100) ."' * coalesce(sb.waarde,0)) bedrag_slb, -sum(coalesce(l.bedrag,0)) bedrag_liq, -sum(coalesce(o.bedrag,0)) bedrag_opg
	FROM tblRubriek r
	 join tblRubriekuser ru on (r.rubId = ru.rubId)
	 join tblSalber sb on (sb.tblId = ru.rubuId)
	 left join (
		SELECT date_format(l.datum,'%Y') jaar, l.rubuId, sum(l.bedrag) bedrag
		FROM tblLiquiditeit l
		 join tblRubriekuser ru on (l.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(l.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(l.datum,'%Y'), rubuId
	 ) l on (l.rubuId = ru.rubuId and date_format(sb.datum,'%Y') = l.jaar)
	 left join (
		SELECT date_format(o.datum,'%Y') jaar, o.rubuId, sum(o.bedrag) bedrag
		FROM tblOpgaaf o
		 join tblRubriekuser ru on (o.rubuId = ru.rubuId)
		WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and year(o.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and ru.sal = 1
		GROUP BY date_format(o.datum,'%Y'), rubuId
	 ) o on (o.rubuId = ru.rubuId and o.jaar = l.jaar)
	WHERE ru.lidId = '". mysqli_real_escape_string($db,$lidId) ."' and sb.tbl = 'ru' and year(sb.datum) = '". mysqli_real_escape_string($db,$toon_jaar) ."' and r.actief = 1 and ru.sal = 1
	 and r.rubId = 1
	GROUP BY r.credeb
 ) som
"
?>