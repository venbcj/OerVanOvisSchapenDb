<?php
/*
 <!-- 20-12-2020 : Pagina gemaakt 
 29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php -->
 */

// TODO: #0004138 dit is een kopie uit menu1. Moet een functie worden. --BCB
$meld_color = 'grey';
if ($modmeld != 0) {
    $meld_color = 'blue';
    // Kijken of er nog meldingen openstaan
    $req_open = mysqli_query($db, "
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db, $lidId)." and h.skip = 0 and isnull(r.dmmeld) and m.skip <> 1 ");
    $row = mysqli_fetch_assoc($req_open);
    if ($row['aant'] > 0) {
        $meld_color = 'red';
    }
}

$melding_color = 'grey';
if ($modmeld != 0) {
    $melding_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    '',
    (object)['caption' => 'Melden RVO', 'href' => 'Melden.php', 'class' => $meld_color],
    (object)['caption' => 'Meldingen', 'href' => 'Meldingen.php', 'class' => $melding_color],
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
];
?>

<td width='150' height='100' valign='top'>
Menu : </br>
<hr class="blue">

<?php
foreach ($menu_items as $item) :
    if ($item) {
        echo View::link_to($item->caption, $item->href, ['class' => $item->class]);
    } else {
        echo '<br/>';
    }
echo PHP_EOL.'<hr class="grey">'.PHP_EOL;
endforeach; ?>

<?php include "versie.tpl.php"; ?>
</td>
