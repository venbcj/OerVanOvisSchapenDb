 <!--
 Toegepast in :
	- Afvoerstal.php 1x
	- HokOverpl.php 1x
	- HokAfleveren.php 1x
	- HokAfsluiten.php 3x
	- Inkopen.php 1x
	- invSchaap.php 3x
	- Med_registratie.php 1x
	- UpdSchaap.php 5x
 --> 
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>jQuery UI Datepicker - Default functionality</title>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <link rel="stylesheet" href="/resources/demos/style.css">

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>



$(document).ready(function() {

  var options = {
    dateFormat: "dd-mm-yy",
    dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
    monthNames: [ "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December" ]
    }
        
    $('#datepicker1').datepicker(options);
    $('#datepicker2').datepicker(options);
    $('#datepicker3').datepicker(options);
    $('#datepicker4').datepicker(options);
    $('#datepicker5').datepicker(options);

  	/*$('#datepicker2').datepicker({
  	dateFormat: "dd-mm-yy",
  	dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
  	monthNames: [ "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December" ]
    });*/
	
	
});

  </script>
