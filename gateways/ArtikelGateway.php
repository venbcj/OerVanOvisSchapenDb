<?php

class ArtikelGateway extends Gateway {

    public function pilForLid($lidId) {
return mysqli_query($this->db,"
SELECT a.artId, a.naam
FROM tblEenheiduser eu
 join tblArtikel a on (eu.enhuId = a.enhuId)
 join tblInkoop i on (a.artId = i.artId)
 join tblNuttig n on (n.inkId = i.inkId)
WHERE eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and a.soort = 'pil'
GROUP BY a.naam
ORDER BY a.naam
");
    }

    public function zoek_soort($artId) {
        $pArtId = mysqli_real_escape_string($this->db, $artId);
        return $this->first_field(<<<SQL
SELECT a.soort
FROM tblArtikel a
WHERE a.artId = $pArtId
SQL
        , [[':artId', $artId, self::INT]], 'pil');
    }

    public function pilregels($artId) {
        return mysqli_query($this->db, "
SELECT i.inkId, a.naam, date_format(i.dmink,'%d-%m-%Y') toedm, i.charge, round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) totat, e.eenheid
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblNuttig n on (n.inkId = i.inkId) 
WHERE a.artId = ".mysqli_real_escape_string($this->db,$artId)."
GROUP BY i.inkId, a.naam, i.dmink, i.charge, i.inkat, e.eenheid
ORDER BY i.dmink desc, i.inkId
");
    }

    public function voerregels($artId) {
        return mysqli_query($this->db, "
SELECT i.inkId, a.naam, date_format(i.dmink,'%d-%m-%Y') toedm, NULL charge, round(i.inkat - sum(coalesce(v.nutat*v.stdat,0)),0) totat, e.eenheid
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblVoeding v on (v.inkId = i.inkId) 
WHERE a.artId = ".mysqli_real_escape_string($this->db,$artId)."
GROUP BY i.inkId, a.naam, i.dmink, i.inkat, e.eenheid
ORDER BY i.dmink desc, i.inkId
");
    }

    public function voer($lidId) {
        return mysqli_query($this->db,"
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.inkat-coalesce(v.vbrat,0) vrdat, round((i.inkat-coalesce(v.vbrat,0))/a.stdat,2) toedat
FROM tblArtikel a
 join (
    SELECT i.artId, i.enhuId, sum(i.inkat) inkat
    FROM tblEenheiduser eu
     join tblInkoop i on (i.enhuId = eu.enhuId)
     join tblArtikel a on (a.artId = i.artId)
    WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'voer'
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
    WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'voer'
    GROUP BY a.artId
 ) v on (i.artId = v.artId)
WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'voer' and i.inkat-coalesce(v.vbrat,0) > 0
ORDER BY a.naam
");
}

public function pil($lidId) {
    return mysqli_query($this->db,"
SELECT a.artId, a.naam, a.stdat, e.eenheid, i.charge, sum(i.inkat-coalesce(n.vbrat,0)) vrdat, round(sum((i.inkat-coalesce(n.vbrat,0))/a.stdat),2) toedat, artvrd.totvrd
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
    WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'pil'
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
        WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'pil'
        GROUP BY i.inkId
     ) vrd
    GROUP BY artId
 ) artvrd on (artvrd.artId = a.artId)
WHERE eu.lidId = ".mysqli_real_escape_string($this->db,$lidId)." and a.soort = 'pil' and i.inkat-coalesce(n.vbrat,0) > 0 
GROUP BY a.artId, a.naam, a.stdat, e.eenheid, i.charge, artvrd.totvrd
ORDER BY a.naam, i.inkId
 ");
    }

public function zoek_voer($lidId) {
   return mysqli_query($this->db,"
SELECT a.artId, a.naam
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
WHERE a.soort = 'voer' and eu.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
GROUP BY a.artId, a.naam 
ORDER BY a.naam
");
}

}
