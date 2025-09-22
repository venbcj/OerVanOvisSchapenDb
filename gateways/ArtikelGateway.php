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

}
