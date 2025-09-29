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

}
