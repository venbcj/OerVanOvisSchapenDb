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

}
