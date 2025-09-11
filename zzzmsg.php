
<?php
/* Toegepast in : 
- menu1.php
- menuBeheer.php
- 
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