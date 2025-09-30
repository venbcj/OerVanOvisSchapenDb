<?php

class HokGateway extends Gateway {

    public function kzlHok($lidId) {
        return $this->db->query("
SELECT hokId, hoknr
FROM tblHok
WHERE actief = 1 and lidId = '" . mysqli_real_escape_string($this->db,$lidId) . "' 
ORDER BY hoknr 
");
    }

public function lidIdByHokId($hok) {
$zoek_lid = mysqli_query($this->db,"
SELECT lidId
FROM tblHok
WHERE hokId = ".mysqli_real_escape_string($this->db,$hok)." 
");
while ($row = mysqli_fetch_assoc($zoek_lid)) { $lidId = $row['lidId']; }
return $lidId;
}

public function zoek_verblijf($lidId) {
    return mysqli_query($this->db,"
SELECT hoknr, scan
FROM tblHok
WHERE actief = 1 and lidId = ".mysqli_real_escape_string($this->db,$lidId)."
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
WHERE h.lidId = '".mysqli_real_escape_string($this->db,$lidId)."'
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
WHERE h.lidId = '".mysqli_real_escape_string($this->db,$lidId)."' and ".db_null_filter('i.artId', $artId)."
GROUP BY h.hoknr
");
}

}
