<?php

class Request {

    public static function maak_request($datb, $lidid, $fldCode) {
        $insert_tblRequest = "INSERT INTO tblRequest SET lidId_new = ".mysqli_real_escape_string($datb, $lidid).", code = '".mysqli_real_escape_string($datb, $fldCode)."' ";
        mysqli_query($datb, $insert_tblRequest) or die(mysqli_error($datb));
        $req_open = mysqli_query($datb, "SELECT max(reqId) reqId
            FROM tblRequest 
            WHERE lidId_new = ".mysqli_real_escape_string($datb, $lidid)." and isnull(dmmeld) and code = '$fldCode' ") ;
        if ($req_open) {
            $open = mysqli_fetch_assoc($req_open);
            return $open['reqId'];
        }
        return false;
    }

}
