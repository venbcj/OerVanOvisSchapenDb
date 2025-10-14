<?php
$response = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = `git pull`;
}

?>
<form method="post">
<input type="submit" value="git pull">
</form>
<?= $response ?>
