<?php

/*5-9-2021 : functie inlezen_voer gemaakt 22-9-2021 functie inlezen_pil gemaakt */

function volgende_inkoop_voer($datb, $artikel) {

    $zoek_volgende_inkoopdatum = mysqli_query($datb, "
  SELECT min(dmink) dmink
  FROM tblInkoop i
   left join tblVoeding v on (i.inkId = v.inkId) 
  WHERE artId = '" . mysqli_real_escape_string($datb, $artikel) . "' and isnull(v.inkId)
");

    while ($v_inkdm = mysqli_fetch_assoc($zoek_volgende_inkoopdatum)) {
        $dmink = $v_inkdm['dmink'];
    }

    $zoek_volgende_inkId = mysqli_query($datb, "
  SELECT min(i.inkId) inkId
  FROM tblInkoop i
   left join tblVoeding v on (i.inkId = v.inkId)
  WHERE artId = '" . mysqli_real_escape_string($datb, $artikel) . "' and dmink = '" . mysqli_real_escape_string($datb, $dmink) . "' and isnull(v.inkId)
");

    while ($v_inkId = mysqli_fetch_assoc($zoek_volgende_inkId)) {
        $new_inkId = $v_inkId['inkId'];
    }

    $zoek_inkoophoeveelheid = mysqli_query($datb, "
  SELECT i.inkId, i.inkat, a.stdat
  FROM tblInkoop i
   join tblArtikel a on (i.artId = a.artId)
  WHERE inkId = '" . mysqli_real_escape_string($datb, $new_inkId) . "'
");

    while ($ih = mysqli_fetch_assoc($zoek_inkoophoeveelheid)) {
        $inkoop = array($ih['inkId'], $ih['inkat'], $ih['stdat']);
    }

    return $inkoop;
}

function zoek_voorraad_oudste_inkoop_voer($datb, $artikel) {

    $zoek_inkId_en_resterende_voorraad_van_laatst_aangesproken_voorraad = mysqli_query($datb, "
SELECT i.inkId, i.inkat - coalesce(n.nutat,0) vrdat, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 left join (
    SELECT inkId, sum(nutat*stdat) nutat
    FROM tblVoeding 
    GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '" . mysqli_real_escape_string($datb, $artikel) . "' and i.inkat > (i.inkat - coalesce(n.nutat,0)) and (i.inkat - coalesce(n.nutat,0)) > 0
");
    while ($i_vrd = mysqli_fetch_assoc($zoek_inkId_en_resterende_voorraad_van_laatst_aangesproken_voorraad)) {
        $inkoop = array($i_vrd['inkId'], $i_vrd['vrdat'], $i_vrd['stdat']);
    }

    if (!isset($inkoop[0])) {
        $inkoop = volgende_inkoop_voer($datb, $artikel);
    }

    return $inkoop;
}

function inlezen_voer($datb, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid) {

    $ink_voorraad = zoek_voorraad_oudste_inkoop_voer($datb, $artid);

    $inkId = $ink_voorraad[0];
    $rest_ink_vrd = $ink_voorraad[1];
    $stdat = $ink_voorraad[2];


    if ($rest_toedat > $rest_ink_vrd) {
        $inlezen_voer = "INSERT INTO tblVoeding SET periId = '" . mysqli_real_escape_string($datb, $periode_id) . "', inkId = '" . mysqli_real_escape_string($datb, $inkId) . "', nutat = '" . mysqli_real_escape_string($datb, $rest_ink_vrd) . "', stdat = '" . mysqli_real_escape_string($datb, $stdat) . "', datum = " . db_null_input($toediendatum) . ", readerId = " . db_null_input($readerid) . " ";

        mysqli_query($datb, $inlezen_voer);


        $rest_toedat = $rest_toedat - $rest_ink_vrd;


        inlezen_voer($datb, $artid, $rest_toedat, $toediendatum, $periode_id, $readerid);
    } else {
        $inlezen_voer = "INSERT INTO tblVoeding SET periId = '" . mysqli_real_escape_string($datb, $periode_id) . "', inkId = '" . mysqli_real_escape_string($datb, $inkId) . "', nutat = '" . mysqli_real_escape_string($datb, $rest_toedat) . "', stdat = '" . mysqli_real_escape_string($datb, $stdat) . "', datum = " . db_null_input($toediendatum) . ", readerId = " . db_null_input($readerid) . " ";

        mysqli_query($datb, $inlezen_voer);
    }
}



function volgende_inkoop_pil($datb, $artikel) {
    // deze query leverde nooit geen-rijen op! MIN() zonder GROUP BY is onvoorspelbaar --BCB
    $zoek_volgende_inkoopdatum = mysqli_query($datb, "
  SELECT min(dmink) dmink
  FROM tblInkoop i
   left join tblNuttig n on (i.inkId = n.inkId) 
  WHERE artId = '" . mysqli_real_escape_string($datb, $artikel) . "'
 and isnull(n.inkId)
GROUP BY i.inkId
");
    while ($v_inkdm = mysqli_fetch_assoc($zoek_volgende_inkoopdatum)) {
        $dmink = $v_inkdm['dmink'];
    }
    $zoek_volgende_inkId = mysqli_query($datb, "
  SELECT min(i.inkId) inkId
  FROM tblInkoop i
   left join tblNuttig n on (i.inkId = n.inkId)
  WHERE artId = '" . mysqli_real_escape_string($datb, $artikel) . "'
 and dmink = '" . mysqli_real_escape_string($datb, $dmink) . "'
 and isnull(n.inkId)
");
    while ($v_inkId = mysqli_fetch_assoc($zoek_volgende_inkId)) {
        $new_inkId = $v_inkId['inkId'];
    }
    $inkoop = [1, 1, 1];
    $zoek_inkoophoeveelheid = mysqli_query($datb, "
  SELECT i.inkId, i.inkat, a.stdat
  FROM tblInkoop i
   join tblArtikel a on (i.artId = a.artId)
  WHERE inkId = '" . mysqli_real_escape_string($datb, $new_inkId) . "'
");
    while ($ih = mysqli_fetch_assoc($zoek_inkoophoeveelheid)) {
        $inkoop = array($ih['inkId'], $ih['inkat'], $ih['stdat']);
    }
    return $inkoop;
}

function zoek_voorraad_oudste_inkoop_pil($datb, $artikel) {
    $zoek_inkId_en_resterende_voorraad_van_laatst_aangesproken_voorraad = mysqli_query($datb, "
SELECT i.inkId, i.inkat - coalesce(n.nutat,0) vrdat, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 left join (
    SELECT inkId, sum(nutat*stdat) nutat
    FROM tblNuttig 
    GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '" . mysqli_real_escape_string($datb, $artikel) . "'
 and i.inkat > (i.inkat - coalesce(n.nutat,0))
 and (i.inkat - coalesce(n.nutat,0)) > 0
");
    while ($i_vrd = mysqli_fetch_assoc($zoek_inkId_en_resterende_voorraad_van_laatst_aangesproken_voorraad)) {
        $inkoop = array($i_vrd['inkId'], $i_vrd['vrdat'], $i_vrd['stdat']);
    }
    if (!isset($inkoop[0])) {
        $inkoop = volgende_inkoop_pil($datb, $artikel);
    }
    return $inkoop;
}

function inlezen_pil($datb, $hisid, $artid, $rest_toedat, $toediendatum, $reduid) {
    $ink_voorraad = zoek_voorraad_oudste_inkoop_pil($datb, $artid);
    $inkId = $ink_voorraad[0];
    $rest_ink_vrd = $ink_voorraad[1];
    $stdat = $ink_voorraad[2];
# @TODO: #0004202 zorg dat je niet deelt door 0
    $rest_toedien_vrd = $rest_ink_vrd / $stdat;
    if ($rest_toedat > $rest_toedien_vrd) {
        $aantal = $rest_toedien_vrd;
        nuttig_pil($datb, $hisid, $inkId, $stdat, $reduid, $aantal);
        $rest_toedat = $rest_toedat - $rest_toedien_vrd;
        inlezen_pil($datb, $hisid, $artid, $rest_toedat, $toediendatum, $reduid);
    } else {
        $aantal = $rest_toedat;
        nuttig_pil($datb, $hisid, $inkId, $stdat, $reduid, $aantal);
    }
}

function nuttig_pil($datb, $hisid, $inkId, $stdat, $reduid, $aantal) {
    $inlezen_pil = "INSERT INTO tblNuttig
        SET hisId = '" . mysqli_real_escape_string($datb, $hisid) . "',
        inkId = '" . mysqli_real_escape_string($datb, $inkId) . "',
        nutat = '" . mysqli_real_escape_string($datb, $aantal) . "',
        stdat = '" . mysqli_real_escape_string($datb, $stdat) . "',
        reduId = " . db_null_input($reduid) . " ";
    mysqli_query($datb, $inlezen_pil);
}
