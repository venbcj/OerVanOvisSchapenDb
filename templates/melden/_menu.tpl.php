<?php foreach ($links as $index => $link) { ?>
<tr>
<td>
<?php echo View::link_to($link['caption'], $link['href'], ['class' => 'blue']);
?>
</td>
<td style = "font-size : 12px;">
<?php echo $link['remark']; ?>
</td>
</tr>
<?php } ?>
