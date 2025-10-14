<?php
$response = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    exec('git pull 2>&1', $out);
    $response = ':'.implode('<br>', $out);
}

?>
<form method="post">
<input type="submit" value="git pull">
</form>
<?= $response ?>
