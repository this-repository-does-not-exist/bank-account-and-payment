<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use PhpCsFixer\Finder;
use PhpCsFixerConfig\Factory;

return Factory::createForProject()
    ->setFinder(
        Finder::create()
            ->files()
            ->ignoreDotFiles(false)
            ->in(__DIR__),
    )
    ->setUsingCache(false);
