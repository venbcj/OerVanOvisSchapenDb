<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQuerySqlArgumentToSeparateMethodRector\Fixture;

// nog niet af
$res = mysqli_query($db, "SELECT $fields, $dmVroeg, $voerat FROM $table WHERE lidId ='".mysqli_real_escape_string($db, $lidId)."' ORDER BY worst $limit");

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQuerySqlArgumentToSeparateMethodRector\Fixture;

// nog niet af
$res = mysqli_query($db, "SELECT $fields, $dmVroeg, $voerat FROM $table WHERE lidId ='".mysqli_real_escape_string($db, $lidId)."' ORDER BY worst $limit");

?>
