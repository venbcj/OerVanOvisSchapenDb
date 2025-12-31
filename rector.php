<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/classes',
        __DIR__ . '/fpdf_stub',
        __DIR__ . '/gateways',
        __DIR__ . '/kladjes',
        __DIR__ . '/tests',
        __DIR__ . '/utils',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withRules([
         Utils\Rector\Rector\MysqliQueryFunctionCallToDbMethodCallRector::class,
         Utils\Rector\Rector\MysqliFetchRowFunctionCallToDbMethodCallRector::class,
         Utils\Rector\Rector\MysqliFetchArrayFunctionCallToDbMethodCallRector::class,
         Utils\Rector\Rector\MysqliFetchAssocFunctionCallToDbMethodCallRector::class,
         Utils\Rector\Rector\MysqliRealescapestringFunctionCallToDbMethodCallRector::class,
         Utils\Rector\Rector\MysqliNumRowsFunctionCallToDbPropertyRector::class,
         Utils\Rector\Rector\SplitSqlConcatenationRector::class,
         Utils\Rector\Rector\RemoveOrDieConstructRector::class,
    ])
    ->withTypeCoverageLevel(0)
    ->withPhpVersion(PhpVersion::PHP_72)
;
