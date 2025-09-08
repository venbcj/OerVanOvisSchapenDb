<br>
<td>
<form method="POST" action="<?php echo $file ?>">
<table border = 0 align = center>
<tr align = center>
<td colspan = 3> Je bent niet ingelogd </td>
</tr>
<?php
$pagina_naam = strtok($_SERVER["REQUEST_URI"], '?');
if ($pagina_naam == '/index.php') : ?>
<tr align = center>
 <td colspan = 3>
    <input type="text" name="txtUser" size="20"><br>
 </td>
</tr>
<tr align = center>
 <td colspan = 3>
    <input type="password" name="txtPassw" size="20"><br>
 </td>
</tr>
<tr align = center>
<td width = 300></td>
 <td>
    <input type="submit" value="Inloggen" name="knpLogin">
 </td>
 <td width = 300>
<?php if ($url == 'https://test.oervanovis.nl/' || $url == 'http://localhost:8080/Schapendb/') : ?>
    <input type="submit" value="Basisgegevens" name="knpBasis">
<?php endif; ?>
 </td>
</tr>
<?php endif; ?>
</table>
</form>
</td>
