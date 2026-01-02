<?php

declare(strict_types=1);

use Rector\ValueObject\PhpVersion;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {

    $rectorConfig->paths([
        __DIR__ ,
        __DIR__ . '/classes',
        __DIR__ . '/fpdf_stub',
        __DIR__ . '/gateways',
        __DIR__ . '/kladjes',
        __DIR__ . '/tests',
        __DIR__ . '/utils',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_74);

    $rectorConfig->sets([
        # LevelSetList::UP_TO_PHP_74,
        # LevelSetList::CODE_QUALITY,
    ]);

    $rectorConfig->rules([
        Utils\Rector\Rector\SplitSqlConcatenationRector::class,
        Utils\Rector\Rector\MysqliQueryVariableToRunQueryRector::class,
        Utils\Rector\Rector\MysqliQueryStringToRunQueryRector::class,
        # vraagt nog meer uitdenken # Utils\Rector\Rector\ExtractGatewayMethodRector::class,
          Utils\Rector\Rector\MysqliFetchRowFunctionCallToDbMethodCallRector::class,
          Utils\Rector\Rector\MysqliFetchArrayFunctionCallToDbMethodCallRector::class,
          Utils\Rector\Rector\MysqliFetchAssocFunctionCallToDbMethodCallRector::class,
          Utils\Rector\Rector\MysqliNumRowsFunctionCallToDbPropertyRector::class,
          Utils\Rector\Rector\RemoveOrDieConstructRector::class,
    ]);

};
