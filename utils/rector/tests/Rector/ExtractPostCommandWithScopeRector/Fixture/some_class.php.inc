<?php

namespace Rector\Tests\TypeDeclaration\Rector\ExtractPostCommandWithScopeRector\Fixture;

if (isset($_POST['knop'])) {
    $var = "something";
}

?>
-----
<?php

namespace Rector\Tests\TypeDeclaration\Rector\ExtractPostCommandWithScopeRector\Fixture;

if (isset($_POST['knop'])) {
    function handle_post_5(array $input, array $__pagestate): array
    {
        extract($__pagestate);
        $var = "something";
        $__pagestate['var'] = $var;
        return $__pagestate;
    }
    foreach (get_defined_vars() as $varName) {
        if (!in_array($varName, ['_GET', '_POST', '_COOKIE', '_SESSION', '_FILES', '_SERVER', '_ENV', 'GLOBALS', '__pageState', 'input'])) {
            $__pageState[$varName] = $value;
        }
    }
    $__pagestate = handle_post_5($_POST, $__pagestate ?? []);
    extract($__pagestate);
}

?>
