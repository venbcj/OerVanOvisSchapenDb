<form action="Vader.php" method="post">
<table border=0>
<tr>
  <td width=300> </td>
  <td align="center"> <b> Halsnr</b> </td>
  <td align="center"> <b> Dekram</b> </td>
  <td></td>
  <td></td>
  <td width=200 align="right">
    <?php echo View::link_to('print pagina', "Vader_pdf.php?Id=$pdf", ['style' => 'color: blue;']); ?>
  </td>
</tr>
<tr>
  <td></td>
  <td colspan=4><hr></td>
</tr>
<?php foreach ($records as $record) : ?>
<?php // TODO: FIXME: html is stuk. Je stopt geen tr in een td.  ?>
<tr class="schaap">
  <td></td>
  <td align="right"> <?php echo $record['halsnr']; ?> </td>
  <td align="center"> <?php echo $record['werknr']; ?> </td>            
  <td></td>
</tr>
<?php endforeach; ?>
</table>
</form>
