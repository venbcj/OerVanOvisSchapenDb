<?php

class HokGateway extends Gateway {

    public function findLongestHoknr($lidId) {
        return $this->db
                    ->query("SELECT max(length(hoknr)) lengte FROM`tblHok`WHERE lidId ='".$this->db->real_escape_string($lidId)."' ")
                    ->fetch_row()[0];
    }

    public function kzlHok($lidId) {
        return $this->db->query("
SELECT hokId, hoknr
FROM tblHok
WHERE actief = 1 and lidId = '" . $this->db->real_escape_string($lidId) . "' 
ORDER BY hoknr 
");
    }

public function lidIdByHokId($hok) {
$zoek_lid = $this->db->query("
SELECT lidId
FROM tblHok
WHERE hokId = ".$this->db->real_escape_string($hok)." 
");
while ($row = $zoek_lid->fetch_assoc()) { $lidId = $row['lidId']; }
return $lidId;
}

public function zoek_verblijf($lidId) {
    return $this->db->query("
SELECT hoknr, scan
FROM tblHok
WHERE actief = 1 and lidId = ".$this->db->real_escape_string($lidId)."
ORDER BY hoknr
");
}

public function countVerblijven($lidId, $artId, $doelId) {
    $vw = $this->db->query("
SELECT count(p.periId) aant
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = '".$this->db->real_escape_string($lidId)."'
 and ".db_null_filter('i.artId', $artId)."
 and p.doelId = $doelId
");
return $vw->fetch_row()[0];
}

public function kzlHokVoer($lidId, $artId) {
    return $this->db->query("
SELECT h.hokId, h.hoknr
FROM tblHok h
 join tblPeriode p on (p.hokId = h.hokId)
 left join tblVoeding v on (p.periId = v.periId)
 left join tblInkoop i on (i.inkId = v.inkId)
WHERE h.lidId = '".$this->db->real_escape_string($lidId)."' and ".db_null_filter('i.artId', $artId)."
GROUP BY h.hoknr
");
}

}
