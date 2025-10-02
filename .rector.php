<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

try {
    return RectorConfig::configure()
        ->withFileExtensions(['php'])
        ->withCache(
            cacheDirectory: '.rector.result.cache',
            cacheClass: FileCacheStorage::class,
        )
        ->withPhpSets(
            php81: true,
        )
        ->withPaths([
            __DIR__,
        ])
        ->withSkipPath(__DIR__ . '/vendor')
        ->withSkip([
            # skip: use static methods
            PreferPHPUnitThisCallRector::class
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            codingStyle: true,
            typeDeclarations: true,
            privatization: true,
            naming: true,
            instanceOf: true,
            earlyReturn: true,
            strictBooleans: false,
            carbon: true,
            rectorPreset: true,
            phpunitCodeQuality: true,
            doctrineCodeQuality: false,
            symfonyCodeQuality: false,
            symfonyConfigs: false,
        );
} catch (InvalidConfigurationException $exception) {
    echo $exception->getMessage();
    exit(1);
}
