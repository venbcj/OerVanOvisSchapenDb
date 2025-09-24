<!DOCTYPE html>
<?php  $versie = '17-2-14'; /*insInkat = ln['vrbat']*($_POST['txtBstat']); gewijzigd naar insInkat = $_POST['txtBstat']; zodat de totale hoeveelheid kan worden ingevoerd bij inkoop ipv het totale aantal / verbruikeenheid in te voeren.*/
$versie = '27-11-2014'; /*chargenr toegevoegd.*/ 
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '20-12-2015'; /* Inkoop ook toegevoegd aan tblOpgaaf indien module financieel in gebruik */
$versie = '16-6-2018'; /* Bedrag bij ingekochte artikelen wijzigbaar. Bedrag bij inkoop niet verplicht. function verplicht() toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2018'; /* javascript toegevoegd tbv eenheid artikel wijzigen */
$versie = '7-4-2019'; /* Prijs in tblOpgaaf incl. btw gemaakt */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 1-8-2020 : kalender toegevoegd */
$versie = '28-11-2020'; /* 28-11-2020 velde chkDel toegevoegd */

Session::start();

 ?>

<html>
  
<head>
    <title>
      Purchase
    </title>
<!--    <script src= "https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <style type="text/css">
        .selectt {
            color: #fff;
            padding: 30px;
            display: compact;
            margin-top: 30px;
            width: 60%;
            background: grey;
        }
          
       
    </style>
</head>
  
<body>

<center>

<table>
<?php for ($i = 2018; $i <= 2022; $i++) {
$currentYear = 2021; ?>

<tr>
 <td>
    <input type="checkbox" name="Checkbox" value= <?php echo $i; if($i == 2021) { ?> checked <?php } ?> > <?php echo $i; ?>
     <div class= "<?php echo $i; ?>  selectt" id = "<?php echo $i; ?>" >
          This data belongs to this year</div>
 </td>
</tr>
<?php } ?>
</table>

<script type="text/javascript">
var cur_year = new Date().getFullYear();

$('div:not(#' + cur_year + ')').hide();  // hide everything that isn't #currentyear

    $(document).ready(function() {
        $('input[type="checkbox"]').click(function() {
            var inputValue = $(this).attr("value");
            $("." + inputValue).toggle();
        });
    });
</script>

</center>
</body>
  
</html>
