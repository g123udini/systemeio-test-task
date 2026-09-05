<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create();
$finder->in(__DIR__ . \DIRECTORY_SEPARATOR . 'src')
       ->in(__DIR__ . \DIRECTORY_SEPARATOR . 'tests')
       ->exclude('tests/Fixtures');

/** @var Config $config */
$config = require __DIR__ . '/vendor/g123udini/php-cs-fixer/.php-cs-fixer.dist.php';
$config->setFinder($finder);

return $config;
