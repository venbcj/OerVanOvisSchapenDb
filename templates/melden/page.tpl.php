<TD valign='top'>    
<br><br>
<h2 align="center" style="color:blue">Hier kun je meldingen bij RVO indienen.</h2>
<table border="0" align="center">
<tr height="40"><td></td></tr>
<?php
    if (isset($links)) {
        View::render('melden/_menu', ['links' => $links]);
    } else {
        View::render('melden/_niet_mogelijk');
    }
?>
</table>
<br><br><br>
</TD>
