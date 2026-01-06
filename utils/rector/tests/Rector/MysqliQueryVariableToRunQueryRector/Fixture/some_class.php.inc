<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\Fixture;

mysqli_query($db, $var);

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\Fixture;

// @TODO: remove if covered by gateway-call
mysqli_query($db, $var);

?>
