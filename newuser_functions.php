<?php

function generatekey($length) {
    $options = 'abcdefghijklmnopqrstuvwxyz013456789';
    $code = '';
    for($i = 0; $i < $length; $i++) {
        $key = rand(0, strlen($options) - 1);
        $code .= $options[$key];
    }
    return $code;
}

function getApiKey($datb) {
        $apikey = generatekey(64);

        $result = mysqli_query($datb,"SELECT count(*) aant FROM tblLeden WHERE readerkey = '".mysqli_real_escape_string($datb,$apikey)."' ;") or die (mysqli_error($datb)); 

        while ($row = mysqli_fetch_assoc($result)) { $count = $row['aant']; }

        if ($count > 0) { $apikey = getApiKey($datb); }

    return $apikey;
}

function getAlias($datb,$username,$vlgnr) {
    if($vlgnr > 0) { $alias = $username.$vlgnr; } else { $alias = $username; }

    $result = mysqli_query($datb,"SELECT count(*) aant FROM tblLeden WHERE alias = '".mysqli_real_escape_string($datb,$alias)."' ;") or die (mysqli_error($datb)); 

    while ($row = mysqli_fetch_assoc($result))
        { $count = $row['aant']; } 

        
    if($count > 0) {
        $vlgnr = ++$vlgnr;

            $alias = getAlias($datb,$username,$vlgnr);
        }
            
    return $alias;

}
