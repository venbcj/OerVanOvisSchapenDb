<?php

namespace Rector\Tests\TypeDeclaration\Rector\SplitSqlConcatenationRector\Fixture;

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '" . mysqli_real_escape_string($db, $STALID) . "',
        datum = '" . mysqli_real_escape_string($db, $DATUM) . "',
            actId = '" . mysqli_real_escape_string($db, $ACTID) . "' ";

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\SplitSqlConcatenationRector\Fixture;

public function insert_tblHistorie($STALID, $DATUM, $ACTID) {
$sql = <<<SQL
INSERT INTO tblHistorie SET stalId = ':param1',
        datum = ':param2',
            actId = ':param3' 
SQL;
$arguments = [[':param1', $STALID], [':param2', $DATUM], [':param3', $ACTID]];
$this->run_query($sql, $arguments);
    }

?>
