<?php

/* TODO: verbouwen.
 * De beslissing over de kleuren (afhankelijk van gebruikers-rechten) hoort niet in de view. Zeker niet met een query erin.
Hoe dan?
- er zijn nu al twee plekken die een menu tonen: bovenin horizontaal, en rechts vertikaal.
- beide plekken hebben een inhoud (menu-items, dat zijn links) en een opmaak -> scheiden in rekenbestand en templatebestand
  horizontaal menu werkt met ul/li, vertikaal menu met td(hr)--er zit geen tr in die template, en alles zit in 1 td, dus daar kan nog wat html naar de omliggende template
- header.tpl wordt alleen opgehaald in login.php. Nee, dat is helaas niet waar.
    Groeiresultaat.php -> doet al include login
    Leveranciers.php -> wordt nergens gebruikt, zit niet in navigatie.
    Leverancier.php idem
    Klanten.php -> wordt nergens gebruikt, zit niet in navigatie.
    Klant.php idem
    Welkom.php -> problematisch, zet niet de module-globals waar header op rekent. Zit niet in navigatie.
    Welkom2.php idem
    demo_database_legen.php -> doet al include login
    Meldpagina.php -> doet al include login
    Worpindex.php -> idem, plus "deze pagina is nog in ontwikkeling"
    test_javascript.php -> dit soort bestanden zou je in een subfolder moeten zetten
  afgezien daarvan: de rekencode kan dus verhuizen naar login, om te beginnen.
- maak een functie die deze kleuren teruggeeft, en dan niet als vier globals, maar als een array.
- maak een functie die de menuitems teruggeeft. Of twee: sitenav(), en pagenav(); voor in het bovenste en rechter menu.
  Een menuitem kan een leeg element zijn (voor de witte cellen in het pagina-menu).
  Een menuitem kan kinderen hebben (voor in het site-menu)
 */

$tech_color = 'grey';
if ($modtech != 0) {
    $tech_color = 'blue';
}

$fin_color = 'grey';
if ($modfin == 1) {
    $fin_color = 'black';
}

$meld_color = 'grey';
if ($modmeld != 0) {
    // NOTE: kleur is hier zwart, in menu1 (en zo) blauw. Misschien stijlen hernoemen naar 'inactief', 'actief', 'attentie' --BCB
    $meld_color = 'black';
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

$reader_color = 'red';
if (isset($actuele_versie)) {
    $reader_color = 'black';
}

?>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="menu.css">

<?php
include "back_to_top.js.php";
?>

<div id = "rechts_uitlijnen" class = 'header_breed'>
    <section style="text-align : center">
<?php # TODO: waarom de spaties? # ?>
        <?php echo $titel . str_repeat('&nbsp;', 28); ?>
    </section>
    <img src='OER_van_OVIS.jpg' />
</div>

<ul class="header_smal topnav" id = <?php echo Url::getTagId(); ?> >
<?php if (Auth::is_logged_in()) {
include "topnav.tpl.php";
} ?>
    <li id = "rechts_uitlijnen">
<?php if (Auth::is_logged_in()) { ?>
        <?php echo View::link_to('Uitloggen', 'index.php', ['class' => 'black']); ?>
<?php } else { ?>
        <?php echo View::link_to('Inloggen', 'index.php', ['class' => 'black']); ?>
<?php } ?>
    </li>
</ul>

<table id ="table1" align="center">
<tbody>
<tr height = 90> </tr>
<TR>
