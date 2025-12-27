<?php
// name, collection, selected
foreach ($collection as $caption => $value) {
?>
<input type=radio name="<?php echo $name ?>" value="<?php echo $value ?>"<?php
if ($selected == $value) {
     echo " checked";
}
?>><?php echo $caption ?>
<?php
}
