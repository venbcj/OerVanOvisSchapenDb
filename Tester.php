<?php

if (php_uname('n') != 'basq') {
    return;
}

require_once "autoload.php";
require "just_connect_db.php";

$scr = '';
if (isset($_REQUEST['skript'])) {
    $scr = urldecode($_REQUEST['skript']);
}

$results = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $format = isset($_REQUEST['format']);
    ob_start();
    try {
        eval($scr);
    } catch (ParseError $e) {
        if (isset($e->xdebug_message)) {
            # @NOTE: echos below, want we zitten in ob()
            echo '<table>' . $e->xdebug_message . '</table>';
        } else {
            echo '[' . $e->getCode() . '] ' . $e->getMessage();
        }
    } catch (FatalError $e) {
        echo "$e";
    } catch (Exception $e) {
        if (isset($e->xdebug_message)) {
            echo '<table>' . $e->xdebug_message . '</table>';
        } else {
            echo '[' . $e->getCode() . '] ' . $e->getMessage();
        }
    }
    $results = ob_get_clean();
}

$checked = '';
if (isset($_REQUEST['format'])) {
    $checked = 'checked="true"';
}

$format = isset($_REQUEST['format']);
?>
<form method="POST" action="">
<input type="checkbox" id="tab2txt" />tab opvatten als inspringen<br/>
<textarea style="width:100%;" name="skript" rows="20" cols="80" ><?= $scr ?></textarea>
    <br/><input type="checkbox" name="format" value="1" <?= $checked ?>/>preformat
<input type="submit" name="btnOK" value="OK">
</form>
<?php

if ($format) : ?>
<pre><?= $results ?></pre>
<?php else : ?>
    <?= $results ?>
<?php endif; ?>
<script language="javascript" type="text/javascript">
var textareas = document.getElementsByTagName('textarea');
// tabs to spaces. Nice to have, niet essentieel
for (i=0; i<textareas.length; i++) {
    textareas[i].onkeydown = function(e) {
        if (document.getElementById('tab2txt').checked) {
            if (e.keyCode==9 || (event && event.which==9)){
                e.preventDefault();
                var s = this.selectionStart;
                this.value = this.value.substring(0, s) + "    " + this.value.substring(this.selectionEnd);
                this.selectionEnd = s+4; 
            }
        }
    }
}
</script>
