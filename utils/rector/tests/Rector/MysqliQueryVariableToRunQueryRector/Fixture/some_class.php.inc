<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\Fixture;

mysqli_query($db, 'SELECT');

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\Fixture;

$this->db->query('SELECT');

?>
