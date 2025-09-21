<?php

class HistorieGateway extends Gateway {

    public function zoek_eerste_datum_stalop($recId) {
        $first_day = null;
        $eerste_dag = null;
        $vw = mysqli_query($this->db, "
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
 join (
    SELECT st.stalId, h.hisId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
     join tblMelding m on (m.hisId = h.hisId)
    WHERE m.meldId = '$recId'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
 WHERE a.op = 1
");
            while ($mi = mysqli_fetch_assoc($vw)) {
                $first_day = $mi['date'];
                $eerste_dag = $mi['datum'];
            }
        // TODO: record teruggeven ipv anonieme array?
        // TODO in een veel later stadium: Tell, Don't Ask -> verplaats het gedrag op basis van deze data hierheen
        return [$first_day, $eerste_dag];
    }

    public function setDatum($day, $recId) {
        mysqli_query($this->db, "
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".mysqli_real_escape_string($this->db, $day)."'
 WHERE m.meldId = '$recId' 
 ");
    }

}
