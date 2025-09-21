<?php
/*
 <!-- 20-12-2020 : Pagina gemaakt 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.js.php -->
 */

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

include "javascriptsAfhandeling.js.php";
$menu_items = [
    (object)['caption' => 'Home', 'href' => 'Home.php', 'class' => 'blue'],
    '',
    (object)['caption' => 'Ooitjes uit meerlingen', 'href' => 'OoilamSelectie.php', 'class' => $tech_color],
    '',
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
