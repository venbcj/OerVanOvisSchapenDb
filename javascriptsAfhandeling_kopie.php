<?php
/* 29-8-2021 Dit bestand heeft msg.php vervangen en autocomplete off is toegevoegd 

Toegepast in : 
- menu1.php
- menuAlerts.php
- menuBeheer.php
- menuFinance.php
- menuInkoop.php
- menuMelden.php
- menuRapport.php
- menuRapport1.php
*/


if (isset($goed)) {$msg = $goed;}

If (isset($fout))  {$msg = $fout;}

If (isset($msg))
{ 
?>	
<script language = 'javascript'> 
 var fout = '<?php echo "$msg"; ?>' ;
 alert(fout); 
 </script>
<?php
}


?>

<script>
$('#datepicker1').attr('autocomplete','off');
$('#datepicker2').attr('autocomplete','off');
$('#datepicker3').attr('autocomplete','off');
$('#datepicker4').attr('autocomplete','off');
$('#datepicker5').attr('autocomplete','off');
</script>