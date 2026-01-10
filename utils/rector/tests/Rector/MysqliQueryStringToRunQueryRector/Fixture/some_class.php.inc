<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQueryStringToRunQueryRector\Fixture;

$view = mysqli_query($db, "SELECT * FROM tblLeden WHERE lidId='".mysql_real_escape_string($db, $lidId)."'");

?>
-----
<?php

$lid_gateway = new LidGateway();
$view = $lid_gateway->view($lidId);
public function view($lidId) {
    $sql = <<<SQL
SELECT * FROM tblLeden WHERE lidId=:lidId
SQL;
    $args = [[':lidId', $lidId, Type::INT]];
    return $this->run_query($sql, $args);
}

// @todo fill code after

?>
