<select name="<?php echo $name; ?>" <?php echo View::attributes($attributes) ?>>
<?php
if ($empty_option) {
    echo '<option></option>';
}
foreach ($collection as $key => $waarde) {
    $selected_attribute = '';
    if ($selected == $key) {
        $selected_attribute = ' selected="selected"';
    }
    echo '<option value="' . $key . '"' . $selected_attribute . '>' . $waarde . '</option>';
}
?> 
</select>
