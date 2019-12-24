<?php

use Wieni\wmcodestyle\PhpCsFixer\Config\Factory;
use Wieni\wmcodestyle\PhpCsFixer\Config\RuleSet\Php71;

$config = Factory::fromRuleSet(new Php71);

$config->getFinder()
    ->ignoreVCSIgnored(true)
    ->in(__DIR__)
    ->name('/\.(php|module|inc|install|test|profile|theme)$/');

$config->setCacheFile(__DIR__ . '/.php_cs.cache');

return $config;
