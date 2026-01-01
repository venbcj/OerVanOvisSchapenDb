<?php

declare(strict_types=1);

use Rector\ValueObject\PhpVersion;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Utils\Rector\Rector\SplitSqlConcatenationRector;
use Utils\Rector\Rector\MysqliQueryFunctionCallToDbMethodCallRector;

return static function (RectorConfig $rectorConfig): void {

    // 1️⃣ Project directories
    $rectorConfig->paths([
        __DIR__ ,
        __DIR__ . '/classes',
        __DIR__ . '/fpdf_stub',
        __DIR__ . '/gateways',
        __DIR__ . '/kladjes',
        __DIR__ . '/tests',
        __DIR__ . '/utils',
    ]);

    // 2️⃣ PHP versie target
    $rectorConfig->phpVersion(PhpVersion::PHP_74);

    // 3️⃣ PHP upgrade ruleset (optioneel, handig voor clean-up)
    $rectorConfig->sets([
        # LevelSetList::UP_TO_PHP_74,
        // LevelSetList::CODE_QUALITY, // kan optioneel nog
    ]);

    // 4️⃣ Eigen Rector rules
    $rectorConfig->rules([
        Utils\Rector\Rector\SplitSqlConcatenationRector::class,
        Utils\Rector\Rector\MysqliQueryToRunQueryRector::class,
        # MysqliQueryFunctionCallToDbMethodCallRector::class,
        // voeg hier andere rules toe als nodig
#          # Utils\Rector\Rector\MysqliQueryFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchRowFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchArrayFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchAssocFunctionCallToDbMethodCallRector::class,
#          # Utils\Rector\Rector\MysqliRealescapestringFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliNumRowsFunctionCallToDbPropertyRector::class,
#          Utils\Rector\Rector\SplitSqlConcatenationRector::class,
#          Utils\Rector\Rector\RemoveOrDieConstructRector::class,
    ]);

    // 5️⃣ Type coverage (0 = geen strikte check)
    # $rectorConfig->typeCoverageLevel(0);
};
# oude config voor 1.2
# <?php
# 
# declare(strict_types=1);
# 
# use Rector\Config\RectorConfig;
# use Rector\ValueObject\PhpVersion;
# use Rector\Set\ValueObject\LevelSetList;
# use Utils\Rector\Rector\DebugAssignRector;
# 
# return RectorConfig::configure()
#     ->withPaths([
#         __DIR__ . '/classes',
#         __DIR__ . '/fpdf_stub',
#         __DIR__ . '/gateways',
#         __DIR__ . '/kladjes',
#         __DIR__ . '/tests',
#         __DIR__ . '/utils',
#     ])
#     // uncomment to reach your current PHP version
#     # ->withSets([LevelSetList::UP_TO_PHP_74])
#     ->withRules([
#          # Utils\Rector\Rector\MysqliQueryFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchRowFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchArrayFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliFetchAssocFunctionCallToDbMethodCallRector::class,
#          # Utils\Rector\Rector\MysqliRealescapestringFunctionCallToDbMethodCallRector::class,
#          Utils\Rector\Rector\MysqliNumRowsFunctionCallToDbPropertyRector::class,
#          Utils\Rector\Rector\SplitSqlConcatenationRector::class,
#          Utils\Rector\Rector\RemoveOrDieConstructRector::class,
#     ])
#     ->withTypeCoverageLevel(0)
#     ->withPhpVersion(PhpVersion::PHP_72)
# ;
