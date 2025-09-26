<?php

# TODO: #0004157 vervangen door flash-struktuur
if (isset($info_beheer)) {
    $msg = $info_beheer;
}
if (isset($goed)) {
    $msg = $goed;
}
if (isset($fout)) {
    $msg = $fout;
}
if (isset($msg)) {
?>
<script language = 'javascript'> 
 alert('<?php echo "$msg"; ?>');
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
